# Architecture and Layers

## Architectural Style
- **Laravel MVC core** with a **modular monolith** flavor provided by `nwidart/laravel-modules` for optional add-ons (e.g., TaxModule, AI). Modules follow their own `Config`, `Routes`, `Providers`, and `Entities` namespaces while sharing the main app's infrastructure.
- **Domain-focused services** live in `app/CentralLogics` (business logic helpers) and `app/Library` (constants, responses) to keep controllers thin.
- **RESTful APIs** exposed under `routes/api/v1` and `routes/api/v2` for customer, vendor, and delivery clients. Web/Admin/Vendor panels are defined in dedicated route files (`routes/web.php`, `routes/admin.php`, `routes/vendor.php`).
- **Middleware** layers handle authentication (Passport and custom guards), localization, installation gating, module permissions, subscription checks, and maintenance mode.

## Backend Layers
- **Controllers (`app/Http/Controllers`)**: Split by surface (Admin, Api/V1+V2, DeliveryMan, Vendor) plus payment controllers per gateway. Install/Update controllers support deployment flows.
- **Models (`app/Models`)**: Rich set modeling users/admins/vendors/delivery men, restaurants, menus (Food, AddOn, Attributes, Categories), orders and fulfillment (Order, OrderDetail, DeliveryHistory), finances (WalletTransaction, PaymentRequest, WithdrawRequest), promotions (Coupon, Campaign, Advertisement, Cashback, LoyaltyPointTransaction), subscriptions, logistics (Zone, Vehicle, TrackDeliveryman), and content (Banners, Notifications, Messages).
- **Logic Helpers (`app/CentralLogics`)**: Shared business logic for categories, campaigns, products, restaurants, customers, orders, coupons, SMS, file management, etc., autoloaded via Composer.
- **Services/Utilities**: Payment integrations are encapsulated in dedicated controllers; spatial queries via `matanyadaev/laravel-eloquent-spatial` and AWS/S3 filesystem via Flysystem.
- **Jobs/Events/Observers**: Observers (e.g., `app/Observers`) manage model lifecycle hooks; queue configurations enable async mail/notifications if configured.
- **Middleware (`app/Http/Middleware`)**: Includes localization, API/installation checks, vendor/deliveryman token validators, module permission gate, subscription validation, maintenance, and React app guard.
- **Policies/Traits/Scopes**: Traits like `ReportFilter` and scopes such as `ZoneScope` (used in orders) encapsulate reusable query logic.

## Request-to-Response Flow
1. **Routing**: Requests enter via route files (web/admin/vendor/api). Each route group applies middleware stacks for auth, throttling, localization, and surface-specific guards.
2. **Controller Handling**: Controllers call CentralLogics helpers, Models, or services to process business rules (pricing, eligibility, availability, delivery zone checks).
3. **Persistence**: Eloquent models with casts/scopes manage database interactions; spatial/zone logic applies when necessary.
4. **Responses**: API controllers return JSON (often paginated) using helper responses; web routes render Blade views; payment controllers redirect to gateways and handle callbacks.
5. **Async/External**: Notifications, emails, SMS, and payment webhooks use events/listeners or direct service SDK calls.

## Module Bootstrapping
- Module providers (e.g., `Modules/TaxModule/Providers/TaxVatServiceProvider.php`) register routes/config and bind services (`CalculateTaxService`).
- Module enablement toggled via `modules_statuses.json` allowing feature gating without code removal.

