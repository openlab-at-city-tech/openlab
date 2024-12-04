<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\Hooks;

class ThemeTranslations {
	protected static $texts = array();

	public static function load() {
		$texts       = require_once Theme::rootDirectory() . '/inc/theme-translations.php';
		self::$texts = Hooks::prefixed_apply_filters( 'theme_translations', $texts );
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
}
