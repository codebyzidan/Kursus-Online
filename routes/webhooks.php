<?php

use App\Http\Controllers\Webhooks\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

// Endpoint webhook pembayaran (tanpa auth, tanpa CSRF — diverifikasi lewat signature).
Route::post('webhooks/midtrans', MidtransWebhookController::class)->name('webhooks.midtrans');
