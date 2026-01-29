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

		// Register dashboard widget.
		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
	}

	/**
	 * Register dashboard widget.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_dashboard_widget(): void {
		// Only show to users with required capability.
		if ( current_user_can( REQUIRED_CAPABILITY ) ) {
			wp_add_dashboard_widget(
				WIDGET_ID,
				__( 'Export Customer Data', 'export-woo-customer-data' ),
				array( $this, 'render_dashboard_widget' )
			);
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
		// Check WooCommerce availability.
		$wc_active = is_woocommerce_active();

		// Generate nonce.
		$nonce = wp_create_nonce( NONCE_EXPORT_ACTION );

		// Build export URLs.
		$url_all_users = home_url( '/wp-admin/exportwcd-reports/' . CSV_FILENAME_ALL_USERS );
		$url_customers = home_url( '/wp-admin/exportwcd-reports/' . CSV_FILENAME_CUSTOMERS );

		// Render widget template.
		printf(
			'<div class="export-woo-customer-data-widget">%s%s%s</div>',
			sprintf(
				'<p>%s</p>',
				esc_html__( 'Export customer data to CSV format.', 'export-woo-customer-data' )
			),
			sprintf(
				'<p><a href="%s" class="button button-primary">%s</a></p>',
				esc_url( $url_all_users ),
				esc_html__( 'Export All Users', 'export-woo-customer-data' )
			),
			$wc_active ? sprintf(
				'<p><a href="%s" class="button button-secondary">%s</a></p>',
				esc_url( $url_customers ),
				esc_html__( 'Export Customers Only', 'export-woo-customer-data' )
			) : sprintf(
				'<p><em>%s</em></p>',
				esc_html__( 'Install WooCommerce to export customers only.', 'export-woo-customer-data' )
			)
		);
	}
}
