<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\Checkout\ProcessPaymentWebhook;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, ProcessPaymentWebhook $action): JsonResponse
    {
        $payload = $request->all();

        try {
            $action->handle($payload);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 403);
        }

        // Selalu balas 200 agar gateway tidak mengulang notifikasi yang sah.
        return response()->json(['status' => 'ok']);
    }
}
