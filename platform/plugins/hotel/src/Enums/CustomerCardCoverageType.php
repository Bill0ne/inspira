<?php

namespace Botble\Hotel\Enums;

enum CustomerCardCoverageType: string
{
    case NONE = 'none';
    case PARTIAL = 'partial';
    case FULL = 'full';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Keine Abdeckung',
            self::PARTIAL => 'Teilweise Abdeckung',
            self::FULL => 'Volle Abdeckung',
        };
    }
}
