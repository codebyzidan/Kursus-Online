<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Diminta',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Paid => 'Dibayar',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
