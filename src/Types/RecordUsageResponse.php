<?php

declare(strict_types=1);

namespace Crovver\Types;

class RecordUsageResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            message: $data['message'] ?? null,
        );
    }
}
