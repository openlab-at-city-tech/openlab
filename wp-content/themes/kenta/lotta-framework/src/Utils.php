<?php

namespace LottaFramework;

use LottaFramework\Container\Container;

class Utils {

	/**
	 * Echo version for clsx
	 *
	 * @param ...$args
	 */
	public static function the_clsx( ...$args ) {
		echo esc_attr( self::clsx( ...$args ) );
	}

	/**
	 * A utility for constructing className strings conditionally.
	 *
	 * @param ...$args
	 *
	 * @return string
	 */
	public static function clsx( ...$args ) {
		$classNames = array();

		foreach ( $args as $arg ) {
			if ( is_string( $arg ) && $arg !== '' ) {
				$classNames[] = $arg;
			} else if ( is_array( $arg ) ) {
				foreach ( $arg as $k => $v ) {
					if ( is_string( $v ) ) {
						$classNames[] = $v;
					} else if ( is_bool( $v ) && $v === true ) {
						$classNames[] = $k;
					}
				}
			}
		}

		return implode( ' ', $classNames );
	}

	/**
	 * Print attribute string
	 *
	 * @param $attributes
	 */
	public static function print_attribute_string( $attributes ) {
		echo self::render_attribute_string( $attributes );
	}

	/**
	 * Render attribute string
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	public static function render_attribute_string( $attributes ) {
		$attrs = [];

		foreach ( $attributes as $attr => $value ) {
			$attrs[] = $attr . '=' . '"' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $attrs );
	}

	/**
	 * Encode uri component
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public static function encode_uri_component( $str ) {
		$revert = [
			'%21' => '!',
			'%2A' => '*',
			'%27' => "'",
			'%28' => '(',
			'%29' => ')',
		];

		return strtr( rawurlencode( $str ), $revert );
	}

	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @See: https://github.com/laravel/framework
	 *
	 * @param $array
	 * @param $depth
	 *
	 * @return array
	 */
	public static function array_flatten( $array, $depth = INF ) {
		$result = [];

		foreach ( $array as $item ) {

			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = $depth === 1
					? array_values( $item )
					: self::array_flatten( $item, $depth - 1 );

				foreach ( $values as $value ) {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Collapse an array of arrays into a single array.
	 *
	 * @See: https://github.com/laravel/framework
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public static function array_collapse( $array ) {
		$results = [];

		foreach ( $array as $values ) {
			if ( ! is_array( $values ) ) {
				continue;
			}

			$results[] = $values;
		}

		return array_merge( [], ...$results );
	}

	/**
	 * Just like array_pluck function in laravel
	 *
	 * @param $key
	 * @param $arr
	 *
	 * @return array
	 */
	public static function array_pluck( $key, $arr ) {
		return array_map( function ( $item ) use ( $key ) {
			return $item[ $key ];
		}, $arr );
	}

	/**
	 * Find value in an array using a string path
	 *
	 * @param $arr
	 * @param $path
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public static function array_path( $arr, $path, $default = null ) {
		$keys   = explode( '.', $path );
		$source = $arr;

		while ( count( $keys ) > 0 ) {
			$key = array_shift( $keys );

			// collect value
			if ( $key === '[]' ) {
				$result = [];

				foreach ( $source as $item ) {
					$result[] = self::array_path( $item, implode( '.', $keys ), $default );
				}

				return $result;
			}

			if ( is_array( $source ) && isset( $source[ $key ] ) ) {
				$source = $source[ $key ];
			} else {
				// current key doesn't exist, stop loop and return default value
				return $default;
			}
		}

		// we have reached the end of the path
		return $source;
	}

	/**
	 * Generate rand key
	 *
	 * @return string
	 */
	public static function rand_key() {
		return 'lotta_rand_' . md5( time() . '-' . uniqid( wp_rand(), true ) . '-' . wp_rand() );
	}

	/**
	 * Get units config
	 *
	 * @param array $overrides
	 *
	 * @return array[]
	 */
	public static function units_config( array $overrides = [] ) {
		$units = [
			[
				'unit' => 'px',
				'min'  => 0,
				'max'  => 40,
			],
			[
				'unit' => 'em',
				'min'  => 0,
				'max'  => 30,
			],
			[
				'unit' => '%',
				'min'  => 0,
				'max'  => 100,
			],
			[
				'unit' => 'vw',
				'min'  => 0,
				'max'  => 100,
			],
			[
				'unit' => 'vh',
				'min'  => 0,
				'max'  => 100,
			],
			[
				'unit' => 'pt',
				'min'  => 0,
				'max'  => 100,
			],
			[
				'unit' => 'rem',
				'min'  => 0,
				'max'  => 30,
			],
		];

		foreach ( $overrides as $single_override ) {
			foreach ( $units as $key => $single_unit ) {
				if ( $single_override['unit'] === $single_unit['unit'] ) {
					$units[ $key ] = $single_override;
				}
			}
		}

		return $units;
	}

	/**
	 * Polyfill for `str_contains()` function added in PHP 8.0.
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
	 */
	public static function str_contains( $haystack, $needle ) {
		return ( '' === $needle || false !== strpos( $haystack, $needle ) );
	}

	/**
	 * Polyfill for `str_starts_with()` function added in PHP 8.0.
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
	 */
	public static function str_starts_with( $haystack, $needle ) {
		if ( function_exists( 'str_starts_with' ) ) {
			return str_starts_with( $haystack, $needle );
		}

		if ( '' === $needle ) {
			return true;
		}

		return 0 === strpos( $haystack, $needle );
	}

	/**
	 * Polyfill for `str_ends_with()` function added in PHP 8.0.
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
	 */
	public static function str_ends_with( $haystack, $needle ) {
		if ( function_exists( 'str_ends_with' ) ) {
			return str_ends_with( $haystack, $needle );
		}

		if ( '' === $haystack && '' !== $needle ) {
			return false;
		}
		$len = strlen( $needle );

		return 0 === substr_compare( $haystack, $needle, - $len, $len );
	}

	/**
	 * Echo version for customizer_url
	 *
	 * @param $location
	 *
	 * @return void
	 */
	public static function the_customizer_url( $location ) {
		echo esc_url( self::customizer_url( $location ) );
	}

	/**
	 * Get customizer_url
	 *
	 * @param $location
	 *
	 * @return string
	 */
	public static function customizer_url( $location ) {
		$query                     = array();
		$query['lotta_auto_focus'] = $location;

		return add_query_arg( $query, admin_url( 'customize.php' ) );
	}

	/**
	 * Register translation string
	 *
	 * @param $str
	 * @param $domain
	 */
	public static function register_translate_string( $str, $domain ) {
		if ( function_exists( 'pll_register_string' ) ) {
			pll_register_string( $domain, $str, self::app()->id() );
		} else {
			do_action( 'wpml_register_single_string', self::app()->id(), $domain, $str );
		}
	}

	/**
	 * Get application instance
	 *
	 * @param null $abstract
	 * @param array $parameters
	 *
	 * @return Application|mixed|object
	 */
	public static function app( $abstract = null, array $parameters = [] ) {
		if ( is_null( $abstract ) ) {
			return Container::getInstance();
		}

		return Container::getInstance()->make( $abstract, $parameters );
	}

	/**
	 * Get translate string
	 *
	 * @param string $str
	 * @param string $domain
	 *
	 * @return mixed
	 */
	public static function __( $str, $domain ) {
		if ( function_exists( 'pll__' ) ) {
			return pll__( $str );
		}

		return apply_filters( 'wpml_translate_single_string', $str, self::app()->id(), $domain );
	}
}