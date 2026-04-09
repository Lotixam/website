<?php

namespace App\Enums;

enum LotStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case UnderCompromise = 'under_compromise';
    case Sold = 'sold';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Reserved => 'Réservé',
            self::UnderCompromise => 'Sous compromis',
            self::Sold => 'Vendu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Reserved => 'warning',
            self::UnderCompromise => 'info',
            self::Sold => 'gray',
        };
    }
}
