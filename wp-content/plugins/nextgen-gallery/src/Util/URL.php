<?php

namespace Imagely\NGG\Util;

class URL {

	public static function get_source( $source_name ) {
		// Nonce checks are not necessary: nothing is happening here, only the mapping of string to variable.
		//
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        // phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( 'request' === $source_name ) {
			return $_REQUEST;
		} elseif ( 'get' === $source_name ) {
			return $_GET;
		} elseif ( 'post' === $source_name ) {
			return $_POST;
		} elseif ( 'server' === $source_name ) {
			return $_SERVER;
		}

        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        // phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	public static function param( string $name, string $source = 'request', string $validation_method = 'sanitize_text_field' ) {
		if ( ! self::has_param( $name ) ) {
			return null;
		}

		$source = self::get_source( $source );
		return $validation_method( wp_unslash( $source[ $name ] ) );
	}

	public static function has_param( string $name, string $source = 'request' ): bool {
		$source = self::get_source( $source );
		return isset( $source[ $name ] );
	}
}
