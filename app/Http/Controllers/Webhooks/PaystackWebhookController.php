<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request, PaystackService $paystack): JsonResponse
    {
        $raw = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (! $paystack->verifyWebhookSignature($raw, $signature)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $payload = $request->all();
        $payment = $paystack->handleWebhook($payload);

        return response()->json([
            'success' => true,
            'processed' => (bool) $payment,
        ]);
    }
}
