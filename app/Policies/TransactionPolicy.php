<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_transactions');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('view_transactions');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_transactions');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('edit_transactions');
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('delete_transactions');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_transactions');
    }
}
