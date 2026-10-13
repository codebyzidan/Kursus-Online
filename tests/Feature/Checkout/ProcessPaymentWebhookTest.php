<?php

use App\Actions\Checkout\ProcessPaymentWebhook;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\Facades\Queue;
use Tests\Fakes\FakePaymentGateway;

beforeEach(function () {
    Queue::fake();

    $this->gateway = new FakePaymentGateway;
    $this->app->instance(PaymentGateway::class, $this->gateway);

    $this->student = User::factory()->student()->create();
    $this->course = Course::factory()->published()->create(['price' => 150000, 'sale_price' => null]);

    $this->order = Order::factory()->create([
        'user_id' => $this->student->getKey(),
        'subtotal' => 150000,
        'total' => 150000,
    ]);

    $this->order->items()->create([
        'course_id' => $this->course->getKey(),
        'instructor_id' => $this->course->instructor_id,
        'course_title' => $this->course->title,
        'price' => 150000,
        'discount' => 0,
        'instructor_earning' => 105000,
    ]);
});

it('menandai pesanan dibayar dan mendaftarkan siswa', function () {
    $order = app(ProcessPaymentWebhook::class)->handle([
        'order_id' => $this->order->code,
        'status' => OrderStatus::Paid->value,
        'channel' => 'va',
        'reference' => 'trx-123',
    ]);

    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->paid_at)->not->toBeNull()
        ->and($order->gateway_ref)->toBe('trx-123');

    expect($this->student->fresh()->hasEnrolled($this->course))->toBeTrue();
});

it('idempotent saat webhook dikirim berulang', function () {
    $payload = ['order_id' => $this->order->code, 'status' => OrderStatus::Paid->value];

    app(ProcessPaymentWebhook::class)->handle($payload);
    app(ProcessPaymentWebhook::class)->handle($payload);

    expect(Enrollment::query()->count())->toBe(1);
});

it('menolak webhook dengan tanda tangan tidak valid', function () {
    $this->gateway->signatureValid = false;

    app(ProcessPaymentWebhook::class)->handle([
        'order_id' => $this->order->code,
        'status' => OrderStatus::Paid->value,
    ]);
})->throws(InvalidArgumentException::class);

it('menandai pesanan kedaluwarsa', function () {
    $order = app(ProcessPaymentWebhook::class)->handle([
        'order_id' => $this->order->code,
        'status' => OrderStatus::Expired->value,
    ]);

    expect($order->status)->toBe(OrderStatus::Expired)
        ->and($this->student->fresh()->hasEnrolled($this->course))->toBeFalse();
});
