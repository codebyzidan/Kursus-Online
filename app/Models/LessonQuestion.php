<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read User $user
 * @property-read Lesson $lesson
 */
class LessonQuestion extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['lesson_id', 'user_id', 'parent_id', 'body', 'answered_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['answered_at' => 'datetime'];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function isAnswer(): bool
    {
        return $this->answered_at !== null;
    }
}
