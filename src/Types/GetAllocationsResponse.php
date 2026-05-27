<?php

declare(strict_types=1);

namespace Crovver\Types;

class AllocationUser
{
    public function __construct(
        public readonly string  $externalUserId,
        public readonly ?string $email,
        public readonly ?string $name,
        public readonly string  $status,
        public readonly string  $allocatedAt,
        public readonly ?string $removedAt,
        /** @var array<string, mixed> */
        public readonly array   $metadata,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            externalUserId: $data['externalUserId'] ?? '',
            email:          $data['email']          ?? null,
            name:           $data['name']            ?? null,
            status:         $data['status']          ?? 'active',
            allocatedAt:    $data['allocatedAt']     ?? '',
            removedAt:      $data['removedAt']       ?? null,
            metadata:       $data['metadata']        ?? [],
        );
    }
}

class AllocationCapacity
{
    public function __construct(
        public readonly int  $activeCount,
        public readonly int  $capacityUnits,
        public readonly int  $utilizationPercentage,
        public readonly bool $exceeded,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            activeCount:           (int)  ($data['activeCount']           ?? 0),
            capacityUnits:         (int)  ($data['capacityUnits']         ?? 0),
            utilizationPercentage: (int)  ($data['utilizationPercentage'] ?? 0),
            exceeded:              (bool) ($data['exceeded']              ?? false),
        );
    }
}

class AllocationPagination
{
    public function __construct(
        public readonly int  $total,
        public readonly int  $page,
        public readonly int  $limit,
        public readonly int  $totalPages,
        public readonly bool $hasNextPage,
        public readonly bool $hasPreviousPage,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            total:           (int)  ($data['total']           ?? 0),
            page:            (int)  ($data['page']            ?? 1),
            limit:           (int)  ($data['limit']           ?? 50),
            totalPages:      (int)  ($data['totalPages']      ?? 0),
            hasNextPage:     (bool) ($data['hasNextPage']     ?? false),
            hasPreviousPage: (bool) ($data['hasPreviousPage'] ?? false),
        );
    }
}

class GetAllocationsResponse
{
    /**
     * @param AllocationUser[] $allocations
     */
    public function __construct(
        public readonly array               $allocations,
        public readonly AllocationCapacity  $capacity,
        public readonly AllocationPagination $pagination,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            allocations: array_map(
                fn(array $u) => AllocationUser::fromArray($u),
                $data['allocations'] ?? []
            ),
            capacity:   AllocationCapacity::fromArray($data['capacity']     ?? []),
            pagination: AllocationPagination::fromArray($data['pagination'] ?? []),
        );
    }
}
