<?php

namespace Tests\Fakes;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Models\Order;
use App\Services\Payment\PaymentGateway;
use App\Services\Payment\PaymentResult;

class FakePaymentGateway implements PaymentGateway
{
    public bool $signatureValid = true;

    /** @var array<string, string> */
    public array $transactions = [];

    public function createTransaction(Order $order): string
    {
        $token = 'fake-'.$order->code;

        $this->transactions[$order->code] = $token;

        $order->forceFill(['snap_token' => $token])->save();

        return $token;
    }

    public function verifySignature(array $payload): bool
    {
        return $this->signatureValid;
    }

    public function handleNotification(array $payload): PaymentResult
    {
        return new PaymentResult(
            orderCode: (string) $payload['order_id'],
            status: OrderStatus::from($payload['status']),
            channel: isset($payload['channel']) ? PaymentChannel::from($payload['channel']) : null,
            reference: $payload['reference'] ?? null,
            raw: $payload,
        );
    }
}
