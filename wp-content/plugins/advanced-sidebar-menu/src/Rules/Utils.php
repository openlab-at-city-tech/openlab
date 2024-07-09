<?php

namespace Advanced_Sidebar_Menu\Rules;

/**
 * Enforce rules of the Utils class to guarantee signature does not
 * change without a major version update.
 *
 * @author OnPoint Plugins
 * @since  9.5.0
 */
interface Utils {
	/**
	 * Is a widget's checkbox checked?
	 *
	 * @param string               $name     - name of checkbox.
	 * @param array<string, mixed> $settings - Widget settings.
	 */
	public function is_checked( $name, array $settings ): bool;


	/**
	 * Is a setting available and not an empty string?
	 *
	 * @param array<string, mixed> $settings - Settings to compare against.
	 * @param string               $key      - Key of settings which may be available.
	 */
	public function is_empty( array $settings, string $key ): bool;


	/**
	 * Apply a callback to all elements of an array recursively.
	 *
	 * @param callable             $callback   - Callback for each element in each level of array.
	 * @param array<string, mixed> $to_recurse - Array to recurse.
	 *
	 * @return array<string, mixed>
	 */
	public function array_map_recursive( callable $callback, array $to_recurse ): array;


	/**
	 * Get the label for used post type.
	 *
	 * @param string $type   - Post type to get label for.
	 * @param bool   $single - Singular label or plural.
	 */
	public function get_post_type_label( string $type, $single = true ): string;


	/**
	 * Is this value a truthy value?
	 *
	 * For checking types which may be stored differently in the database
	 * based on context (E.G., '1' true 'checked').
	 *
	 * @param bool|string|int $value - Value to check.
	 *
	 * @return bool
	 */
	public function is_truthy( $value ): bool;
}
