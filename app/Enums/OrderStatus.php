<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Expired = 'expired';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu pembayaran',
            self::Paid => 'Dibayar',
            self::Expired => 'Kedaluwarsa',
            self::Failed => 'Gagal',
            self::Refunded => 'Dana dikembalikan',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Paid, self::Expired, self::Failed, self::Refunded], true);
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
