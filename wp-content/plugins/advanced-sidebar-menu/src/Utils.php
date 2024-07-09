<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Rules\Utils as Rules;
use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Various helpers for the Advanced Sidebar Menu plugin.
 *
 * @author OnPoint Plugins
 * @since  8.4.1
 */
class Utils implements Rules {
	use Singleton;

	/**
	 * Is a widget's checkbox checked?
	 *
	 * Checks first for a value then verifies the value = checked.
	 *
	 * @param string $name     - name of checkbox.
	 * @param array<string, mixed> $settings - Widget settings.
	 *
	 * @return bool
	 */
	public function is_checked( $name, array $settings ): bool {
		// Handle array type names (e.g. open-links[all]).
		preg_match( '/(?<field>\S*?)\[(?<key>\S*?)]/', $name, $array );
		if ( isset( $array['field'], $array['key'] ) ) {
			return isset( $settings[ $array['field'] ][ $array['key'] ] ) && 'checked' === $settings[ $array['field'] ][ $array['key'] ];
		}

		// Standard non array names.
		return isset( $settings[ $name ] ) && 'checked' === $settings[ $name ];
	}


	/**
	 * Is a setting available and not an empty string?
	 *
	 * @since 9.5.0
	 *
	 * @param array<string, mixed> $settings - Settings to compare against.
	 * @param string               $key      - Key of settings which may be available.
	 *
	 * @return bool
	 */
	public function is_empty( array $settings, string $key ): bool {
		return ! isset( $settings[ $key ] ) ||
			'' === $settings[ $key ] ||
			'0' === $settings[ $key ] ||
			0 === $settings[ $key ] ||
			false === $settings[ $key ];
	}


	/**
	 * Apply a callback to all elements of an array recursively.
	 *
	 * Like `array_walk_recursive` except returns the result as
	 * a new array instead of requiring you pass the array element by reference
	 * and alter it directly.
	 *
	 * @since 8.6.5
	 *
	 * @param callable $callback   - Callback for each element in each level of array.
	 * @param array<string, mixed> $to_recurse - Array to recurse.
	 *
	 * @return array<string, mixed>
	 */
	public function array_map_recursive( callable $callback, array $to_recurse ): array {
		$output = [];
		foreach ( $to_recurse as $key => $data ) {
			if ( \is_array( $data ) ) {
				$output[ $key ] = $this->array_map_recursive( $callback, $data );
			} else {
				$output[ $key ] = $callback( $data );
			}
		}

		return $output;
	}


	/**
	 * Get the label for used post type.
	 *
	 * For adjusting widget option labels.
	 *
	 * @since 9.5.0
	 *
	 * @param string $type   - Post type to get label for.
	 * @param bool   $single - Singular label or plural.
	 *
	 * @return string
	 */
	public function get_post_type_label( string $type, $single = true ): string {
		$post_type = get_post_type_object( $type );
		if ( 'page' !== $type && null === $post_type ) {
			$post_type = get_post_type_object( 'page' ); // Sensible fallback.
		}
		if ( null === $post_type ) {
			return $single ? __( 'Page', 'advanced-sidebar-menu' ) : __( 'Pages', 'advanced-sidebar-menu' );
		}

		return $single ? $post_type->labels->singular_name : $post_type->labels->name;
	}


	/**
	 * Is this value a truthy value?
	 *
	 * For checking types which may be stored differently in the database
	 * based on context (E.G., '1' true 'checked').
	 *
	 * @since 9.5.0
	 *
	 * @param bool|string|int $value - Value to check.
	 *
	 * @return bool
	 */
	public function is_truthy( $value ): bool {
		return true === $value || 'true' === $value || 1 === $value || '1' === $value || 'checked' === $value || 'on' === $value;
	}
}
