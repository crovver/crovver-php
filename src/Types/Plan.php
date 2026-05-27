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
        );
    }
}
