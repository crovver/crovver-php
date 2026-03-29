<?php

declare(strict_types=1);

namespace Crovver\Types;

class PaymentProviderMapping
{
    public function __construct(
        public readonly string $providerId,
        public readonly string $providerType,
        public readonly string $providerName,
        public readonly bool $isActive,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            providerId: $data['provider_id'],
            providerType: $data['provider_type'],
            providerName: $data['provider_name'],
            isActive: $data['is_active'],
        );
    }
}
