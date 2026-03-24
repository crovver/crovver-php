<?php

declare(strict_types=1);

namespace Crovver;

use Crovver\Types\CheckUsageLimitResponse;
use Crovver\Types\CreateCheckoutSessionRequest;
use Crovver\Types\CreateCheckoutSessionResponse;
use Crovver\Types\CreateTenantRequest;
use Crovver\Types\CreateTenantResponse;
use Crovver\Types\GetPlansResponse;
use Crovver\Types\GetSubscriptionsResponse;
use Crovver\Types\ProrationCheckoutResponse;
use Crovver\Types\GetSupportedProvidersResponse;
use Crovver\Types\GetTenantResponse;
use Crovver\Types\RecordUsageResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class CrovverClient
{
    private Client $http;
    private CrovverConfig $config;

    private const BASE_DELAY_MS   = 1000;
    private const MAX_DELAY_MS    = 30000;

    public function __construct(CrovverConfig $config)
    {
        $this->config = $config;
        $this->http   = new Client([
            'base_uri'              => $config->baseUrl,
            'timeout'               => $config->timeout,
            'http_errors'           => false,
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $config->apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Tenant Management
    // -------------------------------------------------------------------------

    public function createTenant(CreateTenantRequest $request): CreateTenantResponse
    {
        $data = $this->post('/api/public/tenants', $request->toArray(), retry: true);
        return CreateTenantResponse::fromArray($data);
    }

    public function getTenant(string $externalTenantId): GetTenantResponse
    {
        $data = $this->get('/api/public/tenants', ['externalTenantId' => $externalTenantId], retry: true);
        return GetTenantResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Plans
    // -------------------------------------------------------------------------

    public function getPlans(): GetPlansResponse
    {
        $data = $this->get('/api/public/plans', [], retry: true);
        return GetPlansResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Subscriptions
    // -------------------------------------------------------------------------

    public function getSubscriptions(string $requestingEntityId): GetSubscriptionsResponse
    {
        $data = $this->get('/api/public/subscriptions', ['requestingEntityId' => $requestingEntityId], retry: true);
        return GetSubscriptionsResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Checkout  (NOT retried — avoids duplicate payments)
    // -------------------------------------------------------------------------

    public function createCheckoutSession(CreateCheckoutSessionRequest $request): CreateCheckoutSessionResponse
    {
        $data = $this->post('/api/public/checkout', $request->toArray(), retry: false);
        return CreateCheckoutSessionResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Entitlements
    // -------------------------------------------------------------------------

    public function canAccess(string $requestingEntityId, string $featureKey): bool
    {
        $data = $this->post('/api/public/can-access', [
            'requestingEntityId' => $requestingEntityId,
            'featureKey'         => $featureKey,
        ], retry: true);

        return (bool) ($data['canAccess'] ?? false);
    }

    public function recordUsage(
        string $requestingEntityId,
        string $metric,
        int $value = 1,
        array $metadata = []
    ): RecordUsageResponse {
        $body = array_filter([
            'requestingEntityId' => $requestingEntityId,
            'metric'             => $metric,
            'value'              => $value,
            'metadata'           => $metadata ?: null,
        ], fn($v) => $v !== null);

        $data = $this->post('/api/public/record-usage', $body, retry: true);
        return RecordUsageResponse::fromArray($data);
    }

    public function checkUsageLimit(string $requestingEntityId, string $metric): CheckUsageLimitResponse
    {
        $data = $this->post('/api/public/check-usage-limit', [
            'requestingEntityId' => $requestingEntityId,
            'metric'             => $metric,
        ], retry: true);

        return CheckUsageLimitResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Proration
    // -------------------------------------------------------------------------

    /**
     * Confirm proration and create a checkout session for seat capacity upgrades.
     *
     * After the user confirms the prorated charge, call this to get a payment
     * checkout URL. Capacity is upgraded after payment confirmation via webhook.
     *
     * @param string      $requestingEntityId  External tenant ID
     * @param int         $newCapacity         Total seat count after upgrade
     * @param string|null $planId              Plan ID (required when tenant has multiple active plans)
     * @param string|null $successUrl          Redirect URL on successful payment
     * @param string|null $cancelUrl           Redirect URL on cancelled payment
     */
    public function createProrationCheckout(
        string $requestingEntityId,
        int $newCapacity,
        ?string $planId = null,
        ?string $successUrl = null,
        ?string $cancelUrl = null,
    ): ProrationCheckoutResponse {
        $body = array_filter([
            'requestingEntityId' => $requestingEntityId,
            'newCapacity'        => $newCapacity,
            'planId'             => $planId,
            'successUrl'         => $successUrl,
            'cancelUrl'          => $cancelUrl,
        ], fn($v) => $v !== null);

        $data = $this->post('/api/public/capacity/proration-checkout', $body, retry: false);
        return ProrationCheckoutResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Payment Providers
    // -------------------------------------------------------------------------

    public function getSupportedProviders(): GetSupportedProvidersResponse
    {
        $data = $this->get('/api/public/supported-providers', [], retry: true);
        return GetSupportedProvidersResponse::fromArray($data);
    }

    // -------------------------------------------------------------------------
    // Internal HTTP helpers
    // -------------------------------------------------------------------------

    private function get(string $path, array $query = [], bool $retry = true): array
    {
        return $this->request('GET', $path, query: $query, retry: $retry);
    }

    private function post(string $path, array $body = [], bool $retry = true): array
    {
        return $this->request('POST', $path, body: $body, retry: $retry);
    }

    private function request(
        string $method,
        string $path,
        array $query = [],
        array $body = [],
        bool $retry = true
    ): array {
        $options = [];

        if ($query) {
            $options[RequestOptions::QUERY] = $query;
        }

        if ($body) {
            $options[RequestOptions::JSON] = $body;
        }

        $maxAttempts = $retry ? max(1, $this->config->maxRetries) : 1;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $this->log("→ {$method} {$path}", $body ?: $query);

                $response   = $this->http->request($method, $path, $options);
                $statusCode = $response->getStatusCode();
                $raw        = json_decode((string) $response->getBody(), true) ?? [];

                $this->log("← {$statusCode} {$path}");

                // Unwrap ApiResponse envelope
                if (isset($raw['success'])) {
                    if ($raw['success'] === false) {
                        $errMsg  = $raw['error']['message'] ?? 'Unknown API error';
                        $errCode = $raw['error']['code'] ?? null;
                        throw new CrovverError($errMsg, $statusCode, $errCode);
                    }
                    return $raw['data'] ?? $raw;
                }

                if ($statusCode >= 400) {
                    $errMsg = $raw['message'] ?? $raw['error'] ?? "HTTP {$statusCode}";
                    throw new CrovverError((string) $errMsg, $statusCode);
                }

                return $raw;

            } catch (CrovverError $e) {
                if (!$retry || !$e->isRetryable() || $attempt >= $maxAttempts) {
                    throw $e;
                }
                $this->sleep($attempt);

            } catch (ConnectException $e) {
                if (!$retry || $attempt >= $maxAttempts) {
                    throw new CrovverError('Network error: ' . $e->getMessage(), null, null, $e);
                }
                $this->sleep($attempt);

            } catch (RequestException $e) {
                $status = $e->getResponse()?->getStatusCode();
                if (!$retry || !CrovverError::isRetryableStatus($status) || $attempt >= $maxAttempts) {
                    throw new CrovverError($e->getMessage(), $status, null, $e);
                }
                $this->sleep($attempt);
            }
        }

        throw new CrovverError('Max retries exceeded');
    }

    private function sleep(int $attempt): void
    {
        // Exponential backoff with ±25% jitter
        $base   = self::BASE_DELAY_MS * (2 ** ($attempt - 1));
        $jitter = $base * 0.25 * (mt_rand() / mt_getrandmax() * 2 - 1);
        $delay  = (int) min($base + $jitter, self::MAX_DELAY_MS);

        $this->log("Retrying in {$delay}ms (attempt {$attempt})");
        usleep($delay * 1000);
    }

    private function log(string $message, array $context = []): void
    {
        if (!$this->config->debug) {
            return;
        }

        if ($this->config->logger !== null) {
            ($this->config->logger)($message, $context);
            return;
        }

        $extra = $context ? ' ' . json_encode($context) : '';
        echo "[Crovver SDK] {$message}{$extra}" . PHP_EOL;
    }
}
