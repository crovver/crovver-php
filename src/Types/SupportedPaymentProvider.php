<?php

declare(strict_types=1);

namespace Crovver\Types;

class SupportedPaymentProvider
{
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $name,
        public readonly bool $isEnabled,
        public readonly bool $supportsRecurringBilling,
        public readonly bool $supportsWebhooks,
        public readonly bool $supportsTestMode,
        /** @var list<string> */
        public readonly array $capabilities,
        public readonly ?string $description = null,
        public readonly ?string $logoUrl = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            code: $data['code'],
            name: $data['name'],
            isEnabled: $data['isEnabled'],
            supportsRecurringBilling: $data['supportsRecurringBilling'],
            supportsWebhooks: $data['supportsWebhooks'],
            supportsTestMode: $data['supportsTestMode'],
            capabilities: $data['capabilities'] ?? [],
            description: $data['description'] ?? null,
            logoUrl: $data['logoUrl'] ?? null,
        );
    }
}
