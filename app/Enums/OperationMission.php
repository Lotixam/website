<?php

namespace App\Enums;

enum OperationMission: string
{
    case Acquire = 'acquire';
    case Sell = 'sell';
    case Divide = 'divide';

    public function label(): string
    {
        return match ($this) {
            self::Acquire => 'Acquérir / Acheter',
            self::Sell => 'Vendre',
            self::Divide => 'Diviser',
        };
    }
}
