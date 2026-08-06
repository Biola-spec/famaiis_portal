<?php

namespace App\Http\Controllers\Backend\Wallet;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Models\Wallet;
use App\Models\WalletFundingRequest;
use App\Services\Payments\PaymentGatewayManager;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WalletFundingController extends Controller
{
    public function __construct(private readonly PaymentGatewayManager $gatewayManager)
    {
    }

    public function initialize(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Parent', 'Admin'), 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'provider' => ['nullable', 'string', 'in:paystack,flutterwave'],
        ]);

        $provider = $validated['provider'] ?? config('services.school_payment.default', 'paystack');
        $amount = round((float) $validated['amount'], 2);
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'role' => strtolower($user->role ?: 'user'), 'status' => 'active']
        );
        $funding = WalletFundingRequest::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'amount' => $amount,
            'provider' => $provider,
            'reference' => $this->generateReference($provider),
        ]);

        try {
            $data = $this->gatewayManager->driver($provider)->initialize(
                $user->email,
                (int) round($amount * 100),
                $funding->reference,
                route('wallet.fund.callback'),
                ['wallet_funding_id' => $funding->id, 'wallet_id' => $wallet->id, 'user_id' => $user->id]
            );
        } catch (Throwable $e) {
            $funding->update(['status' => 'failed']);

            return back()->with([
                'message' => 'Wallet funding initialization failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->away((string) ($data['authorization_url'] ?? route('wallet.view')));
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference');

        if ($reference === '') {
            return redirect()->route('wallet.view')->with([
                'message' => 'Missing wallet funding reference.',
                'alert-type' => 'error',
            ]);
        }

        $funding = WalletFundingRequest::where('reference', $reference)->first();

        if (!$funding) {
            return redirect()->route('wallet.view')->with([
                'message' => 'Wallet funding record not found.',
                'alert-type' => 'error',
            ]);
        }

        $verified = $this->verifyAndCredit($funding);

        return redirect()->route('wallet.view')->with([
            'message' => $verified ? 'Wallet funding verified successfully.' : 'Wallet funding could not be verified.',
            'alert-type' => $verified ? 'success' : 'error',
        ]);
    }

    public function paystackWebhook(Request $request)
    {
        $secret = (string) (PaymentSetting::where('is_active', true)->value('secret_key') ?: config('services.paystack.secret_key'));
        $signature = (string) $request->header('x-paystack-signature');
        $payload = $request->getContent();

        if ($secret === '' || !hash_equals(hash_hmac('sha512', $payload, $secret), $signature)) {
            Log::warning('Invalid Paystack wallet webhook signature.');
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $reference = (string) $request->input('data.reference');

        if ($event !== 'charge.success' || $reference === '') {
            return response()->json(['message' => 'Ignored']);
        }

        $funding = WalletFundingRequest::where('reference', $reference)->first();
        if ($funding) {
            $this->verifyAndCredit($funding);
        }

        return response()->json(['message' => 'Processed']);
    }

    private function verifyAndCredit(WalletFundingRequest $funding): bool
    {
        return DB::transaction(function () use ($funding) {
            $lockedFunding = WalletFundingRequest::whereKey($funding->id)->lockForUpdate()->firstOrFail();

            if ($lockedFunding->status === 'success') {
                return true;
            }

            try {
                $verification = $this->gatewayManager->driver($lockedFunding->provider)->verify($lockedFunding->reference);
            } catch (Throwable $e) {
                $lockedFunding->update(['status' => 'failed']);
                Log::error('Wallet funding verification failed', ['reference' => $lockedFunding->reference, 'error' => $e->getMessage()]);
                return false;
            }

            $isValid = ($verification['status'] ?? null) === 'success'
                && (string) ($verification['reference'] ?? '') === $lockedFunding->reference
                && (int) ($verification['amount'] ?? 0) === (int) round($lockedFunding->amount * 100);

            if (!$isValid) {
                $lockedFunding->update(['status' => 'failed', 'provider_payload' => $verification]);
                return false;
            }

            $wallet = Wallet::whereKey($lockedFunding->wallet_id)->lockForUpdate()->firstOrFail();
            $wallet->credit(
                $lockedFunding->amount,
                'Verified wallet funding',
                $lockedFunding->user_id,
                ['provider' => $lockedFunding->provider, 'reference' => $lockedFunding->reference],
                WalletFundingRequest::class,
                $lockedFunding->id
            );

            $lockedFunding->update([
                'status' => 'success',
                'transaction_id' => (string) ($verification['id'] ?? $verification['transaction_id'] ?? $lockedFunding->reference),
                'payment_method' => (string) ($verification['channel'] ?? $verification['authorization']['channel'] ?? 'online'),
                'paid_at' => Carbon::now(),
                'provider_payload' => $verification,
            ]);

            return true;
        });
    }

    private function generateReference(string $provider): string
    {
        do {
            $reference = strtoupper($provider) . '-WALLET-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(8));
        } while (WalletFundingRequest::where('reference', $reference)->exists());

        return $reference;
    }
}
