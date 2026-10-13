<?php

namespace App\Enums;

enum LessonType: string
{
    case Video = 'video';
    case Text = 'text';
    case Attachment = 'attachment';

    public function label(): string
    {
        return match ($this) {
            self::Video => 'Video',
            self::Text => 'Teks',
            self::Attachment => 'Lampiran',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
