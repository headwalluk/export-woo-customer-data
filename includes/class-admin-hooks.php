<?php
/**
 * Admin hooks handler.
 *
 * @package Export_Woo_Customer_Data
 */

namespace Export_Woo_Customer_Data;

defined( 'ABSPATH' ) || die();

/**
 * Handles all admin-related hooks and functionality.
 *
 * @since 1.0.0
 */
class Admin_Hooks {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( string $version ) {
		$this->version = $version;
	}

	/**
	 * Register dashboard widget.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_dashboard_widget(): void {
		if ( current_user_can( REQUIRED_CAPABILITY ) ) {
			wp_add_dashboard_widget( WIDGET_ID, __( 'Export Customer Data', 'export-woo-customer-data' ), array( $this, 'render_dashboard_widget' ) );
		}
	}

	/**
	 * Render dashboard widget content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_dashboard_widget(): void {
		include EXPORT_WCD_ADMIN_TEMPLATES_DIR . 'dashboard-widget.php';
	}
}
