<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property-read User|null $instructor
 * @property-read Category|null $category
 * @property-read Collection<int, Review> $reviews
 * @property-read string|null $thumbnail_url
 */
class Course extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia, LogsActivity, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'level',
        'price',
        'sale_price',
        'thumbnail',
        'promo_video_url',
        'status',
        'rejection_reason',
        'outcomes',
        'requirements',
        'duration_minutes',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => CourseLevel::class,
            'status' => CourseStatus::class,
            'price' => 'integer',
            'sale_price' => 'integer',
            'outcomes' => 'array',
            'requirements' => 'array',
            'duration_minutes' => 'integer',
            'lessons_count' => 'integer',
            'students_count' => 'integer',
            'rating_avg' => 'float',
            'rating_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /** Route binding memakai slug agar URL ramah SEO. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('promo_video')->singleFile();
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<Section, $this> */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('position');
    }

    /**
     * @return HasManyThrough<Lesson, Section, $this>
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['progress_percent', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /** @return HasMany<Quiz, $this> */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published)
            ->whereNotNull('published_at');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Pembantu
    |--------------------------------------------------------------------------
    */

    /** Harga efektif setelah diskon kursus (sale_price). */
    public function getEffectivePriceAttribute(): int
    {
        return (int) ($this->sale_price ?? $this->price);
    }

    public function isFree(): bool
    {
        return $this->getEffectivePriceAttribute() === 0;
    }

    public function isPublished(): bool
    {
        return $this->status === CourseStatus::Published;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail') ?: ($this->thumbnail ?: null);
    }

    /** Hitung ulang rating dari ulasan yang disetujui. */
    public function recalculateRating(): void
    {
        $reviews = $this->approvedReviews();

        $this->forceFill([
            'rating_count' => (clone $reviews)->count(),
            'rating_avg' => round((float) (clone $reviews)->avg('rating'), 2),
        ])->save();
    }

    /** Perbarui cache jumlah materi (bagian + jumlah siswa). */
    public function refreshCounters(): void
    {
        $this->forceFill([
            'lessons_count' => $this->lessons()->count(),
            'students_count' => $this->enrollments()->count(),
            'duration_minutes' => (int) round($this->lessons()->sum('duration') / 60),
        ])->save();
    }
}
