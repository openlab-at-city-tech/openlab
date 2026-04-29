<?php

namespace Imagely\NGG\Util;

/**
 * Serializable utility class.
 */
class Serializable {

	/**
	 * Serializes the data
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function serialize( $value ) {
		// Try encoding using JSON. It's usually Unicode safe but still, sometimes trips over things.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$serialized = @\wp_json_encode( $value );

		if ( ! $serialized ) {
			$serialized = \preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $value );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$serialized = @\wp_json_encode( $serialized );
		}

		// Using json_encode here because PHP's serialize is not Unicode safe.
		return \base64_encode( $serialized );
	}

	/**
	 * Unserializes data using our proprietary format
	 *
	 * @throws \Exception This method will not unserialize any objects
	 * @param string $value
	 * @return mixed
	 */
	public static function unserialize( $value ) {
		$retval = null;
		if ( \is_string( $value ) ) {
			$retval = \stripcslashes( $value );

			if ( \strlen( $value ) > 1 ) {
				// We can't always rely on base64_decode() or json_decode() to return FALSE as their documentation
				// claims so check if $retval begins with a: as that indicates we have a serialized PHP object.
				if ( \strpos( $retval, 'a:' ) === 0 ) {
					if ( self::check_for_serialized_objects( $value ) ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Translated string is safe
						throw new \Exception( \__( 'NextGEN Gallery will not unserialize data with objects', 'nggallery' ) );
					}

					// The second parameter was added by PHP 7.0.
					if ( \version_compare( \phpversion(), '7.0', '>=' ) ) {
						// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Required for legacy data handling
						$retval = @\unserialize( $value, [ 'allowed_classes' => false ] );
					} else {
						// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Required for legacy data handling
						$retval = @\unserialize( $value );
					}
				} else {
					// We use json_decode() here because PHP's unserialize() is not Unicode safe.
					$retval = \json_decode( \base64_decode( $retval ), true );
				}
			}
		}

		return $retval;
	}

	/**
	 * Determines if a string may hold a serialized PHP object
	 *
	 * @param $value
	 * @return bool
	 */
	public static function check_for_serialized_objects( $value ) {
		if ( ! \is_string( $value ) ) {
			return false;
		}
		$value = \trim( $value );
		return (bool) \preg_match( '/(O|C):\+?[0-9]+:/is', $value );
	}
}
