<?php
/**
 * Export Woo Customer Data: Uninstaller
 *
 * Runs when the plugin is deleted from the Plugins screen, not on deactivation.
 * The plugin keeps no settings: this removes the stored version and the
 * updater's cache. Exported files are never stored on the server.
 *
 * @package Export_Woo_Customer_Data
 */

namespace Export_Woo_Customer_Data;

// Bail out if not invoked by WordPress's uninstall handler.
defined( 'WP_UNINSTALL_PLUGIN' ) || die();

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/includes/class-github-updater.php';

delete_option( OPTION_VERSION );
Github_Updater::delete_cache( basename( __DIR__ ) );
