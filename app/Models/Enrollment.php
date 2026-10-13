<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read User $user
 * @property-read Course $course
 * @property-read Certificate|null $certificate
 */
class Enrollment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'last_lesson_id',
        'source',
        'progress_percent',
        'enrolled_at',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress_percent' => 'integer',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Enrollment $enrollment): void {
            $enrollment->enrolled_at ??= now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lastLesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'last_lesson_id');
    }

    /** Progres materi milik siswa ini pada kursus terkait. */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'course_id', 'course_id')
            ->where('user_id', $this->user_id);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function hasCertificate(): bool
    {
        return $this->certificate()->exists();
    }

    /** Hitung ulang persentase progres dari lesson_progress. */
    public function updateProgress(): void
    {
        $totalLessons = Lesson::query()
            ->whereHas('section', fn ($q) => $q->where('course_id', $this->course_id))
            ->where('is_published', true)
            ->count();

        $completedLessons = LessonProgress::query()
            ->where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->whereNotNull('completed_at')
            ->count();

        $percent = $totalLessons > 0
            ? (int) round($completedLessons / $totalLessons * 100)
            : 0;

        $this->progress_percent = $percent;

        if ($percent >= 100 && $this->completed_at === null) {
            $this->completed_at = now();
        }

        $this->save();
    }
}
