<?php

use Crovver\Types\RemoveSeatResponse;

it('reads the vacant capacity from a seat removal', function () {
    $r = RemoveSeatResponse::fromArray([
        'externalUserId' => 'user-42',
        'alreadyRemoved' => false,
        'removedAt' => '2026-10-18T12:00:00.000Z',
        'capacity' => ['activeCount' => 2, 'capacityUnits' => 5, 'vacantSeats' => 3],
    ]);

    expect($r->externalUserId)->toBe('user-42')
        ->and($r->alreadyRemoved)->toBeFalse()
        ->and($r->activeCount)->toBe(2)
        ->and($r->capacityUnits)->toBe(5)
        ->and($r->vacantSeats)->toBe(3);
});

it('marks a retried removal', function () {
    $r = RemoveSeatResponse::fromArray([
        'externalUserId' => 'user-42',
        'alreadyRemoved' => true,
        'removedAt' => '2026-10-18T10:00:00.000Z',
        'capacity' => ['activeCount' => 2, 'capacityUnits' => 5, 'vacantSeats' => 3],
    ]);
    expect($r->alreadyRemoved)->toBeTrue();
});
