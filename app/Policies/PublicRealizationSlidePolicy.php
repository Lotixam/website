<?php

namespace App\Policies;

use App\Models\PublicRealizationSlide;
use App\Models\User;

class PublicRealizationSlidePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function view(User $user, PublicRealizationSlide $publicRealizationSlide): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function update(User $user, PublicRealizationSlide $publicRealizationSlide): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function delete(User $user, PublicRealizationSlide $publicRealizationSlide): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }
}
