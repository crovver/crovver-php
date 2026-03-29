<?php

declare(strict_types=1);

namespace Crovver\Types;

class CheckUsageLimitResponse
{
    public function __construct(
        public readonly bool $allowed,
        public readonly int $current,
        public readonly ?int $limit,
        public readonly ?int $remaining,
        public readonly ?float $percentage,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            allowed: $data['allowed'],
            current: (int) $data['current'],
            limit: isset($data['limit']) ? (int) $data['limit'] : null,
            remaining: isset($data['remaining']) ? (int) $data['remaining'] : null,
            percentage: isset($data['percentage']) ? (float) $data['percentage'] : null,
        );
    }
}
