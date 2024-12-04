<?php


namespace ColibriWP\Theme\Core;

class Utils {

	public static function camel2dashed( $string ) {
		return strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $string ) );
	}

	public static function replace_file_extension( $filename, $old_extenstion, $new_extension ) {

		return preg_replace( '#\\' . $old_extenstion . '$#', $new_extension, $filename );
	}

	public static function pathSet( &$data, $path, $value ) {
		if ( ! is_array( $path ) ) {
			$path = preg_replace( '#\.\.+#', '.', $path );
			$path = explode( '.', (string) $path );
		}

		$ref = &$data;

		foreach ( $path as $parent ) {
			if ( isset( $ref ) && ! is_array( $ref ) ) {
				$ref = array();
			}

			$ref = &$ref[ $parent ];
		}

		$ref = $value;

		return $data;
	}

	public static function recursiveOnly( $from, $except ) {

		foreach ( $from as $key => $value ) {
			if ( ! in_array( $key, $except ) ) {
				unset( $from[ $key ] );
			} else {
				if ( is_array( $value ) ) {
					$from[ $key ] = static::recursiveOnly( $value, $except );
				}
			}
		}

		return $from;
	}

	public static function recursiveWithout( $from, $except ) {

		foreach ( $from as $key => $value ) {
			if ( in_array( $key, $except ) ) {
				unset( $from[ $key ] );
			} else {
				if ( is_array( $value ) ) {
					$from[ $key ] = static::recursiveWithout( $value, $except );
				}
			}
		}

		return $from;
	}

	public static function arrayGetAt( $array, $index = 0, $fallback = false ) {
		return self::pathGet( $array, (string) $index, $fallback );
	}


	/**
	 * @param $data
	 * @param string|null $path
	 * @param string|null $fallback
	 *
	 * @return mixed|null
	 */
	public static function pathGet( $data, $path = null, $fallback = null ) {

		if ( strlen( $path ) === 0 || $path === null ) {
			return $data;
		}

		$path = preg_replace( '#\.\.+#', '.', $path );

		$result = $data;

		$path = explode( '.', $path );

		if ( count( $path ) ) {
			foreach ( $path as $key ) {

				if ( ! isset( $result[ $key ] ) ) {
					$result = $fallback;
					break;
				}

				$result = $result[ $key ];
			}
		}

		return $result;

	}

	public static function flatten( $array, $prefix = '' ) {
		$return = array();

		foreach ( $array as $key => $value ) {
			$key = trim( "{$prefix}.{$key}", '.' );
			if ( ! is_array( $value ) ) {
				$return[ $key ] = $value;
			} else {
				$return = array_merge( $return, static::flatten( $value, $key ) );
			}
		}

		return $return;
	}

	public static function slugify( $text ) {
		// replace non letter or digits by -
		$text = preg_replace( '~[^\pL\d]+~u', '-', $text );

		// transliterate
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

		// remove unwanted characters
		$text = preg_replace( '~[^-\w]+~', '', $text );

		// trim
		$text = trim( $text, '-' );

		// remove duplicate -
		$text = preg_replace( '~-+~', '-', $text );

		// lowercase
		$text = strtolower( $text );

		if ( empty( $text ) ) {
			return 'n-a';
		}

		return $text;
	}

	public static function buffer_wrap( $callback, $strip_tags = false ) {
		ob_start();
		call_user_func( $callback );
		$content = ob_get_clean();

		if ( $strip_tags ) {
			$content = strip_tags( $content );
		}

		return $content;
	}


	/**
	 * @param array        $array
	 * @param array|string $key
	 *
	 * @return array
	 */
	public static function pathDelete( $array, $key ) {
		$keys = (array) $key;

		foreach ( $keys as $key ) {
			$key_parts = explode( '.', $key );
			$ref       = &$array;

			while ( count( $key_parts ) ) {
				$key_part = array_shift( $key_parts );
				if ( array_key_exists( $key_part, $ref ) ) {

					if ( count( $key_parts ) === 0 ) {
						unset( $ref[ $key_part ] );
					} else {
						$ref = &$array[ $key_part ];
					}
				} else {
					break;
				}
			}
		}

		return $array;

	}

	public static function sanitizeSelectControl( $control_data, $current_value ) {

		if ( $control_data['type'] === 'linked-select' ) {
			$possible_values = array();

			foreach ( self::pathGet( $control_data, 'choices', array() ) as $choices_data ) {
				$possible_values = array_merge( $possible_values, array_keys( $choices_data ) );
			}
		} else {
			$possible_values = array_keys( self::pathGet( $control_data, 'choices', array() ) );

		}

		if ( in_array( $current_value, $possible_values ) ) {
			return $current_value;
		}

		return null;
	}

	public static function sanitizeEscapedJSON( $value ) {

		if ( is_array( $value ) || is_object( $value ) ) {
			return $value;
		}

		$value = json_decode( urldecode( $value ), true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			return $value;
		}

		return null;
	}
}
