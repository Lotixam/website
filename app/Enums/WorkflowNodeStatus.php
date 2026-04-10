<?php

namespace App\Enums;

enum WorkflowNodeStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Blocked = 'blocked';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::InProgress => 'En cours',
            self::Completed => 'Terminé',
            self::Skipped => 'Passée',
            self::Blocked => 'Bloqué',
            self::Rejected => 'Refusé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::Skipped => 'gray',
            self::Blocked => 'danger',
            self::Rejected => 'danger',
        };
    }
}
