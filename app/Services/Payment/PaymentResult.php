<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;

/**
 * Hasil normalisasi notifikasi/status dari payment gateway.
 */
final readonly class PaymentResult
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $orderCode,
        public OrderStatus $status,
        public ?PaymentChannel $channel = null,
        public ?string $reference = null,
        public ?string $fraudStatus = null,
        public array $raw = [],
    ) {}
}
