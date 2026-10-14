# Changelog

## [Unreleased]

Release only after the Crovver server release that ships per-product customer types — an older server rejects a D2C
`createTenant`.

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
