<?php

namespace App\Services\Payments\Contracts;

interface PaymentGatewayInterface
{
    public function initialize(string $email, int $amountInKobo, string $reference, string $callbackUrl, array $metadata = []): array;

    public function verify(string $reference): array;
}
