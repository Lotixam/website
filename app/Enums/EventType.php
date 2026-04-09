<?php

namespace App\Enums;

enum EventType: string
{
    case Meeting = 'meeting';
    case Deadline = 'deadline';
    case Reminder = 'reminder';
    case LegalDeadline = 'legal_deadline';

    public function label(): string
    {
        return match ($this) {
            self::Meeting => 'Rendez-vous',
            self::Deadline => 'Échéance',
            self::Reminder => 'Rappel',
            self::LegalDeadline => 'Délai légal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Meeting => 'info',
            self::Deadline => 'warning',
            self::Reminder => 'gray',
            self::LegalDeadline => 'danger',
        };
    }
}
