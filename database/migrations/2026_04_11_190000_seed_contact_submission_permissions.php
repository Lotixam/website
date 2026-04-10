<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Les entrées existaient déjà dans RoleSeeder ; sans seed en prod, la table permissions
     * ne les contenait pas → Filament / can() lève PermissionDoesNotExist.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');
        $names = ['view_contact_submissions', 'delete_contact_submissions'];

        foreach ($names as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
            );
        }

        foreach (['admin', 'collaborator'] as $roleName) {
            $role = Role::query()
                ->where('name', $roleName)
                ->where('guard_name', $guard)
                ->first();

            if ($role) {
                $role->givePermissionTo($names);
            }
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        Permission::query()
            ->where('guard_name', $guard)
            ->whereIn('name', ['view_contact_submissions', 'delete_contact_submissions'])
            ->delete();
    }
};
