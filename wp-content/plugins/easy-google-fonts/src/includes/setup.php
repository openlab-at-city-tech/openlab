<?php
/**
 * Plugin Setup
 *
 * Contains information about the file structure of
 * the plugin along with any setup logic.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Setup;

/**
 * Get Plugin File URL
 *
 * Gets the URL to the /src/ directory
 * in the plugin directory with the
 * trailing slash.
 *
 * @return string URL to the src directory with the trailing slash.
 * @since 2.0.0
 */
function get_plugin_src_url() {
	return trailingslashit( plugins_url( 'easy-google-fonts/src' ) );
}

/**
 * Get Plugin File Path
 *
 * Gets the file path to the /src/ directory
 * in the plugin directory.
 *
 * @return string Filepath to the src directory.
 * @since 2.0.0
 */
function get_plugin_src_file_path() {
	return plugin_dir_path( __DIR__ );
}
