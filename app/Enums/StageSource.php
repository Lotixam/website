<?php

namespace App\Enums;

enum StageSource: string
{
    case Default = 'default';
    case Admin = 'admin';
    case Collaborator = 'collaborator';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Par défaut',
            self::Admin => 'Lotixam',
            self::Collaborator => 'Collaborateur',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Default => '#b1e90e',
            self::Admin => '#b1e90e',
            self::Collaborator => '#3b82f6',
        };
    }
}
