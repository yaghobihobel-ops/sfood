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

