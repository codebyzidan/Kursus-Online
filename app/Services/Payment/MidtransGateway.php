<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransGateway implements PaymentGateway
{
    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$clientKey = (string) config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Order $order): string
    {
        $order->loadMissing(['items', 'user']);

        $payload = [
            'transaction_details' => [
                'order_id' => $order->code,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id' => (string) $item->course_id,
                'price' => (int) $item->net_price,
                'quantity' => 1,
                'name' => mb_substr($item->course_title, 0, 50),
            ])->all(),
            'expiry' => [
                'unit' => 'minutes',
                'duration' => (int) config('kursus.order_expiry_minutes', 60),
            ],
        ];

        $token = Snap::getSnapToken($payload);

        $order->forceFill(['snap_token' => $token])->save();

        return $token;
    }

    public function verifySignature(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '' || empty($payload['signature_key'])) {
            return false;
        }

        $expected = hash('sha512', sprintf(
            '%s%s%s%s',
            $payload['order_id'] ?? '',
            $payload['status_code'] ?? '',
            $payload['gross_amount'] ?? '',
            $serverKey,
        ));

        return hash_equals($expected, (string) $payload['signature_key']);
    }

    public function handleNotification(array $payload): PaymentResult
    {
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = isset($payload['fraud_status']) ? (string) $payload['fraud_status'] : null;
        $paymentType = (string) ($payload['payment_type'] ?? '');

        $status = match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? OrderStatus::Pending : OrderStatus::Paid,
            'settlement' => OrderStatus::Paid,
            'pending' => OrderStatus::Pending,
            'deny', 'cancel' => OrderStatus::Failed,
            'expire' => OrderStatus::Expired,
            'refund', 'partial_refund' => OrderStatus::Refunded,
            default => OrderStatus::Pending,
        };

        return new PaymentResult(
            orderCode: (string) ($payload['order_id'] ?? ''),
            status: $status,
            channel: $this->mapChannel($paymentType),
            reference: isset($payload['transaction_id']) ? (string) $payload['transaction_id'] : null,
            fraudStatus: $fraudStatus,
            raw: $payload,
        );
    }

    private function mapChannel(string $paymentType): ?PaymentChannel
    {
        return match (true) {
            in_array($paymentType, ['bank_transfer', 'echannel', 'cstore'], true) => PaymentChannel::VirtualAccount,
            in_array($paymentType, ['gopay', 'shopeepay', 'akulaku'], true) => PaymentChannel::Ewallet,
            $paymentType === 'qris' => PaymentChannel::Qris,
            $paymentType === 'credit_card' => PaymentChannel::Card,
            default => null,
        };
    }
}
