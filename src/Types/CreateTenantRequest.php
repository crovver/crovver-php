<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreateTenantRequest
{
    /**
     * @param ?string $externalTenantId The workspace ID (B2B). Pass null for an individual customer (D2C):
     *                                  the tenant is keyed on $externalUserId and the field is not sent.
     * @param ?string $ownerEmail       Also becomes the customer's email in the dashboard when it has none.
     */
    public function __construct(
        public readonly ?string $externalTenantId,
        public readonly string $name,
        public readonly string $externalUserId,
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
            'externalUserId'      => $this->externalUserId,
            'ownerEmail'          => $this->ownerEmail,
            'ownerName'           => $this->ownerName,
            'slug'                => $this->slug,
            'metadata'            => $this->metadata,
        ], fn($v) => $v !== null);
    }
}
