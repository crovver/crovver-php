<?php

use Crovver\Types\CreateTenantRequest;

it('keeps the positional B2B call and sends externalTenantId', function () {
    $request = new CreateTenantRequest('ws-1', 'Acme', 'user-1');
    expect($request->toArray())->toBe(['externalTenantId' => 'ws-1', 'name' => 'Acme', 'externalUserId' => 'user-1']);
});

it('creates a D2C customer when externalTenantId is null, omitting the field', function () {
    $request = new CreateTenantRequest(externalTenantId: null, name: 'Ada', externalUserId: 'user-1', ownerEmail: 'ada@example.com');
    expect($request->toArray())->toBe(['name' => 'Ada', 'externalUserId' => 'user-1', 'ownerEmail' => 'ada@example.com']);
});
