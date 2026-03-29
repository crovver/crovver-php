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
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly PlanPricing $pricing,
        public readonly array $trial,
        public readonly bool $testMode,
        public readonly bool $isFree,
        public readonly array $features,
        public readonly array $limits,
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
            trial: $data['trial'],
            testMode: $data['test_mode'],
            isFree: $data['isFree'] ?? $data['is_free'] ?? false,
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
