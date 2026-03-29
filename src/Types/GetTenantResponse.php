<?php

declare(strict_types=1);

namespace Crovver\Types;

class GetTenantResponse
{
    /**
     * @param TenantOwner[]        $members
     * @param array<string, mixed> $organization
     */
    public function __construct(
        public readonly Tenant $tenant,
        public readonly array $members,
        public readonly array $organization,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            tenant: Tenant::fromArray($data['tenant']),
            members: array_map(fn($m) => TenantOwner::fromArray($m), $data['members'] ?? []),
            organization: $data['organization'],
        );
    }
}
