# Changelog

## [2.0.0] - Unreleased

The SDK for the revamped Crovver server. Release only after that server release: an older server rejects a D2C
`createTenant` and sends no `seatTiers`. Packagist takes the version from the `v2.0.0` tag.

### Removed (breaking)
- `recordUsage()` and `RecordUsageResponse`. The server's usage-events route is deprecated and nothing reads what it
  writes; consume credit pools with `consumeCredits()`.
- `checkUsageLimit()` and `CheckUsageLimitResponse`; read pool balances with `getCreditsBalance()`.

### Changed
- `CreateTenantRequest::$externalTenantId` is nullable: pass `null` to create an individual (D2C) customer keyed on
  `externalUserId`. It stays the first argument, so existing positional and named calls are unchanged.
- Plans' `product` array now carries `type` (`b2b` / `d2c`); the plans response's `organization['type']` is deprecated, and
  the tenant / subscriptions responses no longer send it.

### Added

- Tiered seat plans: `Plan::$seatTiers` and, on subscription responses, `PlanPricing::$seatTiers` (`PlanSeatTier`: `currency`, `minQuantity`, `maxQuantity`, `price`). A band's price is the whole period's price for any seat count in its range; a tiered plan's `basePrice` and `perSeatPrice` are 0.
- `Plan::periodCharge($seats, $currency)` and `PlanSeatTier::for($tiers, $currency, $seats)`: price a plan at a seat count by the rules Crovver charges, tiered or not.

### Fixed

- `ProrationCheckoutResponse::$prorationId` is nullable, and the response carries `invoiceId`. A seat increase that stays inside a tiered plan's band answers `prorationId: null`, which made `fromArray()` throw a `TypeError`.

## [1.0.0] - 2026-03-27
### Added
- Initial release with Tenant, Plan, Subscription, Checkout, Entitlement, and Proration support.
