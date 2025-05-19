<?php
/**
 * SeoKar AI Uninstaller - Final Production Version
 *
 * This script runs when the user deletes the SeoKar AI plugin from the WordPress admin.
 * It's designed to clean up options, transients, scheduled cron jobs, and custom cache files
 * created by the plugin. The level of cleanup can be controlled by a plugin option.
 *
 * @package     SeoKar_AI
 * @subpackage  Uninstall
 * @version     2.0.0
 * @author      Sajjad Akbari <info@sajjadakbari.ir>
 * @license     GPLv2 or later
 * @link        https://sajjadakbari.ir/
 * @since       0.2.0
 */

// Exit if accessed directly and not during an uninstall process.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// --- Configuration ---
// Set to true to enable logging to WP_DEBUG_LOG during uninstall.
// It's highly recommended to keep this conditional on WP_DEBUG.
$enable_uninstall_logging = ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG );

if ( $enable_uninstall_logging ) {
	// Ensure PHP error logging is directed to the WordPress debug.log file if not already.
	if ( ! ini_get( 'log_errors' ) ) {
		@ini_set( 'log_errors', '1' );
	}
	$error_log_path = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR . '/debug.log' : '';
	if ( $error_log_path && ! ini_get( 'error_log' ) ) {
		@ini_set( 'error_log', $error_log_path );
	}
	error_log( 'SeoKar AI Uninstaller: Process initiated.' );
}

// --- Permission Check ---
// Verify that the current user has the capability to uninstall plugins.
if ( ! current_user_can( 'uninstall_plugins' ) ) {
	if ( $enable_uninstall_logging ) {
		error_log( 'SeoKar AI Uninstaller: Permission denied. User lacks "uninstall_plugins" capability.' );
	}
	// Output a standard WordPress error message.
	wp_die(
		esc_html__( 'You do not have sufficient permissions to uninstall this plugin.', 'seokar-ai' ),
		esc_html__( 'Plugin Uninstall Error', 'seokar-ai' ),
		array( 'response' => 403, 'back_link' => true )
	);
}

// --- User Preference for Data Removal ---
// Retrieve the user's preference for deleting all plugin data.
// Defaults to 'no', meaning only essential options are removed unless explicitly set to 'yes'.
// This option 'seokar_ai_uninstall_delete_data' should be configurable in the plugin's settings page.
$delete_all_plugin_data = get_option( 'seokar_ai_uninstall_delete_data', 'no' );

if ( $enable_uninstall_logging ) {
	error_log( "SeoKar AI Uninstaller: User preference for 'delete_all_data' is set to: '{$delete_all_plugin_data}'." );
}

// --- Allow other plugins/themes to hook before cleanup ---
/**
 * Fires before SeoKar AI starts its uninstall cleanup process.
 *
 * @since 2.0.0
 * @param string $delete_all_plugin_data User's preference ('yes' or 'no') for deleting all data.
 */
do_action( 'seokar_ai_before_uninstall_cleanup', $delete_all_plugin_data );


// --- Define Data for Removal ---
// Options that should always be removed, regardless of the user's full deletion preference.
// These are typically core settings, version info, and the uninstall preference itself.
$options_always_remove = array(
	'seokar_ai_settings',                 // Main array of plugin settings.
	'seokar_ai_version',                  // Stored version of the plugin.
	'seokar_ai_uninstall_delete_data',    // The uninstall preference option.
	// Add other options critical for a clean slate if the plugin is reinstalled.
);

// Options and data types to be removed only if the user has opted for complete data deletion.
$options_conditional_remove = array(
	'seokar_ai_active_services',          // Example: Cached status of API connections.
	'seokar_ai_last_api_check_results',   // Example: Results of the last API health check.
	'seokar_ai_license_key',              // If licensing is implemented.
	'seokar_ai_usage_stats',              // Example: If plugin tracks usage statistics.
	// Add any other options storing user-generated data, extensive configurations, or non-critical info.
);

