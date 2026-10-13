<?php

namespace App\Models;

use App\Enums\LessonType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property-read Section $section
 * @property-read Collection<int, LessonQuestion> $questions
 */
class Lesson extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'section_id',
        'title',
        'slug',
        'type',
        'content',
        'video_url',
        'video_provider',
        'duration',
        'position',
        'is_preview',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LessonType::class,
            'duration' => 'integer',
            'position' => 'integer',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    public function registerMediaCollections(): void
    {
        // Lampiran materi disimpan di disk privat; diakses lewat signed URL.
        $this->addMediaCollection('attachments')->useDisk('private');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LessonQuestion::class);
    }

    public function isVideo(): bool
    {
        return $this->type === LessonType::Video;
    }

    public function isPreview(): bool
    {
        return $this->is_preview;
    }

    public function course(): Course
    {
        return $this->section->course;
    }
}
