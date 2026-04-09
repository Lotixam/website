<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_operations', 'create_operations', 'edit_operations', 'delete_operations',
            'view_lots', 'create_lots', 'edit_lots', 'delete_lots',
            'view_contacts', 'create_contacts', 'edit_contacts', 'delete_contacts',
            'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions',
            'view_documents', 'create_documents', 'edit_documents', 'delete_documents',
            'view_partners', 'create_partners', 'edit_partners', 'delete_partners',
            'view_events', 'create_events', 'edit_events', 'delete_events',
            'view_stages', 'create_stages', 'edit_stages', 'delete_stages',
            'manage_users', 'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $collaborator = Role::firstOrCreate(['name' => 'collaborator']);
        $collaborator->syncPermissions(array_filter($permissions, fn ($p) => ! in_array($p, ['manage_users', 'manage_settings'])));

        $readonly = Role::firstOrCreate(['name' => 'readonly']);
        $readonly->syncPermissions(array_filter($permissions, fn ($p) => str_starts_with($p, 'view_')));
    }
}
