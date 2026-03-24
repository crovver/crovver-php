<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreateCheckoutSessionRequest
{
    public function __construct(
        public readonly string $requestingUserId,
        public readonly string $planId,
        public readonly string $provider,
        public readonly ?string $requestingTenantId = null,
        public readonly ?string $userEmail = null,
        public readonly ?string $userName = null,
        public readonly ?string $successUrl = null,
        public readonly ?string $cancelUrl = null,
        public readonly ?array $metadata = null,
        public readonly ?int $quantity = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'requestingUserId'   => $this->requestingUserId,
            'planId'             => $this->planId,
            'provider'           => $this->provider,
            'requestingTenantId' => $this->requestingTenantId,
            'userEmail'          => $this->userEmail,
            'userName'           => $this->userName,
            'successUrl'         => $this->successUrl,
            'cancelUrl'          => $this->cancelUrl,
            'metadata'           => $this->metadata,
            'quantity'           => $this->quantity,
        ], fn($v) => $v !== null);
    }
}

class CreateCheckoutSessionResponse
{
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $checkoutUrl,
        public readonly string $sessionId,
        public readonly ?string $error = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            subscriptionId: $data['subscriptionId'],
            checkoutUrl: $data['checkoutUrl'],
            sessionId: $data['sessionId'],
            error: $data['error'] ?? null,
        );
    }
}
