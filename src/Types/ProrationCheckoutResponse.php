<?php

declare(strict_types=1);

namespace Crovver\Types;

class ProrationCheckoutResponse
{
    /**
     * @param array<string, mixed> $prorationDetails
     */
    public function __construct(
        public readonly string $prorationId,
        public readonly ?string $checkoutUrl,
        public readonly bool $requiresPayment,
        public readonly float $prorationAmount,
        public readonly array $prorationDetails,
        public readonly string $message,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            prorationId: $data['prorationId'],
            checkoutUrl: $data['checkoutUrl'] ?? null,
            requiresPayment: (bool) ($data['requiresPayment'] ?? false),
            prorationAmount: (float) ($data['prorationAmount'] ?? 0),
            prorationDetails: $data['prorationDetails'] ?? [],
            message: $data['message'] ?? '',
        );
    }
}
