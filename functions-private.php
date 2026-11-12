<?php
/**
 * Private helper functions. Internal to the plugin: sites should use the hooks
 * in docs/developers/hooks-and-filters.md instead.
 *
 * @package Export_Woo_Customer_Data
 * @since 1.0.0
 */

namespace Export_Woo_Customer_Data;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Check whether WooCommerce is loaded.
 *
 * @since 1.0.0
 *
 * @return bool True when WooCommerce is active.
 */
function is_woocommerce_active(): bool {
	return function_exists( 'WC' );
}

/**
 * Get the Dashboard widget's title, translated.
 *
 * Shared with the expired-link message, which names the widget.
 *
 * @since 2.0.0
 *
 * @return string Widget title.
 */
function get_widget_title(): string {
	return __( 'Export Customer Data', 'export-woo-customer-data' );
}

/**
 * Check whether a report type can be exported on this site.
 *
 * @since 2.0.0
 *
 * @param string $report_type Report type.
 * @return bool True for a known report whose requirements are met.
 */
function is_available_report_type( string $report_type ): bool {
	$is_available = false;

	if ( REPORT_TYPE_ALL_USERS === $report_type ) {
		$is_available = true;
	} elseif ( REPORT_TYPE_CUSTOMERS === $report_type ) {
		$is_available = is_woocommerce_active();
	} else {
		// Unknown report type.
	}

	return $is_available;
}

/**
 * Build the nonced download URL for a report.
 *
 * @since 2.0.0
 *
 * @param string $report_type Report type.
 * @return string Download URL, unescaped.
 */
function get_export_url( string $report_type ): string {
	// Not wp_nonce_url(): it returns the URL already HTML-escaped.
	return add_query_arg(
		array(
			'action'         => ADMIN_POST_ACTION,
			QUERY_ARG_REPORT => $report_type,
			'_wpnonce'       => wp_create_nonce( NONCE_EXPORT_ACTION ),
		),
		admin_url( 'admin-post.php' )
	);
}

/**
 * Check the nonce on an admin action link.
 *
 * A namespaced function so WordPress.Security.NonceVerification, which only
 * recognises a nonce check made through a function call, can be told about it
 * in phpcs.xml.
 *
 * @since 2.0.0
 *
 * @param string $action Nonce action name.
 * @return bool True when a valid nonce for $action was supplied.
 */
function verify_admin_action_nonce( string $action ): bool {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- This is the nonce check; sanitised on the same line.
	$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

	return '' !== $nonce && false !== wp_verify_nonce( $nonce, $action );
}

/**
 * Make a value safe to write as one CSV cell.
 *
 * Arrays and objects become an empty cell. A value that a spreadsheet would
 * run as a formula gets a leading apostrophe, unless it is only a number or a
 * phone number.
 *
 * @since 2.0.0
 *
 * @param mixed $value Raw value from the users table or user meta.
 * @return string Cell text.
 */
function to_csv_cell( mixed $value ): string {
	$cell = '';

	if ( is_scalar( $value ) ) {
		$cell = (string) $value;
	}

	if ( '' !== $cell && in_array( $cell[0], CSV_FORMULA_TRIGGERS, true ) && 1 !== preg_match( CSV_NUMERIC_PATTERN, $cell ) ) {
		$cell = "'" . $cell;
	}

	return $cell;
}

/**
 * Log a genuine failure. Always logs, whatever WP_DEBUG says.
 *
 * Never pass user data: it is personal data.
 *
 * @since 2.0.0
 *
 * @param string $message Message to log.
 */
function log_error( string $message ): void {
	error_log( 'Export Woo Customer Data: ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Errors must reach the server log without WP_DEBUG.
}

/**
 * Log routine flow tracing. Only logs when WP_DEBUG is on.
 *
 * Never pass user data: it is personal data.
 *
 * @since 2.0.0
 *
 * @param string $message Message to log.
 */
function log_debug( string $message ): void {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'Export Woo Customer Data: ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Flow tracing, only under WP_DEBUG.
	}
}
