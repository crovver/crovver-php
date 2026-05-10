<?php

declare(strict_types=1);

namespace Crovver\Types;

class AddonPurchaseResponse
{
    public function __construct(
        public readonly string $purchaseId,
        public readonly ?string $checkoutUrl,
        public readonly bool $requiresPayment,
        public readonly string $addonName,
        public readonly int $creditQty,
        public readonly float $amount,
        public readonly string $currency,
        public readonly bool $alreadyProcessed,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            purchaseId: $data['purchaseId'],
            checkoutUrl: $data['checkoutUrl'] ?? null,
            requiresPayment: (bool) ($data['requiresPayment'] ?? false),
            addonName: $data['addonName'],
            creditQty: (int) ($data['creditQty'] ?? 0),
            amount: (float) ($data['amount'] ?? 0),
            currency: $data['currency'],
            alreadyProcessed: (bool) ($data['alreadyProcessed'] ?? false),
        );
    }
}
