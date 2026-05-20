<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\CheckoutPayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AssistBillingController extends Controller
{
    public function upgrade(Request $request, string $plan, CheckoutPayService $checkout): RedirectResponse|View
    {
        $plan = Plan::where('slug', $plan)->where('is_active', true)->firstOrFail();
        $user = $request->user();
        $currency = strtolower($request->query('currency', $user->billing_currency ?? 'ngn'));

        if ($plan->slug === 'free' || $plan->priceForCurrency($currency) <= 0) {
            return redirect()->route('assist.dashboard')->with('status', 'You are on the free plan.');
        }

        try {
            $payment = $checkout->createPaymentRequest($user, $plan, $currency);
        } catch (RuntimeException $e) {
            return redirect()->route('assist.pricing')->withErrors(['billing' => $e->getMessage()]);
        }

        return redirect()->route('assist.billing.payment', [
            'transaction' => $payment->transaction_id,
        ]);
    }

    public function payment(Request $request, string $transaction, CheckoutPayService $checkout): View
    {
        $payment = $request->user()->payments()->where('transaction_id', $transaction)->firstOrFail();
        $data = $payment->checkout_payload ?? [];

        try {
            $status = $checkout->getPaymentStatus($transaction);
            if (($status['data']['status'] ?? '') === 'approved') {
                $checkout->activatePlanFromWebhook([
                    'event' => 'payment.approved',
                    'transaction_id' => $transaction,
                    'service' => 'assist-plan:'.$payment->plan_slug,
                ]);
                $payment->refresh();
            }
        } catch (\Throwable) {
            // polling optional
        }

        return view('assist.billing.payment', [
            'payment' => $payment,
            'bank' => $data,
            'plan' => Plan::where('slug', $payment->plan_slug)->first(),
        ]);
    }
}
