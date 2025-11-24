# Modules and Features

## Core Domains (app/)
- **User & Access:** Admin, Vendor, Restaurant, DeliveryMan, User/Guest with wallets and loyalty/referral programs.
- **Catalog:** Category, Product, AddOn, Attribute, Cuisine; CentralLogics helpers (`ProductLogic`, `CategoryLogic`, `RestaurantLogic`) manage availability, pricing, and listing flows.
- **Ordering:** Order lifecycle (Order, OrderDetail, DeliveryHistory, Subscription/SubscriptionLog), order scheduling, proof uploads, and tracking with scopes for status filtering (e.g., `Order::scopeOngoing`).
- **Promotions:** Coupons, Campaigns, Advertisements, and Banner management with helper logic for eligibility and presentation.
- **Finance:** Transactions (OrderTransaction, Admin/Restaurant wallets), refunds, cashback history, offline payments, and payout/withdrawal methods.
- **Communication:** Conversations between users, vendors, and delivery personnel; Notifications/Mail classes; Reviews and ratings for delivery and restaurants.
- **Operations:** Zones (geo fenced with ZoneScope), Vehicles, Delivery assignment, live location, and reporting filters via Traits/Scopes.

## Module: AI (Modules/AI)
- **Purpose:** Admin-side AI-assisted product authoring and analysis.
- **Key Components:**
  - Routes: `Modules/AI/routes/admin/routes.php` exposes `admin.product.*` endpoints for auto-filling titles, descriptions, SEO, variation setup, price sections, and image analysis.
  - Service Provider: `app/Providers/AIServiceProvider` loads translations, config, views, migrations, and route provider.
  - Integrations: Depends on `openai-php/laravel` for LLM-powered content generation; uses Vite for module asset bundling.
- **Interaction:** Hooks into product management screens; guarded by `module:settings` middleware and admin prefix/namespace.

## Module: TaxModule (Modules/TaxModule)
- **Purpose:** Extend tax handling with dedicated entities, services, traits, and exports.
- **Key Components:**
  - Entities and services under `Modules/TaxModule/Entities` and `Services` for tax calculation logic.
  - Routes and config enabling tax add-on management; migrations in `Modules/TaxModule/Database/Migrations` integrate with core orders (e.g., `OrderTax`).
  - Traits for reusing tax behaviors across models.
- **Interaction:** Used by core models such as `App\Models\Order` (relation to `Modules\TaxModule\Entities\OrderTax`) for storing tax breakdowns.

## Module: Gateways
- **Purpose:** Pluggable payment gateway definitions beyond core config.
- **Status:** Enabled in `modules_statuses.json`; module code expected under `Modules/Gateways` (not present in repo snapshot), implying support for extending payment methods without touching core.

## Auxiliary Feature Areas
- **Installation/Update:** Routes under `routes/install.php` and `routes/update.php` plus scripts in `installation/` to gate setup steps.
- **Websockets:** Config and migrations for real-time delivery tracking and notifications.
- **Exports/Imports:** Excel exports via `maatwebsite/excel` and `rap2hpoutre/fast-excel`; PDF generation via `mpdf` and `barryvdh/laravel-dompdf`.

