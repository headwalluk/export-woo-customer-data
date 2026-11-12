<?php
/**
 * Dashboard widget: the export buttons.
 *
 * Rendered by Dashboard_Widget::render().
 *
 * @package Export_Woo_Customer_Data
 * @since 1.0.0
 *
 * @var string $all_users_url Nonced download URL for every user.
 * @var string $customers_url Nonced download URL for customers, or '' without WooCommerce.
 */

namespace Export_Woo_Customer_Data;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

$export_woo_customer_data_button = '<p><a href="%s" class="button" style="display:block;"><span class="dashicons dashicons-download" style="position:relative;top:0.2em;" aria-hidden="true"></span> %s</a></p>';

echo '<div class="export-woo-customer-data-widget">';

printf( '<p>%s</p>', esc_html__( 'Download customer data as a CSV file.', 'export-woo-customer-data' ) );

printf(
	$export_woo_customer_data_button, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed markup defined above.
	esc_url( $all_users_url ),
	esc_html_x( 'Export All Users', 'button label: download a CSV of every user account', 'export-woo-customer-data' )
);

if ( '' !== $customers_url ) {
	printf(
		$export_woo_customer_data_button, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed markup defined above.
		esc_url( $customers_url ),
		esc_html_x( 'Export Customers Only', 'button label: download a CSV of only the users who have placed an order', 'export-woo-customer-data' )
	);
} else {
	printf( '<p><em>%s</em></p>', esc_html__( 'Exporting only customers needs WooCommerce, which is not active on this site.', 'export-woo-customer-data' ) );
}

echo '</div>';
