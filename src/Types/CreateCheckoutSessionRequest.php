<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreateCheckoutSessionRequest
{
    public function __construct(
        public readonly string $requestingUserId,
        public readonly string $planId,
        public readonly ?string $provider = null,
        public readonly ?string $requestingTenantId = null,
        public readonly ?string $userEmail = null,
        public readonly ?string $userName = null,
        public readonly ?string $successUrl = null,
        public readonly ?string $cancelUrl = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata = null,
        public readonly ?int $quantity = null,
    ) {}

    /** @return array<string, mixed> */
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
