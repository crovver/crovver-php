<?php

declare(strict_types=1);

namespace Crovver\Types;

class ConsumeResponse
{
    public function __construct(
        public readonly string $result,
        public readonly int $remaining,
        public readonly bool $alreadyProcessed,
        public readonly string $poolKey,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            result: $data['result'],
            remaining: (int) $data['remaining'],
            alreadyProcessed: (bool) ($data['alreadyProcessed'] ?? false),
            poolKey: $data['poolKey'],
        );
    }
}
