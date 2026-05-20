<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\CheckoutPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutPayWebhookController extends Controller
{
    public function handle(Request $request, CheckoutPayService $checkout): JsonResponse
    {
        $payload = $request->all();
        $payment = $checkout->activatePlanFromWebhook($payload);

        return response()->json([
            'success' => true,
            'processed' => (bool) $payment,
            'transaction_id' => $payload['transaction_id'] ?? null,
        ]);
    }
}
