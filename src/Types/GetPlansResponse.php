<?php

declare(strict_types=1);

namespace Crovver\Types;

class GetPlansResponse
{
    /** @param Plan[] $plans */
    public function __construct(
        public readonly array $plans,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            plans: array_map(fn($p) => Plan::fromArray($p), $data['plans'] ?? $data),
        );
    }
}
