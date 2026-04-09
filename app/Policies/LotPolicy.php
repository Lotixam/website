<?php

namespace App\Policies;

use App\Models\Lot;
use App\Models\User;

class LotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_lots');
    }

    public function view(User $user, Lot $lot): bool
    {
        return $user->hasPermissionTo('view_lots');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_lots');
    }

    public function update(User $user, Lot $lot): bool
    {
        return $user->hasPermissionTo('edit_lots');
    }

    public function delete(User $user, Lot $lot): bool
    {
        return $user->hasPermissionTo('delete_lots');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_lots');
    }
}
