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
