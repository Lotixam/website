<?php

namespace App\Policies;

use App\Models\PublicRealization;
use App\Models\User;

class PublicRealizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function view(User $user, PublicRealization $publicRealization): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function update(User $user, PublicRealization $publicRealization): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function delete(User $user, PublicRealization $publicRealization): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }
}
