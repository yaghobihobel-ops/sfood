# Database and Models

## Schema Overview
- **User/Access Tables:** `users`, `admins`, `vendors`, `restaurants`, `delivery_men`, roles/permissions tables, wallets for admins/vendors/restaurants/delivery men, password resets, failed jobs, notifications.
- **Catalog Tables:** `categories`, `products`, `add_ons`, `attributes`, `addon_categories`, `cuisines`, images/media attachments.
- **Order & Fulfillment:** `orders`, `order_details`, `order_transactions`, `delivery_histories`, `delivery_addresses`, `subscriptions` & `subscription_logs`, `order_payments`, `order_proofs`, and `order_references`.
- **Promotions:** `coupons`, `campaigns`, `advertisements`, `banners`, loyalty and cashback histories.
- **Finance & Tax:** `refunds`, `transactions`, offline payments, payout/withdraw methods, tax tables via TaxModule (`order_taxes`, tax rules/configs).
- **Engagement & Content:** `conversations`, `notifications`, `reviews`, `newsletters`, `testimonials`, chats.
- **Operations:** `zones`, `vehicles`, geo coordinates (spatial columns via `matanyadaev/laravel-eloquent-spatial`), web socket statistics.

## Notable Model Relationships
- **Order (`app/Models/Order.php`):**
  - `belongsTo` **Customer/User**, **Restaurant**, **DeliveryMan**, **Coupon**, optional **Guest**, **Subscription**, **Zone**.
  - `hasMany` **OrderDetail**, **OrderPayment**, **DeliveryHistory**, **SubscriptionLog**, **Log** entries; `hasOne` **Refund**, **OrderTransaction**, **OrderReference**, **CashBackHistory**.
  - `hasOne` **OrderTax** from TaxModule for tax data.
  - Scopes for order state filtering (e.g., `scopeOngoing`, `scopePreparing`, `scopeFailed`, etc.).
  - Casts for monetary and status fields; accessor builds `order_proof_full_url` from stored JSON/images via helper.
- **Zone Scope (`app/Scopes/ZoneScope.php`):** Applied to geo-aware models (e.g., Restaurant, Order) to filter queries by zone context.
- **Central Traits:** `ReportFilter` trait used on models like Order to support dynamic report filtering.

## Migrations
- Laravel base migrations (users/password_resets/failed_jobs) plus domain-specific migrations starting 2021 for admins, attributes, categories, vendors, restaurants, add-ons, etc.
- Module migrations loaded via service providers (e.g., AI and TaxModule) and published to `database/migrations` when needed.
- Websocket statistics migration `0000_00_00_000000_create_websockets_statistics_entries_table.php` supports real-time tracking metrics.

## Data Considerations
- Monetary fields cast to floats; delivery charges rounded to three decimals in setters.
- JSON handling in `order_proof` with helper-based URL resolution for stored images.
- Geo/spatial columns likely used for zones and coordinates via spatial package; ensure MySQL with spatial support.
- Subscription-aware orders filter logs by schedule date to fetch current active deliveries.
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

