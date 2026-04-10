<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        $permissions = [
            'view_operations', 'create_operations', 'edit_operations', 'delete_operations',
            'view_lots', 'create_lots', 'edit_lots', 'delete_lots',
            'view_contacts', 'create_contacts', 'edit_contacts', 'delete_contacts',
            'view_contact_submissions', 'delete_contact_submissions',
            'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions',
            'view_documents', 'create_documents', 'edit_documents', 'delete_documents',
            'view_partners', 'create_partners', 'edit_partners', 'delete_partners',
            'view_events', 'create_events', 'edit_events', 'delete_events',
            'view_stages', 'create_stages', 'edit_stages', 'delete_stages',
            'view_document_requests', 'create_document_requests', 'edit_document_requests',
            'view_messages', 'send_messages',
            'manage_users', 'manage_settings',
            'view_own_operations', 'upload_documents',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $admin->syncPermissions($permissions);

        $collaboratorPerms = array_filter($permissions, fn ($p) => ! in_array($p, [
            'manage_users', 'manage_settings', 'view_own_operations', 'upload_documents',
        ]));
        $collaborator = Role::firstOrCreate(['name' => 'collaborator', 'guard_name' => $guard]);
        $collaborator->syncPermissions($collaboratorPerms);

        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => $guard]);
        $client->syncPermissions([
            'view_own_operations', 'upload_documents', 'view_messages', 'send_messages',
            'view_document_requests',
        ]);

        $seller = Role::firstOrCreate(['name' => 'seller', 'guard_name' => $guard]);
        $seller->syncPermissions([
            'view_own_operations', 'upload_documents', 'view_messages', 'send_messages',
            'view_document_requests',
        ]);

        // Nettoyage ancien rôle readonly
        Role::where('name', 'readonly')->delete();
    }
}
