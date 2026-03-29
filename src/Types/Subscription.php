<?php

declare(strict_types=1);

namespace Crovver\Types;

class Subscription
{
    /**
     * @param array<string, mixed>      $billing
     * @param array<string, mixed>      $trial
     * @param array<string, mixed>      $cancellation
     * @param array<string, mixed>      $plan
     * @param array<string, mixed>      $product
     * @param array<string, mixed>      $paymentProviders
     * @param array<string, mixed>      $metadata
     * @param array<string, mixed>|null $capacity
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?string $providerSubscriptionId,
        public readonly array $billing,
        public readonly array $trial,
        public readonly array $cancellation,
        public readonly array $plan,
        public readonly array $product,
        public readonly array $paymentProviders,
        public readonly array $metadata,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?array $capacity = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: $data['status'],
            providerSubscriptionId: $data['provider_subscription_id'] ?? $data['providerSubscriptionId'] ?? null,
            billing: $data['billing'] ?? [],
            trial: $data['trial'] ?? [],
            cancellation: $data['cancellation'] ?? [],
            plan: $data['plan'] ?? [],
            product: $data['product'] ?? [],
            paymentProviders: $data['payment_providers'] ?? $data['paymentProviders'] ?? [],
            metadata: $data['metadata'] ?? [],
            createdAt: $data['created_at'] ?? $data['createdAt'] ?? null,
            updatedAt: $data['updated_at'] ?? $data['updatedAt'] ?? null,
            capacity: $data['capacity'] ?? null,
        );
    }
}
