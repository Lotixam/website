<?php

namespace App\Enums;

enum StageStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::InProgress => 'En cours',
            self::Completed => 'Terminé',
            self::Skipped => 'Ignoré',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::Skipped => 'info',
        };
    }
}
