<?php

use App\Actions\Checkout\PlaceOrder;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\Payment\PaymentGateway;
use Illuminate\Validation\ValidationException;
use Tests\Fakes\FakePaymentGateway;

beforeEach(function () {
    $this->gateway = new FakePaymentGateway;
    $this->app->instance(PaymentGateway::class, $this->gateway);
});

it('membuat pesanan dengan item, kupon, dan transaksi gateway', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create(['price' => 200000, 'sale_price' => null]);
    $coupon = Coupon::factory()->fixed(50000)->create(['min_purchase' => 100000]);

    $order = app(PlaceOrder::class)->handle($student, [$course->getKey()], $coupon->code);

    expect($order->subtotal)->toBe(200000)
        ->and($order->discount)->toBe(50000)
        ->and($order->total)->toBe(150000)
        ->and($order->items)->toHaveCount(1)
        ->and($order->snap_token)->toBe('fake-'.$order->code);

    expect($coupon->fresh()->used)->toBe(1);
});

it('menolak kupon yang tidak memenuhi minimum pembelian', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create(['price' => 50000, 'sale_price' => null]);
    $coupon = Coupon::factory()->fixed(10000)->create(['min_purchase' => 100000]);

    app(PlaceOrder::class)->handle($student, [$course->getKey()], $coupon->code);
})->throws(ValidationException::class);

it('menolak membeli kursus yang sudah diikuti', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    Enrollment::factory()->create([
        'user_id' => $student->getKey(),
        'course_id' => $course->getKey(),
    ]);

    app(PlaceOrder::class)->handle($student, [$course->getKey()]);
})->throws(ValidationException::class);

it('membagi total diskon ke seluruh item', function () {
    $student = User::factory()->student()->create();
    $courseA = Course::factory()->published()->create(['price' => 100000, 'sale_price' => null]);
    $courseB = Course::factory()->published()->create(['price' => 300000, 'sale_price' => null]);
    $coupon = Coupon::factory()->fixed(40000)->create(['min_purchase' => 100000]);

    $order = app(PlaceOrder::class)->handle($student, [$courseA->getKey(), $courseB->getKey()], $coupon->code);

    expect($order->items->sum('discount'))->toBe(40000)
        ->and($order->total)->toBe(360000);
});
