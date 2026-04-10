<?php

namespace App\Policies;

use App\Models\DocumentRequest;
use App\Models\User;

class DocumentRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_document_requests');
    }

    public function view(User $user, DocumentRequest $documentRequest): bool
    {
        if ($user->hasRole('client') || $user->hasRole('seller')) {
            return $documentRequest->assigned_to_user_id === $user->id;
        }

        if ($user->hasRole('collaborator')) {
            return $documentRequest->operation
                ->assignedUsers()
                ->where('user_id', $user->id)
                ->exists();
        }

        return $user->hasPermissionTo('view_document_requests');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_document_requests');
    }

    public function update(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->hasPermissionTo('edit_document_requests');
    }

    public function delete(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->hasRole('admin');
    }
}
