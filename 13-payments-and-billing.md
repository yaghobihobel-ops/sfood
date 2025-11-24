# Payments and Billing

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
