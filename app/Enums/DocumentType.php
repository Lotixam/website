<?php

namespace App\Enums;

enum DocumentType: string
{
    case Deed = 'deed';
    case Plan = 'plan';
    case Permit = 'permit';
    case Diagnostic = 'diagnostic';
    case Compromise = 'compromise';
    case Invoice = 'invoice';
    case Photo = 'photo';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Deed => 'Acte',
            self::Plan => 'Plan',
            self::Permit => 'Permis',
            self::Diagnostic => 'Diagnostic',
            self::Compromise => 'Compromis',
            self::Invoice => 'Facture',
            self::Photo => 'Photo',
            self::Other => 'Autre',
        };
    }
}
