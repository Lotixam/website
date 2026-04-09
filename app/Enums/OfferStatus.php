<?php

namespace App\Enums;

enum OfferStatus: string
{
    case Proposed = 'proposed';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Proposed => 'Proposée',
            self::Accepted => 'Acceptée',
            self::Rejected => 'Refusée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Proposed => 'warning',
            self::Accepted => 'success',
            self::Rejected => 'danger',
        };
    }
}
