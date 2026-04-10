<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_messages');
    }

    public function view(User $user, Message $message): bool
    {
        if ($user->hasRole('client')) {
            return $message->operation
                ->assignedUsers()
                ->where('user_id', $user->id)
                ->exists();
        }

        return $user->hasPermissionTo('view_messages');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('send_messages');
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->hasRole('admin');
    }
}
