<?php

namespace App\Services\Workflow;

use App\Enums\DocumentRequestStatus;
use App\Enums\OperationParticipantKind;
use App\Enums\WorkflowApprovalState;
use App\Enums\WorkflowNodeStatus;
use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowReopenScope;
use App\Enums\WorkflowValidationPolicy;
use App\Models\DocumentRequest;
use App\Models\Operation;
use App\Models\OperationWorkflowApproval;
use App\Models\OperationWorkflowNode;
use App\Models\User;
use App\Models\WorkflowAuditEvent;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowTemplateNode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WorkflowEngine
{
    public function instantiateFromTemplate(Operation $operation, WorkflowTemplate $template): void
    {
        if ($operation->workflowNodes()->exists()) {
            throw new InvalidArgumentException('Ce projet a déjà un workflow instancié.');
        }

        DB::transaction(function () use ($operation, $template) {
            $operation->update(['workflow_template_id' => $template->id]);

            $allTemplateNodes = WorkflowTemplateNode::query()
                ->where('workflow_template_id', $template->id)
                ->orderBy('sort_order')
                ->get();

            $byParent = $allTemplateNodes->groupBy(fn (WorkflowTemplateNode $n) => $n->parent_id ?? 0);

            $copy = function (WorkflowTemplateNode $tNode, ?int $parentInstanceId) use (&$copy, $byParent, $operation) {
                $inst = OperationWorkflowNode::create([
                    'operation_id' => $operation->id,
                    'workflow_template_node_id' => $tNode->id,
                    'parent_id' => $parentInstanceId,
                    'parallel_group' => $tNode->parallel_group,
                    'is_merge_node' => $tNode->is_merge_node,
                    'sort_order' => $tNode->sort_order,
                    'title' => $tNode->title,
                    'description' => $tNode->description,
                    'validation_policy' => $tNode->validation_policy,
                    'participant_visibility' => $tNode->participant_visibility,
                    'status' => WorkflowNodeStatus::Pending,
                    'metadata' => $tNode->metadata,
                ]);

                foreach ($byParent->get($tNode->id, collect()) as $child) {
                    $copy($child, $inst->id);
                }
            };

            foreach ($byParent->get(0, collect()) as $root) {
                $copy($root, null);
            }

            $operation->unsetRelation('workflowNodes');
            foreach ($operation->workflowRootNodes as $root) {
                $this->startNode($root);
            }

            $this->audit($operation, auth()->id(), 'workflow_instantiated', [
                'template_id' => $template->id,
                'template_key' => $template->key,
            ]);
        });
    }

    public function startNode(OperationWorkflowNode $node): void
    {
        if ($node->status !== WorkflowNodeStatus::Pending && $node->status !== WorkflowNodeStatus::Blocked) {
            return;
        }

        if ($node->parent_id) {
            $parent = $node->parent;
            if (! in_array($parent->status, [WorkflowNodeStatus::Completed, WorkflowNodeStatus::Skipped], true)) {
                return;
            }
        }

        if ($node->is_merge_node && ! $this->canStartMergeNode($node)) {
            $node->update([
                'status' => WorkflowNodeStatus::Blocked,
                'blocked_by_node_id' => $this->findMergeBlocker($node)?->id,
            ]);

            return;
        }

        $node->update([
            'status' => WorkflowNodeStatus::InProgress,
            'started_at' => now(),
            'blocked_by_node_id' => null,
        ]);

        $this->resetAndCreateApprovals($node);

        $this->audit($node->operation, auth()->id(), 'node_started', [
            'node_id' => $node->id,
            'title' => $node->title,
        ]);
    }

    public function submitApproval(User $user, OperationWorkflowNode $node, bool $approved, ?string $comment = null): void
    {
        if ($node->status !== WorkflowNodeStatus::InProgress) {
            throw new InvalidArgumentException('Cette étape n’est pas en cours de validation.');
        }

        $approval = $this->findResolvableApproval($user, $node);
        if (! $approval) {
            throw new InvalidArgumentException('Aucune validation en attente pour cet utilisateur sur cette étape.');
        }

        DB::transaction(function () use ($approval, $approved, $comment, $user, $node) {
            $approval->update([
                'state' => $approved ? WorkflowApprovalState::Approved : WorkflowApprovalState::Rejected,
                'comment' => $comment,
                'decided_at' => now(),
                'user_id' => $user->id,
            ]);

            $this->audit($node->operation, $user->id, $approved ? 'approval_granted' : 'approval_rejected', [
                'node_id' => $node->id,
                'approval_id' => $approval->id,
            ]);

            if (! $approved) {
                $node->update(['status' => WorkflowNodeStatus::Rejected]);

                return;
            }

            if ($this->shouldCompleteAfterApproval($node)) {
                $this->finalizeNodeCompletion($node, $user);
            }
        });
    }

    public function completeNodeDirectly(User $user, OperationWorkflowNode $node): void
    {
        if (! $user->hasRole('admin')) {
            throw new InvalidArgumentException('Seuls les administrateurs peuvent forcer la complétion.');
        }

        if ($node->validation_policy !== WorkflowValidationPolicy::LotixamOnly) {
            throw new InvalidArgumentException('La complétion directe est réservée aux étapes « Lotixam uniquement ».');
        }

        DB::transaction(function () use ($user, $node) {
            $node->approvals()->delete();
            $this->finalizeNodeCompletion($node, $user);
        });
    }

    public function skipNode(User $user, OperationWorkflowNode $node): void
    {
        if (! $user->hasRole('admin')) {
            throw new InvalidArgumentException('Seuls les administrateurs peuvent passer une étape.');
        }

        DB::transaction(function () use ($user, $node) {
            $node->update([
                'status' => WorkflowNodeStatus::Skipped,
                'completed_at' => now(),
            ]);
            $node->approvals()->delete();

            $this->audit($node->operation, $user->id, 'node_skipped', ['node_id' => $node->id]);

            $this->afterNodeFinished($node);
        });
    }

    public function rejectAndReopen(
        User $user,
        OperationWorkflowNode $node,
        WorkflowReopenScope $scope,
        ?int $documentRequestId = null,
        ?string $comment = null,
    ): void {
        if (! $user->hasRole('admin')) {
            throw new InvalidArgumentException('Action réservée aux administrateurs.');
        }

        DB::transaction(function () use ($user, $node, $scope, $comment) {
            $this->audit($node->operation, $user->id, 'reopen_requested', [
                'node_id' => $node->id,
                'scope' => $scope->value,
                'comment' => $comment,
            ]);

            $targets = match ($scope) {
                WorkflowReopenScope::NodeOnly => collect([$node]),
                WorkflowReopenScope::BranchSubtree => $this->subtreeNodes($node),
                WorkflowReopenScope::FromRoot => $node->operation->workflowNodes,
                WorkflowReopenScope::SingleDocument => collect(),
            };

            if ($scope === WorkflowReopenScope::SingleDocument) {
                $this->reopenSingleDocument($user, $node, $documentRequestId, $comment);

                return;
            }

            foreach ($targets as $n) {
                $n->update([
                    'status' => WorkflowNodeStatus::Pending,
                    'started_at' => null,
                    'completed_at' => null,
                    'blocked_by_node_id' => null,
                ]);
                $n->approvals()->delete();
            }

            if ($scope === WorkflowReopenScope::FromRoot) {
                $root = $node->operation->workflowRootNodes()->orderBy('sort_order')->first();
                if ($root) {
                    $this->startNode($root);
                }
            } elseif ($scope === WorkflowReopenScope::NodeOnly) {
                $this->startNode($node);
            } else {
                $this->startNode($node);
            }
        });
    }

    public function visibleNodesForUser(Operation $operation, User $user): Collection
    {
        $nodes = $operation->workflowNodes()->orderBy('sort_order')->get();

        if ($user->hasRole('admin')) {
            return $nodes;
        }

        $nodes = $nodes->filter(function (OperationWorkflowNode $n) {
            if ($n->participant_visibility === WorkflowParticipantVisibility::AdminOnly) {
                return false;
            }

            return true;
        });

        $assignment = $operation->assignedUsers()->where('users.id', $user->id)->first();
        $pivot = $assignment?->pivot;
        $isPartner = $pivot && ($pivot->participant_kind === 'partner' || ($pivot->role === 'collaborator' && $pivot->participant_kind === 'partner'));
        $isSeller = $pivot && $pivot->participant_kind === OperationParticipantKind::Seller->value;

        $filtered = $nodes->filter(function (OperationWorkflowNode $n) use ($user, $isPartner, $isSeller) {
            if ($n->participant_visibility === WorkflowParticipantVisibility::AdminOnly) {
                return $user->hasRole('admin');
            }
            if ($n->participant_visibility === WorkflowParticipantVisibility::HideFromB2b && $isPartner) {
                return false;
            }
            if ($n->participant_visibility === WorkflowParticipantVisibility::HideFromSeller && $isSeller) {
                return false;
            }

            return true;
        });

        if ($pivot && $pivot->workflow_entry_node_id && $pivot->hide_upstream_steps) {
            $entry = OperationWorkflowNode::find($pivot->workflow_entry_node_id);
            if ($entry && $entry->operation_id === $operation->id) {
                $allowedIds = $this->subtreeNodes($entry)->pluck('id')->all();
                $filtered = $filtered->filter(fn (OperationWorkflowNode $n) => in_array($n->id, $allowedIds, true));
            }
        }

        return $filtered->values();
    }

    public function blockingSummaryForAdmin(Operation $operation): array
    {
        $blocked = $operation->workflowNodes()
            ->where('status', WorkflowNodeStatus::Blocked)
            ->get();

        return $blocked->map(fn (OperationWorkflowNode $n) => [
            'node_id' => $n->id,
            'title' => $n->title,
            'blocked_by_node_id' => $n->blocked_by_node_id,
            'blocked_by_title' => $n->blockedBy?->title,
        ])->all();
    }

    protected function finalizeNodeCompletion(OperationWorkflowNode $node, User $user): void
    {
        $node->update([
            'status' => WorkflowNodeStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->audit($node->operation, $user->id, 'node_completed', [
            'node_id' => $node->id,
            'title' => $node->title,
        ]);

        $this->afterNodeFinished($node);
    }

    protected function afterNodeFinished(OperationWorkflowNode $node): void
    {
        $this->activateChildrenOf($node);
        $this->tryUnblockMergesForParent($node);
    }

    protected function activateChildrenOf(OperationWorkflowNode $node): void
    {
        foreach ($node->children()->orderBy('sort_order')->get() as $child) {
            if ($child->is_merge_node) {
                if ($this->canStartMergeNode($child)) {
                    $this->startNode($child);
                } else {
                    $child->update([
                        'status' => WorkflowNodeStatus::Blocked,
                        'blocked_by_node_id' => $this->findMergeBlocker($child)?->id,
                    ]);
                }
            } else {
                $this->startNode($child);
            }
        }
    }

    protected function tryUnblockMergesForParent(OperationWorkflowNode $finished): void
    {
        $parent = $finished->parent;
        if (! $parent) {
            return;
        }

        foreach ($parent->children()->where('is_merge_node', true)->get() as $merge) {
            if (! in_array($merge->status, [WorkflowNodeStatus::Pending, WorkflowNodeStatus::Blocked], true)) {
                continue;
            }
            if ($this->canStartMergeNode($merge)) {
                $merge->update(['status' => WorkflowNodeStatus::Pending, 'blocked_by_node_id' => null]);
                $this->startNode($merge);
            } else {
                $merge->update([
                    'status' => WorkflowNodeStatus::Blocked,
                    'blocked_by_node_id' => $this->findMergeBlocker($merge)?->id,
                ]);
            }
        }
    }

    protected function canStartMergeNode(OperationWorkflowNode $merge): bool
    {
        if (! $merge->is_merge_node) {
            return true;
        }

        $parent = $merge->parent;
        if (! $parent) {
            return true;
        }

        $branchRoots = $parent->children()
            ->where('id', '!=', $merge->id)
            ->where('is_merge_node', false)
            ->get();

        if ($branchRoots->isEmpty()) {
            return true;
        }

        foreach ($branchRoots as $root) {
            if (! $this->isSubtreeTerminal($root)) {
                return false;
            }
        }

        return true;
    }

    protected function findMergeBlocker(OperationWorkflowNode $merge): ?OperationWorkflowNode
    {
        $parent = $merge->parent;
        if (! $parent) {
            return null;
        }

        $branchRoots = $parent->children()
            ->where('id', '!=', $merge->id)
            ->where('is_merge_node', false)
            ->get();

        foreach ($branchRoots as $root) {
            if (! $this->isSubtreeTerminal($root)) {
                return $this->firstIncompleteInSubtree($root) ?? $root;
            }
        }

        return null;
    }

    protected function isSubtreeTerminal(OperationWorkflowNode $root): bool
    {
        if (! in_array($root->status, [WorkflowNodeStatus::Completed, WorkflowNodeStatus::Skipped], true)) {
            return false;
        }

        foreach ($root->children as $child) {
            if (! $this->isSubtreeTerminal($child)) {
                return false;
            }
        }

        return true;
    }

    protected function firstIncompleteInSubtree(OperationWorkflowNode $root): ?OperationWorkflowNode
    {
        if (! in_array($root->status, [WorkflowNodeStatus::Completed, WorkflowNodeStatus::Skipped], true)) {
            return $root;
        }

        foreach ($root->children()->orderBy('sort_order')->get() as $child) {
            $found = $this->firstIncompleteInSubtree($child);
            if ($found) {
                return $found;
            }
        }

        return null;
    }

    protected function subtreeNodes(OperationWorkflowNode $root): Collection
    {
        $out = collect([$root]);
        foreach ($root->children as $child) {
            $out = $out->merge($this->subtreeNodes($child));
        }

        return $out;
    }

    protected function resetAndCreateApprovals(OperationWorkflowNode $node): void
    {
        $node->approvals()->delete();

        $op = $node->operation;

        match ($node->validation_policy) {
            WorkflowValidationPolicy::LotixamOnly => $this->createLotixamApproval($node),
            WorkflowValidationPolicy::ClientOnly => $this->createClientApprovals($node, $op),
            WorkflowValidationPolicy::BothAll => tap(null, function () use ($node, $op) {
                $this->createLotixamApproval($node);
                $this->createClientApprovals($node, $op);
            }),
            WorkflowValidationPolicy::CustomNOfM => $this->createCustomApprovals($node, $op),
        };
    }

    protected function createLotixamApproval(OperationWorkflowNode $node): void
    {
        OperationWorkflowApproval::create([
            'operation_workflow_node_id' => $node->id,
            'actor_role' => 'lotixam',
            'state' => WorkflowApprovalState::Pending,
        ]);
    }

    protected function createClientApprovals(OperationWorkflowNode $node, Operation $op): void
    {
        $clients = $op->assignedUsers()->wherePivotIn('role', ['client', 'seller'])->get();
        if ($clients->isEmpty()) {
            OperationWorkflowApproval::create([
                'operation_workflow_node_id' => $node->id,
                'actor_role' => 'client',
                'state' => WorkflowApprovalState::Pending,
            ]);

            return;
        }

        foreach ($clients as $client) {
            OperationWorkflowApproval::create([
                'operation_workflow_node_id' => $node->id,
                'user_id' => $client->id,
                'actor_role' => 'client',
                'state' => WorkflowApprovalState::Pending,
            ]);
        }
    }

    protected function createCustomApprovals(OperationWorkflowNode $node, Operation $op): void
    {
        $approvers = $op->assignedUsers()->get();

        if ($approvers->isEmpty()) {
            $this->createLotixamApproval($node);

            return;
        }

        foreach ($approvers as $approver) {
            OperationWorkflowApproval::create([
                'operation_workflow_node_id' => $node->id,
                'user_id' => $approver->id,
                'actor_role' => 'custom',
                'state' => WorkflowApprovalState::Pending,
            ]);
        }
    }

    public function userCanApprove(User $user, OperationWorkflowNode $node): bool
    {
        return $this->findResolvableApproval($user, $node) !== null;
    }

    protected function findResolvableApproval(User $user, OperationWorkflowNode $node): ?OperationWorkflowApproval
    {
        $pending = $node->approvals()->where('state', WorkflowApprovalState::Pending)->get();

        foreach ($pending as $approval) {
            if ($approval->actor_role === 'lotixam' && $this->isLotixamApprover($user, $node->operation)) {
                return $approval;
            }
            if ($approval->actor_role === 'client') {
                if ($approval->user_id && $approval->user_id === $user->id) {
                    return $approval;
                }
                if (! $approval->user_id && ($user->hasRole('client') || $user->hasRole('seller'))) {
                    return $approval;
                }
            }
            if ($approval->actor_role === 'custom') {
                if ($approval->user_id && $approval->user_id === $user->id) {
                    return $approval;
                }
                if (! $approval->user_id && $this->isLotixamApprover($user, $node->operation)) {
                    return $approval;
                }
            }
        }

        return null;
    }

    protected function shouldCompleteAfterApproval(OperationWorkflowNode $node): bool
    {
        if ($node->approvals()->where('state', WorkflowApprovalState::Rejected)->exists()) {
            return false;
        }

        if ($node->validation_policy === WorkflowValidationPolicy::CustomNOfM) {
            $approvedCount = $node->approvals()->where('state', WorkflowApprovalState::Approved)->count();

            return $approvedCount >= $this->requiredApprovalsForCustom($node);
        }

        return ! $node->approvals()->where('state', WorkflowApprovalState::Pending)->exists();
    }

    protected function requiredApprovalsForCustom(OperationWorkflowNode $node): int
    {
        $required = (int) data_get($node->metadata, 'required_approvals', 1);
        $total = max(1, $node->approvals()->count());

        return max(1, min($required, $total));
    }

    protected function isLotixamApprover(User $user, Operation $operation): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (! $user->hasRole('collaborator')) {
            return false;
        }

        return $operation->assignedUsers()
            ->where('users.id', $user->id)
            ->wherePivot('participant_kind', 'internal')
            ->exists();
    }

    protected function reopenSingleDocument(
        User $user,
        OperationWorkflowNode $node,
        ?int $documentRequestId,
        ?string $comment = null,
    ): void {
        if (! $documentRequestId) {
            throw new InvalidArgumentException('Sélectionnez un document à réviser.');
        }

        $documentRequest = DocumentRequest::query()
            ->whereKey($documentRequestId)
            ->where('operation_id', $node->operation_id)
            ->where('operation_workflow_node_id', $node->id)
            ->first();

        if (! $documentRequest) {
            throw new InvalidArgumentException('Le document sélectionné ne correspond pas à cette étape.');
        }

        $documentRequest->update([
            'status' => DocumentRequestStatus::Pending,
            'reviewed_at' => null,
            'reviewed_by_user_id' => null,
            'document_id' => null,
        ]);

        if (in_array($node->status, [WorkflowNodeStatus::Completed, WorkflowNodeStatus::Rejected, WorkflowNodeStatus::Blocked], true)) {
            $node->update([
                'status' => WorkflowNodeStatus::InProgress,
                'completed_at' => null,
                'blocked_by_node_id' => null,
            ]);
        }

        $this->audit($node->operation, $user->id, 'single_document_reopen', [
            'node_id' => $node->id,
            'document_request_id' => $documentRequest->id,
            'comment' => $comment,
        ]);
    }

    protected function audit(Operation $operation, ?int $userId, string $type, array $payload = []): void
    {
        WorkflowAuditEvent::create([
            'operation_id' => $operation->id,
            'user_id' => $userId,
            'event_type' => $type,
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }

    /**
     * @return Collection<int, OperationWorkflowNode>
     */
    public function orderedVisibleNodesForDisplay(Operation $operation, User $user): Collection
    {
        $visible = $this->visibleNodesForUser($operation, $user);
        $byId = $visible->keyBy('id');

        $depth = function (OperationWorkflowNode $n) use ($byId): int {
            $d = 0;
            $cur = $n;
            while ($cur->parent_id && $byId->has($cur->parent_id)) {
                $d++;
                $cur = $byId[$cur->parent_id];
            }

            return $d;
        };

        return $visible->sort(function (OperationWorkflowNode $a, OperationWorkflowNode $b) use ($depth) {
            $da = $depth($a);
            $db = $depth($b);
            if ($da !== $db) {
                return $da <=> $db;
            }
            if ($a->parent_id !== $b->parent_id) {
                return ($a->parent_id ?? 0) <=> ($b->parent_id ?? 0);
            }

            return $a->sort_order <=> $b->sort_order;
        })->values();
    }

    public function clientProgressPercent(Operation $operation, User $user): int
    {
        $visible = $this->visibleNodesForUser($operation, $user);
        if ($visible->isEmpty()) {
            $operation->loadMissing(['stages']);
            $completed = $operation->stages->where('pivot.status', 'completed')->count();
            $total = $operation->stages->count();

            return $total > 0 ? (int) round($completed / $total * 100) : 0;
        }

        $done = $visible->filter(fn (OperationWorkflowNode $n) => in_array($n->status, [WorkflowNodeStatus::Completed, WorkflowNodeStatus::Skipped], true));

        return (int) round($done->count() / max($visible->count(), 1) * 100);
    }

    public function closeOperationByLotixam(User $user, Operation $operation): void
    {
        if (! $user->hasRole('admin')) {
            throw new InvalidArgumentException('Seuls les administrateurs peuvent clôturer définitivement un projet.');
        }

        $operation->update([
            'closed_at' => now(),
            'closed_by_user_id' => $user->id,
        ]);

        $this->audit($operation, $user->id, 'operation_closed', []);
    }
}
