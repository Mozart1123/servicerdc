<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription/pricing page.
     */
    public function index(): View
    {
        $plans = SubscriptionPlan::where('is_active', true)
                                 ->orderBy('sort_order')
                                 ->get();

        $activeSubscription = Subscription::where('user_id', Auth::id())
                                          ->where('status', 'active')
                                          ->where(function($q) {
                                              $q->whereNull('ends_at')
                                                ->orWhere('ends_at', '>=', now());
                                          })
                                          ->with('subscriptionPlan')
                                          ->latest()
                                          ->first();

        return view('user.subscription.index', compact('plans', 'activeSubscription'));
    }

    /**
     * Show the checkout form for a selected plan.
     */
    public function checkout(Request $request): View
    {
        $plan = SubscriptionPlan::where('slug', $request->plan)->where('is_active', true)->firstOrFail();
        $billing = $request->billing === 'yearly' ? 'yearly' : 'monthly';

        return view('user.subscription.checkout', compact('plan', 'billing'));
    }

    /**
     * Process subscription payment.
     */
    public function subscribe(Request $request, \App\Services\KpayService $kpayService): RedirectResponse
    {
        $request->validate([
            'plan_id'        => ['required', 'exists:subscription_plans,id'],
            'billing_cycle'  => ['required', 'in:monthly,yearly'],
            'payment_method' => ['required', 'in:mobile_money,visa_mastercard,cash'],
            'payment_phone'  => ['required_if:payment_method,mobile_money', 'nullable', 'string', 'max:20'],
        ], [
            'plan_id.required'          => 'Veuillez choisir un plan.',
            'payment_method.required'   => 'Veuillez choisir un mode de paiement.',
            'payment_phone.required_if' => 'Le numéro Mobile Money est requis.',
        ]);

        $plan   = SubscriptionPlan::findOrFail($request->plan_id);
        $billing = $request->billing_cycle;
        $amount  = $billing === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        // Cancel any existing active subscription
        Subscription::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);

        $externalId = 'PAY-' . time() . '-' . \Illuminate\Support\Str::random(5);

        $subscription = Subscription::create([
            'user_id'              => Auth::id(),
            'subscription_plan_id' => $plan->id,
            'status'               => $amount == 0 ? 'active' : 'pending',
            'billing_cycle'        => $billing,
            'payment_method'       => $request->payment_method,
            'payment_phone'        => $request->payment_phone,
            'transaction_ref'      => $externalId,
            'amount_paid'          => $amount,
            'starts_at'            => now()->toDateString(),
            'ends_at'              => $billing === 'yearly'
                                      ? now()->addYear()->toDateString()
                                      : now()->addMonth()->toDateString(),
            'paid_at'              => $amount == 0 ? now() : null,
        ]);

        if ($amount > 0) {
            $transaction = new \App\Models\Transaction();
            $transaction->user_id = Auth::id();
            $transaction->amount = $amount;
            $transaction->currency = 'XAF'; // Adjust based on predicted provider later if needed
            $transaction->status = 'pending';
            $transaction->type = 'subscription';
            $transaction->reference_id = $externalId; 
            $transaction->item_id = $plan->id;
            $transaction->description = "Abonnement " . $plan->name;
            $transaction->save();
        }

        if ($amount == 0) {
            return redirect()->route('user.subscription.index')
                             ->with('success', 'Votre abonnement Starter est maintenant actif !');
        }

        if ($request->payment_method === 'mobile_money') {
            try {
                $provider = $kpayService->predictProvider($request->payment_phone);
                
                if (!$provider) {
                    return redirect()->back()->withErrors(['payment_phone' => 'Opérateur Mobile Money non reconnu pour ce numéro.'])->withInput();
                }

                $providerCurrency = $kpayService->getCurrencyForProvider($provider);
                $localAmount = (int) round($kpayService->convertUsdToLocal($amount, $providerCurrency));

                if ($amount > 0) {
                    $transaction->update([
                        'currency' => $providerCurrency,
                        'amount' => $localAmount
                    ]);
                }

                $kpayResponse = $kpayService->initiatePayment([
                    'amount'      => $localAmount,
                    'provider'    => $provider,
                    'phoneNumber' => preg_replace('/[^0-9]/', '', $request->payment_phone),
                    'externalId'  => $externalId,
                    'description' => "Abonnement " . $plan->name,
                ]);

                if (isset($kpayResponse['reference'])) {
                    $transaction->update(['kpay_reference' => $kpayResponse['reference']]);
                }

                return redirect()->route('user.subscription.index')
                                 ->with('success', 'Veuillez valider le paiement sur votre téléphone (Popup USSD) !');
                                 
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Subscription payment init failed: ' . $e->getMessage());
                return redirect()->back()->withErrors(['payment' => 'Erreur lors de l\'initialisation du paiement: ' . $e->getMessage()])->withInput();
            }
        }

        return redirect()->route('user.subscription.index')
                         ->with('success', 'Souscription enregistrée. Veuillez procéder au paiement manuel selon les instructions.');
    }

    /**
     * Cancel the active subscription.
     */
    public function cancel(): RedirectResponse
    {
        Subscription::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);

        return redirect()->route('user.subscription.index')
                         ->with('success', 'Votre abonnement a été annulé.');
    }
}
