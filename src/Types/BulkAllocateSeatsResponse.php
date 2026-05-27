<?php

declare(strict_types=1);

namespace Crovver\Types;

class BulkAllocateSeatsCapacity
{
    public function __construct(
        public readonly int $activeCount,
        public readonly int $capacityUnits,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            activeCount:   (int) ($data['activeCount'] ?? 0),
            capacityUnits: (int) ($data['capacityUnits'] ?? 0),
        );
    }
}

class BulkAllocateSeatsResponse
{
    /**
     * @param string[]                       $allocated  User IDs inserted this call
     * @param string[]                       $skipped    Already active — not re-inserted
     * @param string[]                       $rejected   Exceeded capacity — not inserted
     */
    public function __construct(
        public readonly array $allocated,
        public readonly array $skipped,
        public readonly array $rejected,
        public readonly BulkAllocateSeatsCapacity $capacity,
        public readonly ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            allocated: $data['allocated'] ?? [],
            skipped:   $data['skipped']   ?? [],
            rejected:  $data['rejected']  ?? [],
            capacity:  BulkAllocateSeatsCapacity::fromArray($data['capacity'] ?? []),
            message:   $data['message']   ?? null,
        );
    }
}
