<?php
/**
 * Compat Function
 *
 * @since 3.5.1
 *
 * @package NextGEN Gallery
 */

if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * Polyfill for str_contains
	 *
	 * @since 3.5.1
	 *
	 * @param array  $haystack Array to search.
	 * @param string $needle What to find.
	 *
	 * @return bool
	 */
	function str_contains( $haystack, $needle ) {
		return '' !== $needle && false !== mb_strpos( $haystack, $needle );
	}
}
