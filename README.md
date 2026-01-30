# Export Woo Customer Data

![Version](https://img.shields.io/badge/version-1.1.2-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-9.0%2B-96588a.svg)
![License](https://img.shields.io/badge/license-GPLv2-green.svg)
![HPOS Compatible](https://img.shields.io/badge/HPOS-Compatible-green.svg)

**Version:** 1.1.2  
**Requires:** WordPress 6.0+, PHP 8.0+, WooCommerce 9.0+  
**License:** GPLv2 or later

A lightweight WordPress/WooCommerce utility plugin to export customer data via CSV with customizable fields.

---

## Features

- ✅ **Dashboard Widget** - One-click export buttons on WordPress dashboard
- ✅ **Two Export Modes** - Export all users or WooCommerce customers only
- ✅ **Pretty URLs** - Clean download links like `/wp-admin/exportwcd-reports/customers-only.csv`
- ✅ **HPOS Compatible** - Works with WooCommerce High-Performance Order Storage
- ✅ **Customizable Fields** - Extensible via filter hooks
- ✅ **Excel-Friendly** - UTF-8 BOM for proper character encoding
- ✅ **Secure** - Admin-only access with capability checks

---

## Installation

### Via WordPress Admin

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate via **Plugins** → **Installed Plugins**
3. Navigate to **Dashboard** to see the widget

### Via WP-CLI

```bash
wp plugin activate export-woo-customer-data
```

---

## Usage

### Basic Export

1. Go to the WordPress Dashboard
2. Find the **Export Customer Data** widget
3. Click one of two buttons:
   - **Export All Users** - All WordPress user accounts
   - **Export Customers Only** - Users who have placed orders (requires WooCommerce)
4. CSV file downloads automatically

### Default Export Fields

| Field           | Source                  |
|-----------------|-------------------------|
| User ID         | `wp_users.ID`           |
| First Name      | `wp_usermeta.first_name`|
| Last Name       | `wp_usermeta.last_name` |
| Email           | `wp_users.user_email`   |
| Billing Phone   | `wp_usermeta.billing_phone` |
| Shipping Phone  | `wp_usermeta.shipping_phone` |

---

## Developer Documentation

### Customizing Export Fields

Use the `export_woo_customer_data_fields` filter to modify field mapping:

```php
add_filter( 'export_woo_customer_data_fields', function( $fields ) {
    // Add user login
    $fields['username'] = array(
        'table'  => 'users',
        'column' => 'user_login',
    );
    
    // Add custom user meta
    $fields['phone'] = array(
        'table'    => 'usermeta',
        'meta_key' => 'phone',
    );
    
    // Remove a field
    unset( $fields['shipping_phone'] );
    
    return $fields;
} );
```

### Field Configuration Structure

Each field requires:

**For `wp_users` table:**
```php
array(
    'table'  => 'users',
    'column' => 'column_name',  // e.g., 'user_email', 'user_login', 'ID'
)
```

**For `wp_usermeta` table:**
```php
array(
    'table'    => 'usermeta',
    'meta_key' => 'meta_key_name',  // e.g., 'first_name', 'billing_phone'
)
```

### Pretty URL Structure

Export URLs follow this pattern:

- All Users: `/wp-admin/exportwcd-reports/all-users.csv`
- Customers Only: `/wp-admin/exportwcd-reports/customers-only.csv`

These URLs:
- Require `manage_options` capability
- Generate CSV on-the-fly (no file storage)
- Stream directly to browser
- Trigger download automatically

---

## Security

- **Capability Check:** Only users with `manage_options` (admins) can export
- **HPOS Compatible:** Uses WooCommerce CRUD methods for order data
- **No File Storage:** CSV generated in-memory and streamed
- **Input Validation:** Report types validated against whitelist
- **WordPress Standards:** Follows WordPress Security Best Practices

---

## HPOS Compatibility

The plugin automatically detects and supports WooCommerce High-Performance Order Storage (HPOS):

- **HPOS Enabled:** Queries `wp_wc_orders` table
- **Legacy Mode:** Queries `wp_postmeta` for `_customer_user`

Compatibility is declared via `FeaturesUtil::declare_compatibility()`.

---

## File Structure

```
export-woo-customer-data/
├── export-woo-customer-data.php  # Main plugin file
├── constants.php                 # Plugin constants
├── functions-private.php         # Helper functions
├── includes/
│   ├── class-plugin.php          # Main plugin class
│   ├── class-admin-hooks.php     # Dashboard widget
│   └── class-csv-exporter.php    # CSV generation logic
├── dev-notes/                    # Development documentation
├── README.md                     # This file
├── readme.txt                    # WordPress.org readme
└── CHANGELOG.md                  # Version history
```

---

## Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 8.0 or higher
- **WooCommerce:** 7.0+ (optional, required for "Customers Only" export)

---

## Privacy & Compliance

This plugin exports sensitive user data including:
- Email addresses
- Phone numbers
- User IDs

**Important:** Ensure you have:
- User consent for data export (GDPR compliance)
- Appropriate data handling policies
- Secure download/storage of exported files

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

---

## Support

For issues, questions, or feature requests:

- **Website:** [https://headwall.tech](https://headwall.tech)
- **Plugin Repository:** GitHub (link TBD)

---

## License

This plugin is licensed under GPLv2 or later.

```
Copyright (C) 2026 Headwall Tech

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

---

## Credits

**Developed by:** [Headwall Tech](https://headwall.tech)  
**Version:** 1.0.0  
**Release Date:** January 29, 2026
