<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_contacts');
    }

    public function view(User $user, Contact $contact): bool
    {
        return $user->hasPermissionTo('view_contacts');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_contacts');
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->hasPermissionTo('edit_contacts');
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->hasPermissionTo('delete_contacts');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_contacts');
    }
}
