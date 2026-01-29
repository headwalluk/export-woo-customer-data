<?php
/**
 * Private/internal helper functions.
 *
 * @package Export_Woo_Customer_Data
 */

namespace Export_Woo_Customer_Data;

defined( 'ABSPATH' ) || die();

/**
 * Get the global plugin instance.
 *
 * @since 1.0.0
 *
 * @return Plugin Plugin instance.
 */
function get_plugin_instance(): Plugin {
	global $export_wcd_plugin;
	return $export_wcd_plugin;
}

/**
 * Check if WooCommerce is active.
 *
 * @since 1.0.0
 *
 * @return bool True if WooCommerce is active.
 */
function is_woocommerce_active(): bool {
	return function_exists( 'WC' );
}
