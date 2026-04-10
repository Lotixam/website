<?php

namespace App\Enums;

enum WorkflowParticipantVisibility: string
{
    case AllAssigned = 'all_assigned';
    case AdminOnly = 'admin_only';
    case HideFromB2b = 'hide_from_b2b';

    public function label(): string
    {
        return match ($this) {
            self::AllAssigned => 'Tous les participants assignés',
            self::AdminOnly => 'Admins uniquement',
            self::HideFromB2b => 'Masquer aux partenaires B2B',
        };
    }
}
