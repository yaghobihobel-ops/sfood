# Date/Time Module Deep Dive

## Scope & Guardrails
- Keep existing date/time field semantics, validation, and response formats intact; only add helpers/overrides alongside current behavior.

## Core Components
- **ConfigController (`app/Http/Controllers/Api/V1/ConfigController.php`):** loads `timeformat`, `timezone`-sensitive currency/time settings, default location, maintenance windows, and subscription trial durations in the `/config` payload.【F:app/Http/Controllers/Api/V1/ConfigController.php†L33-L200】【F:app/Http/Controllers/Api/V1/ConfigController.php†L169-L199】
- **Carbon usage:** order/campaign/product/category logic uses Carbon for time-aware calculations (e.g., `CategoryLogic`, `ProductLogic`, `CouponLogic`).【F:app/CentralLogics/CategoryLogic.php†L8-L24】【F:app/CentralLogics/ProductLogic.php†L8-L24】【F:app/CentralLogics/CouponLogic.php†L8-L20】
- **Processor trait:** timestamps asset filenames with `Carbon::now()->toDateString()` when storing media.【F:app/Traits/Processor.php†L1-L12】

## Key Behaviors
- **Time format exposure:** `/config` response includes `timeformat` string and scheduling flags so clients render times consistently.【F:app/Http/Controllers/Api/V1/ConfigController.php†L169-L199】
- **Scheduling toggles:** subscription free-trial calculations handle day/month/year inputs to compute trial period length.【F:app/Http/Controllers/Api/V1/ConfigController.php†L157-L163】
- **Geo + time:** default location lat/lng with timezone-agnostic coordinates are bundled with business metadata for downstream map/time logic.【F:app/Http/Controllers/Api/V1/ConfigController.php†L169-L180】

## Integration Points
- **Order/delivery windows:** Delivery/order controllers rely on business settings (`schedule_order`, `order_delivery_verification`, `dm_shift`) for temporal gating; keep these settings stable when extending features.【F:routes/api/v1/api.php†L124-L171】【F:app/Http/Controllers/Api/V1/ConfigController.php†L169-L199】
- **Localization:** Top-level API middleware `localization` ensures date/time strings respect locale; adding new locales should hook into existing translation flow without altering current keys.【F:routes/api/v1/api.php†L62-L71】
## Core Configuration Layer
- `app/Providers/ConfigServiceProvider.php` centralizes runtime time settings:
  - Sets translator week metadata (first day Monday, weekend Sunday) via CarbonImmutable for locale-sensitive calendars.【F:app/Providers/ConfigServiceProvider.php†L35-L38】
  - Loads timezone from `business_settings` (`key=timezone`), applies to `config('timezone')`, and calls `date_default_timezone_set` to align PHP runtime; also sets 12/24-hour display format (`timeformat`).【F:app/Providers/ConfigServiceProvider.php†L203-L215】
  - Uses Carbon parsing in maintenance-mode cache expiry to reset after end date passes.【F:app/Providers/ConfigServiceProvider.php†L254-L294】

## Validation & OTP Timing Controls
- `App\Http\Controllers\Api\V1\Auth\CustomerAuthController` enforces OTP attempts/timeouts using Carbon/CarbonInterval, blocking rapid retries and translating wait times for clients.【F:app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php†L31-L140】

## Distance & Scheduling Utilities
- `App\CentralLogics\Helpers::get_distance` computes Haversine distances in km/nautical miles; leveraged by delivery assignment and shipping charge calculations (e.g., `deliverymen_list_formatting`).【F:app/CentralLogics/Helpers.php†L3995-L4018】
- ConfigServiceProvider also sets `timeformat` and timezone used by schedule rendering and restaurant shift calculations across controllers that rely on `config('timeformat')`.【F:app/Providers/ConfigServiceProvider.php†L203-L215】

## Integration Points
- API endpoints expose map/time-related config to clients via `routes/api/v1/api.php` config group (`place-api-*`, `distance-api`, `geocode-api`, `get-vehicles`, `most-tips`) enabling clients to align map/distance and time presentation with backend settings.【F:routes/api/v1/api.php†L232-L270】
- Delivery-man endpoints provide last-location and shift windows that depend on stored timestamps and timezone settings to ensure consistent SLA handling.【F:routes/api/v1/api.php†L114-L171】

## Hardening/Next Steps
- Audit all date formatting in responses to ensure they respect `timeformat` and timezone; many controllers rely on default `now()`/Carbon parsing—confirm output standardization with Resources/transformers when added.
- Add unit coverage for OTP block windows and maintenance cache expiry to prevent regressions when changing business rules.
