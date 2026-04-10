<?php

namespace App\Enums;

enum WorkflowValidationPolicy: string
{
    case LotixamOnly = 'lotixam_only';
    case ClientOnly = 'client_only';
    case BothAll = 'both_all';
    case CustomNOfM = 'custom_n_of_m';

    public function label(): string
    {
        return match ($this) {
            self::LotixamOnly => 'Lotixam uniquement',
            self::ClientOnly => 'Client / partie uniquement',
            self::BothAll => 'Lotixam et client (tous)',
            self::CustomNOfM => 'Validation personnalisée (N sur M)',
        };
    }
}
