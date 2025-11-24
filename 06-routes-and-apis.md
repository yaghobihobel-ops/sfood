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

