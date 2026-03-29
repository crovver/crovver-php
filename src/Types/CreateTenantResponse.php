<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreateTenantResponse
{
    public function __construct(
        public readonly Tenant $tenant,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            tenant: Tenant::fromArray($data['tenant']),
        );
    }
}
