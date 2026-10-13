<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Course|null $course
 * @property-read Collection<int, Question> $questions
 */
class Quiz extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'description',
        'passing_grade',
        'max_attempts',
        'duration_minutes',
        'is_final',
        'is_published',
        'position',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'passing_grade' => 'integer',
            'max_attempts' => 'integer',
            'duration_minutes' => 'integer',
            'is_final' => 'boolean',
            'is_published' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /** @return HasMany<Question, $this> */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFinal(Builder $query): Builder
    {
        return $query->where('is_final', true);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }

    public function attemptsUsedBy(User $user): int
    {
        return $this->attempts()->where('user_id', $user->getKey())->count();
    }

    public function hasAttemptsRemainingFor(User $user): bool
    {
        if ($this->max_attempts === null) {
            return true;
        }

        return $this->attemptsUsedBy($user) < $this->max_attempts;
    }
}
