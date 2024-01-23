<?php
/**
 * Content container component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

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

				add_action( 'tha_content_top', __CLASS__ . '::outer', 10 );
				add_action( 'tha_content_top', __CLASS__ . '::inner', 20 );
				add_action( 'tha_content_top', __CLASS__ . '::main', 30 );

				add_action( 'tha_content_bottom', __CLASS__ . '::main', 80 );
				add_action( 'tha_content_bottom', __CLASS__ . '::inner', 90 );
				add_action( 'tha_content_bottom', __CLASS__ . '::outer', 100 );

	} // /init

	/**
	 * Content outer container.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function outer() {

		// Output

			if ( doing_action( 'tha_content_top' ) ) {
				echo PHP_EOL.PHP_EOL . '<div id="content" class="site-content">';
			} elseif ( doing_action( 'tha_content_bottom' ) ) {
				echo PHP_EOL . '</div><!-- /#content.site-content -->' . PHP_EOL.PHP_EOL;
			}

	} // /outer

	/**
	 * Content inner container.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function inner() {

		// Output

			if ( doing_action( 'tha_content_top' ) ) {
				echo PHP_EOL . "\t" . '<div class="content-area">';
			} elseif ( doing_action( 'tha_content_bottom' ) ) {
				echo PHP_EOL . "\t" . '</div><!-- /.content-area -->';
			}

	} // /inner

	/**
	 * Content main.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function main() {

		// Output

			if ( doing_action( 'tha_content_top' ) ) {
				echo PHP_EOL . "\t\t" . '<main id="main" class="site-main">' . PHP_EOL.PHP_EOL;
			} elseif ( doing_action( 'tha_content_bottom' ) ) {
				echo PHP_EOL.PHP_EOL . "\t\t" . '</main><!-- /#main.site-main -->';
			}

	} // /main

}
