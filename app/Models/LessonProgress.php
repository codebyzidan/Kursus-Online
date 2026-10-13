<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    /**
     * @var list<string>
     */
    protected $fillable = ['user_id', 'lesson_id', 'course_id', 'last_position', 'completed_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_position' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function markCompleted(): void
    {
        if ($this->completed_at === null) {
            $this->completed_at = now();
            $this->save();
        }
    }
}
