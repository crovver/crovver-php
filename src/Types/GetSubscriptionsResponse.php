<?php

declare(strict_types=1);

namespace Crovver\Types;

class GetSubscriptionsResponse
{
    /** @param Subscription[] $subscriptions */
    public function __construct(
        public readonly array $subscriptions,
    ) {}

    /** @param array<string|int, mixed> $data */
    public static function fromArray(array $data): self
    {
        $items = $data['subscriptions'] ?? (isset($data[0]) ? $data : []);
        return new self(
            subscriptions: array_map(fn($s) => Subscription::fromArray($s), $items),
        );
    }
}
