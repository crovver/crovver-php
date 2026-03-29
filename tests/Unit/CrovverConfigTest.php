<?php

use Crovver\CrovverConfig;

it('uses production base URL by default', function () {
    $config = new CrovverConfig(apiKey: 'test-key');
    expect($config->baseUrl)->toBe('https://app.crovver.com');
});

it('accepts a custom base URL for local development', function () {
    $config = new CrovverConfig(apiKey: 'test-key', baseUrl: 'http://localhost:3000');
    expect($config->baseUrl)->toBe('http://localhost:3000');
});

it('strips trailing slash from base URL', function () {
    $config = new CrovverConfig(apiKey: 'test-key', baseUrl: 'https://app.crovver.com/');
    expect($config->baseUrl)->toBe('https://app.crovver.com');
});

it('throws when apiKey is empty', function () {
    new CrovverConfig(apiKey: '');
})->throws(\InvalidArgumentException::class);
