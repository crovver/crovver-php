<?php

declare(strict_types=1);

namespace Crovver\Types;

class AllocateSeatCapacity
{
    public function __construct(
        public readonly int $activeCount,
        public readonly int $capacityUnits,
        public readonly bool $exceeded,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            activeCount:   $data['activeCount'] ?? 0,
            capacityUnits: $data['capacityUnits'] ?? 0,
            exceeded:      $data['exceeded'] ?? false,
        );
    }
}

class AllocateSeatProration
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly string $calculation,
        public readonly float $daysRemaining,
        public readonly float $totalDaysInPeriod,
        public readonly float $perSeatPrice,
        public readonly int $currentSeats,
        public readonly int $newSeats,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            amount:             (float) ($data['amount'] ?? 0),
            currency:           $data['currency'] ?? 'USD',
            description:        $data['description'] ?? '',
            calculation:        $data['calculation'] ?? '',
            daysRemaining:      (float) ($data['daysRemaining'] ?? 0),
            totalDaysInPeriod:  (float) ($data['totalDaysInPeriod'] ?? 0),
            perSeatPrice:       (float) ($data['perSeatPrice'] ?? 0),
            currentSeats:       (int) ($data['currentSeats'] ?? 0),
            newSeats:           (int) ($data['newSeats'] ?? 0),
        );
    }
}

class AllocateSeatResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly ?AllocateSeatCapacity $capacity = null,
        /** Set when adding a seat would exceed capacity — confirm to proceed. */
        public readonly bool $requiresProration = false,
        public readonly ?AllocateSeatProration $proration = null,
        /** Set after proration confirmed — call createProrationCheckout to pay. */
        public readonly bool $requiresCheckout = false,
        public readonly ?int $newCapacity = null,
        public readonly ?float $prorationAmount = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success:           $data['success'] ?? true,
            message:           $data['message'] ?? null,
            capacity:          isset($data['capacity']) ? AllocateSeatCapacity::fromArray($data['capacity']) : null,
            requiresProration: $data['requiresProration'] ?? false,
            proration:         isset($data['proration']) ? AllocateSeatProration::fromArray($data['proration']) : null,
            requiresCheckout:  $data['requiresCheckout'] ?? false,
            newCapacity:       isset($data['newCapacity']) ? (int) $data['newCapacity'] : null,
            prorationAmount:   isset($data['prorationAmount']) ? (float) $data['prorationAmount'] : null,
        );
    }
}
