<?php
/**
 * Post CSS class component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Entry;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Post_Class implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Filters

				add_filter( 'post_class', __CLASS__ . '::set', 98 );

	} // /init

	/**
	 * Post/entry classes.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $classes
	 *
	 * @return  array
	 */
	public static function set( array $classes ): array {

		// Processing

			// A generic class for easy styling.
			$classes[] = 'entry';
			$classes[] = sprintf( 'entry-type-%s', get_post_type() );

			// Compensation for sticky entry class on paginated posts list.
			if ( is_sticky() ) {
				$classes[] = 'is-sticky';
			}


		// Output

			return $classes;

	} // /set

}
