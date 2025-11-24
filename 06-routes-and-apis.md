# Routes and APIs

## Route Files
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

