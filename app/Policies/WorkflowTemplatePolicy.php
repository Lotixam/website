<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowTemplate;

class WorkflowTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('view_stages');
    }

    public function view(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('view_stages');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('create_stages');
    }

    public function update(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('edit_stages');
    }

    public function delete(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('delete_stages');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('delete_stages');
    }
}
