<?php
/**
 * CSV Exporter
 *
 * @package Export_Woo_Customer_Data
 * @since 1.0.0
 */

namespace Export_Woo_Customer_Data;

use Automattic\WooCommerce\Utilities\OrderUtil;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Builds a report's rows and writes them as CSV, a batch of users at a time.
 *
 * @since 1.0.0
 */
class CSV_Exporter {

	/**
	 * Send a report as a CSV download.
	 *
	 * The caller exits afterwards. A customers report with no customers ends in
	 * wp_die() before any header is sent.
	 *
	 * @since 2.0.0
	 *
	 * @param string $report_type One of the REPORT_TYPE_* constants, already validated.
	 */
	public function send( string $report_type ): void {
		$user_ids = null;
		if ( REPORT_TYPE_CUSTOMERS === $report_type ) {
			$user_ids = $this->get_customer_ids();
		}

		// php://output is a stream, not a file, so WP_Filesystem doesn't apply.
		$output = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Output stream, not a file.

		if ( is_array( $user_ids ) && empty( $user_ids ) ) {
			wp_die(
				esc_html__( 'No customers have placed an order yet, so there is nothing to export.', 'export-woo-customer-data' ),
				esc_html__( 'Nothing to export', 'export-woo-customer-data' ),
				array( 'response' => 404 )
			);
		} elseif ( false === $output ) {
			log_error( 'Could not open php://output for the CSV download.' );
			wp_die(
				esc_html__( 'The export could not be started. The server log has the details.', 'export-woo-customer-data' ),
				esc_html__( 'Export failed', 'export-woo-customer-data' ),
				array( 'response' => 500 )
			);
		} else {
			nocache_headers();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="' . REPORT_FILENAMES[ $report_type ] . '"' );
			header( 'X-Content-Type-Options: nosniff' );

			$row_count = $this->write_csv( $output, $user_ids );
			fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Output stream, not a file.

			log_debug( sprintf( 'Exported %d users for the %s report.', $row_count, $report_type ) );
		}
	}

