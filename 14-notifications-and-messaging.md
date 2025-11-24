# Notifications, Messaging, and Outreach

## Push Notifications
- FCM push constructed in `App\CentralLogics\Helpers::send_push_notif_to_device`, using service account credentials (`push_notification_service_file_content`) to build Google OAuth JWT tokens and send rich payloads (title/body/image, conversation/ad/order metadata).【F:app/CentralLogics/Helpers.php†L1230-L1320】
- Delivery, vendor, and customer workflows call this helper after business events (orders, referrals, maintenance mode) to target device tokens supplied in auth/profile endpoints (e.g., update-fcm-token).【F:routes/api/v1/api.php†L124-L189】

## SMS Gateways
- `App\CentralLogics\SMS_module` provides a unified interface with priority fallbacks: Twilio, Nexmo, 2Factor, MSG91, Alphanet. Each method builds templated OTP messages and handles API calls/cURL posting; returns status for callers to branch on success/error.【F:app/CentralLogics/SMS_module.php†L10-L200】
- Customer authentication uses OTP flows that rely on SMS module plus database-backed attempt tracking for throttling.【F:app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php†L31-L140】

## Email
- Mail credentials and driver selection are loaded dynamically from `business_settings` by `ConfigServiceProvider`, allowing runtime changes without redeploy (host/port/user/pass/encryption/from).【F:app/Providers/ConfigServiceProvider.php†L41-L58】
- Helper hooks (e.g., `order_place`, wallet funding) send transactional emails such as order verification and add-fund receipts when mail status flags permit.【F:app/helpers.php†L20-L120】

## In-App Conversations
- Messaging endpoints (`/customer/message/*`, `/delivery-man/message/*`, `/vendor/message/*`) route to ConversationController variants to fetch/search threads, message details, send messages, and upload chat images (customer).【F:routes/api/v1/api.php†L147-L154】【F:routes/api/v1/api.php†L316-L340】【F:routes/api/v1/api.php†L303-L335】
- Push tokens are associated with conversations to deliver real-time notifications via the push helper above.【F:routes/api/v1/api.php†L124-L189】【F:app/CentralLogics/Helpers.php†L1230-L1320】

## Recommendations
- Add unified notification preferences per channel (mail/push/sms) surfaced via user profile APIs to allow opt-outs without DB tweaks.
- Consider queueing SMS/email sends to offload latency from auth/order flows and improve resilience.
