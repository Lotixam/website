<?php

namespace Tests\Feature;

use App\Enums\DocumentRequestStatus;
use App\Enums\OperationParticipantKind;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Enums\WorkflowNodeStatus;
use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowReopenScope;
use App\Models\DocumentRequest;
use App\Models\Operation;
use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Services\Workflow\WorkflowEngine;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WorkflowTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(WorkflowTemplateSeeder::class);
    }

    public function test_instantiate_sets_root_in_progress(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $op = $this->makeOperation();
        $template = WorkflowTemplate::query()->where('key', 'terrain_vendeur_particulier')->firstOrFail();

        app(WorkflowEngine::class)->instantiateFromTemplate($op, $template);

        $root = $op->fresh()->workflowRootNodes()->first();
        $this->assertNotNull($root);
        $this->assertSame(WorkflowNodeStatus::InProgress, $root->status);
    }

    public function test_merge_node_stays_blocked_until_parallel_branches_are_done(): void
    {
        $admin = $this->makeAdmin();
        $client = $this->makeClient('client-merge');
        $this->actingAs($admin);

        $operation = $this->makeOperation();
        $operation->assignedUsers()->attach($client->id, [
            'role' => 'client',
            'participant_kind' => 'buyer',
            'hide_upstream_steps' => false,
        ]);

        $template = WorkflowTemplate::query()->where('key', 'division_parcellaire')->firstOrFail();
        $engine = app(WorkflowEngine::class);
        $engine->instantiateFromTemplate($operation, $template);

        $start = $operation->fresh()->workflowNodes()->where('title', 'Démarrage du projet')->firstOrFail();
        $engine->completeNodeDirectly($admin, $start);

        $financeur = $operation->fresh()->workflowNodes()->where('title', 'Branche financeur')->firstOrFail();
        $apporteur = $operation->fresh()->workflowNodes()->where('title', 'Branche apporteur d’affaires')->firstOrFail();
        $ma = $operation->fresh()->workflowNodes()->where('title', 'Branche constructeur')->firstOrFail();
        $merge = $operation->fresh()->workflowNodes()->where('title', 'Point de fusion')->firstOrFail();

        $this->assertSame(WorkflowNodeStatus::InProgress, $financeur->status);
        $this->assertSame(WorkflowNodeStatus::InProgress, $apporteur->status);
        $this->assertSame(WorkflowNodeStatus::InProgress, $ma->status);

        $this->actingAs($client);
        $engine->submitApproval($client, $financeur, true);
        $this->actingAs($admin);
        $engine->submitApproval($admin, $financeur->fresh(), true);
        $engine->completeNodeDirectly($admin, $apporteur);

        $merge = $merge->fresh();
        $this->assertSame(WorkflowNodeStatus::Blocked, $merge->status);
        $this->assertNotNull($merge->blocked_by_node_id);

        $this->actingAs($client);
        $engine->submitApproval($client, $ma, true);
        $this->actingAs($admin);
        $engine->submitApproval($admin, $ma->fresh(), true);

        $this->assertSame(WorkflowNodeStatus::InProgress, $merge->fresh()->status);
    }

    public function test_visibility_filters_admin_and_b2b_nodes(): void
    {
        $admin = $this->makeAdmin();
        $partner = $this->makeCollaborator('partner-b2b');
        $this->actingAs($admin);

        $operation = $this->makeOperation();
        $operation->assignedUsers()->attach($partner->id, [
            'role' => 'collaborator',
            'participant_kind' => 'partner',
            'hide_upstream_steps' => false,
        ]);

        $template = WorkflowTemplate::query()->where('key', 'division_parcellaire')->firstOrFail();
        $engine = app(WorkflowEngine::class);
        $engine->instantiateFromTemplate($operation, $template);

        $financeur = $operation->fresh()->workflowNodes()->where('title', 'Branche financeur')->firstOrFail();
        $financeur->update(['participant_visibility' => WorkflowParticipantVisibility::HideFromB2b]);

        $visible = $engine->visibleNodesForUser($operation->fresh(), $partner)
            ->pluck('title')
            ->all();

        $this->assertNotContains('Branche apporteur d’affaires', $visible);
        $this->assertNotContains('Branche financeur', $visible);
        $this->assertContains('Branche constructeur', $visible);
    }

    public function test_hide_from_seller_filters_timeline_for_seller_participant(): void
    {
        $admin = $this->makeAdmin();
        $seller = $this->makeSeller('seller-parcel');
        $this->actingAs($admin);

        $operation = $this->makeOperation();
        $operation->assignedUsers()->attach($seller->id, [
            'role' => 'seller',
            'participant_kind' => OperationParticipantKind::Seller->value,
            'hide_upstream_steps' => false,
        ]);

        $template = WorkflowTemplate::query()->where('key', 'division_parcellaire')->firstOrFail();
        $engine = app(WorkflowEngine::class);
        $engine->instantiateFromTemplate($operation, $template);

        $visible = $engine->visibleNodesForUser($operation->fresh(), $seller)
            ->pluck('title')
            ->all();

        $this->assertNotContains('Démarrage du projet', $visible);
        $this->assertNotContains('Branche financeur', $visible);
        $this->assertNotContains('Branche constructeur', $visible);
        $this->assertNotContains('Point de fusion', $visible);
        $this->assertContains('Mise en vente des lots', $visible);
        $this->assertContains('Validation conjointe & clôture', $visible);
    }

    public function test_create_client_approvals_includes_pivot_seller_role(): void
    {
        $admin = $this->makeAdmin();
        $seller = $this->makeSeller('seller-approvals');
        $this->actingAs($admin);

        $operation = $this->makeOperation();
        $operation->assignedUsers()->attach($seller->id, [
            'role' => 'seller',
            'participant_kind' => OperationParticipantKind::Seller->value,
            'hide_upstream_steps' => false,
        ]);

        $template = WorkflowTemplate::query()->where('key', 'terrain_vendeur_particulier')->firstOrFail();
        $engine = app(WorkflowEngine::class);
        $engine->instantiateFromTemplate($operation, $template);

        $firstNode = $operation->fresh()->workflowRootNodes()->firstOrFail();
        $userIds = $firstNode->approvals()
            ->where('actor_role', 'client')
            ->pluck('user_id')
            ->all();

        $this->assertContains($seller->id, $userIds);
    }

    public function test_single_document_reopen_resets_document_and_reopens_node(): void
    {
        $admin = $this->makeAdmin();
        $client = $this->makeClient('client-doc');
        $this->actingAs($admin);

        $operation = $this->makeOperation();
        $operation->assignedUsers()->attach($client->id, [
            'role' => 'client',
            'participant_kind' => 'buyer',
            'hide_upstream_steps' => false,
        ]);

        $template = WorkflowTemplate::query()->where('key', 'terrain_vendeur_particulier')->firstOrFail();
        $engine = app(WorkflowEngine::class);
        $engine->instantiateFromTemplate($operation, $template);

        $node = $operation->fresh()->workflowRootNodes()->firstOrFail();
        $this->actingAs($client);
        $engine->submitApproval($client, $node, true);
        $this->actingAs($admin);
        $engine->submitApproval($admin, $node->fresh(), true);

        $documentRequest = DocumentRequest::create([
            'operation_id' => $operation->id,
            'operation_workflow_node_id' => $node->id,
            'requested_by_user_id' => $admin->id,
            'assigned_to_user_id' => $client->id,
            'name' => 'Pièce à revoir',
            'status' => DocumentRequestStatus::Rejected,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => $admin->id,
        ]);

        $engine->rejectAndReopen(
            $admin,
            $node->fresh(),
            WorkflowReopenScope::SingleDocument,
            $documentRequest->id,
            'Document non conforme',
        );

        $this->assertSame(DocumentRequestStatus::Pending, $documentRequest->fresh()->status);
        $this->assertNull($documentRequest->fresh()->reviewed_at);
        $this->assertSame(WorkflowNodeStatus::InProgress, $node->fresh()->status);
    }

    private function makeOperation(): Operation
    {
        return Operation::create([
            'name' => 'Operation test',
            'type' => OperationType::Terrain,
            'status' => OperationStatus::Prospection,
        ]);
    }

    private function makeAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function makeClient(string $username): User
    {
        $user = User::factory()->create(['username' => $username]);
        $user->assignRole('client');

        return $user;
    }

    private function makeSeller(string $username): User
    {
        $user = User::factory()->create(['username' => $username]);
        $user->assignRole('seller');

        return $user;
    }

    private function makeCollaborator(string $username): User
    {
        $user = User::factory()->create(['username' => $username]);
        $user->assignRole('collaborator');

        return $user;
    }
}
