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

