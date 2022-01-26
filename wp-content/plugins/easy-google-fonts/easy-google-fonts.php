<?php
/**
 * Plugin Name: Easy Google Fonts
 * Description: A simple and easy way to add google fonts to your WordPress theme.
 * Version: 2.0.4
 * Author: Titanium Themes
 * Author URI: https://titaniumthemes.com
 * Plugin URI: https://wordpress.org/plugins/easy-google-fonts/
 * Text Domain: easy-google-fonts
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package     easy-google-fonts
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2021, Titanium Themes
 * @version     2.0.4
 */

namespace EGF;

// Prevent direct file access.
if ( ! defined( 'WPINC' ) ) {
	die;
}

load_all_plugin_files();



/**
 * Load All Plugin Files
 *
 * Loads all of the required files for this
 * plugin to function.
 *
 * @throws WP_Error Error message if and file was not found.
 * @return boolean True if all files were loaded, false if not.
 *
 * @since 2.0.0
 */
function load_all_plugin_files() {
	$files_loaded = array_map(
		__NAMESPACE__ . '\\load_file',
		[
			'admin',
			'api',
			'customizer',
			'data',
			'deprecated',
			'frontend',
			'sanitization',
			'settings',
			'setup',
			'utils',
		]
	);

	return ! in_array(
		true,
		array_map( 'is_wp_error', $files_loaded ),
		true
	);
}

/**
 * Load Single File
 *
 * Attempts to locate a single php file
 * from the src/includes directory.
 *
 * @param string $file_name File name slug without the .php suffix.
 * @return boolean|WP_Error True if file was located | Error if file not found.
 *
 * @since 2.0.0
 */
function load_file( $file_name ) {
	$file = plugin_dir_path( __FILE__ ) . "src/includes/{$file_name}.php";

	if ( file_exists( $file ) ) {
		include_once $file;
		return true;
	}

	return new \WP_Error(
		'file_not_found',
		sprintf(
			/* translators: file_not_found plugin error with file path. */
			__( 'Could not locate the plugin file: %s', 'easy-google-fonts' ),
			$file
		)
	);
}

// Refresh permalinks when plugin is
// activated and deactivated.
register_activation_hook(
	__FILE__,
	function() {
		update_option( 'egf_version', '2.0.3' );
		update_option( 'egf_force_user_redirect', get_current_user_id() );
		update_option( 'egf_show_admin_pointer', true );
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	function() {
		delete_option( 'egf_version' );
		delete_option( 'egf_force_user_redirect' );
		delete_option( 'egf_show_admin_pointer' );
		flush_rewrite_rules();
	}
);
