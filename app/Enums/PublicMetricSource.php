<?php

namespace App\Enums;

enum PublicMetricSource: string
{
    case Manual = 'manual';
    case OperationsCompleted = 'operations_completed';
    case LotsSold = 'lots_sold';
    case OperationsInProgress = 'operations_in_progress';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Valeur manuelle',
            self::OperationsCompleted => 'Opérations terminées',
            self::LotsSold => 'Biens vendus',
            self::OperationsInProgress => 'Opérations en cours',
        };
    }
}
