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

    /** @param array<string, mixed> $data */
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
