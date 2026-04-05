<?php

declare(strict_types=1);

namespace Crovver\Types;

class CancelSubscriptionResponse
{
    public function __construct(
        public readonly string  $subscriptionId,
        public readonly string  $status,
        public readonly ?string $canceledAt,
        public readonly ?string $currentPeriodEnd,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            subscriptionId:   $data['subscriptionId']    ?? '',
            status:           $data['status']             ?? 'pending_cancel',
            canceledAt:       $data['canceledAt']         ?? null,
            currentPeriodEnd: $data['currentPeriodEnd']   ?? null,
        );
    }
}
