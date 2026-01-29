<?php
/**
 * Main plugin class.
 *
 * @package Export_Woo_Customer_Data
 */

namespace Export_Woo_Customer_Data;

defined( 'ABSPATH' ) || die();

/**
 * Main plugin orchestration class.
 *
 * @since 1.0.0
 */
class Plugin {


	/**
	 * Admin hooks handler.
	 *
	 * @var Admin_Hooks|null
	 */
	private ?Admin_Hooks $admin_hooks = null;

	/**
	 * CSV exporter instance.
	 *
	 * @var CSV_Exporter|null
	 */
	private ?CSV_Exporter $csv_exporter = null;


	/**
	 * Run the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		// Register activation hook.
		register_activation_hook( EXPORT_WCD_BASENAME, array( $this, 'activate' ) );

		// Register deactivation hook.
		register_deactivation_hook( EXPORT_WCD_BASENAME, array( $this, 'deactivate' ) );

		// Initialize admin hooks.
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'init_admin_hooks' ) );
		}

		// Initialize URL rewrite handling.
		add_action( 'init', array( $this, 'register_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_export_request' ) );
	}

	/**
	 * Plugin activation hook.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate(): void {
		// Register rewrite rules.
		$this->register_rewrite_rules();

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation hook.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function deactivate(): void {
		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Initialize admin hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_admin_hooks(): void {
		$this->get_admin_hooks();
	}

	/**
	 * Get admin hooks instance (lazy loaded).
	 *
	 * @since 1.0.0
	 *
	 * @return Admin_Hooks Admin hooks instance.
	 */
	public function get_admin_hooks(): Admin_Hooks {
		if ( is_null( $this->admin_hooks ) ) {
			$this->admin_hooks = new Admin_Hooks( $this->get_version() );
		}

		return $this->admin_hooks;
	}

	/**
	 * Get CSV exporter instance (lazy loaded).
	 *
	 * @since 1.0.0
	 *
	 * @return CSV_Exporter CSV exporter instance.
	 */
	public function get_csv_exporter(): CSV_Exporter {
		if ( is_null( $this->csv_exporter ) ) {
			$this->csv_exporter = new CSV_Exporter();
		}

		return $this->csv_exporter;
	}

	/**
	 * Register URL rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_rewrite_rules(): void {
		add_rewrite_rule(
			'^wp-admin/exportwcd-reports/([^/]+)\.csv$',
			'index.php?' . QUERY_VAR_REPORT . '=$matches[1]',
			'top'
		);
	}

	/**
	 * Register custom query vars.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, string> $vars Query vars.
	 *
	 * @return array<int, string> Modified query vars.
	 */
	public function register_query_vars( array $vars ): array {
		$vars[] = QUERY_VAR_REPORT;
		return $vars;
	}

	/**
	 * Handle export request via pretty URL.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function handle_export_request(): void {
		$report_type = get_query_var( QUERY_VAR_REPORT, '' );

		// Exit early if not an export request.
		if ( empty( $report_type ) ) {
			return;
		}

		// Verify user capability.
		if ( ! current_user_can( REQUIRED_CAPABILITY ) ) {
			wp_die(
				esc_html__( 'You do not have permission to export customer data.', 'export-woo-customer-data' ),
				esc_html__( 'Permission Denied', 'export-woo-customer-data' ),
				array( 'response' => 403 )
			);
		}

		// Validate report type.
		if ( ! in_array( $report_type, array( REPORT_TYPE_ALL_USERS, REPORT_TYPE_CUSTOMERS ), true ) ) {
			wp_die(
				esc_html__( 'Invalid report type.', 'export-woo-customer-data' ),
				esc_html__( 'Invalid Request', 'export-woo-customer-data' ),
				array( 'response' => 400 )
			);
		}

		// Generate and serve CSV export.
		$exporter = $this->get_csv_exporter();

		if ( REPORT_TYPE_ALL_USERS === $report_type ) {
			$exporter->export_all_users();
		} elseif ( REPORT_TYPE_CUSTOMERS === $report_type ) {
			$exporter->export_customers_only();
		}

		exit;
	}

	/**
	 * Get plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version.
	 */
	public function get_version(): string {
		return EXPORT_WCD_VERSION;
	}
}
