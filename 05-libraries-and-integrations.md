# Libraries and Integrations

## PHP Composer Dependencies
- **Framework**: `laravel/framework` ^12, `laravel/passport` (API auth), `laravel/socialite` (OAuth), `laravel/tinker` (REPL).
- **Modularity**: `nwidart/laravel-modules` for add-on packaging.
- **Payments**: Stripe, Razorpay, Paystack, PayPal (via controllers), PhonePe SDK, MercadoPago, Paytabs, Paytm, Flutterwave, SenangPay; offline payment support via models/config.
- **Messaging & Notifications**: `twilio/sdk` for SMS/voice; email via Laravel mailers and configurable SMTP.
- **Files & Media**: `intervention/image` for image manipulation, `league/flysystem-aws-s3-v3` for S3 storage, `simplesoftwareio/simple-qrcode` for QR.
- **Data Export/Docs**: `barryvdh/laravel-dompdf`, `mpdf/mpdf`, `maatwebsite/excel`, `rap2hpoutre/fast-excel` for PDFs/Excel exports.
- **Utilities**: `guzzlehttp/guzzle`, `doctrine/dbal` (schema ops), `gregwar/captcha`, `madnest/madzipper` (zip), spatial queries via `matanyadaev/laravel-eloquent-spatial`, Firebase via `kreait/firebase-php`, OpenAI via `openai-php/laravel`.
- **Dev Tools**: Debugbar, Sail, Pint, Pail, Collision, PHPUnit.

## Node/NPM Tooling
- **Build System**: Laravel Mix v6 with scripts for dev/prod/watch/hot reload.
- **Front-end Libraries**: Axios for HTTP, Lodash utilities, PostCSS processing.

## External Services
- **Payments**: Multiple gateway controllers under `app/Http/Controllers` (e.g., Paystack, Stripe, RazorPay, Paytm, MercadoPago, Paymob, LiqPay). Modules enable extending gateways.
- **SMS/Push**: Twilio SDK; Firebase integration via `FirebaseController` and config for push notifications.
- **Maps/Geo**: Zone and spatial calculations depend on spatial Eloquent extensions; delivery tracking models capture coordinates.
- **AI**: OpenAI integration available via `openai-php/laravel`; AI module scaffold ready.

## Configuration Hotspots
- `.env` and `config/*` hold credentials for mail, queue, cache, broadcasting, payment gateways, Firebase, and storage drivers.
- Localization-related env keys: `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE` (see `config/app.php`).
- `modules_statuses.json` toggles modules; module config files (e.g., `Modules/TaxModule/Config/config.php`) expose feature settings.

