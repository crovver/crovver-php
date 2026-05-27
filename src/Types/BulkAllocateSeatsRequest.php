<?php

declare(strict_types=1);

namespace Crovver\Types;

class BulkAllocateSeatUser
{
    public function __construct(
        public readonly string  $externalUserId,
        public readonly ?string $email = null,
        public readonly ?string $name = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        // Exclude null and empty-string values so the API correctly rejects
        // missing externalUserId rather than receiving an empty string that
        // passes PHP type-checking but fails the API's non-empty trim() check.
        return array_filter([
            'externalUserId' => $this->externalUserId,
            'email'          => $this->email,
            'name'           => $this->name,
        ], fn($v) => $v !== null && $v !== '');
    }
}

class BulkAllocateSeatsRequest
{
    /**
     * @param string                    $requestingEntityId  External tenant ID
     * @param BulkAllocateSeatUser[]    $users               List of users to allocate (max 100)
     * @param array<string, mixed>|null $metadata            Attached to every allocation
     */
    public function __construct(
        public readonly string $requestingEntityId,
        public readonly array $users,
        public readonly ?array $metadata = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'requestingEntityId' => $this->requestingEntityId,
            'users'              => array_map(fn(BulkAllocateSeatUser $u) => $u->toArray(), $this->users),
            'metadata'           => $this->metadata,
        ], fn($v) => $v !== null);
    }
}
