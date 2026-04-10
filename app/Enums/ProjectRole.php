<?php

namespace App\Enums;

enum ProjectRole: string
{
    case Collaborator = 'collaborator';
    case Client = 'client';
    case Seller = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::Collaborator => 'Collaborateur',
            self::Client => 'Client',
            self::Seller => 'Vendeur',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Collaborator => 'info',
            self::Client => 'success',
            self::Seller => 'warning',
        };
    }
}
