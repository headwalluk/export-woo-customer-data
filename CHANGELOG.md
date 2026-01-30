# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.2] - 2026-01-30

### Changed
- Dashboard widget description text updated for clarity ("Download customer data as a CSV file")
- Button styling simplified to use standard WordPress button class
- Buttons now display as block elements for improved layout

### Added
- Download icons (dashicons-download) to both export buttons

### Removed
- Primary/secondary button classes in favor of unified styling

---

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

---

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

---

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

---

## [Unreleased]

### Planned Features
- Scheduled/automated exports
- Email delivery of CSV reports
- Date range filtering
- Order status filtering for customer exports
- Additional default fields (address, registration date, etc.)
- Export progress indicator for large datasets
- WP-CLI command support

---

## Version History

- **1.0.0** - Initial public release (2026-01-29)

---

**Legend:**
- `Added` - New features
- `Changed` - Changes to existing functionality
- `Deprecated` - Soon-to-be removed features
- `Removed` - Removed features
- `Fixed` - Bug fixes
- `Security` - Security improvements
