<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Published = 'published';
    case Rejected = 'rejected';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::PendingReview => 'Menunggu review',
            self::Published => 'Terbit',
            self::Rejected => 'Ditolak',
            self::Archived => 'Diarsipkan',
        };
    }

    public function isPubliclyVisible(): bool
    {
        return $this === self::Published;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
