<?php

declare(strict_types=1);

namespace Crovver\Types;

/**
 * One seat band of a tiered plan. The band's price is the whole billing period's price for any seat
 * count in [minQuantity, maxQuantity] — a flat price for the range, not a per-seat rate. A tiered
 * plan's basePrice and perSeatPrice are 0; use Plan::periodCharge() to price it.
 */
class PlanSeatTier
{
    public function __construct(
        public readonly string $currency,
        public readonly int $minQuantity,
        /** null for the open top band */
        public readonly ?int $maxQuantity,
        /** Two-decimal string, e.g. "250.00" */
        public readonly string $price,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            currency: (string) $data['currency'],
            minQuantity: (int) $data['minQuantity'],
            maxQuantity: isset($data['maxQuantity']) ? (int) $data['maxQuantity'] : null,
            price: (string) $data['price'],
        );
    }

    public function holds(int $seats): bool
    {
        return $this->minQuantity <= $seats && ($this->maxQuantity === null || $seats <= $this->maxQuantity);
    }

    /**
     * The band holding $seats in one currency's ladder, or null.
     *
     * @param PlanSeatTier[] $tiers
     */
    public static function for(array $tiers, string $currency, int $seats): ?self
    {
        $cur = strtoupper($currency);
        $ladder = array_values(array_filter($tiers, fn(self $t) => strtoupper($t->currency) === $cur));
        usort($ladder, fn(self $a, self $b) => $a->minQuantity <=> $b->minQuantity);
        foreach ($ladder as $tier) {
            if ($tier->holds($seats)) {
                return $tier;
            }
        }
        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return PlanSeatTier[]
     */
    public static function listFromArray(array $rows): array
    {
        return array_map(fn($t) => self::fromArray($t), $rows);
    }
}
