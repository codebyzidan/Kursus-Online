<?php

namespace App\Enums;

enum CouponType: string
{
    case Fixed = 'fixed';
    case Percent = 'percent';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Nominal',
            self::Percent => 'Persen',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
