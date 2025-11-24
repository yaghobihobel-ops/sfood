# Architecture and Layers

## Architectural Style
- **Laravel MVC core** augmented by **module-oriented** packages (nwidart) for separable domains (AI, TaxModule, Gateways).
- **Service/logic helpers** centralized under `app/CentralLogics/*` for cross-controller business rules (orders, restaurants, products, coupons, SMS, file storage).
- **Middleware-driven cross-cutting concerns** for authentication guards, localization, installation gating, module permissions, throttling (`actch` custom guards), subscriptions, and maintenance mode.

## Backend Layers
- **Routing Layer:**
  - Versioned public APIs in `routes/api/v1/api.php` (customer, vendor, delivery-man, auth, order flows) and a lightweight v2 endpoint.
  - Web/admin/vendor entry points in `routes/web.php`, `routes/admin.php`, `routes/vendor.php`, plus install/update routes.
  - Module routes (e.g., `Modules/AI/routes/admin/routes.php`) mounted via module providers.
- **Controllers:**
  - Organized by audience: `app/Http/Controllers/Admin/*`, `.../Vendor/*`, `.../Api/V1/*` (Auth, Restaurant, Order, Product, Wallet, etc.).
  - API controllers leverage request validators, CentralLogics helpers, and Eloquent models.
- **Requests & Validation:**
  - Form request classes under `app/Http/Requests` encapsulate validation rules and authorize checks.
- **Models/Entities:**
  - Rich Eloquent models for commerce (Order, OrderDetail, Coupon, Campaign), catalog (Product, Category, AddOn, Attribute), logistics (Zone with `ZoneScope`, DeliveryMan, Vehicle), finance (Transaction, Payment, Refund, Wallets), and user management (Admin, Vendor, Restaurant, User).
  - Traits and scopes (`app/Traits`, `app/Scopes`) add shared query filters (e.g., `ReportFilter`, zone scoping).
- **Services/Helpers:**
  - `CentralLogics` classes encapsulate data retrieval and mutation for menu/catalog, campaign, banner, restaurant availability, and SMS/file utilities.
  - `app/Library/*` defines constants and API responses helpers.
- **Jobs/Events/Observers:**
  - Observers (e.g., `app/Observers`) handle model lifecycle side effects; websocket support configured via `config/websockets.php` and base migration `0000_00_00_000000_create_websockets_statistics_entries_table.php`.
- **Modules:**
  - **AI:** Service provider registers admin routes for AI auto-fill controllers; resources (views/lang/migrations) scoped under the module.
  - **TaxModule:** Provides tax entities, services, routes, and add-ons for flexible tax rules and reporting.
  - **Gateways:** Enabled via `modules_statuses.json` to house payment gateway abstractions.

## Request-to-Response Flow (API v1 Example)
1. **Route** resolves to an API controller (e.g., `Api\V1\OrderController`) via route groups with middleware `localization` and custom app guards.
2. **Middleware** stack ensures localization, authentication (`auth:api` for users/delivery), module activation (`module:settings`), and subscription/permission checks.
3. **Request validation** via Form Requests; normalized data passed to CentralLogics helpers/services.
4. **Business logic** executed through helpers, Eloquent models with scopes/relations, and module services (e.g., TaxModule for taxes, AI for auto-fill).
5. **Response** returned as JSON resources or direct arrays; notifications dispatched via Mail/Notification classes; events may trigger websockets and queues.

## Frontend/Assets
- Blade views under `resources/views/` for web/admin/vendor portals; Mix builds assets per `webpack.mix.js`.
- Module-specific views/assets (e.g., `Modules/AI/resources/views`) published via module providers.
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

