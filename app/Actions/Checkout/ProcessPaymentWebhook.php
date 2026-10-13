<?php

namespace App\Actions\Checkout;

use App\Enums\OrderStatus;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProcessPaymentWebhook
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    /**
     * Proses notifikasi pembayaran dari gateway secara idempotent.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws InvalidArgumentException
     */
    public function handle(array $payload): ?Order
    {
        if (! $this->gateway->verifySignature($payload)) {
            throw new InvalidArgumentException('Tanda tangan notifikasi pembayaran tidak valid.');
        }

        $result = $this->gateway->handleNotification($payload);

        $order = Order::query()->where('code', $result->orderCode)->first();

        if ($order === null) {
            return null;
        }

        $wasPaid = $order->isPaid();

        return DB::transaction(function () use ($order, $result, $wasPaid): Order {
            $order->forceFill([
                'status' => $result->status,
                'payment_channel' => $result->channel ?? $order->payment_channel,
                'gateway_ref' => $result->reference ?? $order->gateway_ref,
                'payload' => $result->raw,
                'paid_at' => $result->status === OrderStatus::Paid
                    ? ($order->paid_at ?? now())
                    : $order->paid_at,
            ])->save();

            // Hanya kirim event sekali, meski webhook dikirim berulang.
            if ($result->status === OrderStatus::Paid && ! $wasPaid) {
                OrderPaid::dispatch($order->fresh());
            }

            return $order;
        });
    }
}
