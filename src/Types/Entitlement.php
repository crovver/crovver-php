<?php

declare(strict_types=1);

namespace Crovver\Types;

class RecordUsageResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            message: $data['message'] ?? null,
        );
    }
}

class CheckUsageLimitResponse
{
    public function __construct(
        public readonly bool $allowed,
        public readonly int $current,
        public readonly ?int $limit,
        public readonly ?int $remaining,
        public readonly ?float $percentage,
    ) {}

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
