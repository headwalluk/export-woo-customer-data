<?php
/**
 * Plugin Constants
 *
 * @package Export_Woo_Customer_Data
 * @since 1.0.0
 */

namespace Export_Woo_Customer_Data;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Options.
 *
 * @since 2.0.0
 */
const OPTION_VERSION = 'export_woo_customer_data_version';

/**
 * Prefix shared by every option the plugin stores.
 *
 * @since 2.0.0
 */
const OPTION_PREFIX = 'export_woo_customer_data_';

/**
 * Updater filter, passed to Github_Updater. The plugin's other hooks are named
 * literally at each call, so phpcs can check their prefix.
 *
 * @since 2.0.0
 */
const FILTER_UPDATER_ENABLED = 'export_woo_customer_data_updater_enabled';

/**
 * GitHub updater.
 *
 * @since 2.0.0
 */
const UPDATER_GITHUB_REPO = 'headwalluk/export-woo-customer-data';

/**
 * Dashboard widget ID. WordPress stores each user's widget order and hidden
 * widgets under this ID.
 *
 * @since 1.0.0
 */
const WIDGET_ID = 'export_woo_customer_data_widget';

/**
 * Capability needed to see the widget and download an export.
 *
 * @since 1.0.0
 */
const REQUIRED_CAPABILITY = 'manage_options';

/**
 * Download request: admin-post.php?action=ADMIN_POST_ACTION&report=...&_wpnonce=...
 *
 * @since 2.0.0
 */
const ADMIN_POST_ACTION   = 'export_woo_customer_data_export';
const QUERY_ARG_REPORT    = 'report';
const NONCE_EXPORT_ACTION = 'export_woo_customer_data_export';

/**
 * Report types, and the filename each downloads as.
 *
 * @since 1.0.0
 */
const REPORT_TYPE_ALL_USERS = 'all-users';
const REPORT_TYPE_CUSTOMERS = 'customers-only';

const REPORT_FILENAMES = array(
	REPORT_TYPE_ALL_USERS => 'all-users.csv',
	REPORT_TYPE_CUSTOMERS => 'customers-only.csv',
);

/**
 * The 1.x download URLs, /wp-admin/exportwcd-reports/<report>.csv. They had no
 * nonce, so they now redirect to the Dashboard instead of exporting.
 *
 * @since 2.0.0
 */
const LEGACY_URL_PATTERN = '#/wp-admin/exportwcd-reports/[^/]+\.csv/?$#i';

/**
 * Field mapping sources.
 *
 * @since 2.0.0
 */
const FIELD_TABLE_USERS    = 'users';
const FIELD_TABLE_USERMETA = 'usermeta';

/**
 * Columns of the users table a field can export. user_pass, user_activation_key
 * and user_status are deliberately absent.
 *
 * @since 2.0.0
 */
const EXPORTABLE_USER_COLUMNS = array(
	'ID',
	'user_login',
	'user_nicename',
	'user_email',
	'user_url',
	'user_registered',
	'display_name',
);

/**
 * Default field mapping: CSV column => source. Passed through the
 * export_woo_customer_data_fields filter.
 *
 * @since 1.0.0
 */
const DEFAULT_FIELDS = array(
	'user_id'        => array(
		'table'  => FIELD_TABLE_USERS,
		'column' => 'ID',
	),
	'first_name'     => array(
		'table'    => FIELD_TABLE_USERMETA,
		'meta_key' => 'first_name', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Field-mapping key, not a query argument.
	),
	'last_name'      => array(
		'table'    => FIELD_TABLE_USERMETA,
		'meta_key' => 'last_name', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Field-mapping key, not a query argument.
	),
	'email'          => array(
		'table'  => FIELD_TABLE_USERS,
		'column' => 'user_email',
	),
	'billing_phone'  => array(
		'table'    => FIELD_TABLE_USERMETA,
		'meta_key' => 'billing_phone', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Field-mapping key, not a query argument.
	),
	'shipping_phone' => array(
		'table'    => FIELD_TABLE_USERMETA,
		'meta_key' => 'shipping_phone', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Field-mapping key, not a query argument.
	),
);

/**
 * Users read per query, so a large site is exported in pages rather than all
 * at once.
 *
 * @since 2.0.0
 */
const USER_BATCH_SIZE = 500;

/**
 * Order statuses that don't count as a placed order: the Checkout block's
 * draft, and WordPress's own draft and trash states.
 *
 * @since 2.0.0
 */
const NON_ORDER_STATUSES = array(
	'wc-checkout-draft',
	'auto-draft',
	'draft',
	'trash',
);

/**
 * A cell starting with one of these is read as a formula by Excel and other
 * spreadsheets, so it is written with a leading apostrophe.
 *
 * @since 2.0.0
 */
const CSV_FORMULA_TRIGGERS = array( '=', '+', '-', '@', "\t", "\r" );

/**
 * A value made only of these characters is a phone number or a plain number,
 * not a formula, even when it starts with + or -.
 *
 * @since 2.0.0
 */
const CSV_NUMERIC_PATTERN = '/^[+\-]?[0-9 ().\-]+$/D';
