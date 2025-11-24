# API Overview

## Compatibility Guardrails
- **Do not change** existing route names/paths, required inputs, response JSON keys/shapes/status codes, or database table names/types. Only additive changes (new routes/classes/settings) are allowed alongside the current surface to keep mobile/web clients aligned.

## Versioning and Entry Points
- **V1:** Main surface under `routes/api/v1/api.php` with namespace `Api\V1` and top-level middleware `localization` + `react` to standardize locale/react-client context.【F:routes/api/v1/api.php†L62-L71】
- **V2:** Reserved incremental surface at `routes/api/v2/api.php` currently exposing `POST /api/v2/ls-lib-update` for library updates.【F:routes/api/v2/api.php†L1-L7】

## Authentication Model
- **Guard:** Passport (`auth:api`) configured for users in `config/auth.php` under the `api` guard; vendor/delivery apps layer `vendor.api`/`dm.api` plus `actch:*` channel checks for client-type enforcement.【F:config/auth.php†L59-L114】【F:routes/api/v1/api.php†L89-L160】
- **Flows:** Customer (`auth/*`), delivery (`auth/delivery-man/*` with `actch:deliveryman_app`), and vendor (`auth/vendor/*` with `actch:restaurant_app`) login/reset/OTP/Firebase flows are provided by dedicated controllers under `App\Http\Controllers\Api\V1\Auth`.【F:routes/api/v1/api.php†L73-L105】

## Middleware and Cross-Cutting Concerns
- **Localization/react:** Applied globally to V1 ensuring locale resolution before controllers run.【F:routes/api/v1/api.php†L62-L71】
- **Auth layers:** Protected feature blocks use `auth:api` plus audience guards (`dm.api`, `vendor.api`) and channel gating (`actch:*`).【F:routes/api/v1/api.php†L114-L260】

## Domain Coverage (V1)
- **Public discovery:** Zones, ads, addon categories, campaigns, banners, cuisines, categories, restaurants, and products for browse/search flows.【F:routes/api/v1/api.php†L64-L314】
- **Customer:** Authenticated profile, notifications, loyalty, wallet, wishlist, addresses, conversations, subscriptions, cart/order placement & tracking, coupons/cashback with `apiGuestCheck` where applicable.【F:routes/api/v1/api.php†L293-L375】
- **Vendor:** Profile, orders, campaigns, withdraw/bank/Firebase tokens, announcements, POS, menu/attribute/addon management, coupons/ads, reports, subscription transactions, disbursement/withdraw methods, dine-in OTP/table helpers.【F:routes/api/v1/api.php†L174-L342】
- **Delivery:** Profile, notifications, shifts, live location capture, orders, payments/withdraw methods, OTP, chat, earning/wallet adjustments under `delivery-man` prefix.【F:routes/api/v1/api.php†L114-L171】
- **Config/utility:** Configuration payload, zone lookup, map/geocode/distance helpers, analytics scripts, vehicles, payment methods, offline payments, extra charges, tips lists.【F:routes/api/v1/api.php†L232-L314】【F:routes/api/v1/api.php†L492-L508】
