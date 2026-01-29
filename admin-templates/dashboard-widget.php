<?php
/**
 * Dashboard widget template.
 *
 * Displays export buttons for customer data.
 *
 * @package Export_Woo_Customer_Data
 * @since 1.0.0
 */

namespace Export_Woo_Customer_Data;

defined( 'ABSPATH' ) || die();

// Build export URLs.
$url_all_users = home_url( '/wp-admin/exportwcd-reports/' . CSV_FILENAME_ALL_USERS );
$url_customers = home_url( '/wp-admin/exportwcd-reports/' . CSV_FILENAME_CUSTOMERS );

echo '<div class="export-woo-customer-data-widget">';

printf( '<p>%s</p>', esc_html__( 'Export customer data to CSV format.', 'export-woo-customer-data' ) );

printf( '<p><a href="%s" class="button button-primary">%s</a></p>', esc_url( $url_all_users ), esc_html__( 'Export All Users', 'export-woo-customer-data' ) );

if ( is_woocommerce_active() ) {
	printf( '<p><a href="%s" class="button button-secondary">%s</a></p>', esc_url( $url_customers ), esc_html__( 'Export Customers Only', 'export-woo-customer-data' ) );
} else {
	printf( '<p><em>%s</em></p>', esc_html__( 'Install WooCommerce to export customers only.', 'export-woo-customer-data' ) );
}

echo '</div>'; // .export-woo-customer-data-widget
