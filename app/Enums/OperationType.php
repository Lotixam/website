<?php

namespace App\Enums;

enum OperationType: string
{
    case Maison = 'maison';
    case Terrain = 'terrain';
    case Immeuble = 'immeuble';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Maison => 'Maison',
            self::Terrain => 'Terrain',
            self::Immeuble => 'Immeuble',
            self::Autre => 'Autre',
        };
    }
}
