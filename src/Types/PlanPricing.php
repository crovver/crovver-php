<?php

declare(strict_types=1);

namespace Crovver\Types;

class PlanPricing
{
    public function __construct(
        public readonly string $currency,
        public readonly string $interval,
        public readonly bool $isSeatBased,
        public readonly ?float $amount = null,
        public readonly ?float $basePrice = null,
        public readonly ?int $includedSeats = null,
        public readonly ?float $perSeatPrice = null,
        public readonly ?int $minSeats = null,
        public readonly ?int $maxSeats = null,
        /**
         * Subscription responses only: the plan's seat bands in the subscription's currency.
         *
         * @var PlanSeatTier[]
         */
        public readonly array $seatTiers = [],
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            currency: $data['currency'],
            interval: $data['interval'],
            isSeatBased: $data['isSeatBased'],
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            basePrice: isset($data['basePrice']) ? (float) $data['basePrice'] : null,
            includedSeats: isset($data['includedSeats']) ? (int) $data['includedSeats'] : null,
            perSeatPrice: isset($data['perSeatPrice']) ? (float) $data['perSeatPrice'] : null,
            minSeats: isset($data['minSeats']) ? (int) $data['minSeats'] : null,
            maxSeats: isset($data['maxSeats']) ? (int) $data['maxSeats'] : null,
            seatTiers: PlanSeatTier::listFromArray($data['seatTiers'] ?? []),
        );
    }
}
