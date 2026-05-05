<?php

declare(strict_types=1);

namespace Crovver\Types;

class AllocateSeatRequest
{
    public function __construct(
        public readonly string $requestingEntityId,
        public readonly ?string $externalUserId = null,
        public readonly ?string $email = null,
        public readonly ?string $name = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'requestingEntityId' => $this->requestingEntityId,
            'externalUserId'     => $this->externalUserId,
            'email'              => $this->email,
            'name'               => $this->name,
            'metadata'           => $this->metadata,
        ], fn($v) => $v !== null);
    }
}
