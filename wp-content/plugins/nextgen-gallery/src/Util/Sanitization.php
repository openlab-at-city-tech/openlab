<?php

namespace Imagely\NGG\Util;

/**
 * Utility class for data sanitization operations.
 *
 * Provides methods for cleaning and sanitizing various types of data.
 */
class Sanitization {

	/**
	 * Recursively calls stripslashes() on strings, arrays, and objects
	 *
	 * @param mixed $value Value to be processed
	 * @return mixed Resulting value
	 */
	public static function recursive_stripslashes( $value ) {
		if ( is_string( $value ) ) {
			$value = stripslashes( $value );
		} elseif ( is_array( $value ) ) {
			foreach ( $value as &$tmp ) {
				$tmp = self::recursive_stripslashes( $tmp );
			}
		} elseif ( is_object( $value ) ) {
			foreach ( get_object_vars( $value ) as $key => $data ) {
				$value->{$key} = self::recursive_stripslashes( $data );
			}
		}

		return $value;
	}

	/**
	 * Returns an exception's message, guaranteed non-empty.
	 *
	 * Several handlers assign `$ex->getMessage()` straight into an `error` field whose emptiness
	 * then decides whether a failure is reported at all - `! empty( $retval['error'] )` gates the
	 * 500 header the uploader reads, and a blank entry in an `errors` list is invisible. Not every
	 * exception carries a message: `E_EntityNotFoundException` (nggallery.php:51) is an empty class
	 * extending RuntimeException and `DataStorage\Manager::import_image_file()` throws it bare, so
	 * `getMessage()` is `''`. Catching RuntimeException to show a better message therefore turned a
	 * reported failure into a silent success for exactly that throw.
	 *
	 * Callers pass a fallback describing the operation, so the user sees which step failed even
	 * when the exception says nothing. The class name is appended to the fallback because it is the
	 * only remaining signal about what was thrown.
	 *
	 * @param \Throwable $ex       The caught exception.
	 * @param string     $fallback Human-readable message to use when the exception carries none.
	 * @return string A non-empty message.
	 */
	public static function exception_message( $ex, $fallback ) {
		$message = trim( (string) $ex->getMessage() );

		if ( '' !== $message ) {
			return $message;
		}

		$fallback = trim( (string) $fallback );

		if ( '' === $fallback ) {
			$fallback = __( 'An unexpected error occurred.', 'nggallery' );
		}

		return sprintf(
			/* translators: 1: fallback error message, 2: exception class name. */
			__( '%1$s (%2$s)', 'nggallery' ),
			$fallback,
			get_class( $ex )
		);
	}
}
