<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PaymentGatewayManager;
use App\Services\VoucherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AssistBillingController extends Controller
{
    public function upgrade(
        Request $request,
        string $plan,
        PaymentGatewayManager $gateways,
        VoucherService $vouchers,
    ): RedirectResponse|View {
        $plan = Plan::where('slug', $plan)->where('is_active', true)->firstOrFail();
        $user = $request->user();
        $currency = strtolower($request->query('currency', $user->billing_currency ?? 'ngn'));

        if ($plan->slug === 'free' || $plan->priceForCurrency($currency) <= 0) {
            return redirect()->route('assist.dashboard')->with('status', 'You are on the free plan.');
        }

        $preferredGateway = $request->query('gateway');
        $voucher = null;
        $breakdown = null;

        try {
            $code = $request->query('voucher');
            if ($code) {
                $voucher = $vouchers->findValid($code, $user, $plan, $currency);
                $breakdown = $vouchers->priceBreakdown(
                    $plan->priceForCurrency($currency),
                    $voucher,
                    $currency
                );
            }
            $payment = $gateways->createPayment(
                $user,
                $plan,
                $currency,
                $voucher,
                $breakdown,
                $preferredGateway
            );
        } catch (RuntimeException $e) {
            return redirect()
                ->route('assist.pricing', array_filter(['voucher' => $request->query('voucher')]))
                ->withErrors(['billing' => $e->getMessage()]);
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
