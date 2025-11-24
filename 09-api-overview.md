# API Overview

## Versioning and Entry Points
- **Versioned routes:** `routes/api/v1/api.php` hosts the primary surface under the `Api\V1` namespace, while `routes/api/v2/api.php` is reserved for incremental updates (currently a single `ls-lib-update` endpoint).【F:routes/api/v1/api.php†L1-L270】【F:routes/api/v2/api.php†L1-L7】
- **Prefix and middleware:** The V1 group applies `localization` and `react` middleware globally, then nests feature-specific middleware such as `auth:api`, `apiGuestCheck`, `dm.api`, `vendor.api`, and custom `actch:*` guards for per-client enforcement.【F:routes/api/v1/api.php†L62-L271】

## Authentication Model
- **Guard:** API requests rely on the Passport driver (`auth:api`) configured in `config/auth.php`, mapping to the `users` provider; vendor/delivery flows add role-specific middleware layers.【F:config/auth.php†L59-L114】【F:routes/api/v1/api.php†L89-L170】
- **Flows:**
  - Customer auth: register/login, OTP verification, password reset, Firebase token verification, and guest checkout flows under `auth/*` endpoints.【F:routes/api/v1/api.php†L73-L105】
  - Delivery-man/vendor auth: login/register/reset with client-specific middleware (`actch:deliveryman_app`, `actch:restaurant_app`).【F:routes/api/v1/api.php†L89-L105】

## Common Middleware and Cross-Cutting Concerns
- **Localization:** Applied at the top-level API group, driving locale-sensitive responses and translation helper usage across controllers.【F:routes/api/v1/api.php†L62-L271】
- **Rate/Channel checks:** Custom `actch:*` middleware gates endpoints by consuming client (delivery app, restaurant app, React web) and is stacked with `dm.api`/`vendor.api` for authenticated sub-flows.【F:routes/api/v1/api.php†L89-L270】
- **Passport tokens:** Controllers generate Passport access tokens after validation (e.g., `CustomerAuthController@verify_phone_or_email`).【F:app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php†L31-L100】

## High-Level Domain Coverage
- Customer-facing: catalog discovery (zones, restaurants, cuisines, categories, products), ordering/cart/checkout, coupons/cashback, loyalty, wallet, subscriptions, conversations/notifications, newsletters.【F:routes/api/v1/api.php†L64-L270】
- Vendor-facing: order lifecycle, menu/product management, campaigns, reporting, withdrawals, POS, deliveryman assignment, announcements, advertisements, business setup, subscription management.【F:routes/api/v1/api.php†L174-L270】
- Delivery-facing: profile, shifts, order handling, payments/withdrawals, chat, ratings, live location endpoints.【F:routes/api/v1/api.php†L114-L171】

## External-Facing Configuration Endpoints
- `config/*` endpoints expose zone lookup, Google/Map APIs (place/geocode/distance), analytic scripts, vehicles, and payment method lists to clients for dynamic UI wiring.【F:routes/api/v1/api.php†L232-L270】
