<?php

namespace App\Enums;

enum ContactType: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';
    case Notary = 'notary';
    case Surveyor = 'surveyor';
    case Constructor = 'constructor';
    case Agent = 'agent';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Buyer => 'Acquéreur',
            self::Seller => 'Vendeur',
            self::Notary => 'Notaire',
            self::Surveyor => 'Géomètre',
            self::Constructor => 'Constructeur',
            self::Agent => 'Agent',
            self::Other => 'Autre',
        };
    }
}
