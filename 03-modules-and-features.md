# Modules and Features

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

