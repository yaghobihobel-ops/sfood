# Localization Implementation Plan

This document outlines the technical strategy for localizing the `stdckfood` Laravel application to Persian (Farsi).

## 1. Package Selection

The following packages will be installed via Composer to facilitate the localization process:

- **`morilog/jalali`**: The de-facto standard for converting Gregorian dates and Carbon instances to Jalali (Shamsi) calendar system in Laravel. It provides helpers and a `Jalalian` class that is indispensable for this task.
- **`cviebrock/eloquent-sluggable`**: While the package is already a dependency, it needs to be reconfigured to properly handle Persian (Unicode) characters for generating SEO-friendly URL slugs. We will create a custom sluggable strategy if the default configuration is insufficient.

## 2. Localization File Structure (`resources/lang/fa`)

We will adopt a modular and clean structure for our language files to ensure maintainability.

- A `fa` directory will be created under `resources/lang/`.
- Translation files will be organized by module and feature, not as a single monolithic file.
- **Example Structure:**
  ```
  resources/
  └── lang/
      ├── en/
      └── fa/
          ├── messages.php       // General system messages
          ├── validation.php     // Standard validation messages
          ├── auth.php           // Authentication-related text
          ├── pagination.php     // Pagination links
          ├── addon.php          // Module-specific: Addon
          ├── attributes.php     // attribute names
          ├── category.php       // Module-specific: Category
          ├── order.php          // Module-specific: Order
          └── ...                // etc. for other modules
  ```

## 3. RTL Strategy

A hybrid approach will be used for Right-To-Left (RTL) styling to ensure minimal disruption to the existing CSS codebase.

1.  **Primary Stylesheet:** A new stylesheet, `rtl.css`, will be created in the `public/css/` directory.
2.  **Layout Direction:** The main application layout file (`resources/views/layouts/app.blade.php` or equivalent) will be updated to include `dir="rtl"` and `` on the `` tag.
3.  **Loading Logic:** The `rtl.css` will be conditionally loaded after the main stylesheet to override LTR-specific rules. This ensures that we only write CSS for the properties that need changing.
4.  **CSS Overrides in `rtl.css` will include:**
    - `text-align: right;`
    - Flipping `margin-left` to `margin-right` and vice-versa.
    - Flipping `padding-left` to `padding-right` and vice-versa.
    - Changing `flex-direction` where necessary (e.g., `row` to `row-reverse`).
    - Adjusting `border-radius` properties if they are asymmetrical.

## 4. Database & Date Strategy

The core principle is to **store in Gregorian, display in Jalali.**

- **Database:** No changes will be made to the database schema. All `created_at`, `updated_at`, and other timestamp columns will remain as standard `DATETIME` or `TIMESTAMP` types, storing data in Gregorian format. This maintains data integrity and compatibility with database functions.
- **Display Logic:**
    - A custom Blade directive, `@jalali($dateObject)`, will be created in `AppServiceProvider`. This will allow for easy date formatting directly in the views (e.g., `{{ @jalali($order->created_at) }}`).
    - For more complex scenarios or API responses, we will use model accessors. For example, a `getCreatedAtShamsiAttribute` accessor can be added to the `Order` model to return a formatted Jalali date string.

## 5. Helper Functions

A new helper file, `app/Helpers/PersianHelper.php`, will be created and autoloaded via `composer.json`. This will contain common localization functions that can be used globally.

- `toPersianDigits($string)`: Converts English numerals (0-9) to their Persian counterparts (۰-۹). This will be used strictly in the presentation layer (Blade views).
- `formatToman($amount)`: A function to format currency numbers with commas as thousands separators and append the "Toman" string (e.g., `۱,۵۰۰ تومان`).

This strategic plan ensures a comprehensive and non-destructive localization of the application, maintaining the integrity of the core logic while providing a fully native experience for Persian-speaking users.
