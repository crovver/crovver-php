<?php

declare(strict_types=1);

namespace Crovver\Types;

class GetSupportedProvidersResponse
{
    /** @param SupportedPaymentProvider[] $providers */
    public function __construct(
        public readonly array $providers,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            providers: array_map(
                fn($p) => SupportedPaymentProvider::fromArray($p),
                $data['providers'] ?? []
            ),
        );
    }
}
