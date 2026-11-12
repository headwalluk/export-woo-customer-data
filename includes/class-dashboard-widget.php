<?php
/**
 * Dashboard Widget
 *
 * @package Export_Woo_Customer_Data
 * @since 2.0.0
 */

namespace Export_Woo_Customer_Data;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * The Dashboard widget holding the export buttons.
 *
 * @since 2.0.0
 */
class Dashboard_Widget {

	/**
	 * Register the widget for users who can export.
	 *
	 * @since 2.0.0
	 */
	public function register(): void {
		if ( current_user_can( REQUIRED_CAPABILITY ) ) {
			wp_add_dashboard_widget(
				WIDGET_ID,
				get_widget_title(),
				array( $this, 'render' )
			);
		}
	}

	/**
	 * Render the widget.
	 *
	 * @since 2.0.0
	 */
	public function render(): void {
		$all_users_url = get_export_url( REPORT_TYPE_ALL_USERS );
		$customers_url = is_available_report_type( REPORT_TYPE_CUSTOMERS ) ? get_export_url( REPORT_TYPE_CUSTOMERS ) : '';

		include EXPORT_WOO_CUSTOMER_DATA_PATH . 'views/dashboard-widget.php';
	}
}
