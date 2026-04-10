<?php

namespace App\Enums;

enum DocumentRequestStatus: string
{
    case Pending = 'pending';
    case Uploaded = 'uploaded';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Uploaded => 'Déposé',
            self::Approved => 'Validé',
            self::Rejected => 'Refusé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Uploaded => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
