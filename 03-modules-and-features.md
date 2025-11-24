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
## Core Domains
- **User Management**: Admins (`app/Models/Admin.php`), customers (`User.php`/`Guest.php`), vendors (`Vendor.php`, `VendorEmployee.php`), delivery men (`DeliveryMan.php`), plus roles/permissions (`AdminRole.php`, `EmployeeRole.php`).
- **Restaurants & Menus**: Restaurants (`Restaurant.php`, `RestaurantSchedule.php`, `RestaurantConfig.php`), cuisines, tags, SEO data, and menu composition (Food, AddOn, Variation/Option, Attribute, Category) with campaign/discount hooks.
- **Ordering & Fulfillment**: Orders with payments, delivery history/tracking, cancellation reasons, refunds, and subscriptions (`Subscription`, `SubscriptionSchedule`, `SubscriptionTransaction`).
- **Finance & Wallets**: Wallets for admins, restaurants, delivery men, and customers; transactions, payouts (`WithdrawRequest`, `PaymentRequest`), and offline payment methods.
- **Engagement & Marketing**: Banners, advertisements, campaigns, coupons, loyalty points, cashback, notifications/messages, newsletters, testimonials, FAQs.
- **Analytics & Settings**: Business settings, currency, zone/geo configuration, analytics scripts, and data settings.

## Modular Add-ons (`Modules/`)
- **TaxModule**
  - **Purpose**: VAT/tax calculation with additional setup records and exports.
  - **Entities**: `Tax`, `Taxable`, `OrderTax`, `TaxAdditionalSetup`, `SystemTaxSetup`.
  - **Services**: `Services/CalculateTaxService.php` encapsulates tax computation.
  - **Providers & Routes**: `Providers/TaxVatServiceProvider.php` and `RouteServiceProvider.php` bootstrap config and routing; admin add-on routes under `Addon/admin_routes.php`.
  - **Traits**: `Traits/VatTaxConfiguration.php` for reusable configuration access.
- **AI**
  - Currently scaffolded with service provider and empty routes/config; ready for future AI-driven features.
- **Gateway Placeholder**
  - `modules_statuses.json` lists a `Gateways` module; implementation not present in repository but reserved for payment gateway extensions.

## API Surfaces
- **API v1**: `routes/api/v1/api.php` exposes customer auth, vendor auth, delivery-man auth, product listing, cart, orders, wishlist, wallet, notifications, campaigns, zones, and vendor-side management (menu, POS, reports, subscriptions, payouts).
- **API v2**: `routes/api/v2/api.php` offers similar endpoints with potential version-specific adjustments (behavior/parity to be verified).
- **Web/Admin/Vendor**: Additional routes in `routes/web.php`, `routes/admin.php`, and `routes/vendor.php` for panel UIs and payment callbacks.

## Surfaces & Roles
- **Admin Panel**: Manages restaurants, menus, users, finances, campaigns, subscriptions, and system settings.
- **Vendor/Restaurant Panel**: Manages menus, orders, delivery staff, ads, reports, and subscriptions.
- **Delivery App**: Handles delivery authentication, task list, status updates, and location tracking.
- **Customer App**: Browsing/restaurants/foods, cart/checkout, tracking, wallet/loyalty, subscriptions, and support conversations.

