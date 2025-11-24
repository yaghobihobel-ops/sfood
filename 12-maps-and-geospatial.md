# Maps and Geospatial Usage

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
