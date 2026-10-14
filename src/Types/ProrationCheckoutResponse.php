<?php

declare(strict_types=1);

namespace Crovver\Types;

class ProrationCheckoutResponse
{
    /**
     * @param array<string, mixed> $prorationDetails
     */
    public function __construct(
        /**
         * Null when nothing is due: on a tiered plan, a seat count that stays in the current band
         * is applied at once, with requiresPayment false.
         */
        public readonly ?string $prorationId,
        public readonly ?string $checkoutUrl,
        public readonly bool $requiresPayment,
        public readonly float $prorationAmount,
        public readonly array $prorationDetails,
        public readonly string $message,
        /** The pending invoice a redirect-gateway (Khalti / eSewa / ConnectIPS) proration settles. */
        public readonly ?string $invoiceId = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            prorationId: $data['prorationId'] ?? null,
            checkoutUrl: $data['checkoutUrl'] ?? null,
            requiresPayment: (bool) ($data['requiresPayment'] ?? false),
            prorationAmount: (float) ($data['prorationAmount'] ?? 0),
            prorationDetails: $data['prorationDetails'] ?? [],
            message: $data['message'] ?? '',
            invoiceId: $data['invoiceId'] ?? null,
        );
    }
}
