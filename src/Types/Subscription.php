<?php

declare(strict_types=1);

namespace Crovver\Types;

class Subscription
{
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

class ProrationCheckoutResponse
{
    public function __construct(
        public readonly string $prorationId,
        public readonly ?string $checkoutUrl,
        public readonly bool $requiresPayment,
        public readonly float $prorationAmount,
        public readonly array $prorationDetails,
        public readonly string $message,
    ) {}

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

class GetSubscriptionsResponse
{
    /** @param Subscription[] $subscriptions */
    public function __construct(
        public readonly array $subscriptions,
    ) {}

    public static function fromArray(array $data): self
    {
        $items = $data['subscriptions'] ?? (isset($data[0]) ? $data : []);
        return new self(
            subscriptions: array_map(fn($s) => Subscription::fromArray($s), $items),
        );
    }
}
