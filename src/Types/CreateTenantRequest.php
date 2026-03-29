<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreateTenantRequest
{
    public function __construct(
        public readonly string $externalTenantId,
        public readonly string $name,
        public readonly string $ownerExternalUserId,
        public readonly ?string $ownerEmail = null,
        public readonly ?string $ownerName = null,
        public readonly ?string $slug = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata = null,
    ) {}

    /** @return array<string, mixed> */
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
