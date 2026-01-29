<?php
/**
 * Plugin Name: Export Woo Customer Data
 * Plugin URI: https://headwall.tech
 * Description: Export WooCommerce customer data to CSV with customizable fields via dashboard widget.
 * Version: 1.0.0
 * Author: Headwall Tech
 * Author URI: https://headwall.tech
 * Text Domain: export-woo-customer-data
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * WC requires at least: 7.0
 * WC tested up to: 9.0
 *
 * @package Export_Woo_Customer_Data
 */

defined( 'ABSPATH' ) || die();

define( 'EXPORT_WCD_VERSION', '1.0.0' );
define( 'EXPORT_WCD_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXPORT_WCD_URL', plugin_dir_url( __FILE__ ) );
define( 'EXPORT_WCD_BASENAME', plugin_basename( __FILE__ ) );

// Declare HPOS compatibility.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				__FILE__,
				true
			);
		}
	}
);

// Load plugin files.
require_once EXPORT_WCD_DIR . 'constants.php';
require_once EXPORT_WCD_DIR . 'functions-private.php';
require_once EXPORT_WCD_DIR . 'includes/class-plugin.php';
require_once EXPORT_WCD_DIR . 'includes/class-admin-hooks.php';
require_once EXPORT_WCD_DIR . 'includes/class-csv-exporter.php';

/**
 * Initialize and run the plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function export_wcd_run(): void {
	global $export_wcd_plugin;
	$export_wcd_plugin = new Export_Woo_Customer_Data\Plugin();
	$export_wcd_plugin->run();
}
export_wcd_run();
