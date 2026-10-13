<?php

namespace App\Actions\Checkout;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Services\CouponService;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function __construct(
        private readonly CouponService $coupons,
        private readonly PaymentGateway $gateway,
    ) {}

    /**
     * Buat pesanan dari daftar kursus, terapkan kupon, dan buat transaksi di gateway.
     *
     * @param  list<int>  $courseIds
     *
     * @throws ValidationException
     */
    public function handle(
        User $user,
        array $courseIds,
        ?string $couponCode = null,
        bool $withPayment = true,
    ): Order {
        $courseIds = array_values(array_unique($courseIds));

        $courses = Course::query()
            ->published()
            ->whereIn('id', $courseIds)
            ->get();

        if ($courses->isEmpty()) {
            throw ValidationException::withMessages([
                'courses' => 'Kursus yang dipilih tidak tersedia.',
            ]);
        }

        $alreadyEnrolled = $user->enrollments()
            ->whereIn('course_id', $courses->pluck('id'))
            ->exists();

        if ($alreadyEnrolled) {
            throw ValidationException::withMessages([
                'courses' => 'Anda sudah terdaftar pada salah satu kursus ini.',
            ]);
        }

        $coupon = $this->coupons->validate(
            $couponCode,
            (int) $courses->sum(fn (Course $course) => $course->getEffectivePriceAttribute()),
            $courses->count() === 1 ? $courses->first()->getKey() : null,
        );

        $order = DB::transaction(function () use ($user, $courses, $coupon): Order {
            $order = Order::create([
                'user_id' => $user->getKey(),
                'coupon_id' => $coupon?->getKey(),
                'status' => OrderStatus::Pending,
                'expired_at' => now()->addMinutes((int) config('kursus.order_expiry_minutes', 60)),
            ]);

            $share = (float) config('kursus.instructor_revenue_share', 0.7);
            $subtotal = 0;

            foreach ($courses as $course) {
                $subtotal += $course->getEffectivePriceAttribute();
            }

            $discountTotal = $coupon ? $this->coupons->discountFor($coupon, $subtotal) : 0;
            $remainingDiscount = $discountTotal;
            $lastIndex = $courses->count() - 1;

            foreach ($courses as $index => $course) {
                $price = $course->getEffectivePriceAttribute();

                $itemDiscount = $index === $lastIndex
                    ? $remainingDiscount
                    : (int) floor($discountTotal * ($subtotal > 0 ? $price / $subtotal : 0));

                $remainingDiscount -= $itemDiscount;
                $net = max(0, $price - $itemDiscount);

                $order->items()->create([
                    'course_id' => $course->getKey(),
                    'instructor_id' => $course->instructor_id,
                    'course_title' => $course->title,
                    'price' => $price,
                    'discount' => $itemDiscount,
                    'instructor_earning' => (int) round($net * $share),
                ]);
            }

            $order->forceFill([
                'subtotal' => $subtotal,
                'discount' => $discountTotal,
                'total' => max(0, $subtotal - $discountTotal),
            ])->save();

            if ($coupon !== null) {
                $this->coupons->recordUsage($coupon, $user, $order, $discountTotal);
            }

            return $order;
        });

        if ($withPayment && $order->total > 0) {
            $this->gateway->createTransaction($order);
        }

        return $order->refresh()->load(['items', 'coupon']);
    }
}
