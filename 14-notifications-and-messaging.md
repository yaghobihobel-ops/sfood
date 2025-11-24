# Notifications, Messaging, and Alerts

## Scope & Guardrails
- Preserve existing payload keys, notification routing, and messaging endpoints; extend via new channels/endpoints without altering current ones.

## Push Notifications
- **Helpers senders:** `Helpers::send_push_notif_to_device` builds Firebase payloads (token, data block, optional click action) for order/chat/ads notifications; topic/device variants exist for bulk dispatch.【F:app/CentralLogics/Helpers.php†L1271-L1320】
- **Usage:** Order and conversation flows invoke these helpers after status changes to inform customers, vendors, and delivery men; keep existing data keys (`title`, `body`, `order_id`, etc.) stable when adding new types.【F:app/CentralLogics/Helpers.php†L1737-L1959】

## SMS
- **Dispatcher:** `App\CentralLogics\SMS_module::send` selects configured provider (Twilio, Nexmo, 2Factor, Msg91, Alphanet SMS) based on `business_settings` flags and sends OTP/message templates.【F:app/CentralLogics/SMS_module.php†L8-L36】
- **Providers:** Implementations per vendor handle OTP templating and API calls with required credentials; new providers should be added additively without removing existing ones.【F:app/CentralLogics/SMS_module.php†L39-L200】

## Email
- **Order & wallet notifications:** Helpers trigger mail when mail status and template flags allow (e.g., add-fund, referral earnings). Any new templates should reuse existing mail status switches to avoid changing current behavior.【F:app/CentralLogics/OrderLogic.php†L1737-L1959】【F:app/CentralLogics/OrderLogic.php†L4437-L4438】

## In-App Messaging
- **ConversationController:** Delivery-man and vendor/customer chats are exposed via `/delivery-man/message/*` and vendor message routes; payloads rely on conversation IDs and are tied to push notifications for live updates.【F:routes/api/v1/api.php†L147-L153】【F:routes/api/v1/api.php†L336-L340】

## Announcement and Notification Feeds
- **Vendor notifications:** `/vendor/notifications` and customer `/customer/notifications` fetch existing notification feeds without altering schema; keep list shapes stable while adding new sources via Helpers or database triggers.【F:routes/api/v1/api.php†L124-L210】【F:routes/api/v1/api.php†L293-L314】
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
