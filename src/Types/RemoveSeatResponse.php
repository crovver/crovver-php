<?php

declare(strict_types=1);

namespace Crovver\Types;

/**
 * A user's seat taken away. The seat stays on the subscription, vacant: `capacityUnits` is unchanged.
 */
class RemoveSeatResponse
{
    public function __construct(
        public readonly string $externalUserId,
        /** True when the user had already been removed: a retry, nothing changed */
        public readonly bool $alreadyRemoved,
        public readonly string $removedAt,
        public readonly int $activeCount,
        public readonly int $capacityUnits,
        public readonly int $vacantSeats,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $capacity = $data['capacity'] ?? [];
        return new self(
            externalUserId: (string) ($data['externalUserId'] ?? ''),
            alreadyRemoved: (bool) ($data['alreadyRemoved'] ?? false),
            removedAt:      (string) ($data['removedAt'] ?? ''),
            activeCount:    (int) ($capacity['activeCount'] ?? 0),
            capacityUnits:  (int) ($capacity['capacityUnits'] ?? 0),
            vacantSeats:    (int) ($capacity['vacantSeats'] ?? 0),
        );
    }
}
