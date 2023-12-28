<?php
/**
 * Header container class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Header;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Container implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'tha_body_top', __CLASS__ . '::site_open' );

				add_action( 'tha_header_top', __CLASS__ . '::outer', 1 );
				add_action( 'tha_header_top', __CLASS__ . '::inner', 9 );

				add_action( 'tha_header_bottom', __CLASS__ . '::inner', 1 );
				add_action( 'tha_header_bottom', __CLASS__ . '::outer', 101 );

	} // /init

	/**
	 * Site container: Open.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function site_open() {

		// Output

			echo '<div id="page" class="site">' . PHP_EOL;

	} // /site_open

	/**
	 * Header outer container.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function outer() {

		// Output

			if ( doing_action( 'tha_header_top' ) ) {
				echo PHP_EOL.PHP_EOL . '<header id="masthead" class="site-header">' . PHP_EOL;
			} elseif ( doing_action( 'tha_header_bottom' ) ) {
				echo PHP_EOL . '</header><!-- /#masthead.site-header -->' . PHP_EOL.PHP_EOL;
			}

	} // /outer

	/**
	 * Header inner container.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function inner() {

		// Output

			if ( doing_action( 'tha_header_top' ) ) {
				echo '<div class="site-header-section">' . PHP_EOL;
				echo '<div class="site-header-content">' . PHP_EOL;
			} elseif ( doing_action( 'tha_header_bottom' ) ) {
				echo PHP_EOL . '</div><!-- /.site-header-content -->';
				echo PHP_EOL . '</div><!-- /.site-header-section -->';
			}

	} // /inner

}
