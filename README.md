# crovver-php

Official PHP SDK for [Crovver](https://crovver.com) — subscription management, feature entitlements, and billing for SaaS applications.

## Requirements

- PHP 8.0+
- Composer

## Installation

```bash
composer require crovver/crovver-php
```

## Quick Start

```php
use Crovver\CrovverClient;
use Crovver\CrovverConfig;

$client = new CrovverClient(new CrovverConfig(
    apiKey: 'your-api-key'
));
```

## Configuration

| Option       | Type             | Default                   | Description                                          |
| ------------ | ---------------- | ------------------------- | ---------------------------------------------------- |
| `apiKey`     | `string`         | **required**              | Your Crovver API key                                 |
| `baseUrl`    | `string`         | `https://app.crovver.com` | Crovver base URL                                     |
| `timeout`    | `int`            | `30`                      | HTTP request timeout in seconds                      |
| `maxRetries` | `int`            | `3`                       | Max retry attempts for retryable errors              |
| `debug`      | `bool`           | `false`                   | Log all requests and responses                       |
| `logger`     | `callable\|null` | `null`                    | Custom logger — receives `(string $msg, array $ctx)` |

```php
$client = new CrovverClient(new CrovverConfig(
    apiKey: 'your-api-key',
    timeout: 15,
    maxRetries: 3,
    debug: true,
    logger: fn(string $msg, array $ctx = []) => error_log("[Crovver] $msg"),
));
```

---

## API Reference

### Tenant Management

Tenants represent your customers (organisations in B2B, users in D2C).

```php
use Crovver\Types\CreateTenantRequest;

// Create a tenant
$response = $client->createTenant(new CreateTenantRequest(
    externalTenantId: 'org_123',
    name: 'Acme Corp',
    externalUserId: 'user_456',
    ownerEmail: 'admin@acme.com',   // optional
    ownerName: 'Jane Doe',          // optional
    slug: 'acme-corp',              // optional
    metadata: ['plan' => 'trial'],  // optional
));

echo $response->tenant->id;
echo $response->tenant->externalTenantId;

// Get a tenant by external ID
$response = $client->getTenant('org_123');
echo $response->tenant->name;
echo $response->tenant->isActive ? 'Active' : 'Inactive';

// Access members and organisation context
foreach ($response->members as $member) {
    echo $member->externalUserId . ' — ' . $member->role;
    echo $member->email;
}
echo $response->organization['type']; // "b2b" or "d2c"
```

**`CreateTenantRequest` parameters:**

| Parameter             | Type           | Required | Description                     |
| --------------------- | -------------- | -------- | ------------------------------- |
| `externalTenantId`    | `string`       | Yes      | Your system's tenant identifier |
| `name`                | `string`       | Yes      | Display name                    |
| `externalUserId`      | `string`       | Yes      | Owner's user ID in your system  |
| `ownerEmail`          | `string\|null` | No       | Owner email                     |
| `ownerName`           | `string\|null` | No       | Owner display name              |
| `slug`                | `string\|null` | No       | URL-safe identifier             |
| `metadata`            | `array\|null`  | No       | Arbitrary key-value pairs       |

---

### Plans

```php
$response = $client->getPlans();

foreach ($response->plans as $plan) {
    echo $plan->name;
    echo $plan->description;
    echo $plan->pricing->currency . ' ' . $plan->pricing->amount;
    echo $plan->pricing->interval;       // "monthly" | "yearly"
    echo $plan->pricing->isSeatBased ? 'Seat-based' : 'Flat';
    echo $plan->isActive ? 'Active' : 'Inactive';

    // Feature flags
    foreach ($plan->features as $key => $value) {
        echo "$key: " . ($value ? 'yes' : 'no');
    }

    // Usage limits
    foreach ($plan->limits as $metric => $limit) {
        echo "$metric: $limit";
    }
}
```

---

### Subscriptions

```php
// B2B: pass externalTenantId  |  D2C: pass externalUserId
$response = $client->getSubscriptions('org_123');

foreach ($response->subscriptions as $sub) {
    echo $sub->id;
    echo $sub->status;                           // "active" | "trialing" | "canceled" | etc.
    echo $sub->billing['current_period_end'];
    echo $sub->trial['is_active'] ? 'On trial' : 'Paid';

    // Seat-based capacity
    if ($sub->capacity) {
        echo 'Seats used: ' . $sub->capacity['used'] . ' / ' . $sub->capacity['total'];
    }
}
```

---

### Checkout

> Checkout calls are **never retried** automatically to prevent duplicate payments.

```php
use Crovver\Types\CreateCheckoutSessionRequest;

// B2B checkout (tenant + user)
$response = $client->createCheckoutSession(new CreateCheckoutSessionRequest(
    externalUserId: 'user_456',
    planId: 'plan_789',
    provider: 'stripe',
    externalTenantId: 'org_123',
    successUrl: 'https://app.example.com/success',
    cancelUrl: 'https://app.example.com/cancel',
));

header('Location: ' . $response->checkoutUrl);
exit;

// D2C checkout (user only)
$response = $client->createCheckoutSession(new CreateCheckoutSessionRequest(
    externalUserId: 'user_456',
    planId: 'plan_789',
    provider: 'stripe',
    userEmail: 'user@example.com',
    userName: 'John Doe',
    successUrl: 'https://app.example.com/success',
    cancelUrl: 'https://app.example.com/cancel',
));
```

**`CreateCheckoutSessionRequest` parameters:**

| Parameter            | Type           | Required | Description                             |
| -------------------- | -------------- | -------- | --------------------------------------- |
| `externalUserId`     | `string`       | Yes      | User initiating the checkout            |
| `planId`             | `string`       | Yes      | Target plan ID                          |
| `provider`           | `string`       | Yes      | Payment provider code (e.g. `"stripe"`) |
| `externalTenantId`   | `string\|null` | No       | Tenant ID for B2B flows                 |
| `userEmail`          | `string\|null` | No       | Pre-fill checkout email                 |
| `userName`           | `string\|null` | No       | Pre-fill customer name                  |
| `successUrl`         | `string\|null` | No       | Redirect after successful payment       |
| `cancelUrl`          | `string\|null` | No       | Redirect after cancelled payment        |
| `quantity`           | `int\|null`    | No       | Seat quantity for seat-based plans      |
| `metadata`           | `array\|null`  | No       | Arbitrary metadata                      |

---

### Entitlements

#### Feature access

```php
if ($client->canAccess('org_123', 'export_csv')) {
    // feature is available for this tenant
}
```

#### Metered usage

```php
// Record usage (defaults to incrementing by 1)
$client->recordUsage('org_123', 'api_calls');
$client->recordUsage('org_123', 'api_calls', 10);
$client->recordUsage('org_123', 'storage_gb', 5, ['source' => 'file_upload']);

// Check against plan limits
$usage = $client->checkUsageLimit('org_123', 'api_calls');

echo $usage->current;     // current usage count
echo $usage->limit;       // plan limit (null = unlimited)
echo $usage->remaining;   // remaining before limit
echo $usage->percentage;  // 0–100 (null if unlimited)
echo $usage->allowed;     // false if limit exceeded

if (!$usage->allowed) {
    // block the action or prompt upgrade
}
```

---

### Seat-based Proration

For seat-based plans, use proration checkout to upgrade capacity mid-cycle with prorated billing.

```php
// Preview and initiate a capacity upgrade
$response = $client->createProrationCheckout(
    externalTenantId: 'org_123',
    newCapacity: 20,              // total seats after upgrade
    planId: 'plan_789',           // optional if only one active plan
    successUrl: 'https://app.example.com/success',
    cancelUrl: 'https://app.example.com/cancel',
);

echo $response->prorationAmount;   // charge for remainder of billing period
echo $response->message;

if ($response->requiresPayment) {
    header('Location: ' . $response->checkoutUrl);
    exit;
}
// If no payment required, capacity is upgraded immediately
```

---

### Payment Providers

```php
$response = $client->getSupportedProviders();

foreach ($response->providers as $provider) {
    echo $provider->name . ' (' . $provider->code . ')';
    echo $provider->isEnabled ? 'Enabled' : 'Disabled';
    echo $provider->supportsTestMode ? ' — test mode available' : '';
    echo $provider->supportsRecurringBilling ? ' — recurring billing' : '';
}
```

---

## Error Handling

```php
use Crovver\CrovverError;

try {
    $response = $client->getSubscriptions('org_123');
} catch (CrovverError $e) {
    echo $e->getMessage();      // human-readable error message
    echo $e->getStatusCode();   // HTTP status code (null for network errors)
    echo $e->getErrorCode();    // API error code string (e.g. "TENANT_NOT_FOUND")
    echo $e->isRetryable();     // bool — whether the SDK already retried

    print_r($e->toArray());     // structured array of all fields
} catch (\Throwable $e) {
    // Unexpected errors
}
```

### Retry behaviour

The SDK retries automatically with **exponential backoff + ±25% jitter** (base 1 s, cap 30 s).

| Condition                              | Retried |
| -------------------------------------- | ------- |
| 5xx server errors                      | Yes     |
| 429 Too Many Requests                  | Yes     |
| 408 Request Timeout                    | Yes     |
| Network / connection errors            | Yes     |
| 4xx client errors (400, 401, 403, 404) | No      |
| Checkout / payment endpoints           | Never   |

---

The server exposes these endpoints:

| Method | Path                               | Description                      |
| ------ | ---------------------------------- | -------------------------------- |
| `GET`  | `/plans`                           | List all plans                   |
| `GET`  | `/tenant?externalTenantId=`        | Get a tenant                     |
| `POST` | `/tenant`                          | Create / provision a tenant      |
| `GET`  | `/subscriptions?externalTenantId=` | List subscriptions               |
| `POST` | `/can-access`                      | Check feature access             |
| `POST` | `/checkout`                        | Create a checkout session        |
| `GET`  | `/providers`                       | List supported payment providers |

---

## License

MIT
