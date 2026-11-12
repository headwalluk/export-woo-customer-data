# Changelog

All notable changes to Export Woo Customer Data will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

**Breaking changes, released as 2.0.0.** The download addresses have changed, and PHP 8.2 and WordPress 6.2 are now required. Version 1.x had no updater, so every site needs this version installed by hand once; later versions update themselves.

### Changed

- **The download links have moved.** The widget's buttons now link to `wp-admin/admin-post.php` with a nonce, instead of `/wp-admin/exportwcd-reports/all-users.csv` and `/wp-admin/exportwcd-reports/customers-only.csv`. The old addresses redirect to the Dashboard and no longer download anything. Update any bookmarks.
- Raised the minimum versions to PHP 8.2 (from 8.0) and WordPress 6.2 (from 6.0).
- Exports are read 500 users at a time and streamed, instead of loading every user into memory at once.
- "Customers only" no longer counts Checkout block drafts, trashed orders or refunds, so someone who reached the checkout but never ordered isn't listed as a customer.
- Values a spreadsheet would run as formulas (starting with `=`, `+`, `-` or `@`) are written with a leading apostrophe, so they open as text. Phone numbers and plain numbers are unchanged.
- Meta values stored as arrays become an empty cell instead of the word `Array`.
- The `export_woo_customer_data_fields` filter's entries are now checked. Entries that don't name an allowed users table column or a meta key are logged and left out, and a filter that returns nothing usable falls back to the default columns. `user_pass`, `user_activation_key` and `user_status` can no longer be exported.
- CSV quoting follows the standard (RFC 4180): a backslash before a quote is no longer treated as an escape.
- The plugin and author links on the Plugins screen now go to the plugin's page and the author's page on headwall-hosting.com.
- Plugin description reworded.

### Added

- Updates from GitHub releases, through the normal WordPress updates screen. Filter `export_woo_customer_data_updater_enabled` turns this off.
- Declares compatibility with the Checkout block, alongside High-Performance Order Storage.
- Translations: de_DE, el_GR, en_GB, es_ES, fr_FR, it_IT, nl_NL, pl_PL.
- `uninstall.php`, which removes the plugin's version option and update cache. User and order data are never touched.
- Documentation in `docs/`, for site owners and for developers.

### Fixed

- The download links were built with `home_url()`, so they broke on sites where WordPress is installed in a subdirectory (1.0.0).
- The downloads relied on a rewrite rule, which never took effect on must-use installs, where the activation hook doesn't run (1.0.0).
- PHP 8.4 and later logged a deprecation notice for every row written (1.0.0).
- A malformed `export_woo_customer_data_fields` filter result caused PHP warnings instead of falling back to the defaults (1.0.0).

### Security

- Every download now needs a valid nonce as well as the `manage_options` capability. Before, a link to the old address could start a download in an administrator's browser from another site. The file only ever went to that administrator, but the download wasn't something they had asked for (1.0.0).
- Cells are guarded against spreadsheet formula injection: a customer could put a formula in their own name and have it run when an administrator opened the export (1.0.0).

### Removed

- `readme.txt`. `README.md`, `CHANGELOG.md` and `docs/` replace it.

## [1.1.2] - 2026-01-30

### Changed
- Dashboard widget description text updated for clarity ("Download customer data as a CSV file")
- Button styling simplified to use standard WordPress button class
- Buttons now display as block elements for improved layout

### Added
- Download icons (dashicons-download) to both export buttons

### Removed
- Primary/secondary button classes in favor of unified styling


## [1.1.1] - 2026-01-29

### Changed
- Performance optimization: Skip total count query in `get_users()` calls
- Added cache suspension to prevent memory issues with large user datasets
- Improved security with filename sanitization in CSV headers

### Added
- Explanatory comments for cache suspension usage
- `'count_total' => false` parameter to all user queries for better performance
- Defense-in-depth filename sanitization

### Technical
- All code standards violations auto-fixed via PHPCBF
- Only acceptable warnings remain (DB queries, meta_key usage)


## [1.1.0] - 2026-01-29

### Changed
- Refactored plugin architecture for better maintainability
- Extracted dashboard widget rendering to separate template file
- Improved code organization with proper class structure
- Enhanced PHPDoc comments throughout codebase

### Added
- Admin_Hooks class for handling admin-related functionality
- Template file `admin-templates/dashboard-widget.php`
- Lazy loading pattern for class instantiation
- Comprehensive inline documentation

### Removed
- Commented-out legacy code blocks
- Unnecessary inline comments
- Redundant code duplication

### Technical
- All code now follows WordPress Coding Standards (PHPCS verified)
- Improved separation of concerns in class structure
- Better template system following WordPress best practices


## [1.0.0] - 2026-01-29

### Added
- Initial release of Export Woo Customer Data plugin
- Dashboard widget with two export buttons (All Users / Customers Only)
- CSV export with six default fields:
  - User ID
  - First Name
  - Last Name
  - Email Address
  - Billing Phone
  - Shipping Phone
- Pretty URL routing for CSV downloads:
  - `/wp-admin/exportwcd-reports/all-users.csv`
  - `/wp-admin/exportwcd-reports/customers-only.csv`
- Filter hook `export_woo_customer_data_fields` for customizable field mapping
- WooCommerce HPOS (High-Performance Order Storage) compatibility
- Automatic detection of HPOS vs legacy order storage
- UTF-8 BOM for Excel compatibility
- Admin-only access with `manage_options` capability check
- On-the-fly CSV generation (no file storage)
- Empty dataset handling with user-friendly error messages
- WordPress Coding Standards compliance
- PHPDoc documentation for all functions and classes
- Translation-ready with text domain `export-woo-customer-data`

### Security
- Capability checks on all export endpoints
- Report type validation against whitelist
- Secure direct file access prevention
- Proper output escaping and input sanitization
