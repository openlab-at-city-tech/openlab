<?php
/**
 * Normalize the CSA `roles` POST input — checkbox group OR CSV — into a
 * CSV of WP role slugs that actually exist on the site.
 *
 * The settings-form checkbox group is the primary shape:
 *   csa_settings[roles][administrator] = "1"
 *   csa_settings[roles][editor]        = "1"
 *
 * Programmatic callers (admin tooling, future REST writes) may send a
 * CSV string instead. Both flow through here so the per-site and
 * network-defaults forms apply the same validation.
 *
 * Unknown slugs are dropped silently — `ed11y_get_developer_role_options()`
 * is the trust boundary; anything not registered there can't see
 * developer alerts anyway.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Stateless POST → validated CSV converter for the CSA `roles` field.
 */
final class RoleNormalizer {

	/**
	 * Normalize a raw `roles` POST value into a validated CSV string.
	 *
	 * Accepts:
	 *   - associative array (checkbox group: `[slug => '1']`)
	 *   - CSV string (`'administrator,editor'`)
	 *   - anything else → empty string
	 *
	 * @param mixed $input Raw posted value (array, string, or other).
	 * @return string CSV of role slugs known to {@see ed11y_get_developer_role_options()}.
	 */
	public static function normalize( $input ): string {
		$known = array_keys( ed11y_get_developer_role_options() );

		if ( is_string( $input ) ) {
			$slugs = array_filter( array_map( 'trim', explode( ',', $input ) ) );
			$input = array_fill_keys( $slugs, '1' );
		}
		if ( ! is_array( $input ) ) {
			$input = array();
		}

		$valid = array_values( array_intersect( array_keys( $input ), $known ) );
		return implode( ',', $valid );
	}

	/**
	 * Whether two roles CSVs name the same set of slugs, ignoring order
	 * and duplicates.
	 *
	 * The checkbox group posts slugs in render order (super-admin pseudo-
	 * role first), while the hardcoded default lists `administrator`
	 * first — so a plain string compare reports the untouched form as a
	 * change. Order carries no meaning for a role set.
	 *
	 * @param string $left  Roles CSV.
	 * @param string $right Roles CSV.
	 */
	public static function same_set( string $left, string $right ): bool {
		$to_set = static function ( string $csv ): array {
			$slugs = array_unique( array_filter( array_map( 'trim', explode( ',', $csv ) ) ) );
			sort( $slugs );
			return array_values( $slugs );
		};
		return $to_set( $left ) === $to_set( $right );
	}
}
