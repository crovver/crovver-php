<?php

declare(strict_types=1);

namespace Crovver\Types;

class Plan
{
    /**
     * @param array<string, mixed>     $trial
     * @param array<string, mixed>     $features
     * @param array<string, mixed>     $limits
     * @param array<string, mixed>     $product
     * @param PaymentProviderMapping[] $paymentProviders
     * @param array<int, array{currency: string, amount: float|null, basePrice: float|null, perSeatPrice: float|null}> $prices
     * @param CreditPool[]             $creditPools
     * @param PlanSeatTier[]           $seatTiers   Every currency's bands; empty unless the plan is tiered
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly PlanPricing $pricing,
        public readonly array $prices,
        public readonly array $creditPools,
        public readonly array $trial,
        public readonly bool $testMode,
        public readonly bool $isFree,
        public readonly bool $isSeatBased,
        public readonly array $features,
        public readonly ?array $limits,
        public readonly array $product,
        public readonly array $paymentProviders,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?bool $isActive = null,
        public readonly ?string $description = null,
        public readonly array $seatTiers = [],
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            pricing: PlanPricing::fromArray($data['pricing']),
            prices: $data['prices'] ?? [],
            creditPools: array_map(
                fn($c) => CreditPool::fromArray($c),
                $data['credit_pools'] ?? $data['creditPools'] ?? []
            ),
            trial: $data['trial'],
            testMode: $data['test_mode'],
            isFree: $data['isFree'] ?? $data['is_free'] ?? false,
            isSeatBased: $data['isSeatBased'] ?? $data['is_seat_based'] ?? false,
            features: $data['features'],
            limits: $data['limits'] ?? null,
            product: $data['product'],
            paymentProviders: array_map(
                fn($p) => PaymentProviderMapping::fromArray($p),
                $data['paymentProviders'] ?? $data['payment_providers'] ?? []
            ),
            createdAt: $data['createdAt'] ?? $data['created_at'] ?? null,
            updatedAt: $data['updatedAt'] ?? $data['updated_at'] ?? null,
            isActive: $data['isActive'] ?? $data['is_active'] ?? null,
            description: $data['description'] ?? null,
            seatTiers: PlanSeatTier::listFromArray($data['seatTiers'] ?? []),
        );
    }

    /**
     * One billing period's price at a seat count, by the rules Crovver charges: free is 0; a tiered
     * plan is the price of the band holding $seats (null when no band does — that count cannot be
     * purchased); another seat-based plan is basePrice + max(0, seats - includedSeats) * perSeatPrice;
     * a flat plan is its amount. Null too when the plan has no price in $currency.
     */
    public function periodCharge(int $seats, ?string $currency = null): ?float
    {
        if ($this->isFree) {
            return 0.0;
        }
        $cur = strtoupper($currency ?? $this->pricing->currency);
        $ladder = array_values(array_filter($this->seatTiers, fn(PlanSeatTier $t) => strtoupper($t->currency) === $cur));
        if ($this->isSeatBased && $ladder !== []) {
            $band = PlanSeatTier::for($ladder, $cur, $seats);
            return $band === null ? null : (float) $band->price;
        }
        $price = null;
        foreach ($this->prices as $row) {
            if (strtoupper((string) $row['currency']) === $cur) {
                $price = $row;
                break;
            }
        }
        if ($price === null) {
            return null;
        }
        if (!$this->isSeatBased) {
            return isset($price['amount']) ? (float) $price['amount'] : null;
        }
        return (float) ($price['basePrice'] ?? 0)
            + max($seats - ($this->pricing->includedSeats ?? 0), 0) * (float) ($price['perSeatPrice'] ?? 0);
    }
}
