<?php

declare(strict_types=1);

namespace Crovver\Types;

class Tenant
{
    public function __construct(
        public readonly string $id,
        public readonly string $externalTenantId,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $isActive,
        public readonly string $createdAt,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            externalTenantId: $data['externalTenantId'],
            name: $data['name'],
            slug: $data['slug'],
            isActive: $data['isActive'],
            createdAt: $data['createdAt'],
            metadata: $data['metadata'] ?? null,
        );
    }
}
