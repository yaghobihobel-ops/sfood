# Libraries and Integrations

## PHP/Laravel Dependencies (composer.json)
- **Framework:** `laravel/framework` 12.
- **Authentication & Security:** `laravel/passport` for API OAuth2, `laravel/socialite` for social login.
- **Async/Real-time:** `laravel/websockets` via config + migrations; `guzzlehttp/guzzle` for HTTP calls.
- **Payments:** `stripe/stripe-php`, `razorpay/razorpay`, `mercadopago/dx-php`, `phonepe/phonepe-pg-php-sdk`, `xendit/xendit-php`, `openai-php/laravel` (AI billing), `iyzico/iyzipay-php`, `devrabiul/laravel-paystack`, `flutterwave` config, `sslcommerz`, `paypal`, `paytm`, S3 storage for assets via `league/flysystem-aws-s3-v3`.
- **Media & Files:** `intervention/image`, `madnest/madzipper`, `mpdf/mpdf`, `barryvdh/laravel-dompdf`, `simplesoftwareio/simple-qrcode`.
- **Data/Exports:** `maatwebsite/excel`, `rap2hpoutre/fast-excel`, `doctrine/dbal` for schema diffs.
- **Notifications/Comm:** `twilio/sdk` for SMS/voice, `kreait/firebase-php` for push notifications.
- **Geo/Spatial:** `matanyadaev/laravel-eloquent-spatial` for geo columns/queries.
- **Module System:** `nwidart/laravel-modules` for modular architecture.

## Node Dependencies (package.json)
- **Build:** Laravel Mix 6, PostCSS.
- **HTTP/Utility:** Axios, Lodash.

## Configurations
- **Payment Configs:** `config/paypal.php`, `config/razor.php`, `config/flutterwave.php`, `config/sslcommerz.php`, `config/paytm.php`, `config/services.php` define credentials/callbacks; `.env.example` includes base app/database/mail keys and OpenAI keys.
- **Modules:** `config/modules.php` configures module namespace, generators, and migration paths; `modules_statuses.json` toggles module activation.
- **Cache/Queue/Session:** Defaults in `.env.example` and `config/cache.php`, `config/queue.php`, `config/session.php`; queue driver set to `sync` by default.
- **Localization:** Middleware `Localization`/`LocalizationMiddleware` applied in routes; language files under `resources/lang/*`.

## External Services Usage
- **AI:** OpenAI via module for generating product text and image analysis.
- **SMS/Phone:** Twilio SDK; phone validation helpers in CentralLogics; likely integration points in controllers/services.
- **Push Notifications:** Firebase config `config/firebase.php` and SDK.
- **File Storage:** S3 support through Flysystem, plus local/public storage for images and proofs.
- **Email:** SMTP configured via `.env.example`; mailable classes under `app/Mail` for system emails.

