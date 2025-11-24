# Project Overview

## Stack and Versions
- **Framework:** Laravel 12 (Laravel Framework constraint `^12.0`).
- **Runtime:** PHP 8.2+ (composer requirement `^8.2|^8.3|^8.4`).
- **Front-end tooling:** Laravel Mix 6 for asset pipeline with Node-based dependencies (Axios, Lodash, PostCSS).
- **Module system:** Nwidart Laravel Modules with status tracking in `modules_statuses.json` enabling `AI`, `TaxModule`, and `Gateways`.

## High-Level Architecture
- **Monolithic Laravel application** extended with **modular packages** under `Modules/` (nwidart) for optional domains (AI-assisted product authoring, tax management, payment gateways).
- **Domain-oriented directories** in `app/` include HTTP (controllers, middleware, requests), domain models, services/utilities under `CentralLogics`, event-driven components (Observers), and shared Traits/Scopes.
- **API-first orientation** with versioned routes (`routes/api/v1/api.php`, `routes/api/v2/api.php`) alongside admin/vendor web routes and installation/update flows.

## Repository Layout
- **Core Laravel:** `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `tests/`.
- **Modular packages:** `Modules/AI`, `Modules/TaxModule`, and status file `modules_statuses.json`.
- **Deployment/install helpers:** `installation/` scripts plus `artisan` and `composer.phar` for CLI tasks.
- **Docs and configuration:** `.env.example`, `php.ini`, `webpack.mix.js`, and payment/config files under `config/`.

## Major Domains / Feature Areas
- **Marketplace & Ordering:** Restaurants, vendors, menus/products, add-ons, orders, order details, scheduling, and subscriptions.
- **Users & Roles:** Admins, vendors, delivery personnel, customers/guests with wallets, loyalty, referrals, and role-based access.
- **Commerce:** Coupons, campaigns, advertisements, taxes (core + `TaxModule`), multiple online/offline payment gateways, refunds, and transactions.
- **Logistics:** Zones, delivery men, vehicles, routes, live tracking (websockets), delivery history, and geo-spatial queries.
- **Engagement:** Notifications, conversations/chats, reviews/ratings, newsletters, testimonials, cashback, and loyalty points.
- **Content & Media:** Banners, cuisines, blogs/FAQs (from views), file management, and image handling with Intervention Image.
- **AI Module:** Admin-only auto-fill for product titles/descriptions/SEO and image analysis powered by OpenAI (via `openai-php/laravel`).

## Localization Snapshot
- Translation files exist for **English (en)**, **Arabic (ar)**, **Bengali (bn)**, and **Spanish (es)** under `resources/lang/` with standard Laravel message groups (`auth`, `validation`, etc.).
- Middleware `Localization` and `LocalizationMiddleware` enforce locale selection for API/web flows.
- Existing **Localization Audit Report** highlights hardcoded strings across blades, controllers, mails, notifications, and modules, plus RTL/CSS risks and date-handling considerations.
## Stack Snapshot
- **Framework**: Laravel 12.x with PHP ^8.2+ requirements and Laravel Mix for asset compilation.
- **Key Packages**: Modular scaffolding via `nwidart/laravel-modules`, Passport for API authentication, Socialite for OAuth, payment SDKs (Stripe, Razorpay, Paystack, PhonePe, MercadoPago, Paytabs, Paytm, Flutterwave, SenangPay), messaging (Twilio), PDF/Excel generation (DOMPDF, mPDF, Maatwebsite Excel, FastExcel), image handling (Intervention Image), QR codes, and Firebase/OpenAI SDKs.
- **Front-end Tooling**: npm scripts backed by Laravel Mix, Axios, Lodash, and PostCSS.

## High-Level Domain
A multi-tenant food ordering and delivery platform with customers, restaurants/vendors, delivery partners, orders, campaigns, subscriptions, and wallets. It includes separate surfaces for admin, vendor/restaurant, delivery personnel, and customer-facing apps (API v1/v2) plus modular add-ons (e.g., TaxModule, AI).

## Repository Layout
- **app/**: Core Laravel application code (controllers, models, services/logic helpers, jobs, events, middleware).
- **Modules/**: Pluggable features using `nwidart/laravel-modules` (e.g., AI, TaxModule) controlled by `modules_statuses.json`.
- **routes/**: Web, admin, vendor, installation/update, and API (v1/v2) route maps.
- **config/**: Application and service configuration (auth, queue, mail, broadcasting, localization, payments, etc.).
- **database/**: Migrations, factories, and seeders defining the relational schema for users, restaurants, orders, subscriptions, etc.
- **resources/**: Blade views, localization files (`lang/ar`, `bn`, `en`, `es`), assets, and front-end entry points.
- **public/**: Public web root for assets/uploads.
- **storage/**: Application storage (logs, cache, uploads) following Laravel conventions.
- **installation/**: Scripts/files for installation and update flows.

## Module & Domain Summary
- **Core Ordering**: Products (foods, add-ons, categories), carts, coupons, campaigns, restaurants/vendors, delivery zones, orders, payments, and tracking.
- **User & Access**: Admins, vendors (restaurants), delivery men, customers/guests, roles/permissions, authentication (Laravel Passport), and localization middleware.
- **Engagement**: Banners, advertisements, push notifications, newsletters, reviews/ratings, loyalty points, cashback, incentives, and referral bonuses.
- **Financials**: Wallets, transactions, payouts/withdrawals, offline/online payment integrations, restaurant subscriptions, and tax/VAT module.
- **Integrations**: Payment gateways, SMS/email, Firebase, OpenAI, and mapping/zone logic.

## Localization Snapshot
- Supported language folders: **ar**, **bn**, **en**, **es** under `resources/lang`.
- Middleware for locale negotiation plus translation helper usage in validation/messages; significant hardcoded strings remain in views/controllers and modules.
- Configuration exposes `APP_LOCALE` and fallback settings for runtime selection.

