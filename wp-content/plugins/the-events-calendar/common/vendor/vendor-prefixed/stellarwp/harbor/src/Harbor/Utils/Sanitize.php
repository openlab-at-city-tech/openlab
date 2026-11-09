<?php

namespace TEC\Common\LiquidWeb\Harbor\Utils;

class Sanitize {
	/**
	 * Sanitizes a key.
	 *
	 * @since 1.2.2
	 *
	 * @param string $key The key to sanitize.
	 *
	 * @return string
	 */
	public static function key( $key ) {
		return str_replace( [ '`', '"', "'" ], '', $key );
	}

	/**
	 * Sanitizes a title, replacing whitespace and a few other characters with hyphens.
	 *
	 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
	 * Whitespace becomes a hyphen.
	 *
	 * @since 1.3.0
	 *
	 * @param string           $title     The title to be sanitized.
	 * @param 'display'|'save' $context   Optional. The operation for which the string is sanitized.
	 *                                    When set to 'save', additional entities are converted to hyphens
	 *                                    or stripped entirely. Default 'save'.
	 * @return string The sanitized title.
	 */
	public static function sanitize_title_with_hyphens( string $title, string $context = 'save' ): string {
		return str_replace( '-', '_', sanitize_title_with_dashes( $title, '', $context ) );
	}
}
