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
