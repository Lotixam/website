<?php

namespace Tests\Feature\Api;

use App\Enums\EventType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Models\Event;
use App\Models\Operation;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_returns_token_for_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => $admin->username,
            'password' => 'password',
            'device_name' => 'PHPUnit',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_id', 'user' => ['id', 'roles']]);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->postJson('/api/v1/auth/login', [
            'username' => $admin->username,
            'password' => 'wrong-password',
            'device_name' => 'PHPUnit',
        ])->assertStatus(422);
    }

    public function test_login_forbidden_for_client_role(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $this->postJson('/api/v1/auth/login', [
            'username' => $client->username,
            'password' => 'password',
            'device_name' => 'PHPUnit',
        ])->assertForbidden();
    }

    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'username' => $user->username,
                'password' => 'wrong',
                'device_name' => 't',
            ]);
        }

        $this->postJson('/api/v1/auth/login', [
            'username' => $user->username,
            'password' => 'wrong',
            'device_name' => 't',
        ])->assertStatus(429);
    }

    public function test_me_requires_bearer_token(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_me_returns_profile_with_bearer(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = $admin->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonFragment(['username' => $admin->username])
            ->assertJsonStructure(['permissions']);
    }

    public function test_logout_revokes_current_token(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = $admin->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/me')
            ->assertUnauthorized();
    }

    public function test_collaborator_cannot_view_unassigned_operation(): void
    {
        $collab = User::factory()->create(['username' => 'collab-api']);
        $collab->assignRole('collaborator');

        $operation = Operation::create([
            'name' => 'Secrète',
            'type' => OperationType::Terrain,
            'status' => OperationStatus::Prospection,
        ]);

        $token = $collab->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/operations/'.$operation->id)
            ->assertForbidden();
    }

    public function test_collaborator_can_view_assigned_operation(): void
    {
        $collab = User::factory()->create(['username' => 'collab-api-2']);
        $collab->assignRole('collaborator');

        $operation = Operation::create([
            'name' => 'Assignée',
            'type' => OperationType::Terrain,
            'status' => OperationStatus::Prospection,
        ]);
        $operation->assignedUsers()->attach($collab->id, [
            'role' => 'collaborator',
            'assigned_at' => now(),
        ]);

        $token = $collab->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/operations/'.$operation->id)
            ->assertOk()
            ->assertJsonPath('data.name', 'Assignée');
    }

    public function test_users_index_forbidden_for_collaborator(): void
    {
        $collab = User::factory()->create();
        $collab->assignRole('collaborator');
        $token = $collab->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/users')
            ->assertForbidden();
    }

    public function test_users_index_ok_for_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = $admin->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/users')
            ->assertOk();
    }

    public function test_events_filtered_by_updated_since(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $op = Operation::create([
            'name' => 'Op',
            'type' => OperationType::Terrain,
            'status' => OperationStatus::Prospection,
        ]);

        Event::create([
            'operation_id' => $op->id,
            'title' => 'Rdv',
            'start_at' => now()->addDay(),
            'type' => EventType::Reminder,
            'is_completed' => false,
        ]);

        $token = $admin->createToken('t', ['*']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/events?updated_since='.now()->subMinute()->toIso8601String())
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    public function test_token_list_and_create_and_revoke(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $first = $admin->createToken('device-a', ['*']);

        $this->withToken($first->plainTextToken)
            ->getJson('/api/v1/tokens')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $create = $this->withToken($first->plainTextToken)
            ->postJson('/api/v1/tokens', ['device_name' => 'device-b'])
            ->assertCreated();

        $secondId = $create->json('token_id');
        $this->assertNotEmpty($create->json('token'));

        $this->withToken($first->plainTextToken)
            ->deleteJson('/api/v1/tokens/'.$secondId)
            ->assertOk();

        $this->withToken($first->plainTextToken)
            ->getJson('/api/v1/tokens')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
