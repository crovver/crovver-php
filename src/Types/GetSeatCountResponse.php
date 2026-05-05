<?php

declare(strict_types=1);

namespace Crovver\Types;

class GetSeatCountResponse
{
    public function __construct(
        public readonly int $activeCount,
        public readonly int $capacityUnits,
        public readonly int $utilizationPercentage,
        public readonly string $billingMode,
        public readonly bool $exceeded,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            activeCount:           (int) ($data['activeCount'] ?? 0),
            capacityUnits:         (int) ($data['capacityUnits'] ?? 0),
            utilizationPercentage: (int) ($data['utilizationPercentage'] ?? 0),
            billingMode:           $data['billingMode'] ?? 'recurring',
            exceeded:              $data['exceeded'] ?? false,
        );
    }
}
