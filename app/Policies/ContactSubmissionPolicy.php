<?php

namespace App\Policies;

use App\Models\ContactSubmission;
use App\Models\User;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_contact_submissions');
    }

    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->hasPermissionTo('view_contact_submissions');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactSubmission $contactSubmission): bool
    {
        return false;
    }

    public function delete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->hasPermissionTo('delete_contact_submissions');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_contact_submissions');
    }

    public function restore(User $user, ContactSubmission $contactSubmission): bool
    {
        return false;
    }

    public function forceDelete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->hasPermissionTo('delete_contact_submissions');
    }
}
