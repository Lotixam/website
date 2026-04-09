<?php

namespace App\Enums;

enum PartnerType: string
{
    case Constructor = 'constructor';
    case Agency = 'agency';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Constructor => 'Constructeur',
            self::Agency => 'Agence',
            self::Other => 'Autre',
        };
    }
}
