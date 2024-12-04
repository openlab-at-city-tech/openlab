<?php


namespace ColibriWP\Theme\Core;

use ColibriWP\Theme\Theme;
use function add_action;
use function add_filter;

/**
 * Class Hooks
 *
 * @package ColibriTheme\Core
 *
 * @method static Hooks prefixed_add_action( string $tag, callable $function_to_add, $priority = 10, $accepted_args = 1 )
 * @method static Hooks prefixed_add_filter( string $tag, callable $function_to_add, $priority = 10, $accepted_args = 1 )
 * @method static Hooks prefixed_do_action( string $tag, ...$args )
 * @method static Hooks mixed prefixed_apply_filters( string $tag, $value, ...$args )
 */
class Hooks {

	/**
	 * @param string   $tag
	 * @param callable $function_to_add
	 * @param int      $priority
	 * @param int      $accepted_args
	 */
	public static function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		add_action( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * @param string   $tag
	 * @param callable $function_to_add
	 * @param int      $priority
	 * @param int      $accepted_args
	 */
	public static function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {

		add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	public static function do_action( $tag, $arg = '' ) {
		return call_user_func_array( 'do_action', func_get_args() );
	}

	/**
	 * @param string $tag The name of the filter hook.
	 * @param mixed  $value The value on which the filters hooked to `$tag` are applied on.
	 * @param mixed  $var,... Additional variables passed to the functions hooked to `$tag`.
	 *
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public static function apply_filters( $tag, $value ) {
		return call_user_func_array( 'apply_filters', func_get_args() );
	}

	public static function __callStatic( $name, $arguments ) {
		if ( strpos( $name, 'prefixed_' ) === 0 ) {
			$name         = str_replace( 'prefixed_', '', $name );
			$arguments[0] = Theme::prefix( $arguments[0] );
			return call_user_func_array( array( __CLASS__, $name ), $arguments );
		}
	}

	public static function add_wp_ajax( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		// error_log(' \'wp_ajax_\'. Theme::prefix($tag, false)'.  'wp_ajax_'. Theme::prefix($tag, false));
		add_action( 'wp_ajax_' . Theme::prefix( $tag, false ), $function_to_add, $priority, $accepted_args );
	}


	/**
	 * @param $data
	 *
	 * @return \Closure
	 */
	public static function identity( $data ) {
		return function () use ( $data ) {
			return $data;
		};
	}
}
