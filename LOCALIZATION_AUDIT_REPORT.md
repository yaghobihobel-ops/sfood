# Localization Audit Report for stdckfood

## 1. Files Requiring Translation (Hardcoded Strings)

### High Priority (Customer Facing)
- `resources/views/` (All Blade files) - Extensive hardcoded strings in UI elements, buttons, labels, and messages.
- `app/Http/Controllers/` - Hardcoded strings in flash messages, redirects, and error responses.
- `app/Http/Requests/` - Validation error messages (though many use `trans()`, some custom rules have hardcoded strings).
- `app/Mail/` - All Mailable classes contain hardcoded email subject lines and content.
- `app/Notifications/` - All Notification classes have hardcoded messages.
- `Modules/*/Resources/views/` - All module views require translation.
- `Modules/*/Http/Controllers/` - All module controllers have hardcoded strings.

### Medium Priority (Admin Panel)
- `resources/views/admin-views/` - Admin panel UI has many hardcoded labels and messages.
- `Modules/Admin/` - The admin module has significant hardcoded text.

## 2. Complex Logic Involving Dates

The application consistently uses `Carbon` for date and time manipulation. The key challenge is not storage but display.

- **Storage:** All `created_at`, `updated_at`, and other date columns are stored in Gregorian format in the database. **This is good and should not be changed.**
- **Display:** Dates are displayed directly in Blade views without conversion.
- **Identified Instances:**
    - `created_at->format('Y-m-d')` is used frequently.
    - `now()` and `Carbon::now()` are used for comparisons and setting current timestamps.
    - **Subscription/Expiry Logic:** While no explicit subscription module was found in the core app, any features related to order deadlines, promotion validity, or delivery time windows will be affected. For example, displaying an "order placed at" or "valid until" time needs conversion to Jalali.
    - **Reports & Analytics:** Any date-based filtering or reporting in the admin panel will need to handle Jalali input and convert it back to Gregorian for database queries.

## 3. External Assets & Fonts

- **Current Font:** The project uses standard web fonts (e.g., Bootstrap's default).
- **Required Font:** To properly render Persian text, a suitable font is required. **Vazirmatn** is an excellent, modern, and open-source choice. It needs to be downloaded and integrated via CSS.

## 4. Potential Risks & Conflicts

- **RTL CSS Conversion:** This is the highest risk. The current CSS is built for LTR. Converting to RTL will require extensive changes to `margin`, `padding`, `float`, `text-align`, and `flex-direction`. A separate `rtl.css` file or a dynamic approach using a library will be necessary.
- **URL Slugs:** The `eloquent-sluggable` package is used, but it's likely not configured for Persian. Slugs for products, categories, or restaurants will need to be generated in Persian.
- **Phone Number Validation:** Current validation rules likely use standard regex for international numbers. This needs to be adapted for Iranian mobile numbers (e.g., `^09[0-9]{9}$`).
- **Dependencies:** The project has many dependencies. We must ensure that any new packages (like `morilog/jalali`) do not create version conflicts with existing ones in `composer.json`.
- **Modularity:** The project uses a modular structure (`Modules/`). Localization needs to be applied consistently across all modules. Language files might need to be published or organized per module.
