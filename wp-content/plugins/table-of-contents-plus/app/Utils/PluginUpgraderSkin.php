<?php
namespace AIOSEO\TableOfContents\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';

/**
 * The PluginSilentUpgraderSkin class.
 *
 * @internal Please do not use this class outside of core AIOSEO development.
 *
 * @since 1.0.0
 */
class PluginUpgraderSkin extends \WP_Upgrader_Skin {
	/**
	 * Empty out the header of its HTML content and only check to see if it has
	 * been performed or not.
	 *
	 * @since 1.0.0
	 */
	public function header() {}

	/**
	 * Empty out the footer of its HTML contents.
	 *
	 * @since 1.0.0
	 */
	public function footer() {}

	/**
	 * Instead of outputting HTML for errors, just return them.
	 * AJAX request will just ignore it.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $errors Array of errors with the install process.
	 * @return void
	 */
	public function error( $errors ) {
		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}
	}

	/**
	 * Empty out JavaScript output that calls function to decrement the update counts.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of update count to decrement.
	 */
	public function decrement_update_count( $type ) {} // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable, PSR1.Methods.CamelCapsMethodName.NotCamelCaps

	/**
	 * @since 1.0.0
	 *
	 * @param  string $feedback Message data.
	 * @param  mixed  ...$args  Optional text replacements.
	 * @return void
	 */
	public function feedback( $feedback, ...$args ) {} // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
}