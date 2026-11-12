# Extending

Recipes for the hooks in [hooks-and-filters.md](hooks-and-filters.md). Put them in a small
must-use plugin or a site-specific plugin.

## Add the billing address

WooCommerce keeps each customer's saved addresses in user meta, with keys such as
`billing_address_1` and `billing_postcode`. This adds the billing address after the default
columns:

```php
/**
 * Add the customer's saved billing address to the export.
 *
 * @param mixed $fields Column name => source.
 * @return mixed The columns, with the billing address added.
 */
function example_ewcd_add_billing_address( $fields ) {
	if ( is_array( $fields ) ) {
		$address_keys = array(
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
		);

		foreach ( $address_keys as $meta_key ) {
			$fields[ $meta_key ] = array(
				'table'    => 'usermeta',
				'meta_key' => $meta_key,
			);
		}
	}

	return $fields;
}
add_filter( 'export_woo_customer_data_fields', 'example_ewcd_add_billing_address' );
```

These are the addresses saved to the customer's account, which WooCommerce updates each time
they check out while logged in. They aren't read from the orders themselves.

## Add the username and registration date, drop the shipping phone

```php
/**
 * Add the login name and registration date, and remove the shipping phone.
 *
 * @param mixed $fields Column name => source.
 * @return mixed The changed columns.
 */
function example_ewcd_account_columns( $fields ) {
	if ( is_array( $fields ) ) {
		unset( $fields['shipping_phone'] );

		$fields['username']   = array(
			'table'  => 'users',
			'column' => 'user_login',
		);
		$fields['registered'] = array(
			'table'  => 'users',
			'column' => 'user_registered',
		);
	}

	return $fields;
}
add_filter( 'export_woo_customer_data_fields', 'example_ewcd_account_columns' );
```

`user_registered` is stored in UTC, as `YYYY-MM-DD HH:MM:SS`.

## Put a column first

The columns follow the array's order. To move `email` to the front, rebuild the array:

```php
/**
 * Make the email address the first column.
 *
 * @param mixed $fields Column name => source.
 * @return mixed The reordered columns.
 */
function example_ewcd_email_first( $fields ) {
	if ( is_array( $fields ) && isset( $fields['email'] ) ) {
		$fields = array( 'email' => $fields['email'] ) + $fields;
	}

	return $fields;
}
add_filter( 'export_woo_customer_data_fields', 'example_ewcd_email_first' );
```

## Rename a column

The header row uses the array keys as they are, so rename a key to rename a column, for an
import tool that expects particular headings:

```php
/**
 * Use the headings a mailing tool expects.
 *
 * @param mixed $fields Column name => source.
 * @return mixed The columns, renamed.
 */
function example_ewcd_mailing_headings( $fields ) {
	$renamed  = array();
	$headings = array(
		'email'      => 'Email Address',
		'first_name' => 'First Name',
		'last_name'  => 'Last Name',
	);

	if ( is_array( $fields ) ) {
		foreach ( $fields as $column_name => $source ) {
			$renamed[ $headings[ $column_name ] ?? $column_name ] = $source;
		}
	} else {
		$renamed = $fields;
	}

	return $renamed;
}
add_filter( 'export_woo_customer_data_fields', 'example_ewcd_mailing_headings' );
```
