<?php

use Crovver\Types\Plan;
use Crovver\Types\ProrationCheckoutResponse;

function tieredPlan(array $tiers): Plan
{
    return Plan::fromArray([
        'id' => 'pl-1',
        'name' => 'Enterprise',
        'pricing' => ['currency' => 'USD', 'interval' => 'monthly', 'isSeatBased' => true, 'basePrice' => 0, 'includedSeats' => 10, 'perSeatPrice' => 0],
        'prices' => [['currency' => 'USD', 'amount' => null, 'basePrice' => 0, 'perSeatPrice' => 0]],
        'seatTiers' => $tiers,
        'trial' => ['days' => 0, 'available' => false],
        'test_mode' => false,
        'isFree' => false,
        'isSeatBased' => true,
        'features' => [],
        'product' => ['id' => 'p-1', 'name' => 'Acme'],
    ]);
}

$ladder = [
    ['currency' => 'USD', 'minQuantity' => 1, 'maxQuantity' => 10, 'price' => '80.00'],
    ['currency' => 'USD', 'minQuantity' => 11, 'maxQuantity' => 50, 'price' => '250.00'],
    ['currency' => 'USD', 'minQuantity' => 51, 'maxQuantity' => null, 'price' => '400.00'],
];

it('prices a tiered plan by the band holding the seat count, not seats times a band', function () use ($ladder) {
    $plan = tieredPlan($ladder);
    expect($plan->seatTiers)->toHaveCount(3);
    expect($plan->seatTiers[2]->maxQuantity)->toBeNull();
    expect($plan->periodCharge(10))->toBe(80.0);
    expect($plan->periodCharge(11))->toBe(250.0);
    expect($plan->periodCharge(500))->toBe(400.0);
});

it('is null for a count no band holds', function () use ($ladder) {
    expect(tieredPlan(array_slice($ladder, 0, 2))->periodCharge(51))->toBeNull();
});

it('keeps base + extra seats times per seat for a plan without bands', function () {
    $plan = Plan::fromArray([
        'id' => 'pl-2', 'name' => 'Team',
        'pricing' => ['currency' => 'USD', 'interval' => 'monthly', 'isSeatBased' => true, 'basePrice' => 50, 'includedSeats' => 5, 'perSeatPrice' => 5],
        'prices' => [['currency' => 'USD', 'amount' => null, 'basePrice' => 50, 'perSeatPrice' => 5]],
        'trial' => ['days' => 0, 'available' => false], 'test_mode' => false, 'isFree' => false, 'isSeatBased' => true,
        'features' => [], 'product' => ['id' => 'p-1', 'name' => 'Acme'],
    ]);
    expect($plan->seatTiers)->toBe([]);
    expect($plan->periodCharge(8))->toBe(65.0);
});

it('reads a same-band seat change, which has no proration id', function () {
    $res = ProrationCheckoutResponse::fromArray([
        'prorationId' => null, 'checkoutUrl' => null, 'invoiceId' => null, 'requiresPayment' => false,
        'prorationAmount' => 0, 'prorationDetails' => [], 'message' => 'Capacity updated. No payment is due.',
    ]);
    expect($res->prorationId)->toBeNull();
    expect($res->requiresPayment)->toBeFalse();
});
