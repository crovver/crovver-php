<?php

declare(strict_types=1);

namespace Crovver;

class CrovverConfig
{
    public readonly string $apiKey;
    public readonly string $baseUrl;
    public readonly int $timeout;
    public readonly int $maxRetries;
    public readonly bool $debug;
    /** @var callable|null */
    public readonly mixed $logger;

    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://app.crovver.com',
        int $timeout = 30,
        int $maxRetries = 3,
        bool $debug = false,
        ?callable $logger = null
    ) {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('apiKey is required');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->maxRetries = $maxRetries;
        $this->debug = $debug;
        $this->logger = $logger;
    }
}
