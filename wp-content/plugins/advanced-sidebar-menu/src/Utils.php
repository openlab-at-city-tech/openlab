<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Various helpers for the Advanced Sidebar Menu plugin.
 *
 * @author OnPoint Plugins
 * @since  8.4.1
 */
class Utils {
	use Singleton;

	/**
	 * Checks if a widget's checkbox is checked.
	 *
	 * Checks first for a value then verifies the value = checked.
	 *
	 * @param string $name     - name of checkbox.
	 * @param array  $settings - Widget settings.
	 *
	 * @return bool
	 */
	public function is_checked( $name, array $settings ) {
		// Handle array type names (e.g. open-links[all]).
		preg_match( '/(?<field>\S*?)\[(?<key>\S*?)]/', $name, $array );
		if ( ! empty( $array['field'] ) && ! empty( $array['key'] ) ) {
			return isset( $settings[ $array['field'] ][ $array['key'] ] ) && 'checked' === $settings[ $array['field'] ][ $array['key'] ];
		}

		// Standard non array names.
		return isset( $settings[ $name ] ) && 'checked' === $settings[ $name ];
	}
}