// Merge option arrays if full removal is requested.
$options_to_remove_list = $options_always_remove;
if ( 'yes' === $delete_all_plugin_data ) {
	$options_to_remove_list = array_merge( $options_always_remove, $options_conditional_remove );
}
$options_to_remove_list = array_unique( $options_to_remove_list ); // Ensure no duplicates.


// --- Remove Plugin Options ---
if ( ! empty( $options_to_remove_list ) ) {
	if ( $enable_uninstall_logging ) {
		error_log( 'SeoKar AI Uninstaller: Preparing to remove the following options: ' . implode( ', ', $options_to_remove_list ) );
	}
	foreach ( $options_to_remove_list as $option_name ) {
		$is_deleted = delete_option( $option_name );
		if ( is_multisite() ) {
			delete_site_option( $option_name ); // Also remove site-wide options in a multisite environment.
		}
		if ( $enable_uninstall_logging ) {
			error_log( "SeoKar AI Uninstaller: Option '{$option_name}' removal attempt. " . ( $is_deleted ? 'Successfully deleted.' : 'Not found or could not be deleted.' ) );
		}
	}
}


// --- Full Data Cleanup (if user opted-in) ---
if ( 'yes' === $delete_all_plugin_data ) {
	global $wpdb; // WordPress database access object.

	// 1. Remove Transients
	$transient_like_patterns = array(
		$wpdb->esc_like( '_transient_seokar_ai_' ) . '%',         // Plugin-specific transients.
		$wpdb->esc_like( '_transient_timeout_seokar_ai_' ) . '%', // Timeout counterparts for transients.
	);
	$site_transient_like_patterns = array(
		$wpdb->esc_like( '_site_transient_seokar_ai_' ) . '%',
		$wpdb->esc_like( '_site_transient_timeout_seokar_ai_' ) . '%',
	);

	foreach ( $transient_like_patterns as $pattern ) {
		$sql    = $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $pattern );
		$result = $wpdb->query( $sql );
		if ( $enable_uninstall_logging ) {
			$db_error = $wpdb->last_error;
			if ( $db_error ) {
				error_log( "SeoKar AI Uninstaller: Database error removing transients (options table) matching '{$pattern}': {$db_error}" );
			} elseif ( false !== $result ) {
				error_log( "SeoKar AI Uninstaller: {$result} transient(s) removed from options table matching '{$pattern}'." );
			}
		}
	}

	if ( is_multisite() ) {
		foreach ( $site_transient_like_patterns as $pattern ) {
			$sql    = $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s", $pattern );
			$result = $wpdb->query( $sql );
			if ( $enable_uninstall_logging ) {
				$db_error = $wpdb->last_error;
				if ( $db_error ) {
					error_log( "SeoKar AI Uninstaller: Database error removing site transients (sitemeta table) matching '{$pattern}': {$db_error}" );
				} elseif ( false !== $result ) {
					error_log( "SeoKar AI Uninstaller: {$result} site transient(s) removed from sitemeta table matching '{$pattern}'." );
				}
			}
		}
	}

	// 2. Remove Scheduled Cron Jobs
    /**
     * Filters the list of cron hook names to be cleared during uninstall.
     *
     * @since 2.0.0
     * @param array $hooks Array of cron hook names.
     */
	$cron_hooks_to_clear = apply_filters( 'seokar_ai_uninstall_cron_hooks', array(
		'seokar_ai_daily_api_health_check', // Example: a daily task.
		'seokar_ai_weekly_usage_report',  // Example: a weekly task.
		// Add other cron hook names defined by your plugin.
	) );

	if ( ! empty( $cron_hooks_to_clear ) ) {
		foreach ( $cron_hooks_to_clear as $hook_name ) {
			$cleared_count = 0;
			// Loop to ensure all scheduled instances of the hook are cleared.
			while ( false !== ( $timestamp = wp_next_scheduled( $hook_name ) ) ) {
				wp_unschedule_event( $timestamp, $hook_name );
				$cleared_count++;
			}
			if ( $enable_uninstall_logging && $cleared_count > 0 ) {
				error_log( "SeoKar AI Uninstaller: Cleared {$cleared_count} scheduled cron event(s) for hook '{$hook_name}'." );
			}
		}
	}

	// 3. Remove Custom Cache Directories/Files
	$upload_dir_info = wp_upload_dir();
	if ( ! empty( $upload_dir_info['basedir'] ) ) {
		$plugin_cache_directory = trailingslashit( $upload_dir_info['basedir'] ) . 'seokar_ai_cache';

		if ( is_dir( $plugin_cache_directory ) ) {
			$dir_deleted = seokar_ai_uninstall_delete_directory_recursively( $plugin_cache_directory );
			if ( $enable_uninstall_logging ) {
				if ( $dir_deleted ) {
					error_log( "SeoKar AI Uninstaller: Custom cache directory '{$plugin_cache_directory}' and its contents successfully removed." );
				} else {
					error_log( "SeoKar AI Uninstaller: Failed to completely remove custom cache directory '{$plugin_cache_directory}'. Check file permissions or manual cleanup might be needed." );
				}
			}
		}
	}

    // 4. Remove Custom Database Tables (if any)
    /*
    $custom_tables = apply_filters( 'seokar_ai_uninstall_custom_tables', array(
        // $wpdb->prefix . 'seokar_ai_custom_data',
    ) );
    if ( ! empty( $custom_tables ) ) {
        foreach ( $custom_tables as $table_name ) {
            $wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
            if ( $enable_uninstall_logging ) {
                error_log( "SeoKar AI Uninstaller: Attempted to drop custom table '{$table_name}'." );
            }
        }
    }
    */

} // End of "if ($delete_all_plugin_data === 'yes')" block.


