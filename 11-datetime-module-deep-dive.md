# Date/Time Module Deep Dive

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
