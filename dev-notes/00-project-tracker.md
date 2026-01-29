# Project Tracker - Export Woo Customer Data

**Version:** 1.1.0
**Last Updated:** 29 January 2026
**Current Phase:** Complete - Production Ready
**Overall Progress:** 100%

---

## Overview

A lightweight WordPress/WooCommerce utility plugin to export customer data as CSV files.

**Core Features:**
- Dashboard widget with two export buttons (All Users | Customers Only)
- CSV export with: First Name, Last Name, Email, Billing Phone, Shipping Phone
- Pretty URL routing: `/wp-admin/exportwcd-reports/customers.csv`
- Filterable field mapping for extensibility
- On-the-fly CSV generation
- Admin-only access (`manage_options`)

---

## Completed Items ✅

All milestones and tasks have been completed successfully. The plugin is production-ready.

### Plugin Foundation
- [x] Create main plugin file `export-woo-customer-data.php`
- [x] Create `constants.php` for plugin constants
- [x] Create `includes/class-plugin.php` (main plugin class)
- [x] Set up proper namespace structure (`Export_Woo_Customer_Data\`)
- [x] Declare WooCommerce HPOS compatibility
- [x] Create `phpcs.xml` configuration
- [x] Add plugin header with metadata

### Core Classes
- [x] Create `includes/class-admin-hooks.php` (handles admin functionality)
- [x] Create `includes/class-csv-exporter.php`
- [x] Integrate URL rewrite and query handling in Plugin class
- [x] Implement lazy loading pattern for class instances

### Dashboard Widget
- [x] Register dashboard widget with WordPress
- [x] Create template `admin-templates/dashboard-widget.php`
- [x] Add "Export All Users" button
- [x] Add "Export Customers Only" button
- [x] Style widget to match WordPress admin (uses native classes)
- [x] Conditional display based on WooCommerce availability

### CSV Export Logic
- [x] Implement query for all users with meta data
- [x] Implement query for customers only (users with orders)
- [x] Create field mapping array (table + column)
- [x] Apply `export_woo_customer_data_fields` filter hook
- [x] Generate CSV headers
- [x] Generate CSV rows
- [x] Set proper HTTP headers for download
- [x] Handle empty datasets gracefully
- [x] Add UTF-8 BOM for Excel compatibility

### URL Rewrite System
- [x] Add rewrite rule for `/wp-admin/exportwcd-reports/{type}.csv`
- [x] Add query var registration
- [x] Create query vars handler
- [x] Flush rewrite rules on activation/deactivation
- [x] Implement route detection logic
- [x] Add capability check before serving CSV

### Security & Validation
- [x] Check `manage_options` capability
- [x] Sanitize all inputs
- [x] Escape all outputs
- [x] Prevent direct file access in all PHP files
- [x] Validate report type against whitelist
- [x] Proper error handling with user-friendly messages

### Testing & Quality
- [x] Run `phpcs` and fix violations (all passing)
- [x] Test empty dataset handling
- [x] Verify HPOS compatibility implementation
- [x] Test filter hook extensibility
- [x] Code refactoring and cleanup (v1.1.0)

### Documentation
- [x] Add inline PHPDoc comments to all functions
- [x] Create README.md with installation instructions
- [x] Document filter hook usage with examples
- [x] Add activation/deactivation hooks documentation
- [x] Create readme.txt for WordPress plugin directory
- [x] Maintain CHANGELOG.md with version history
- [x] Add useful badges to README.md

---

## Active TODO Items

**No active items - all features complete**

---

## Milestones

### ✅ Milestone 1: Plugin Foundation & Core Structure
**Status:** Complete ✅  
**Completed:** 29 January 2026  
**Progress:** 7/7 tasks

**Deliverables:**
- ✅ Main plugin file with proper headers
- ✅ Core class structure with namespaces
- ✅ Constants file for magic values
- ✅ PHPCS configuration
- ✅ HPOS compatibility declaration

**Dependencies:** None

---

### ✅ Milestone 2: Dashboard Widget Implementation
**Status:** Complete ✅  
**Completed:** 29 January 2026

**Deliverables:**
- ✅ Functional dashboard widget
- ✅ Two export buttons (styled)
- ✅ Admin template file
- ✅ Conditional WooCommerce detection

**Dependencies:** Milestone 1

---

### ✅ Milestone 3: CSV Export Engine
**Status:** Complete ✅  
**Completed:** 29 January 2026

**Deliverables:**
- ✅ User query logic (all users)
- ✅ Customer query logic (users with orders)
- ✅ Field mapping system
- ✅ CSV generation with proper headers
- ✅ Filter hook implementation
- ✅ Error handling for edge cases
- ✅ UTF-8 BOM for Excel compatibility

**Dependencies:** Milestone 1

---

### ✅ Milestone 4: URL Rewrite System
**Status:** Complete ✅  
**Completed:** 29 January 2026

**Deliverables:**
- ✅ Pretty URL routing functional
- ✅ Rewrite rules registered
- ✅ Query vars handling
- ✅ Activation hook for flush_rewrite_rules
- ✅ Route detection and CSV serving

**Dependencies:** Milestone 3

---

### ✅ Milestone 5: Security Hardening & Testing
**Status:** Complete ✅  
**Completed:** 29 January 2026

**Deliverables:**
- ✅ All security checks implemented
- ✅ PHPCS validation passing
- ✅ Manual testing complete
- ✅ Documentation complete
- ✅ Ready for deployment

**Dependencies:** Milestones 2, 3, 4

---

### ✅ Milestone 6: Code Refactoring & v1.1.0 Release
**Status:** Complete ✅  
**Completed:** 29 January 2026

**Deliverables:**
- ✅ Extracted Admin_Hooks class
- ✅ Separated dashboard widget template
- ✅ Enhanced PHPDoc documentation
- ✅ Removed commented-out code
- ✅ Improved code organization
- ✅ Updated all documentation
- ✅ Added README badges

**Dependencies:** Milestone 5

---

## Future Enhancements (Backlog)

These features are planned for future releases:

### Potential v1.2.0+
- [ ] Scheduled/automated exports via WP-Cron
- [ ] Email delivery of CSV reports
- [ ] Date range filtering for exports
- [ ] Order status filtering for customer exports
- [ ] Custom column ordering in CSV
- [ ] Additional WooCommerce customer fields (total spent, order count, etc.)
- [ ] Settings page for default field configuration
- [ ] Export history/logging
- [ ] Background processing for large datasets

---

## Technical Debt

**None** - All legacy code and commented blocks have been removed in v1.1.0.

---

## Notes for Development

### Data Sources

**wp_users table:**
- `user_email` → Email Address
- `display_name` OR first_name from meta → First Name
- `user_login` (fallback if needed)

**wp_usermeta table:**
- `first_name` → First Name
- `last_name` → Last Name
- `billing_phone` → Billing Phone
- `shipping_phone` → Shipping Phone

### Customer Detection Logic

Users with at least one order = check for records in `wp_posts` where:
- `post_type = 'shop_order'`
- `post_author = user_id` OR use WC_Order methods
- Consider order statuses (completed, processing, on-hold, etc.)

**Alternative:** Query `wp_postmeta` for `_customer_user` meta key

### Filter Hook Design

```php
$fields = apply_filters( 'export_woo_customer_data_fields', array(
    'first_name'     => array( 'table' => 'usermeta', 'meta_key' => 'first_name' ),
    'last_name'      => array( 'table' => 'usermeta', 'meta_key' => 'last_name' ),
    'email'          => array( 'table' => 'users', 'column' => 'user_email' ),
    'billing_phone'  => array( 'table' => 'usermeta', 'meta_key' => 'billing_phone' ),
    'shipping_phone' => array( 'table' => 'usermeta', 'meta_key' => 'shipping_phone' ),
) );
```

### Pretty URL Structure

Pattern: `/wp-admin/exportwcd-reports/{type}.csv`

Where `{type}` = `all-users` or `customers-only`

**Rewrite Rule:**
```php
add_rewrite_rule(
    '^wp-admin/exportwcd-reports/([^/]+)\.csv$',
    'index.php?exportwcd_report=$matches[1]',
    'top'
);
```

### Code Standards Reminders

- ✅ Use namespaces for all classes
- ✅ Type hints for all parameters and return types
- ✅ Single-Entry Single-Exit (SESE) pattern
- ✅ All magic values in constants.php
- ✅ No inline HTML in PHP functions (use printf/echo)
- ✅ Security: sanitize input, escape output, verify nonces
- ✅ Run phpcs before every commit

