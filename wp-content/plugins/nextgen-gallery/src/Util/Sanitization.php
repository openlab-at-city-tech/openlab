<?php

namespace Imagely\NGG\Util;

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
}
