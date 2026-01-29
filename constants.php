<?php
/**
 * Plugin constants.
 *
 * @package Export_Woo_Customer_Data
 */

namespace Export_Woo_Customer_Data;

defined( 'ABSPATH' ) || die();

// Dashboard widget ID.
const WIDGET_ID = 'export_woo_customer_data_widget';

// Nonce actions.
const NONCE_EXPORT_ACTION = 'export_woo_customer_data_export';

// Rewrite query var.
const QUERY_VAR_REPORT = 'exportwcd_report';

// Report types.
const REPORT_TYPE_ALL_USERS = 'all-users';
const REPORT_TYPE_CUSTOMERS = 'customers-only';

// CSV filename patterns.
const CSV_FILENAME_ALL_USERS = 'all-users.csv';
const CSV_FILENAME_CUSTOMERS = 'customers-only.csv';

// Capability required.
const REQUIRED_CAPABILITY = 'manage_options';

// Default field mapping.
const DEFAULT_FIELDS = array(
	'user_id'        => array(
		'table'  => 'users',
		'column' => 'ID',
	),
	'first_name'     => array(
		'table'    => 'usermeta',
		'meta_key' => 'first_name',
	),
	'last_name'      => array(
		'table'    => 'usermeta',
		'meta_key' => 'last_name',
	),
	'email'          => array(
		'table'  => 'users',
		'column' => 'user_email',
	),
	'billing_phone'  => array(
		'table'    => 'usermeta',
		'meta_key' => 'billing_phone',
	),
	'shipping_phone' => array(
		'table'    => 'usermeta',
		'meta_key' => 'shipping_phone',
	),
);

// Filter hook name.
const FILTER_FIELDS = 'export_woo_customer_data_fields';
