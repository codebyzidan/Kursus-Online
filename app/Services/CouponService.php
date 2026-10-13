<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponService
{
    /**
     * Cari dan validasi kupon berdasarkan kode.
     *
     * @throws ValidationException
     */
    public function validate(?string $code, int $amount, ?int $courseId = null): ?Coupon
    {
        if (blank($code)) {
            return null;
        }

        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [Str::upper(trim($code))])
            ->first();

        if ($coupon === null) {
            throw ValidationException::withMessages([
                'coupon' => 'Kode kupon tidak ditemukan.',
            ]);
        }

        if (! $coupon->isValid($amount)) {
            throw ValidationException::withMessages([
                'coupon' => 'Kupon tidak berlaku atau sudah kedaluwarsa.',
            ]);
        }

        if ($coupon->course_id !== null && $courseId !== null && $coupon->course_id !== $courseId) {
            throw ValidationException::withMessages([
                'coupon' => 'Kupon hanya berlaku untuk kursus tertentu.',
            ]);
        }

        return $coupon;
    }

    public function discountFor(Coupon $coupon, int $amount): int
    {
        return $coupon->computeDiscount($amount);
    }

    public function recordUsage(Coupon $coupon, User $user, Order $order, int $discount): CouponUsage
    {
        $coupon->increment('used');

        return CouponUsage::create([
            'coupon_id' => $coupon->getKey(),
            'user_id' => $user->getKey(),
            'order_id' => $order->getKey(),
            'discount' => $discount,
            'used_at' => now(),
        ]);
    }
}
