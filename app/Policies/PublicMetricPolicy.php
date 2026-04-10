<?php

namespace App\Policies;

use App\Models\PublicMetric;
use App\Models\User;

class PublicMetricPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function view(User $user, PublicMetric $publicMetric): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function update(User $user, PublicMetric $publicMetric): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function delete(User $user, PublicMetric $publicMetric): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }
}
