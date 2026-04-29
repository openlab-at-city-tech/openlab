<?php
/**
 * Trait WordPress\Plugin_Check\Traits\Find_Uninstall
 *
 * @package plugin-check
 */

namespace WordPress\Plugin_Check\Traits;

/**
 * Trait for finding uninstall file.
 *
 * @since 1.8.0
 */
trait Find_Uninstall {

	/**
	 * Checks if a file is the uninstall.php file.
	 *
	 * @since 1.8.0
	 *
	 * @param string $file        The file path to check.
	 * @param string $plugin_path The plugin path.
	 * @return bool True if the file is uninstall.php, false otherwise.
	 */
	protected function is_uninstall_file( $file, $plugin_path ) {
		return $file === $plugin_path . 'uninstall.php';
	}

	/**
	 * Gets the uninstall file path for a plugin.
	 *
	 * @since 1.8.0
	 *
	 * @param string $plugin_path The plugin path.
	 * @return string The uninstall file path.
	 */
	protected function get_uninstall_file( $plugin_path ) {
		return $plugin_path . 'uninstall.php';
	}
}
