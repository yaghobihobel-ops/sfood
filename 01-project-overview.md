# Project Overview

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

