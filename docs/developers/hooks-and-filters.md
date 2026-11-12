# Hooks and filters

The plugin's public extension surface. Anything not listed here (functions, classes, constants)
is internal and may change in any release.

## `export_woo_customer_data_fields`

Filter. The columns in both exports, in order, and where each one's value comes from.

```php
apply_filters( 'export_woo_customer_data_fields', array $fields );
```

`$fields` maps each CSV column name to its source. The column name is the array key, and is
used as-is in the header row. There are two kinds of source:

```php
// A column of the users table.
'email' => array(
	'table'  => 'users',
	'column' => 'user_email',
),

// A user meta value, read with get_user_meta( $user_id, $meta_key, true ).
'billing_phone' => array(
	'table'    => 'usermeta',
	'meta_key' => 'billing_phone',
),
```

The default value:

| Column | Source |
|--------|--------|
| `user_id` | `users`, `ID` |
| `first_name` | `usermeta`, `first_name` |
| `last_name` | `usermeta`, `last_name` |
| `email` | `users`, `user_email` |
| `billing_phone` | `usermeta`, `billing_phone` |
| `shipping_phone` | `usermeta`, `shipping_phone` |

Rules:

- **Users table columns** are limited to `ID`, `user_login`, `user_nicename`, `user_email`,
  `user_url`, `user_registered` and `display_name`. Password hashes and reset keys can't be
  exported
- **Meta values** are read through `get_user_meta()`, so `get_user_metadata` filters apply. A
  value that is an array or an object (serialised meta) becomes an empty cell
- An entry that breaks these rules, or has a non-string key, is left out and logged to the PHP
  error log. If nothing valid is left, or the filter returns something other than an array, the
  defaults are used
- Every cell goes through the formula guard described in
  [How it works](../how-it-works.md#the-file)

The filter runs once per download, before any user is read. The same columns are used for both
exports.

Since 1.0.0. 2.0.0 added the column allow-list and the validation.

## `export_woo_customer_data_updater_enabled`

Filter. Whether the plugin checks GitHub for updates.

```php
apply_filters( 'export_woo_customer_data_updater_enabled', bool $enabled );
```

Return `false` to stop the checks for this plugin only, for example on a site whose updates you
deploy yourself:

```php
add_filter( 'export_woo_customer_data_updater_enabled', '__return_false' );
```

Since 2.0.0.
