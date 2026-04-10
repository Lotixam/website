<?php

namespace App\Policies;

use App\Models\Operation;
use App\Models\User;

class OperationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_operations');
    }

    public function view(User $user, Operation $operation): bool
    {
        if ($user->hasRole('collaborator')) {
            return $operation->assignedUsers()->where('user_id', $user->id)->exists();
        }

        return $user->hasPermissionTo('view_operations');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_operations');
    }

    public function update(User $user, Operation $operation): bool
    {
        if ($user->hasRole('collaborator')) {
            return $operation->assignedUsers()->where('user_id', $user->id)->exists()
                && $user->hasPermissionTo('edit_operations');
        }

        return $user->hasPermissionTo('edit_operations');
    }

    public function delete(User $user, Operation $operation): bool
    {
        return $user->hasPermissionTo('delete_operations');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_operations');
    }
}
