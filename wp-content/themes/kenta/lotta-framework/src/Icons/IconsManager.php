<?php

namespace LottaFramework\Icons;

class IconsManager {

	/**
	 * @var null|array
	 */
	protected static $_fontawesome = null;

	/**
	 * @var array
	 */
	protected static $_fa_library = [];

	/**
	 * @return array
	 */
	public static function fontawesome() {
		if ( self::$_fontawesome === null ) {
			self::$_fontawesome = json_decode( file_get_contents( dirname( __FILE__ ) . '/fontawesome.json' ), true );
		}

		return self::$_fontawesome;
	}

	/**
	 * @param $library
	 *
	 * @return array|mixed
	 */
	public static function faLibrary( $library ) {
		$library = substr( $library, 0, 1 );

		if ( ! isset( $_fa_library[ $library ] ) ) {
			$_fa_library[ $library ] = [];

			foreach ( self::fontawesome() as $icon => $data ) {
				if ( in_array( $library, $data['s'] ) ) {
					$_fa_library[ $library ][ $icon ] = [
						'value' => "fa{$library} fa-{$icon}"
					];
				}
			}
		}

		return $_fa_library[ $library ];
	}

	/**
	 * Get all libraries
	 *
	 * @return array
	 */
	public static function allLibraries() {
		return [
			'fa-regular' => [
				'icons' => self::faLibrary( 'regular' ),
			],
			'fa-solid'   => [
				'icons' => self::faLibrary( 'solid' ),
			],
			'fa-brands'   => [
				'icons' => self::faLibrary( 'brands' ),
			],
		];
	}

	/**
	 * Render icon
	 *
	 * @param $icon
	 *
	 * @return string
	 */
	public static function render( $icon ) {
		return '<i class="' . ( $icon['value'] ?? '' ) . '"></i>';
	}

	/**
	 * Print icon
	 *
	 * @param $icon
	 */
	public static function print( $icon ) {
		echo wp_kses_post( self::render( $icon ) );
	}
}