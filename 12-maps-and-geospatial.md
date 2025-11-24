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
