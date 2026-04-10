<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';
    case PreferNotToSay = 'prefer_not_to_say';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Male => 'Homme',
            self::Female => 'Femme',
            self::Other => 'Autre',
            self::PreferNotToSay => 'Ne souhaite pas répondre',
        };
    }
}
