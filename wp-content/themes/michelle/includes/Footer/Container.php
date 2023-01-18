<?php
/**
 * Footer container class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Footer;

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

				add_action( 'tha_footer_top',    __CLASS__ . '::outer', 1 );
				add_action( 'tha_footer_bottom', __CLASS__ . '::outer', 101 );

				add_action( 'tha_body_bottom', __CLASS__ . '::site_close', 100 );

	} // /init

	/**
	 * Footer outer container.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function outer() {

		// Output

			if ( doing_action( 'tha_footer_top' ) ) {
				echo PHP_EOL.PHP_EOL . '<footer id="colophon" class="site-footer">' . PHP_EOL;
			} elseif ( doing_action( 'tha_footer_bottom' ) ) {
				echo PHP_EOL . '</footer><!-- /#colophon.site-footer -->' . PHP_EOL.PHP_EOL;
			}

	} // /outer

	/**
	 * Site container: Close
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function site_close() {

		// Output

			echo PHP_EOL . '</div><!-- /#page.site -->' . PHP_EOL.PHP_EOL;

	} // /site_close

}
