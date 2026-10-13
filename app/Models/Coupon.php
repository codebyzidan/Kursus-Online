<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property CouponType $type
 * @property int|null $max_discount
 * @property Carbon|null $valid_from
 * @property Carbon|null $valid_until
 * @property-read Course|null $course
 */
class Coupon extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_discount',
        'min_purchase',
        'course_id',
        'quota',
        'used',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'integer',
            'max_discount' => 'integer',
            'min_purchase' => 'integer',
            'quota' => 'integer',
            'used' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function remainingQuota(): ?int
    {
        return $this->quota === null ? null : max(0, $this->quota - $this->used);
    }

    public function isValid(?int $amount = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->quota !== null && $this->used >= $this->quota) {
            return false;
        }

        if ($amount !== null && $amount < $this->min_purchase) {
            return false;
        }

        return true;
    }

    /** Hitung nominal diskon untuk sebuah nilai belanja. */
    public function computeDiscount(int $amount): int
    {
        $discount = match ($this->type) {
            CouponType::Fixed => $this->value,
            CouponType::Percent => (int) floor($amount * $this->value / 100),
        };

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return max(0, min($discount, $amount));
    }
}
