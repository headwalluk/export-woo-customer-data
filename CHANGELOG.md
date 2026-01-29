# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
