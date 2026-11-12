<?php
/**
 * Main Plugin Class
 *
 * @package Export_Woo_Customer_Data
 * @since 1.0.0
 */

namespace Export_Woo_Customer_Data;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Main Plugin Class.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Single instance of the class.
	 *
	 * @since 2.0.0
	 * @var ?Plugin
	 */
	private static $instance = null;

	/**
	 * CSV exporter, created on the first download.
	 *
	 * @since 1.0.0
	 * @var ?CSV_Exporter
	 */
	private ?CSV_Exporter $csv_exporter = null;

	/**
	 * Get single instance of the class.
	 *
	 * @since 2.0.0
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialise hooks.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {
		$dashboard_widget = new Dashboard_Widget();

		add_action( 'wp_dashboard_setup', array( $dashboard_widget, 'register' ) );
		add_action( 'admin_post_' . ADMIN_POST_ACTION, array( $this, 'handle_export_request' ) );
		add_action( 'template_redirect', array( $this, 'redirect_legacy_url' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );

		// Supports MU plugin installation where activation hooks don't fire.
		add_action( 'admin_init', array( $this, 'check_first_run' ), 1 );
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Get the CSV exporter.
	 *
	 * @since 1.0.0
	 * @return CSV_Exporter
	 */
	public function get_csv_exporter(): CSV_Exporter {
		if ( null === $this->csv_exporter ) {
			$this->csv_exporter = new CSV_Exporter();
		}

		return $this->csv_exporter;
	}

	/**
	 * Serve a download from admin-post.php, after the capability and nonce checks.
	 *
	 * @since 1.0.0
	 */
	public function handle_export_request(): void {
		$report_type = '';
		$error       = null;

		if ( ! current_user_can( REQUIRED_CAPABILITY ) ) {
			$error = array( __( 'You do not have permission to export customer data.', 'export-woo-customer-data' ), 403 );
		} elseif ( ! verify_admin_action_nonce( NONCE_EXPORT_ACTION ) ) {
			$error = array(
				sprintf(
					/* translators: %s is the Dashboard widget's title, Export Customer Data */
					__( 'This download link has expired. Go back to the Dashboard and use the button in the %s widget.', 'export-woo-customer-data' ),
					get_widget_title()
				),
				403,
			);
		} else {
			$report_type = isset( $_GET[ QUERY_ARG_REPORT ] ) ? sanitize_key( wp_unslash( $_GET[ QUERY_ARG_REPORT ] ) ) : '';

			if ( ! is_available_report_type( $report_type ) ) {
				$error = array( __( 'That report is not available on this site.', 'export-woo-customer-data' ), 400 );
			}
		}

		if ( null !== $error ) {
			wp_die(
				esc_html( $error[0] ),
				esc_html__( 'Export Customer Data', 'export-woo-customer-data' ),
				array(
					'response'  => (int) $error[1],
					'back_link' => true,
				)
			);
		} else {
			$this->get_csv_exporter()->send( $report_type );
			exit();
		}
	}

	/**
	 * Send the 1.x download URLs to the Dashboard, where the widget's links carry a nonce.
	 *
	 * @since 2.0.0
	 */
	public function redirect_legacy_url(): void {
		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$request_path = (string) wp_parse_url( $request_uri, PHP_URL_PATH );

		if ( 1 === preg_match( LEGACY_URL_PATTERN, $request_path ) ) {
			wp_safe_redirect( admin_url() );
			exit();
		}
	}

	/**
	 * Declare HPOS and Checkout block compatibility.
	 *
	 * @since 1.0.0
	 */
	public function declare_woocommerce_compatibility(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', EXPORT_WOO_CUSTOMER_DATA_FILE, true );
			FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', EXPORT_WOO_CUSTOMER_DATA_FILE, true );
		}
	}

	/**
	 * Load plugin text domain for translations.
	 *
	 * Needed for the translations shipped in the plugin's own languages/ directory.
	 *
	 * @since 2.0.0
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'export-woo-customer-data', false, dirname( EXPORT_WOO_CUSTOMER_DATA_BASENAME ) . '/languages' );
	}

	/**
	 * Record the installed version when activation didn't, and keep it current.
	 *
	 * A first record also flushes the rewrite rules, which drops the rule 1.x added.
	 *
	 * @since 2.0.0
	 */
	public function check_first_run(): void {
		$stored_version = get_option( OPTION_VERSION );

		if ( false === $stored_version ) {
			add_option( OPTION_VERSION, EXPORT_WOO_CUSTOMER_DATA_VERSION, '', true );
			flush_rewrite_rules( false );
		} elseif ( EXPORT_WOO_CUSTOMER_DATA_VERSION !== $stored_version ) {
			update_option( OPTION_VERSION, EXPORT_WOO_CUSTOMER_DATA_VERSION );
		} else {
			// Stored version is already current.
		}
	}
}
