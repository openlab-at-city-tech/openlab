<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\Util\Transient;
use Imagely\NGG\Util\Installer;
use Imagely\NGG\Settings\Settings as SettingsManager;

/**
 * WP-CLI Settings management commands for NextGEN Gallery.
 *
 * Provides settings-related WP-CLI commands for NextGEN Gallery.
 */
class Settings {

	/**
	 * Lists all NextGEN Gallery settings.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Associative arguments.
	 * @subcommand list
	 */
	public function _list( $args, $assoc_args ) {
		$settings  = SettingsManager::get_instance();
		$temporary = $settings->to_array();
		$display   = [];
		foreach ( $temporary as $key => $val ) {
			$display[] = [
				'key'   => $key,
				'value' => $val,
			];
		}
		\WP_CLI\Utils\format_items( 'table', $display, [ 'key', 'value' ] );
	}

	/**
	 * Edits a specific NextGEN Gallery setting.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Associative arguments.
	 * @synopsis <key> <value>
	 */
	public function edit( $args, $assoc_args ) {
		$settings = SettingsManager::get_instance();
		$settings->set( $args[0], $args[1] );
		$settings->save();
		\WP_CLI::success( 'Setting has been updated' );
	}

	/**
	 * Export all NextGen settings to a file in JSON format
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Associative arguments.
	 * @synopsis <json-file-path>
	 */
	public function export( $args, $assoc_args ) {
		$settings = SettingsManager::get_instance();
		file_put_contents( $args[0], $settings->to_json() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		\WP_CLI::success( "Settings have been stored in {$args[0]}" );
	}

	/**
	 * Import settings from a JSON file
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Associative arguments.
	 * @synopsis <json-file-path>
	 */
	public function import( $args, $assoc_args ) {
		$settings     = SettingsManager::get_instance();
		$file_content = file_get_contents( $args[0] );
		$json         = json_decode( $file_content );

		if ( null === $json ) {
			\WP_CLI::error( 'Could not parse JSON file' );
		}

		foreach ( $json as $key => $value ) {
			$settings->set( $key, $value );
		}

		$settings->save();

		\WP_CLI::success( "Settings have been imported from {$args[0]}" );
	}

	/**
	 * Deactivates NextGen and NextGen Pro and resets all settings to their default state
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function reset( $args, $assoc_args ) {
		\WP_CLI::confirm( 'Are you sure you want to reset all NextGen settings?', $assoc_args );

		$settings = SettingsManager::get_instance();
		Transient::flush();

		if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) || defined( 'NEXTGEN_GALLERY_PRO_VERSION' ) ) {
			Installer::uninstall( 'photocrati-nextgen-pro' );
		}
		if ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) ) {
			Installer::uninstall( 'photocrati-nextgen-plus' );
		}
		Installer::uninstall( 'photocrati-nextgen' );

		// removes all ngg_options entry in wp_options.
		$settings->reset();
		$settings->destroy();

		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'display_type' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 'lightbox_library' ) );

		\WP_CLI::success( 'All NextGen settings have been reset' );
	}
}
