<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;

class PartnerPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin') && $user->can('view_partners')) {
            return true;
        }

        return $user->hasRole('collaborator');
    }

    public function view(User $user, Partner $partner): bool
    {
        if ($user->hasRole('admin') && $user->can('view_partners')) {
            return true;
        }

        return $user->hasRole('collaborator')
            && $user->partner_id !== null
            && (int) $user->partner_id === (int) $partner->getKey();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') && $user->can('create_partners');
    }

    public function update(User $user, Partner $partner): bool
    {
        if ($user->hasRole('admin') && $user->can('edit_partners')) {
            return true;
        }

        return $user->hasRole('collaborator')
            && $user->partner_id !== null
            && (int) $user->partner_id === (int) $partner->getKey();
    }

    public function delete(User $user, Partner $partner): bool
    {
        return $user->hasRole('admin') && $user->can('delete_partners');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin') && $user->can('delete_partners');
    }
}
