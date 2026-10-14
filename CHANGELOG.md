# Changelog

## [Unreleased]

Release only after the Crovver server release that ships per-product customer types — an older server rejects a D2C
`createTenant`.

### Changed
- `CreateTenantRequest::$externalTenantId` is nullable: pass `null` to create an individual (D2C) customer keyed on
  `externalUserId`. It stays the first argument, so existing positional and named calls are unchanged.
- Plans' `product` array now carries `type` (`b2b` / `d2c`); the plans response's `organization['type']` is deprecated, and
  the tenant / subscriptions responses no longer send it.

## [1.0.0] - 2026-03-27
### Added
- Initial release with Tenant, Plan, Subscription, Checkout, Entitlement, and Proration support.
