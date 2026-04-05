<?php

declare(strict_types=1);

namespace Crovver\Types;

class GetInvoicesResponse
{
    /** @param Invoice[] $invoices */
    public function __construct(
        public readonly array $invoices,
    ) {}

    /** @param array<string|int, mixed> $data */
    public static function fromArray(array $data): self
    {
        $items = $data['invoices'] ?? (isset($data[0]) ? $data : []);
        return new self(
            invoices: array_map(fn($i) => Invoice::fromArray($i), $items),
        );
    }
}
