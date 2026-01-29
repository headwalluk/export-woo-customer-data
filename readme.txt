=== Export Woo Customer Data ===
Contributors: headwalltech
Tags: woocommerce, export, csv, customers, users
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Export WooCommerce customer data to CSV with customizable fields via a simple dashboard widget.

== Description ==

Export Woo Customer Data is a lightweight utility plugin that provides a simple dashboard widget for exporting customer data to CSV format.

**Features:**

* Dashboard widget with one-click export buttons
* Export all WordPress users to CSV
* Export WooCommerce customers only (users with orders)
* Pretty URLs for CSV downloads
* Customizable field mapping via filter hooks
* HPOS (High-Performance Order Storage) compatible
* Excel-friendly CSV formatting with UTF-8 BOM

**Default Export Fields:**

* User ID
* First Name
* Last Name
* Email Address
* Billing Phone
* Shipping Phone

**Developer-Friendly:**

The field mapping is fully customizable using the `export_woo_customer_data_fields` filter hook, allowing you to add custom fields or modify existing ones.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/export-woo-customer-data/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the WordPress Dashboard to see the "Export Customer Data" widget
4. Click either "Export All Users" or "Export Customers Only" to download CSV

== Frequently Asked Questions ==

= Who can access the export functionality? =

Only users with the `manage_options` capability (typically administrators) can export customer data.

= What's the difference between "All Users" and "Customers Only"? =

"All Users" exports every WordPress user account. "Customers Only" exports only users who have placed at least one WooCommerce order.

= Does this work with HPOS? =

Yes! The plugin fully supports WooCommerce High-Performance Order Storage (HPOS) and will automatically use the appropriate data source.

= Can I customize the export fields? =

Yes! Use the `export_woo_customer_data_fields` filter to modify the field mapping. See the documentation for examples.

= Where are the CSV files stored? =

CSV files are generated on-demand and streamed directly to the browser. No files are stored on the server.

== Screenshots ==

1. Dashboard widget with export buttons
2. Example CSV output in Excel

== Changelog ==

= 1.1.1 - 2026-01-29 =
* Performance: Skip total count query for better performance with large user datasets
* Performance: Added cache suspension to prevent memory issues
* Security: Added filename sanitization to CSV headers
* Code quality: Fixed all PHPCS spacing violations

= 1.1.0 - 2026-01-29 =
* Refactored plugin architecture for better code organization
* Extracted dashboard widget to separate template file
* Improved PHPDoc documentation throughout
* Enhanced code standards compliance
* Added Admin_Hooks class for admin functionality
* Implemented lazy loading for better performance
* Cleaned up legacy commented code

= 1.0.0 - 2026-01-29 =
* Initial release
* Dashboard widget with export buttons
* Export all users to CSV
* Export WooCommerce customers only
* Customizable field mapping via filter hook
* HPOS compatibility
* Pretty URL routing for downloads
* UTF-8 BOM for Excel compatibility

== Upgrade Notice ==

= 1.0.0 =
Initial release of Export Woo Customer Data plugin.

== Developer Documentation ==

**Customizing Export Fields:**

```php
add_filter( 'export_woo_customer_data_fields', function( $fields ) {
    // Add custom field
    $fields['phone'] = array(
        'table'    => 'usermeta',
        'meta_key' => 'phone',
    );
    
    // Add user login
    $fields['username'] = array(
        'table'  => 'users',
        'column' => 'user_login',
    );
    
    return $fields;
} );
```

**Field Configuration:**

Each field requires either:
* `table` = 'users' and `column` = user table column name
* `table` = 'usermeta' and `meta_key` = meta key name

== Privacy Policy ==

This plugin exports user data including email addresses and phone numbers. Ensure you have appropriate permissions and comply with GDPR and other privacy regulations when exporting customer data.
