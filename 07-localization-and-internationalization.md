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
- **Languages Present**: Arabic (`ar`), Bengali (`bn`), English (`en`), Spanish (`es`) under `resources/lang`.
- **Config**: `config/app.php` reads `APP_LOCALE`, `APP_FALLBACK_LOCALE`, and `APP_FAKER_LOCALE` env variables for runtime locale selection.
- **Middleware**: `localization` and `LocalizationMiddleware` applied on API route groups to set locale per request.
- **Database Translations**: `translations` table (`Translation` model) exists for dynamic key/value overrides, complementing static language files.
- **Hardcoded Text**: Many Blade views, controllers, requests, mailables, notifications, and module views contain inline text (see `LOCALIZATION_AUDIT_REPORT.md` for audit notes).

## Areas Requiring Translation
- **UI Views**: `resources/views` (customer/admin/vendor) and `Modules/*/Resources/views`.
- **Backend Messages**: Validation errors in `app/Http/Requests`, flash/session messages in controllers, logging/user-facing exceptions.
- **Communications**: Emails (`app/Mail`), notifications (`app/Notifications`), SMS templates (helpers), PDF/Excel exports.
- **Content & Settings**: React content tables (`React*` models) and database-seeded texts; configuration-driven labels (business settings, currencies, zones).

## Recommended Translation Key Structure
- **Authentication**: `auth.login.success`, `auth.login.failed`, `auth.register.verification_sent`, `auth.password.reset_sent`.
- **Orders**: `order.create.success`, `order.status.accepted`, `order.status.delivered`, `order.cancel.reason_required`.
- **Payments/Wallet**: `payment.gateway.unavailable`, `payment.success`, `wallet.topup.success`, `withdraw.request_submitted`.
- **Vendors/Restaurants**: `vendor.menu.created`, `vendor.subscription.expired`, `vendor.payout.submitted`.
- **Delivery**: `delivery.assignment.new_task`, `delivery.location.updated`, `delivery.review.thanks`.
- **General/UI**: `common.save`, `common.cancel`, `common.search_placeholder`, `common.no_results`.

## Refactoring Guidance
- Replace hardcoded strings in controllers/views with translation helpers (`__()`, `@lang`, `trans_choice`).
- Centralize validation messages in language files (e.g., `resources/lang/*/validation.php`) or request-specific language keys.
- Use database-backed `translations` for admin-manageable dynamic content; provide fallbacks to file-based keys.
- Introduce locale-aware date/number formatting using Carbon/Intl (e.g., `Carbon::parse()->locale($locale)->isoFormat(...)`).
- For RTL support (Arabic): add `rtl.css` with flipped layout rules, load conditionally based on locale, and apply RTL-friendly fonts (e.g., Vazirmatn). Avoid hardcoded `text-left/right`; use logical properties (`start/end`).
- For Jalali/other calendars: wrap date display helpers to convert Gregorian storage to locale-specific views while keeping database storage unchanged.

## Action Plan for Localization
1. **Inventory Text**: Use static analysis or `rg` to find strings in views/controllers; migrate to language keys.
2. **Create Namespaced Files**: Organize language files by domain (e.g., `auth.php`, `order.php`, `payment.php`, `vendor.php`, `delivery.php`, `common.php`).
3. **Middleware Enhancements**: Ensure locale is persisted via user profile or request header; default to `APP_LOCALE` with fallback.
4. **Asset Strategy**: Add RTL stylesheet and locale-specific font loading; ensure Mix build outputs both LTR/RTL bundles.
5. **QA**: Validate translations in all panels and APIs; add tests to confirm locale switching and translated validation messages.

