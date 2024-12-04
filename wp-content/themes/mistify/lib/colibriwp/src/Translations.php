<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\Hooks;

class Translations {
	protected static $texts = array();

	public static function load() {
		$texts         = require_once Theme::rootDirectory() . '/inc/translations.php';
		static::$texts = Hooks::prefixed_apply_filters( 'translations', $texts );
	}

	public static function e( $key, $params = array() ) {
		static::render( $key, $params );
	}

	public static function render( $key, $params = array() ) {
		echo static::get( $key, $params );
	}

	public static function get( $key, $params = array() ) {
		$text = "__[{$key}]__";
		if ( isset( static::$texts[ $key ] ) ) {
			$text = static::$texts[ $key ];
		}
		$params = (array) $params;

		if ( empty( $params ) ) {
			return $text;
		}

		array_unshift( $params, $text );

		return call_user_func_array( 'sprintf', $params );
	}

	public static function escHtmlE( $key, $params = array() ) {
		echo static::escHtml( $key, $params );
	}

	public static function escHtml( $key, $params = array() ) {
		return esc_html( static::get( $key, $params ) );
	}

	public static function escAttrE( $key, $params = array() ) {
		echo esc_attr( static::get( $key, $params ) );
	}

	public static function translate( $key, $params = array() ) {
		return static::get( $key, $params );
	}

	public static function all() {
		return static::$texts;
	}
}
