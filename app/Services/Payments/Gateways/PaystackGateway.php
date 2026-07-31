<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentSetting;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class PaystackGateway implements PaymentGatewayInterface
{
    public function initialize(string $email, int $amountInKobo, string $reference, string $callbackUrl, array $metadata = []): array
    {
        $settings = $this->resolveSettings();

        try {
            $response = Http::withToken($settings['secret_key'])
                ->acceptJson()
                ->timeout(20)
                ->connectTimeout(10)
                ->post(rtrim($settings['payment_url'], '/') . '/transaction/initialize', [
                    'email' => $email,
                    'amount' => $amountInKobo,
                    'reference' => $reference,
                    'callback_url' => $callbackUrl,
                    'currency' => 'NGN',
                    'metadata' => $metadata,
                ]);
        } catch (Throwable $e) {
            throw new RuntimeException('Could not connect to Paystack: ' . $e->getMessage());
        }

        $payload = $response->json();

        if (!$response->successful() || !($payload['status'] ?? false)) {
            throw new RuntimeException($payload['message'] ?? 'Unable to initialize Paystack transaction.');
        }

        return $payload['data'] ?? [];
    }

    public function verify(string $reference): array
    {
        $settings = $this->resolveSettings();

        try {
            $response = Http::withToken($settings['secret_key'])
                ->acceptJson()
                ->timeout(20)
                ->connectTimeout(10)
                ->get(rtrim($settings['payment_url'], '/') . '/transaction/verify/' . $reference);
        } catch (Throwable $e) {
            throw new RuntimeException('Could not verify with Paystack: ' . $e->getMessage());
        }

        $payload = $response->json();

        if (!$response->successful() || !($payload['status'] ?? false)) {
            throw new RuntimeException($payload['message'] ?? 'Unable to verify Paystack transaction.');
        }

        return $payload['data'] ?? [];
    }

    private function resolveSettings(): array
    {
        $dbSetting = PaymentSetting::query()->where('is_active', true)->first();

        $secretKey = (string) ($dbSetting->secret_key ?? config('services.paystack.secret_key'));
        $paymentUrl = (string) ($dbSetting->payment_url ?? config('services.paystack.payment_url', 'https://api.paystack.co'));

        if ($secretKey === '') {
            throw new RuntimeException('Paystack secret key is missing. Set it in Payment Gateway settings or .env.');
        }

        return [
            'secret_key' => $secretKey,
            'payment_url' => $paymentUrl !== '' ? $paymentUrl : 'https://api.paystack.co',
        ];
    }
}
