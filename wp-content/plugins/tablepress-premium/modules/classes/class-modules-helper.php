<?php
/**
 * TablePress Modules Helper class with functions needed for premium modules in the admin area.
 *
 * @package TablePress
 * @subpackage Modules
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress Modules Helper functions.
 *
 * @package TablePress
 * @subpackage Modules
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Modules_Helper {

	/**
	 * Enqueue a CSS file, possibly with dependencies.
	 *
	 * @since 2.0.0
	 *
	 * @param string   $name         Name of the CSS file, without extension.
	 * @param string[] $dependencies Optional. List of names of CSS stylesheets that this stylesheet depends on, and which need to be included before this one.
	 */
	public static function enqueue_style( string $name, array $dependencies = array() ): void {
		$css_file = "modules/admin/css/build/{$name}.css";
		$css_url = plugins_url( $css_file, TABLEPRESS__FILE__ );
		wp_enqueue_style( "tablepress-{$name}", $css_url, $dependencies, TablePress::version );
	}

	/**
	 * Enqueue a JavaScript file, possibly with dependencies and extra information.
	 *
	 * @since 2.0.0
	 *
	 * @param string               $name         Name of the JS file, without extension.
	 * @param string[]             $dependencies Optional. List of names of JS scripts that this script depends on, and which need to be included before this one.
	 * @param array<string, mixed> $script_data  Optional. JS data that is printed to the page before the script is included. The array key will be used as the name, the value will be JSON encoded.
	 */
	public static function enqueue_script( string $name, array $dependencies = array(), array $script_data = array() ): void {
		$js_file = "modules/admin/js/build/{$name}.js";
		$js_url = plugins_url( $js_file, TABLEPRESS__FILE__ );

		$version = TablePress::version;

		// Load dependencies and version from the auto-generated asset PHP file.
		$script_asset_path = TABLEPRESS_ABSPATH . "modules/admin/js/build/{$name}.asset.php";
		if ( file_exists( $script_asset_path ) ) {
			$script_asset = require $script_asset_path;
			if ( isset( $script_asset['dependencies'] ) ) {
				$dependencies = array_merge( $dependencies, $script_asset['dependencies'] );
			}
			if ( isset( $script_asset['version'] ) ) {
				$version = $script_asset['version'];
			}
		}

		/**
		 * Filters the dependencies of a TablePress script file.
		 *
		 * @since 2.0.0
		 *
		 * @param string[] $dependencies List of the dependencies that the $name script relies on.
		 * @param string   $name         Name of the JS script, without extension.
		 */
		$dependencies = apply_filters( 'tablepress_admin_page_script_dependencies', $dependencies, $name );

		wp_enqueue_script( "tablepress-{$name}", $js_url, $dependencies, $version, true );

		// Load JavaScript translation files, for all scripts that rely on `wp-i18n`.
		if ( in_array( 'wp-i18n', $dependencies, true ) ) {
			wp_set_script_translations( "tablepress-{$name}", 'tablepress', TABLEPRESS_ABSPATH . 'modules/i18n' );
		}

		if ( ! empty( $script_data ) ) {
			foreach ( $script_data as $var_name => $var_data ) {
				$var_data = wp_json_encode( $var_data, JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
				wp_add_inline_script( "tablepress-{$name}", "const tablepress_{$var_name} = {$var_data};", 'before' );
			}
		}
	}

} // class TablePress_Modules_Helper
