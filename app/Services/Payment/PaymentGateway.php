<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGateway
{
    /**
     * Buat transaksi pembayaran dan kembalikan token/URL dari gateway.
     */
    public function createTransaction(Order $order): string;

    /**
     * Validasi tanda tangan notifikasi webhook.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifySignature(array $payload): bool;

    /**
     * Normalisasi payload notifikasi menjadi PaymentResult.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleNotification(array $payload): PaymentResult;
}
