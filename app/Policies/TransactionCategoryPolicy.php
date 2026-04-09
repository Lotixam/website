<?php

namespace App\Policies;

use App\Models\TransactionCategory;
use App\Models\User;

class TransactionCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_transactions');
    }

    public function view(User $user, TransactionCategory $category): bool
    {
        return $user->hasPermissionTo('view_transactions');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function update(User $user, TransactionCategory $category): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function delete(User $user, TransactionCategory $category): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_settings');
    }
}