	/**
	 * Write the CSV to a stream: byte-order mark, header row, one row per user.
	 *
	 * @since 2.0.0
	 *
	 * @param resource $handle   Writable stream.
	 * @param ?int[]   $user_ids Users to export, in order, or null for every user on the site.
	 * @return int Number of user rows written.
	 */
	public function write_csv( $handle, ?array $user_ids ): int {
		$fields    = $this->get_field_mapping();
		$columns   = $this->get_user_columns( $fields );
		$row_count = 0;
		$page      = 1;
		$is_done   = false;

		// UTF-8 byte-order mark, so Excel reads accented names correctly.
		fwrite( $handle, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Output stream, not a file.
		$this->write_row( $handle, array_keys( $fields ) );

		while ( ! $is_done ) {
			$users = $this->get_user_batch( $columns, $user_ids, $page );

			foreach ( $this->format_rows( $users, $fields ) as $row ) {
				$this->write_row( $handle, $row );
				++$row_count;
			}

			if ( null === $user_ids ) {
				$is_done = count( $users ) < USER_BATCH_SIZE;
			} else {
				$is_done = $page * USER_BATCH_SIZE >= count( $user_ids );
			}

			++$page;
		}

		return $row_count;
	}

	/**
	 * Get the IDs of registered users with at least one placed order.
	 *
	 * Guest orders, checkout drafts, trashed orders and refunds don't count.
	 *
	 * @since 2.0.0
	 *
	 * @return int[] User IDs, ascending.
	 */
	public function get_customer_ids(): array {
		global $wpdb;

		$status_placeholders = implode( ', ', array_fill( 0, count( NON_ORDER_STATUSES ), '%s' ) );

		if ( class_exists( OrderUtil::class ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$table_name = OrderUtil::get_table_for_orders();

			// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- One read per export, nothing to cache; the table name comes from WooCommerce and the placeholders are built above.
			$customer_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT customer_id FROM {$table_name}
					WHERE type = 'shop_order' AND customer_id > 0 AND status NOT IN ({$status_placeholders})
					ORDER BY customer_id ASC",
					NON_ORDER_STATUSES
				)
			);
			// phpcs:enable
		} else {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- One read per export, nothing to cache; the placeholders are built above.
			$customer_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT CAST(pm.meta_value AS UNSIGNED) AS customer_id
					FROM {$wpdb->postmeta} pm
					INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key = '_customer_user' AND CAST(pm.meta_value AS UNSIGNED) > 0
					AND p.post_type = 'shop_order' AND p.post_status NOT IN ({$status_placeholders})
					ORDER BY customer_id ASC",
					NON_ORDER_STATUSES
				)
			);
			// phpcs:enable
		}

		return array_map( 'intval', $customer_ids );
	}

	/**
	 * Get the field mapping, through the export_woo_customer_data_fields filter.
	 *
	 * Entries that aren't a valid users column or meta key are logged and left
	 * out. A filter that returns no valid entries falls back to the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, string>> CSV column => source.
	 */
	public function get_field_mapping(): array {
		/**
		 * Filter the columns in the export and where each one comes from.
		 *
		 * @since 1.0.0
		 *
		 * @param array $fields CSV column => array( 'table' => 'users', 'column' => ... )
		 *                      or array( 'table' => 'usermeta', 'meta_key' => ... ).
		 */
		$filtered = apply_filters( 'export_woo_customer_data_fields', DEFAULT_FIELDS );
		$fields   = array();

		if ( ! is_array( $filtered ) ) {
			log_error( 'The export_woo_customer_data_fields filter returned a non-array; using the default fields.' );
			$filtered = array();
		}

		foreach ( $filtered as $field_key => $field_config ) {
			if ( ! is_string( $field_key ) || '' === $field_key ) {
				log_error( 'Skipped a field with no column name in export_woo_customer_data_fields.' );
				continue;
			}

			if ( $this->is_valid_field_config( $field_config ) ) {
				$fields[ $field_key ] = $field_config;
			} else {
				log_error( sprintf( 'Skipped the "%s" field: it must name an exportable users table column, or a meta_key.', $field_key ) );
			}
		}

		if ( empty( $fields ) ) {
			$fields = DEFAULT_FIELDS;
		}

		return $fields;
	}

	/**
	 * Turn a batch of users into CSV rows, in field order.
	 *
	 * @since 2.0.0
	 *
	 * @param object[]                             $users  Rows from the users table, each with an ID.
	 * @param array<string, array<string, string>> $fields Validated field mapping.
	 * @return array<int, string[]> One row of cell text per user.
	 */
	public function format_rows( array $users, array $fields ): array {
		$rows     = array();
		$user_ids = array();

		foreach ( $users as $user ) {
			$user_ids[] = (int) $user->ID;
		}

		// One query for the batch's meta, evicted afterwards so memory stays flat.
		update_meta_cache( 'user', $user_ids );

		foreach ( $users as $user ) {
			$row = array();

			foreach ( $fields as $field_config ) {
				if ( FIELD_TABLE_USERS === $field_config['table'] ) {
					$value = $user->{$field_config['column']} ?? '';
				} else {
					$value = get_user_meta( (int) $user->ID, $field_config['meta_key'], true );
				}

				$row[] = to_csv_cell( $value );
			}

			$rows[] = $row;
		}

		wp_cache_delete_multiple( $user_ids, 'user_meta' );

		return $rows;
	}

	/**
	 * Check one field mapping entry.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $field_config Entry from the filtered mapping.
	 * @return bool True when it names an exportable users column or a meta key.
	 */
	private function is_valid_field_config( mixed $field_config ): bool {
		$is_valid = false;

		if ( ! is_array( $field_config ) || ! isset( $field_config['table'] ) ) {
			$is_valid = false;
		} elseif ( FIELD_TABLE_USERS === $field_config['table'] ) {
			$is_valid = isset( $field_config['column'] ) && in_array( $field_config['column'], EXPORTABLE_USER_COLUMNS, true );
		} elseif ( FIELD_TABLE_USERMETA === $field_config['table'] ) {
			$is_valid = isset( $field_config['meta_key'] ) && is_string( $field_config['meta_key'] ) && '' !== $field_config['meta_key'];
		} else {
			$is_valid = false;
		}

		return $is_valid;
	}

	/**
	 * Get the users table columns to read: ID, plus every column a field names.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, array<string, string>> $fields Validated field mapping.
	 * @return string[] Column names.
	 */
	private function get_user_columns( array $fields ): array {
		$columns = array( 'ID' );

		foreach ( $fields as $field_config ) {
			if ( FIELD_TABLE_USERS === $field_config['table'] ) {
				$columns[] = $field_config['column'];
			}
		}

		return array_values( array_unique( $columns ) );
	}

	/**
	 * Read one page of users.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $columns  Users table columns to read.
	 * @param ?int[]   $user_ids Users to export, or null for every user.
	 * @param int      $page     Page number, from 1.
	 * @return object[] Rows from the users table.
	 */
	private function get_user_batch( array $columns, ?array $user_ids, int $page ): array {
		$query_args = array(
			'fields'        => $columns,
			'orderby'       => 'ID',
			'order'         => 'ASC',
			'count_total'   => false,
			'cache_results' => false,
		);

		if ( null === $user_ids ) {
			$query_args['number'] = USER_BATCH_SIZE;
			$query_args['paged']  = $page;
		} else {
			// write_csv() never asks for a page past the end, so the slice is never empty.
			// An empty include would match every user.
			$query_args['include'] = array_slice( $user_ids, ( $page - 1 ) * USER_BATCH_SIZE, USER_BATCH_SIZE );
		}

		return get_users( $query_args );
	}

	/**
	 * Write one CSV row.
	 *
	 * @since 2.0.0
	 *
	 * @param resource $handle Writable stream.
	 * @param string[] $cells  Cell text.
	 */
	private function write_row( $handle, array $cells ): void {
		// An empty escape character gives standard CSV quoting, and is required from PHP 8.4.
		fputcsv( $handle, $cells, ',', '"', '' );
	}
}
