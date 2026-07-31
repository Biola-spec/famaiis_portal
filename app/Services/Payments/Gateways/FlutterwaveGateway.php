<?php

namespace App\Services\Payments\Gateways;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use RuntimeException;

class FlutterwaveGateway implements PaymentGatewayInterface
{
    public function initialize(string $email, int $amountInKobo, string $reference, string $callbackUrl, array $metadata = []): array
    {
        throw new RuntimeException('Flutterwave gateway is not implemented yet.');
    }

    public function verify(string $reference): array
    {
        throw new RuntimeException('Flutterwave gateway is not implemented yet.');
    }
}
