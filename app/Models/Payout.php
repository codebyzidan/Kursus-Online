<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read User $instructor
 * @property-read User|null $reviewer
 */
class Payout extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'instructor_id',
        'amount',
        'status',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'note',
        'reviewed_by',
        'requested_at',
        'approved_at',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PayoutStatus::class,
            'amount' => 'integer',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payout $payout): void {
            $payout->requested_at ??= now();
        });
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PayoutStatus::Requested);
    }
}
