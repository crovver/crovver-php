<?php

declare(strict_types=1);

namespace Crovver\Types;

class PlanPricing
{
    public function __construct(
        public readonly string $currency,
        public readonly string $interval,
        public readonly bool $isSeatBased,
        public readonly ?float $amount = null,
        public readonly ?float $basePrice = null,
        public readonly ?int $includedSeats = null,
        public readonly ?float $perSeatPrice = null,
        public readonly ?int $minSeats = null,
        public readonly ?int $maxSeats = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            currency: $data['currency'],
            interval: $data['interval'],
            isSeatBased: $data['isSeatBased'],
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            basePrice: isset($data['basePrice']) ? (float) $data['basePrice'] : null,
            includedSeats: isset($data['includedSeats']) ? (int) $data['includedSeats'] : null,
            perSeatPrice: isset($data['perSeatPrice']) ? (float) $data['perSeatPrice'] : null,
            minSeats: isset($data['minSeats']) ? (int) $data['minSeats'] : null,
            maxSeats: isset($data['maxSeats']) ? (int) $data['maxSeats'] : null,
        );
    }
}

class PaymentProviderMapping
{
    public function __construct(
        public readonly string $providerId,
        public readonly string $providerType,
        public readonly bool $isActive,
        public readonly ?string $externalPriceId = null,
        public readonly ?string $externalProductId = null,
        public readonly ?string $externalBasePriceId = null,
        public readonly ?string $externalPerSeatPriceId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            providerId: $data['provider_id'],
            providerType: $data['provider_type'],
            isActive: $data['is_active'],
            externalPriceId: $data['external_price_id'] ?? null,
            externalProductId: $data['external_product_id'] ?? null,
            externalBasePriceId: $data['external_base_price_id'] ?? null,
            externalPerSeatPriceId: $data['external_per_seat_price_id'] ?? null,
        );
    }
}

class Plan
{
    /** @param PaymentProviderMapping[] $paymentProviders */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly PlanPricing $pricing,
        public readonly array $trial,
        public readonly bool $testMode,
        public readonly array $features,
        public readonly array $limits,
        public readonly array $product,
        public readonly array $paymentProviders,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?bool $isActive = null,
        public readonly ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            pricing: PlanPricing::fromArray($data['pricing']),
            trial: $data['trial'],
            testMode: $data['test_mode'],
            features: $data['features'],
            limits: $data['limits'],
            product: $data['product'],
            paymentProviders: array_map(
                fn($p) => PaymentProviderMapping::fromArray($p),
                $data['payment_providers'] ?? []
            ),
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            isActive: $data['isActive'] ?? $data['is_active'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}

class GetPlansResponse
{
    /** @param Plan[] $plans */
    public function __construct(
        public readonly array $plans,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            plans: array_map(fn($p) => Plan::fromArray($p), $data['plans'] ?? $data),
        );
    }
}
