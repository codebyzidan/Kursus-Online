<?php

namespace App\Enums;

enum PaymentChannel: string
{
    case VirtualAccount = 'va';
    case Ewallet = 'ewallet';
    case Qris = 'qris';
    case Card = 'card';

    public function label(): string
    {
        return match ($this) {
            self::VirtualAccount => 'Virtual Account',
            self::Ewallet => 'E-Wallet',
            self::Qris => 'QRIS',
            self::Card => 'Kartu',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
