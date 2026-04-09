<?php

namespace App\Policies;

use App\Models\Stage;
use App\Models\User;

class StagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_stages');
    }

    public function view(User $user, Stage $stage): bool
    {
        return $user->hasPermissionTo('view_stages');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_stages');
    }

    public function update(User $user, Stage $stage): bool
    {
        return $user->hasPermissionTo('edit_stages');
    }

    public function delete(User $user, Stage $stage): bool
    {
        return $user->hasPermissionTo('delete_stages');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_stages');
    }
}
