<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Quiz|null $quiz
 * @property-read Collection<int, Option> $options
 */
class Question extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['quiz_id', 'text', 'explanation', 'points', 'position'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'position' => 'integer',
        ];
    }

    /** @return BelongsTo<Quiz, $this> */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * @return HasMany<Option, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('position');
    }

    public function correctOption(): ?Option
    {
        return $this->options()->where('is_correct', true)->first();
    }
}
