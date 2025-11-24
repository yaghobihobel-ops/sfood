# Developer Guide and Next Steps

## Getting Started
1. **Clone & Install PHP deps:** `composer install` (PHP 8.2+). Composer autoload loads helper files in `app/CentralLogics/*` and `app/helpers.php`.
2. **Install Node deps:** `npm install` (Laravel Mix 6) then `npm run dev` or `npm run production` for assets.
3. **Environment:** Copy `.env.example` to `.env`; set `APP_KEY` (`php artisan key:generate`), database credentials, mail/SMS/Firebase/OpenAI keys, and payment gateway credentials.
4. **Database:** Run `php artisan migrate` to apply core and module migrations (AI/TaxModule load via service providers). Seeders exist under `database/seeders` if provided.
5. **Passport:** Run `php artisan passport:install` to generate OAuth keys for API auth.
6. **Storage/Cache:** Run `php artisan storage:link` for public storage; configure cache/queue drivers (defaults: file/sync).
7. **Serve:** `php artisan serve` or configure web server pointing to `public/`. Ensure websocket/queue workers if using real-time features.

## Project Conventions
- **Modules:** Create new features via `php artisan module:make` (nwidart). Keep module routes/config/views under `Modules/<Name>/`.
- **Logic Placement:** Use `CentralLogics` helpers for shared domain logic; keep controllers thin and delegate heavy lifting.
- **Middleware:** Apply localization, auth guards, and module/subscription checks to new routes to match existing patterns.
- **Testing:** Use PHPUnit (`phpunit.xml`) and Pest compatible; factories live in `database/factories`.

## Localization-Specific Next Steps
- Follow the action plan in `07-localization-and-internationalization.md` to remove hardcoded strings, add RTL assets, and implement locale-aware formatting.
- Prioritize customer-facing flows (auth, checkout, order tracking) then admin/vendor dashboards.
- Ensure modules (AI, TaxModule, Gateways) ship their own translation files and respect locale middleware.

## Risks & Sensitive Areas
- **Payments:** Multiple gateways; always configure sandbox keys and test callbacks (Stripe, Razorpay, MercadoPago, Paystack, PhonePe, Xendit, PayPal, Paytm, SSLCommerz, Flutterwave).
- **Orders & Subscriptions:** Custom scopes and scheduling logic; changes can affect delivery windows and reporting.
- **Geo/Zone Filtering:** ZoneScope affects many queries; ensure new queries respect zone context to avoid data leaks.
- **Real-time/Notifications:** Websocket config and Firebase notifications need correct credentials; fallback to polling where unavailable.
- **RTL/CSS Overhaul:** High risk per localization audit; isolate RTL changes to dedicated stylesheets to avoid regressions.
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

