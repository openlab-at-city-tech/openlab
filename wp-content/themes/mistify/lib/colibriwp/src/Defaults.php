<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Utils;

class Defaults {
	private static $defaults = array();

	private static $loaded = false;

	public static function getDefaults() {
		return static::$defaults;
	}

	public static function get( $key, $fallback = null ) {
		static::load();

		return Utils::pathGet( static::$defaults, $key, $fallback );
	}

	public static function load() {

		if ( static::$loaded ) {
			return;
		}

		$dir      = Theme::rootDirectory();
		$defaults = require_once $dir . '/inc/defaults.php';

		if ( file_exists( $dir . '/inc/template-defaults.php' ) ) {
			$template_defaults = require_once $dir . '/inc/template-defaults.php';
			static::$defaults  = array_replace_recursive( $template_defaults, $defaults );
		}

		static::$defaults = Hooks::prefixed_apply_filters( 'defaults', static::$defaults, $defaults );
		static::$loaded   = true;
	}

}
