<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property-read User|null $user
 * @property-read Collection<int, OrderItem> $items
 * @property-read Coupon|null $coupon
 * @property Carbon|null $paid_at
 * @property Carbon|null $expired_at
 */
class Order extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'coupon_id',
        'subtotal',
        'discount',
        'total',
        'status',
        'payment_channel',
        'payment_method',
        'snap_token',
        'gateway_ref',
        'payload',
        'paid_at',
        'expired_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_channel' => PaymentChannel::class,
            'subtotal' => 'integer',
            'discount' => 'integer',
            'total' => 'integer',
            'payload' => 'array',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (blank($order->code)) {
                $order->code = self::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pembantu
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending);
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    /** Ringkas ulang subtotal, diskon, dan total dari item. */
    public function recalculateTotals(): void
    {
        $subtotal = (int) $this->items()->sum('price');
        $discount = (int) $this->items()->sum('discount');

        $this->forceFill([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ])->save();
    }

    public function markPaid(?string $gatewayRef = null): void
    {
        $this->forceFill([
            'status' => OrderStatus::Paid,
            'paid_at' => $this->paid_at ?? now(),
            'gateway_ref' => $gatewayRef ?: $this->gateway_ref,
        ])->save();
    }
}
