<?php

namespace App\Enums;

enum PartnerType: string
{
    case Financier = 'financier';
    case BusinessIntroducer = 'business_introducer';
    case InternalLotixam = 'internal_lotixam';
    case Constructor = 'constructor';
    case Agency = 'agency';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Financier => 'Financeur',
            self::BusinessIntroducer => 'Apporteur d\'affaires',
            self::InternalLotixam => 'Interne Lotixam',
            self::Constructor => 'Constructeur immobilier',
            self::Agency => 'Agence',
            self::Other => 'Autre',
        };
    }
}
