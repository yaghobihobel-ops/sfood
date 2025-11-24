# Developer Guide and Next Steps

## Setup Steps
1. **Clone & Install**
   - `git clone <repo>` and `cd sfood`.
   - Install PHP dependencies: `composer install` (ensuring PHP 8.2+ and required extensions: curl, json, openssl, simplexml).
   - Install Node tooling: `npm install`.
2. **Environment**
   - Copy `.env.example` to `.env` and set database, cache, queue, mail, storage, and payment gateway credentials.
   - Generate app key: `php artisan key:generate`.
   - Configure Passport keys (`php artisan passport:install`) if not bundled.
3. **Database**
   - Run migrations: `php artisan migrate`.
   - Seed essential data if seeders are available (`php artisan db:seed`).
4. **Build Assets**
   - Development: `npm run dev` or `npm run watch`.
   - Production: `npm run prod`.
5. **Serve**
   - `php artisan serve` (or configure web server pointing to `public/`).
   - Ensure `storage` and `bootstrap/cache` are writable.

## Operational Notes
- **Modules**: Enable/disable via `modules_statuses.json`; ensure module providers are loaded. TaxModule requires migration and config.
- **Queues**: Configure queue driver in `.env` (`QUEUE_CONNECTION`) and run workers if notifications/emails are queued.
- **Caching/Config**: Use `php artisan config:cache` and `php artisan route:cache` in production; clear caches after locale or module changes.
- **Storage**: If using S3 or remote storage, set `FILESYSTEM_DISK` and credentials; image processing depends on Intervention Image.
- **Payments**: Populate gateway credentials; test callbacks via appropriate tunnels in development.

## Next Steps for Localization
- **Short Term**: Extract hardcoded strings to language files; align validation messages; verify middleware correctly persists locale across sessions/API tokens.
- **Medium Term**: Add RTL assets and locale-aware formatting helpers; create translation management UI backed by `translations` table.
- **Long Term**: Expand automated tests to cover locale switching, gateway responses, and subscription edge cases; consider feature toggles per locale/region.

## Risks & Hotspots
- **RTL Conversion**: Requires thorough CSS review; start with layout wrappers to avoid regressions.
- **Gateway Diversity**: Numerous payment integrations increase configuration complexity; isolate credentials per environment.
- **Spatial Logic**: Zone/geo calculations rely on spatial extensions; verify DB support in target environment.
- **Subscription & Tax**: Coupled flows across orders, schedules, and TaxModule—changes require coordinated updates and regression tests.
- **Installation Routes**: Ensure `InstallationMiddleware`/route toggles are secured in production to prevent re-installation exposure.

