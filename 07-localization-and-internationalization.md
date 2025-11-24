# Localization and Internationalization

## Current State
- **Languages available:** English (`en`), Arabic (`ar`), Bengali (`bn`), Spanish (`es`) under `resources/lang/` with default Laravel groups (`auth.php`, `validation.php`, `messages.php`, etc.).
- **Middleware:** `Localization` and `LocalizationMiddleware` enforce locale selection on API/web requests.
- **Audit Findings:** `LOCALIZATION_AUDIT_REPORT.md` notes extensive hardcoded strings in Blade views, controllers, form requests, mailables, notifications, and module views/controllers. It also flags RTL CSS risks, Persian-friendly fonts need, Jalali date display requirements, slug/phone validation considerations, and consistency across modules.

## Hardcoded Text Hotspots
- `resources/views/` (customer/admin/vendor Blades) including buttons, labels, alerts.
- Controllers in `app/Http/Controllers/*` and module controllers with flash/error strings.
- Validation messages in `app/Http/Requests/*` and custom rules.
- `app/Mail/*` and `app/Notifications/*` for email/SMS templates.
- Module views under `Modules/*/resources/views` and controllers under `Modules/*/Http/Controllers`.

## Translation Key Recommendations
- Organize keys by domain and action for clarity, e.g.:
  - `auth.login.success`, `auth.login.failed`, `auth.password.reset_sent`.
  - `order.status.pending`, `order.status.delivered`, `order.cancel.reason_required`.
  - `restaurant.menu.add_success`, `restaurant.menu.validation.name_required`, `restaurant.menu.option_removed`.
  - `payment.gateway.unavailable`, `payment.refund.requested`, `payment.invoice.sent`.
  - `notification.order.assigned_dm`, `notification.order.delivered`, `notification.chat.new_message`.
  - `validation.user.email_required`, `validation.address.phone_invalid`, `validation.cart.minimum_not_met`.

## Implementation Suggestions
- Replace hardcoded strings with `__()`, `@lang`, or `trans_choice` in Blades and controllers; create domain-specific lang files (e.g., `resources/lang/en/orders.php`, `notifications.php`).
- For modules, add lang directories under `Modules/<Module>/lang` and load via module service providers; publish to `resources/lang/modules/<module>` for overrides.
- Add locale-aware **date/time** formatting using Carbon localization or packages like `morilog/jalali` for Persian, ensuring display conversion while storing Gregorian.
- Implement **RTL support**: maintain a dedicated `rtl.css` (or conditional Mix build) that flips layout, margins, text alignment; toggle via `dir="rtl"` and body class per locale.
- **Fonts:** include a legible RTL-friendly font (e.g., Vazirmatn) via asset pipeline and apply conditionally for RTL locales.
- **Numbers/Currency:** use `number_format`/Intl for localized numerals and currency symbols; centralize currency formatting helper.
- **Validation/Phone:** adjust regex for locale-specific phone numbers and slugs; ensure error messages live in translation files.
- **Emails/SMS:** move subjects/bodies to lang files; for SMS consider per-locale templates.

## Actionable Next Steps
1. Inventory hardcoded strings starting from high-priority UI surfaces (customer ordering, checkout, auth flows) and migrate to lang keys.
2. Extend existing lang files with structured keys; generate missing translations for `ar`, `bn`, `es`, and target locale(s).
3. Introduce RTL stylesheet and font assets; update Blade layouts to switch CSS/dir based on locale.
4. Implement localized date/number formatting helpers and apply in views (orders, reports, subscriptions).
5. Validate module parity (AI, TaxModule, future Gateways) by adding module-specific lang files and ensuring middleware picks up locale.

