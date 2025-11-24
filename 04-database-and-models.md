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

