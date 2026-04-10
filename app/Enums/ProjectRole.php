<?php

namespace App\Enums;

enum ProjectRole: string
{
    case Collaborator = 'collaborator';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Collaborator => 'Collaborateur',
            self::Client => 'Client',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Collaborator => 'info',
            self::Client => 'success',
        };
    }
}
