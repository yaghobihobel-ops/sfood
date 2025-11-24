# Routes and APIs

## Route Files
- `routes/api/v1/api.php`: Primary REST API with groups for auth (customers, delivery men, vendors), orders, products, restaurants, coupons, campaigns, wallets, wishlist, notifications, conversations, loyalty points, subscriptions, and zone checks. Middleware includes `localization`, `react`, audience-specific guards (`auth:api`, `actch:deliveryman_app`, `actch:restaurant_app`), and vendor/delivery-specific throttles.
- `routes/api/v2/api.php`: Minimal v2 endpoint for library update hook.
- `routes/admin.php`, `routes/vendor.php`, `routes/web.php`: Web/admin/vendor panels, often protected by auth, localization, module/subscription checks, and installation middleware.
- `routes/install.php`, `routes/update.php`: Setup and upgrade flows.
- Module routes: `Modules/AI/routes/admin/routes.php` (admin product AI auto-fill) loaded via module service provider; TaxModule adds its own routes.

## Middleware Highlights
- **Localization / LocalizationMiddleware:** sets locale based on request/app settings.
- **ReactValid / actch guards:** custom middleware to validate app channel (user, vendor, delivery) and throttle per app.
- **Authentication Guards:** `auth:api`, vendor/delivery JWT/passport tokens; `AdminMiddleware`, `VendorMiddleware`, `DmTokenIsValid`, `VendorTokenIsValid` for role checks.
- **ModulePermissionMiddleware:** ensures enabled modules before allowing access (e.g., AI module routes).
- **Subscription:** verifies vendor package/subscription limits on vendor routes.
- **MaintenanceMode / InstallationMiddleware:** blocks access during maintenance or before installation completion.

## Authentication & Authorization
- API authentication uses **Laravel Passport** tokens; vendor/delivery/customer login/registration handled in `Api\V1\Auth` controllers with password reset and Firebase token verification.
- Role/permission logic for admins/vendors enforced via middleware and controller checks; policies may be applied for sensitive models.

## API Surface (v1 Highlights)
- **Public:** Zone list/check, advertisements, addon categories, configurations, newsletter subscription.
- **Customer Auth:** sign-up/login, phone/email verification, guest access, password reset (traditional + Firebase).
- **Vendor/Delivery Auth:** login/register flows with password reset and Firebase token verification.
- **Ordering:** Cart, order placement/tracking, order history, schedule/subscription management, payment processing, refunds, order proofs, and tips.
- **Catalog:** Restaurants, products/foods, attributes, cuisines, campaign items, search/sorting/filtering.
- **Promotions:** Coupons validation/application, loyalty points, cashback.
- **Vendor Tools:** Product/attribute/add-on CRUD, POS endpoints, reports, deliveryman management, subscription packages, advertisements, withdrawal methods.
- **Deliveryman Tools:** Order queues, status updates, earnings, reviews, last location tracking.
- `routes/api/v1/api.php`: Primary mobile/API surface with customer, vendor, and delivery-man authentication plus feature endpoints (products, zones, cart, orders, wallet, wishlist, notifications, campaigns, conversations, subscription flows, vendor POS, reports, payouts).
- `routes/api/v2/api.php`: Second version of the API mirroring v1 with version-specific controllers.
- `routes/admin.php`: Admin panel routes for managing restaurants, menus, users, campaigns, finances, reports, and system settings.
- `routes/vendor.php`: Vendor/restaurant panel routes for orders, menus, delivery staff, ads, and subscriptions.
- `routes/web.php`: Public web routes plus payment gateway callbacks/redirects, newsletter sign-up, and landing pages.
- `routes/install.php` & `routes/update.php`: Guarded routes used during installation and update flows.
- `routes/vendor.php` & `routes/admin.php`: Surface-specific route maps for authenticated panels.

## Middleware Highlights
- **Auth Guards**: Passport-based `auth:api` for customers; vendor and delivery guards (`actch:restaurant_app`, `actch:deliveryman_app`) enforce app types; web guards for admin/vendor sessions.
- **Localization**: `localization` and `LocalizationMiddleware` pick locales from request headers or params.
- **Access Control**: `ModulePermissionMiddleware` and `Subscription` middleware gate routes based on module enablement and plan limits.
- **Operational Checks**: `InstallationMiddleware` and `MaintenanceMode` guard installation/update states; `APIGuestMiddleware` allows limited guest access; throttling and CSRF per Laravel defaults.

## Authentication Patterns
- **Customer**: Registration/login with OTP/email verification, password resets, Firebase token verification, guest requests (API v1/v2 auth groups).
- **Delivery Man**: Separate login/reset flows with device/app validation; location updates and task management under authenticated routes.
- **Vendor**: Login/register/reset plus menu, order, finance, and subscription management under vendor guard.
- **Admin**: Panel authentication via web guard; policies enforced per role/permission models.

## Endpoint Grouping Examples (API v1)
- `zone/*`: Zone listing and eligibility checks.
- `auth/*`: Customer signup/login/password flows; nested `delivery-man` and `vendor` auth flows.
- `vendor/*`: Vendor subscription checks, package views, product limits.
- `delivery-man/*`: Location and review endpoints; authenticated task management.
- `order/*`: Place, track, cancel, and review orders; subscription order management.
- `vendor/*` (namespace Vendor): Menu CRUD (addons/attributes/foods), POS operations, reports, advertisements, withdrawals.

## API Documentation
- No Swagger/OpenAPI present. Postman collections not included. Use route files and controller docblocks to derive behavior.

