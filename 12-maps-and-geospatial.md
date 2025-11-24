# Maps and Geospatial Usage

## Scope & Guardrails
- Preserve current zone/map endpoint names and response shapes; add new providers/endpoints alongside existing ones without renaming or removing parameters.

## Map Configuration
- **Map API key:** `ConfigController` constructor caches `map_api_key_server` from `business_settings` for use by map endpoints.【F:app/Http/Controllers/Api/V1/ConfigController.php†L33-L40】
- **Default location:** `/config` response returns default `lat`/`lng` (fallback `23.757989/90.360587`) plus map-related toggles to anchor client maps.【F:app/Http/Controllers/Api/V1/ConfigController.php†L169-L180】

## API Endpoints (V1)
- `/config` – aggregated configuration including map defaults and payment gateways.【F:routes/api/v1/api.php†L492-L508】【F:app/Http/Controllers/Api/V1/ConfigController.php†L33-L200】
- `/config/get-zone-id` & `/zone/check` – zone lookup for coordinates/addresses to gate coverage.【F:routes/api/v1/api.php†L62-L66】【F:routes/api/v1/api.php†L492-L508】
- `/config/place-api-autocomplete`, `/config/place-api-details`, `/config/geocode-api`, `/config/distance-api` – geocoding/distance helpers under ConfigController (keep payloads stable when extending).【F:routes/api/v1/api.php†L492-L508】

## Zone Geometry and Coverage
- `ZoneController@get_zones` and `zonesCheck` deliver available zones; downstream controllers rely on this for validating restaurant/delivery coverage before orders are accepted.【F:routes/api/v1/api.php†L62-L66】

## Rendering Patterns
- Clients consume the above endpoints to render zone polygons, autocomplete addresses, and distance calculations; any new provider (e.g., alternative geocoder) should be added behind new routes/config keys without changing existing request/response contracts.
## API Surface
- Config endpoints expose Google-like map helpers: `/config/place-api-autocomplete`, `/config/place-api-details`, `/config/geocode-api`, `/config/distance-api`, and `/config/get-zone-id` for polygon lookup; all are public under `routes/api/v1/api.php` with localization/react middleware.【F:routes/api/v1/api.php†L232-L243】
- Zone browsing: `/zone/list` and `/zone/check` feed client zoning and coverage maps.【F:routes/api/v1/api.php†L64-L66】
- Delivery tracking: delivery-man endpoints expose `/delivery-man/last-location`, `/delivery-man/record-location-data`, and `/delivery-man/order-details` to drive live maps in apps.【F:routes/api/v1/api.php†L114-L171】

## Calculation Utilities
- `App\CentralLogics\Helpers::get_distance` implements Haversine distance (km/nautical miles) used by delivery assignment and charge estimation.【F:app/CentralLogics/Helpers.php†L3995-L4018】
- `Helpers::deliverymen_list_formatting` augments driver payloads with computed distances when restaurant coordinates are available (distance formatting in KM).【F:app/CentralLogics/Helpers.php†L1002-L1065】

## Map Data Sources & Keys
- Map endpoints delegate to `ConfigController` (under `app/Http/Controllers/Api/V1/ConfigController.php`), which reads business settings for map API keys (server/client) and surfaces them to clients alongside extra charges per vehicle.【F:routes/api/v1/api.php†L232-L270】

## Geofencing/Fencing
- Zone and distance checks rely on backend zone definitions; no explicit fence polygon helpers are declared in routes, but `/config/get-zone-id` and `/zone/check` enable client-side geofencing decisions based on backend validation.【F:routes/api/v1/api.php†L64-L66】【F:routes/api/v1/api.php†L232-L243】

## Recommendations
- Centralize map API provider selection (currently implied Google Distance/Geocode) inside ConfigController responses to allow future provider swaps without client changes.
- Add caching on distance/geocode endpoints to reduce external API churn during heavy map usage.
