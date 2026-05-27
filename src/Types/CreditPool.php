<?php

declare(strict_types=1);

namespace Crovver\Types;

class CreditPool
{
    public function __construct(
        public readonly string  $poolKey,
        public readonly string  $displayName,
        public readonly ?int    $limitPerPeriod,
        public readonly string  $refillBehavior,
        public readonly string  $limitBehavior,
        public readonly ?int    $rolloverCap = null,
        public readonly bool    $isActive = true,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            poolKey:         $data['pool_key']          ?? $data['poolKey'],
            displayName:     $data['display_name']      ?? $data['displayName']      ?? '',
            limitPerPeriod:  isset($data['limit_per_period']) ? (int) $data['limit_per_period']
                           : (isset($data['limitPerPeriod'])  ? (int) $data['limitPerPeriod'] : null),
            refillBehavior:  $data['refill_behavior']   ?? $data['refillBehavior']   ?? 'reset',
            limitBehavior:   $data['limit_behavior']    ?? $data['limitBehavior']    ?? 'hard',
            rolloverCap:     isset($data['rollover_cap']) ? (int) $data['rollover_cap']
                           : (isset($data['rolloverCap'])     ? (int) $data['rolloverCap'] : null),
            isActive:        $data['is_active']         ?? $data['isActive']         ?? true,
        );
    }
}
