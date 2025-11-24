# Payments and Billing

## Scope & Guardrails
- Keep existing gateway keys, request params, and callback/status payloads unchanged; add new gateways/routes alongside current ones. Database schemas must remain backward compatible (additive columns only).

## Gateway Configuration
- **Config payload:** `/config` returns digital payment toggles, plugin/default gateway flags, and active methods via `getPaymentMethods` plus offline methods list; sourced from `BusinessSetting` and addon settings.【F:routes/api/v1/api.php†L503-L507】【F:app/Http/Controllers/Api/V1/ConfigController.php†L77-L115】
- **Supported currencies matrix:** `App\Traits\PaymentGatewayTrait::getPaymentGatewaySupportedCurrencies` declares per-gateway currency codes for dozens of providers (Amazon Pay, Stripe, PayPal, Razorpay, Paystack, PhonePe, etc.), guiding validation before charge creation.【F:app/Traits/PaymentGatewayTrait.php†L8-L200】
- **Legacy payment map:** `App\Traits\Payment` maps logical gateways to route stubs (e.g., `payment/stripe/pay`) used by checkout flows; keep these stable to avoid breaking clients.【F:app/Traits/Payment.php†L1-L20】

## Customer Order Payments
- OrderController supports online/offline payment selection and updates (`update_payment_method`, `offline_payment`, `offline_payment_update`) within `customer/order` routes guarded by `apiGuestCheck`. Required parameters and statuses must remain intact.【F:routes/api/v1/api.php†L441-L457】
- Wallet add-fund and adjustment endpoints exist for customers and delivery men (wallet_payment_list/make_wallet_adjustment) and should retain existing JSON contract.【F:routes/api/v1/api.php†L124-L171】【F:routes/api/v1/api.php†L205-L210】

## Vendor Financials
- VendorController provides `make_payment` (collected cash settlement) and `make_wallet_adjustment` plus wallet payment listings for reconciliation; withdrawals and bank updates are handled via withdraw endpoints and BusinessSettingsController updates.【F:routes/api/v1/api.php†L194-L210】
- Reporting endpoints (`get-transaction-report`, `generate-transaction-statement`, `get-disbursement-report`) expose financial summaries; treat outputs as read-only contracts.【F:routes/api/v1/api.php†L211-L223】

## Offline Payments
- Offline methods surfaced via `offline_payment_method_list` route and `OfflinePaymentMethod` lookups in ConfigController; extend by adding new methods/config without altering current ones.【F:routes/api/v1/api.php†L503-L507】【F:app/Http/Controllers/Api/V1/ConfigController.php†L20-L28】
## Configuration Sources
- `ConfigServiceProvider` loads gateway credentials from `settings` table and wires config for Paystack, SSLCommerz, PayPal, Flutterwave, Razorpay, Paytm; also sets delivery verification, pagination, rounding rules that influence monetary calculations.【F:app/Providers/ConfigServiceProvider.php†L60-L187】
- Timezone/timeformat are applied before gateway setup to keep transaction timestamps consistent with business settings.【F:app/Providers/ConfigServiceProvider.php†L203-L215】

## Payment Data Structures
- `App\Library\Payment` DTO encapsulates success/failure hooks, currency, payer/receiver IDs, amount, metadata, platform, and external redirect link for gateway-agnostic flows (order, wallet, subscription).【F:app/Library/Payment.php†L5-L95】
- Helper callbacks `order_place`, `order_failed`, `wallet_success`, `wallet_failed`, `sub_success`, `collect_cash_*` (in `app/helpers.php`) update orders, wallets, and subscriptions, and trigger mail/push notifications when gateways confirm results.【F:app/helpers.php†L20-L200】

## API Endpoints Touching Payments
- Customer order/payment flows: `/customer/order/*` (payment method updates, offline payment, refund) and `/customer/cart/*` managed by OrderController/CartController under `apiGuestCheck` middleware.【F:routes/api/v1/api.php†L336-L374】
- Wallet: `/customer/wallet/add-fund` and wallet transactions/bonuses under `auth:api` guard.【F:routes/api/v1/api.php†L310-L335】
- Vendor/delivery remittances: delivery-man `make-collected-cash-payment`, `make-wallet-adjustment`, wallet lists; vendor equivalents plus withdrawal requests and withdrawal method CRUD under vendor middleware.【F:routes/api/v1/api.php†L165-L170】【F:routes/api/v1/api.php†L192-L233】
- Offline payment methods exposed via config endpoints (`get-PaymentMethods`, `offline_payment_method_list`) for clients to present options.【F:routes/api/v1/api.php†L252-L270】

## Notifications Around Payments
- Helpers trigger push/email on successful wallet funding and order verification mails, respecting notification status and mail config loaded earlier.【F:app/helpers.php†L20-L120】

## Recommendations
- Add per-gateway webhook verification documentation; current flows rely on synchronous callbacks (success/fail hooks) and offline method updates—documenting IPN/endpoints would aid DevOps.
- Standardize currency/rounding in API responses using `round_up_to_digit` to prevent client discrepancies.
