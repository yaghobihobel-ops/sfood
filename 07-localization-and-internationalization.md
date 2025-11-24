# Localization and Internationalization

## Current State
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

