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