// --- Final Cleanup ---
// Clear WordPress object cache to ensure old data isn't served.
wp_cache_flush();
if ( $enable_uninstall_logging ) {
	error_log( 'SeoKar AI Uninstaller: WordPress object cache flushed.' );
}

// --- Allow other plugins/themes to hook after cleanup ---
/**
 * Fires after SeoKar AI has completed its uninstall cleanup process.
 *
 * @since 2.0.0
 * @param string $delete_all_plugin_data User's preference ('yes' or 'no') for deleting all data.
 */
do_action( 'seokar_ai_after_uninstall_cleanup', $delete_all_plugin_data );

if ( $enable_uninstall_logging ) {
	error_log( 'SeoKar AI Uninstaller: Process completed.' );
}

// --- Helper Functions ---
if ( ! function_exists( 'seokar_ai_uninstall_delete_directory_recursively' ) ) {
	/**
	 * Recursively deletes a directory and all its contents.
	 *
	 * @since 2.0.0
	 * @param string $dir_path Path to the directory.
	 * @return bool True on success, false on failure.
	 */
	function seokar_ai_uninstall_delete_directory_recursively( $dir_path ) {
		if ( ! is_dir( $dir_path ) ) {
			// If it's not a directory, or doesn't exist, consider it "successfully" handled for this function's purpose.
			return true;
		}

		// scandir lists '.' and '..' which need to be ignored.
		$items = array_diff( scandir( $dir_path ), array( '.', '..' ) );

		foreach ( $items as $item ) {
			$item_path = $dir_path . DIRECTORY_SEPARATOR . $item;
			if ( is_dir( $item_path ) ) {
				if ( ! seokar_ai_uninstall_delete_directory_recursively( $item_path ) ) {
					return false; // Stop if a subdirectory can't be deleted.
				}
			} else {
				if ( ! @unlink( $item_path ) ) {
					return false; // Stop if a file can't be deleted.
				}
			}
		}
		return @rmdir( $dir_path ); // Attempt to remove the now-empty directory.
	}
}

?>
