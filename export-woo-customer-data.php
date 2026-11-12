<?php
/**
 * Plugin Name: Export Woo Customer Data
 * Plugin URI: https://headwall-hosting.com/plugins/export-woo-customer-data/
 * Description: Download your users or WooCommerce customers as a CSV file from a Dashboard widget, with names, email addresses and phone numbers.
 * Version: 1.1.2
 * Requires at least: 6.2
 * Requires PHP: 8.2
 * WC requires at least: 9.0
 * WC tested up to: 11.3
 * Author: Paul Faulkner
 * Author URI: https://headwall-hosting.com/a-web-guys-blog/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: export-woo-customer-data
 * Domain Path: /languages
 *
 * @package Export_Woo_Customer_Data
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

define( 'EXPORT_WOO_CUSTOMER_DATA_VERSION', '1.1.2' );
define( 'EXPORT_WOO_CUSTOMER_DATA_FILE', __FILE__ );
define( 'EXPORT_WOO_CUSTOMER_DATA_PATH', plugin_dir_path( __FILE__ ) );
define( 'EXPORT_WOO_CUSTOMER_DATA_URL', plugin_dir_url( __FILE__ ) );
define( 'EXPORT_WOO_CUSTOMER_DATA_BASENAME', plugin_basename( __FILE__ ) );

require_once EXPORT_WOO_CUSTOMER_DATA_PATH . 'constants.php';
require_once EXPORT_WOO_CUSTOMER_DATA_PATH . 'functions-private.php';

require_once EXPORT_WOO_CUSTOMER_DATA_PATH . 'includes/class-csv-exporter.php';
require_once EXPORT_WOO_CUSTOMER_DATA_PATH . 'includes/class-dashboard-widget.php';
require_once EXPORT_WOO_CUSTOMER_DATA_PATH . 'includes/class-plugin.php';

// GitHub auto-updates: admin, cron and WP-CLI only.
if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once EXPORT_WOO_CUSTOMER_DATA_PATH . 'includes/class-github-updater.php';
	new Export_Woo_Customer_Data\Github_Updater( EXPORT_WOO_CUSTOMER_DATA_FILE, Export_Woo_Customer_Data\UPDATER_GITHUB_REPO, EXPORT_WOO_CUSTOMER_DATA_VERSION, Export_Woo_Customer_Data\FILTER_UPDATER_ENABLED );
}

/**
 * Record the installed version on activation, and drop the 1.x rewrite rule.
 *
 * Must-use installs never fire this; Plugin::check_first_run() covers them.
 *
 * @since 2.0.0
 */
function export_woo_customer_data_activate(): void {
	add_option( Export_Woo_Customer_Data\OPTION_VERSION, EXPORT_WOO_CUSTOMER_DATA_VERSION, '', true );
	flush_rewrite_rules( false );
}
register_activation_hook( __FILE__, 'export_woo_customer_data_activate' );

/**
 * Initialise the plugin.
 *
 * @since 1.0.0
 */
function export_woo_customer_data_run(): void {
	$plugin = Export_Woo_Customer_Data\Plugin::instance();
	$plugin->run();
}
export_woo_customer_data_run();
