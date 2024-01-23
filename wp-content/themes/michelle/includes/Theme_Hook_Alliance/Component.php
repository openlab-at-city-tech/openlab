<?php
/**
 * Theme Hook Alliance component.
 *
 * @link  https://github.com/zamoose/themehookalliance
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Theme_Hook_Alliance;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			define( 'THA_HOOKS_VERSION', '1.0-draft' );

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::after_setup_theme' );

			// Filters

				add_filter( 'current_theme_supports-tha_hooks', __CLASS__ . '::support', 10, 3 );

	} // /init

	/**
	 * After setup theme.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {

		// Processing

			add_theme_support( 'tha_hooks', array( 'all' ) );

	} // /after_setup_theme

	/**
	 * Determines, whether the specific hook type is actually supported.
	 *
	 * @since  1.0.0
	 *
	 * @param   bool  $bool       True
	 * @param   array $args       The hook type being checked
	 * @param   array $registered All registered hook types
	 *
	 * @return  bool
	 */
	public static function support( bool $bool, array $args, array $registered ): bool {

		// Output

			return
				in_array( $args[0], $registered[0] )
				|| in_array( 'all', $registered[0] );

	} // /support

}
