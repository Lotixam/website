<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_events');
    }

    public function view(User $user, Event $event): bool
    {
        return $user->hasPermissionTo('view_events');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_events');
    }

    public function update(User $user, Event $event): bool
    {
        return $user->hasPermissionTo('edit_events');
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->hasPermissionTo('delete_events');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_events');
    }
}
