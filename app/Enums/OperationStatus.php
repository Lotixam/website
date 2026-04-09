<?php

namespace App\Enums;

enum OperationStatus: string
{
    case Prospection = 'prospection';
    case Acquired = 'acquired';
    case InDivision = 'in_division';
    case OnSale = 'on_sale';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Prospection => 'Prospection',
            self::Acquired => 'Acquis',
            self::InDivision => 'En division',
            self::OnSale => 'En vente',
            self::Completed => 'Terminé',
            self::Cancelled => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Prospection => 'gray',
            self::Acquired => 'info',
            self::InDivision => 'warning',
            self::OnSale => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
