<?php

declare(strict_types=1);

namespace Crovver\Types;

class TenantOwner
{
    public function __construct(
        public readonly string $externalUserId,
        public readonly string $role,
        public readonly ?string $email = null,
        public readonly ?string $name = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            externalUserId: $data['externalUserId'],
            role: $data['role'],
            email: $data['email'] ?? null,
            name: $data['name'] ?? null,
        );
    }
}

class Tenant
{
    public function __construct(
        public readonly string $id,
        public readonly string $externalTenantId,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $isActive,
        public readonly string $createdAt,
        public readonly ?array $metadata = null,
    ) {}

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

class CreateTenantRequest
{
    public function __construct(
        public readonly string $externalTenantId,
        public readonly string $name,
        public readonly string $ownerExternalUserId,
        public readonly ?string $ownerEmail = null,
        public readonly ?string $ownerName = null,
        public readonly ?string $slug = null,
        public readonly ?array $metadata = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'externalTenantId'    => $this->externalTenantId,
            'name'                => $this->name,
            'ownerExternalUserId' => $this->ownerExternalUserId,
            'ownerEmail'          => $this->ownerEmail,
            'ownerName'           => $this->ownerName,
            'slug'                => $this->slug,
            'metadata'            => $this->metadata,
        ], fn($v) => $v !== null);
    }
}

class CreateTenantResponse
{
    public function __construct(
        public readonly Tenant $tenant,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tenant: Tenant::fromArray($data['tenant']),
        );
    }
}

class GetTenantResponse
{
    /** @param TenantOwner[] $members */
    public function __construct(
        public readonly Tenant $tenant,
        public readonly array $members,
        public readonly array $organization,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tenant: Tenant::fromArray($data['tenant']),
            members: array_map(fn($m) => TenantOwner::fromArray($m), $data['members'] ?? []),
            organization: $data['organization'],
        );
    }
}
