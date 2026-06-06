# crovver-php — Codebase Guide

## This Project

The official PHP SDK for Crovver. Published to Packagist as `crovver/crovver-php`. PHP backends install this to call the Crovver API — creating tenants, checking feature access, recording usage, initiating checkout, and managing subscriptions. Mirrors the Node SDK in capability.

**Language:** PHP 8.2+ · **Package manager:** Composer · **HTTP client:** Guzzle · **Tests:** Pest + PHPStan level 8

### Install & Test
```bash
composer install
composer test      # Pest
composer analyse   # PHPStan level 8
composer check     # analyse + test
```

### Structure
```
src/
  CrovverClient.php       ← Main client class (all API methods)
  CrovverConfig.php       ← Configuration: apiKey, baseUrl, timeout
  CrovverError.php        ← Exception class for API errors
  Types/
    CreateTenantRequest.php
    CreateTenantResponse.php
    CreateCheckoutSessionRequest.php
    CreateCheckoutSessionResponse.php
    CancelSubscriptionResponse.php
    CheckUsageLimitResponse.php
    GetInvoicesResponse.php
    ... (one class per request/response shape)
tests/
  Unit/                   ← Unit tests
  Feature/                ← Feature/integration tests
```

### Usage Example
```php
use Crovver\CrovverClient;
use Crovver\CrovverConfig;

$config = new CrovverConfig(apiKey: 'sk_live_...');
$crovver = new CrovverClient($config);

$result = $crovver->canAccess('tenant-123', 'advanced-analytics');
```

### Key Design Decisions
- **Secret key only** (`sk_live_` / `sk_test_`) — server-side use only, never expose to browser
- **Mirrors the Node SDK** — same methods, same API surface, same response shapes
- **Guzzle** for HTTP — standard in the PHP ecosystem, supports retries via middleware
- **PSR-4 autoloading** under the `Crovver\` namespace

---

## Crovver Ecosystem

Crovver is a **subscription management layer** for SaaS products. It sits between a SaaS app and payment providers (Stripe, Khalti, eSewa), handling subscription state, feature entitlements, seat tracking, usage limits, and hosted checkout — so SaaS teams don't build billing themselves. Payment credentials are never stored in the database; they go through Infisical or Vault.

### Sub-Projects
| Folder | What it is | Port / Registry |
|--------|-----------|-----------------|
| `crovver-mvp` | API server + admin dashboard (Next.js 16) | 3000 |
| `crovver-portal` | Customer-facing billing portal (Next.js 15) | 3002 |
| `crovver-node` | Official Node.js/TypeScript SDK | npm: `crovver-node` |
| `crovver-react` | Official React SDK | npm: `crovver-react` |
| `crovver-php` | Official PHP 8.2+ SDK — **this project** | Packagist: `crovver/crovver-php` |
| `docs` | Mintlify documentation site | — |

### Core Data Model
| Entity | Description |
|--------|-------------|
| **Org** | A SaaS company using Crovver. Type `b2b` = workspace-based customers; `d2c` = individual users |
| **Tenant** | The billing unit — a workspace (B2B) or user (D2C). Identified via `external_tenant_id` |
| **Plan** | Pricing tier with `features` (boolean flags) and `limits` (numeric caps). Flat or seat-based |
| **Subscription** | Tenant ↔ Plan binding. Statuses: pending → trialing → active → past_due → canceled |
| **Entitlement** | `canAccess(tenantId, featureKey)` — checks plan features; trial counts as active |

### API Key Types
- `pk_live_` / `pk_test_` — public keys, safe for browser (React SDK)
- `sk_live_` / `sk_test_` — secret keys, backend only — **this SDK uses these**

### Checkout Flow
1. SaaS frontend calls `redirectToCheckout()` on the React SDK
2. React SDK calls `POST /api/public/auth/checkout-token` on crovver-mvp → gets a short-lived JWT
3. Browser redirects to crovver-portal
4. Portal calls `POST /api/public/checkout` → Stripe session created
5. Stripe webhook fires → crovver-mvp activates subscription

### Three API Surfaces on crovver-mvp
| Surface | Base path | Auth method |
|---------|-----------|-------------|
| Public SDK | `/api/public/*` | Bearer `sk_live_` key — **this SDK calls here** |
| Admin dashboard | `/api/admin/*` | Session cookie |
| Webhooks | `/api/webhooks/*` | HMAC signature |
