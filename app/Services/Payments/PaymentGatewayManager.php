<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Gateways\FlutterwaveGateway;
use App\Services\Payments\Gateways\PaystackGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function driver(?string $provider = null): PaymentGatewayInterface
    {
        $provider = strtolower($provider ?: (string) config('services.school_payment.default', 'paystack'));

        return match ($provider) {
            'paystack' => app(PaystackGateway::class),
            'flutterwave' => app(FlutterwaveGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment provider [{$provider}]"),
        };
    }
}
