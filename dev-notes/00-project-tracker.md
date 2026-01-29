# Project Tracker - Export Woo Customer Data

**Version:** 1.0.0
**Last Updated:** 29 January 2026
**Current Phase:** Milestone 1 (Plugin Foundation)
**Overall Progress:** 0%

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

## Active TODO Items

**Current Sprint: Milestone 1**

### Plugin Foundation
- [ ] Create main plugin file `export-woo-customer-data.php`
- [ ] Create `constants.php` for plugin constants
- [ ] Create `includes/class-plugin.php` (main plugin class)
- [ ] Set up proper namespace structure (`Export_Woo_Customer_Data\`)
- [ ] Declare WooCommerce HPOS compatibility
- [ ] Create `phpcs.xml` configuration
- [ ] Add plugin header with metadata

### Core Classes
- [ ] Create `includes/class-dashboard-widget.php`
- [ ] Create `includes/class-csv-exporter.php`
- [ ] Create `includes/class-query-handler.php`
- [ ] Create `includes/class-rewrite-handler.php`

### Dashboard Widget
- [ ] Register dashboard widget with WordPress
- [ ] Create template `admin-templates/dashboard-widget.php`
- [ ] Add "Export All Users" button
- [ ] Add "Export Customers Only" button
- [ ] Add nonce security
- [ ] Style widget to match WordPress admin

### CSV Export Logic
- [ ] Implement query for all users with meta data
- [ ] Implement query for customers only (users with orders)
- [ ] Create field mapping array (table + column)
- [ ] Apply `export_woo_customer_data_fields` filter hook
- [ ] Generate CSV headers
- [ ] Generate CSV rows
- [ ] Set proper HTTP headers for download
- [ ] Handle empty datasets gracefully

### URL Rewrite System
- [ ] Add rewrite rule for `/wp-admin/exportwcd-reports/customers.csv`
- [ ] Add rewrite tag for export type parameter
- [ ] Create query vars handler
- [ ] Flush rewrite rules on activation
- [ ] Implement route detection logic
- [ ] Add capability check before serving CSV

### Security & Validation
- [ ] Verify nonces on export requests
- [ ] Check `manage_options` capability
- [ ] Sanitize all inputs
- [ ] Escape all outputs
- [ ] Prevent direct file access in all PHP files

### Testing & Quality
- [ ] Run `phpcs` and fix violations
- [ ] Test export with no users
- [ ] Test export with users but no customers
- [ ] Test export with large datasets (100+ users)
- [ ] Test pretty URL routing
- [ ] Verify CSV format in Excel/Sheets
- [ ] Test filter hook extensibility

### Documentation
- [ ] Add inline PHPDoc comments to all functions
- [ ] Create README.md with installation instructions
- [ ] Document filter hook usage with examples
- [ ] Add activation/deactivation hooks documentation

---

## Milestones

### ✅ Milestone 0: Project Planning
**Status:** Complete  
**Completed:** 29 January 2026

- [x] Define requirements
- [x] Review WordPress coding standards
- [x] Create project tracker
- [x] Clarify data sources (billing_phone, shipping_phone)

---

### 🔄 Milestone 1: Plugin Foundation & Core Structure
**Status:** In Progress  
**Target:** TBD  
**Progress:** 0/28 tasks

**Deliverables:**
- Main plugin file with proper headers
- Core class structure with namespaces
- Constants file for magic values
- PHPCS configuration
- HPOS compatibility declaration

**Dependencies:** None

---

### 📋 Milestone 2: Dashboard Widget Implementation
**Status:** Not Started  
**Target:** TBD

**Deliverables:**
- Functional dashboard widget
- Two export buttons (styled)
- Admin template file
- Proper nonce security

**Dependencies:** Milestone 1

---

### 📋 Milestone 3: CSV Export Engine
**Status:** Not Started  
**Target:** TBD

**Deliverables:**
- User query logic (all users)
- Customer query logic (users with orders)
- Field mapping system
- CSV generation with proper headers
- Filter hook implementation
- Error handling for edge cases

**Dependencies:** Milestone 1

---

### 📋 Milestone 4: URL Rewrite System
**Status:** Not Started  
**Target:** TBD

**Deliverables:**
- Pretty URL routing functional
- Rewrite rules registered
- Query vars handling
- Activation hook for flush_rewrite_rules
- Route detection and CSV serving

**Dependencies:** Milestone 3

---

### 📋 Milestone 5: Security Hardening & Testing
**Status:** Not Started  
**Target:** TBD

**Deliverables:**
- All security checks implemented
- PHPCS validation passing
- Manual testing complete
- Documentation complete
- Ready for deployment

**Dependencies:** Milestones 2, 3, 4

---

## Technical Debt

*None yet - tracking as we build*

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

