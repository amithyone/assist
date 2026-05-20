<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PaymentGatewayManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AssistBillingController extends Controller
{
    public function upgrade(Request $request, string $plan, PaymentGatewayManager $gateways): RedirectResponse|View
    {
        $plan = Plan::where('slug', $plan)->where('is_active', true)->firstOrFail();
        $user = $request->user();
        $currency = strtolower($request->query('currency', $user->billing_currency ?? 'ngn'));

        if ($plan->slug === 'free' || $plan->priceForCurrency($currency) <= 0) {
            return redirect()->route('assist.dashboard')->with('status', 'You are on the free plan.');
        }

        $gateway = $gateways->gatewayForCurrency($currency);

        try {
            $gateways->assertConfigured($gateway);
            $payment = $gateways->createPayment($user, $plan, $currency);
        } catch (RuntimeException $e) {
            return redirect()->route('assist.pricing')->withErrors(['billing' => $e->getMessage()]);
        }

        if ($payment->gateway === 'paystack' && ! empty($payment->checkout_payload['authorization_url'])) {
            return redirect()->away($payment->checkout_payload['authorization_url']);
        }

        return redirect()->route('assist.billing.payment', [
            'transaction' => $payment->transaction_id,
        ]);
    }

    public function payment(Request $request, string $transaction, PaymentGatewayManager $gateways): View|RedirectResponse
    {
        $payment = $request->user()->payments()->where('transaction_id', $transaction)->firstOrFail();
        $payment = $gateways->syncPaymentStatus($payment);

        if ($payment->gateway === 'paystack' && $payment->status === 'pending' && ! empty($payment->checkout_payload['authorization_url'])) {
            return redirect()->away($payment->checkout_payload['authorization_url']);
        }

        return view('assist.billing.payment', [
            'payment' => $payment,
            'bank' => $payment->checkout_payload ?? [],
            'plan' => Plan::where('slug', $payment->plan_slug)->first(),
        ]);
    }
}
