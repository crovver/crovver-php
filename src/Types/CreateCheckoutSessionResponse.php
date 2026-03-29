<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreateCheckoutSessionResponse
{
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $planName,
        public readonly bool $isFree,
        public readonly ?string $checkoutUrl = null,
        public readonly ?string $sessionId = null,
        public readonly ?string $provider = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $error = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            subscriptionId: $data['subscriptionId'],
            planName:        $data['planName'],
            isFree:          $data['isFree'] ?? false,
            checkoutUrl:     $data['checkoutUrl'] ?? null,
            sessionId:       $data['sessionId'] ?? null,
            provider:        $data['provider'] ?? null,
            amount:          isset($data['amount']) ? (float) $data['amount'] : null,
            currency:        $data['currency'] ?? null,
            error:           $data['error'] ?? null,
        );
    }
}
