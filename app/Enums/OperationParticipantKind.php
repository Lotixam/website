<?php

namespace App\Enums;

enum OperationParticipantKind: string
{
    case Seller = 'seller';
    case Buyer = 'buyer';
    case Partner = 'partner';
    case Collaborator = 'collaborator';
    case Internal = 'internal';

    public function label(): string
    {
        return match ($this) {
            self::Seller => 'Vendeur',
            self::Buyer => 'Acheteur',
            self::Partner => 'Partenaire B2B',
            self::Collaborator => 'Collaborateur',
            self::Internal => 'Interne',
        };
    }
}
