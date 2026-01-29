<?php
/**
 * CSV Exporter class.
 *
 * @package Export_Woo_Customer_Data
 */

namespace Export_Woo_Customer_Data;

defined( 'ABSPATH' ) || die();

/**
 * Handles CSV export generation for customer data.
 *
 * @since 1.0.0
 */
class CSV_Exporter {

	/**
	 * Export all users to CSV.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function export_all_users(): void {
		$users = $this->get_all_users();
		$this->generate_csv( $users, CSV_FILENAME_ALL_USERS );
	}

	/**
	 * Export customers only to CSV.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function export_customers_only(): void {
		$users = $this->get_customers_only();
		$this->generate_csv( $users, CSV_FILENAME_CUSTOMERS );
	}

	/**
	 * Get all WordPress users with metadata.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>> User data array.
	 */
	private function get_all_users(): array {
		// Suspend cache to prevent memory issues with large user datasets.
		wp_suspend_cache_addition( true );
		$users = get_users(
			array(
				'fields'      => 'all',
				'count_total' => false,
			)
		);
		wp_suspend_cache_addition( false );

		$result = $this->format_user_data( $users );

		return $result;
	}

	/**
	 * Get customers only (users who have placed orders).
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>> User data array.
	 */
	private function get_customers_only(): array {
		global $wpdb;

		$result       = array();
		$customer_ids = array();

		// Check if HPOS is enabled.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// Query HPOS tables for customer IDs.
			$customer_ids = $wpdb->get_col(
				"SELECT DISTINCT customer_id
				FROM {$wpdb->prefix}wc_orders
				WHERE customer_id > 0"
			);
		} else {
			// Query legacy postmeta for customer IDs.
			$customer_ids = $wpdb->get_col(
				"SELECT DISTINCT pm.meta_value
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = '_customer_user'
				AND pm.meta_value > 0
				AND p.post_type = 'shop_order'"
			);
		}

		if ( empty( $customer_ids ) ) {
			return $result;
		}

		// Get user objects for customer IDs.
		// Suspend cache to prevent memory issues with large user datasets.
		wp_suspend_cache_addition( true );
		$users = get_users(
			array(
				'include'     => $customer_ids,
				'fields'      => 'all',
				'count_total' => false,
			)
		);
		wp_suspend_cache_addition( false );

		$result = $this->format_user_data( $users );

		return $result;
	}

	/**
	 * Format user data according to field mapping.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, \WP_User> $users Array of WP_User objects.
	 *
	 * @return array<int, array<string, mixed>> Formatted user data.
	 */
	private function format_user_data( array $users ): array {
		$result = array();
		$fields = $this->get_field_mapping();

		// Suspend cache to prevent memory issues with large user datasets.
		wp_suspend_cache_addition( true );
		foreach ( $users as $user ) {
			$row = array();

			foreach ( $fields as $field_key => $field_config ) {
				$value = '';

				if ( 'users' === $field_config['table'] && isset( $field_config['column'] ) ) {
					// Get from user object property.
					$column = $field_config['column'];
					$value  = $user->{$column} ?? '';
				} elseif ( 'usermeta' === $field_config['table'] && isset( $field_config['meta_key'] ) ) {
					// Get from user meta.
					$meta_key = $field_config['meta_key'];
					$value    = get_user_meta( $user->ID, $meta_key, true );
				}

				$row[ $field_key ] = $value;
			}

			$result[] = $row;
		}
		wp_suspend_cache_addition( false );

		return $result;
	}

	/**
	 * Get field mapping with filter applied.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, string>> Field mapping configuration.
	 */
	private function get_field_mapping(): array {
		$result = apply_filters( FILTER_FIELDS, DEFAULT_FIELDS );
		return $result;
	}

	/**
	 * Generate and output CSV file.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, array<string, mixed>> $data     User data to export.
	 * @param string                           $filename CSV filename.
	 *
	 * @return void
	 */
	private function generate_csv( array $data, string $filename ): void {
		// Handle empty dataset.
		if ( empty( $data ) ) {
			wp_die( esc_html__( 'No data available to export.', 'export-woo-customer-data' ), esc_html__( 'Export Failed', 'export-woo-customer-data' ), array( 'response' => 404 ) );
		}

		// Set HTTP headers for CSV download.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Open output stream.
		$output = fopen( 'php://output', 'w' );

		if ( false === $output ) {
			wp_die( esc_html__( 'Failed to open output stream.', 'export-woo-customer-data' ), esc_html__( 'Export Failed', 'export-woo-customer-data' ), array( 'response' => 500 ) );
		}

		// Write UTF-8 BOM for Excel compatibility.
		fprintf( $output, "\xEF\xBB\xBF" );

		// Write CSV headers.
		$headers = array_keys( $data[0] );
		fputcsv( $output, $headers );

		// Write data rows.
		foreach ( $data as $row ) {
			fputcsv( $output, array_values( $row ) );
		}

		fclose( $output );
		exit();
	}
}
