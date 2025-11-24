# Database and Models

## Key Tables (from migrations/models)
- **Users & Auth**: `users`, `admins`, `vendor_employees`, `delivery_men`, `password_resets`, `phone_verifications`, roles/permissions tables for admins/employees.
- **Restaurants & Menu**: `restaurants`, `restaurant_configs`, `restaurant_schedules`, `cuisines`, `cuisine_restaurant`, `food`, `addon_categories`, `add_ons`, `variations`, `variation_options`, `attributes`, `categories`.
- **Ordering**: `orders`, `order_details`, `order_payments`, `order_transactions`, `order_references`, `order_delivery_histories`, `order_cancel_reasons`, `order_edit_logs`, `delivery_histories`, `track_deliverymen`, `order_taxes` (TaxModule), `subscriptions` and related schedules/transactions/logs.
- **Promotions & Engagement**: `campaigns`, `item_campaigns`, `coupons`, `cash_backs`, `loyalty_point_transactions`, `banners`, `advertisements`, `notifications`, `messages`, `newsletters`, `reviews`, `priority_lists`.
- **Finance**: Wallet tables (`admin_wallets`, `delivery_man_wallets`, `restaurant_wallets`, `wallet_transactions`), `withdraw_requests`, `payment_requests`, `refunds`, `offline_payment_methods` and `offline_payments`.
- **Configuration & Content**: `business_settings`, `currencies`, `zones` (with spatial columns), `mail_configs`, `analytics_scripts`, `data_settings`, `react_*` content tables, `translations`, `settings`.

## Representative Model Anatomy
- **Order (`app/Models/Order.php`)**
  - **Casts**: Monetary and foreign keys, scheduling flags, booleans (e.g., `cutlery`, `is_guest`), and timestamps for consistent types.
  - **Accessors**: `order_proof_full_url` builds public URLs for stored proof images using helper utilities and JSON decoding.
  - **Relations**: `hasMany` to `OrderDetail`, `DeliveryHistory`, `OrderPayment`, `SubscriptionLog`; `belongsTo` to `Customer` (`User`), `Restaurant`, `DeliveryMan`, `Coupon`; `hasOne` to `OrderTransaction`, `Refund`, `OrderReference`, `CashBackHistory`; scope helpers for lifecycle stages (`pending`, `ongoing`, `preparing`, etc.).
  - **Business Logic**: Delivery charge mutator rounds values; subscription helper scopes filter active schedules.
- **Restaurant (`app/Models/Restaurant.php`)**
  - Manages schedules, tags, cuisines, configs, wallet, and delivery zones, tying into menus and order eligibility.
- **Subscription Suite**
  - Models `Subscription`, `SubscriptionSchedule`, `SubscriptionTransaction`, `SubscriptionLog`, `SubscriptionPause`, and `SubscriptionBillingAndRefundHistory` capture recurring order flows, billing, pauses, and refunds.
- **TaxModule Entities**
  - `Tax`, `Taxable`, `OrderTax`, `TaxAdditionalSetup`, `SystemTaxSetup` model tax rules, applicability, per-order tax lines, and system-wide defaults.

## Relation Patterns
- **One-to-Many**: Restaurants → Foods/AddOns/Orders; Users → Orders/Addresses; Orders → OrderDetails/Payments/Logs.
- **Many-to-Many**: Restaurants ↔ Cuisines; Foods ↔ Allergies/Tags/Characteristics.
- **Scoped Queries**: Zone-based filtering (e.g., `ZoneScope` on `Order`) and report filters ensure contextual data access.

## Data Considerations
- **Spatial Data**: Zones leverage spatial columns via `matanyadaev/laravel-eloquent-spatial` for geo queries.
- **Soft Deletes**: Not globally applied; verify per-model before assuming archival behavior.
- **Localization**: `translations` table and `Translation` model support dynamic translations alongside static language files.

