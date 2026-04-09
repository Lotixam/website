<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;

class PartnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_partners');
    }

    public function view(User $user, Partner $partner): bool
    {
        return $user->hasPermissionTo('view_partners');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_partners');
    }

    public function update(User $user, Partner $partner): bool
    {
        return $user->hasPermissionTo('edit_partners');
    }

    public function delete(User $user, Partner $partner): bool
    {
        return $user->hasPermissionTo('delete_partners');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_partners');
    }
}
