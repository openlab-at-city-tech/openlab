<?php
/**
 * One Click Demo Import integration component.
 *
 * @link  https://wordpress.org/plugins/one-click-demo-import/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Plugin\One_Click_Demo_Import;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Setup\Media;

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

			// Actions

				add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles', 99 );

				add_action( 'pt-ocdi/before_content_import', __CLASS__ . '::before' );
				add_action( 'pt-ocdi/after_import',          __CLASS__ . '::after' );

			// Filters

				add_filter( 'pt-ocdi/plugin_intro_text', __CLASS__ . '::info' );

				add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

	} // /init

	/**
	 * Info texts.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $text  Default intro text.
	 *
	 * @return  string
	 */
	public static function info( string $text ): string {

		// Processing

			ob_start();
			get_template_part( 'templates/parts/plugin/content-ocdi', 'info' );


		// Output

			return $text . ob_get_clean();

	} // /info

	/**
	 * Before import actions.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function before() {

		// Variables

			$image_sizes = Media::get_image_sizes();


		// Processing

			// Image sizes.
			foreach ( Media::get_default_image_sizes() as $size ) {
				if ( isset( $image_sizes[ $size ] ) ) {
					update_option( $size . '_size_w', $image_sizes[ $size ]['width'] );
					update_option( $size . '_size_h', $image_sizes[ $size ]['height'] );
					update_option( $size . '_crop', $image_sizes[ $size ]['crop'] );
				}
			}

	} // /before

	/**
	 * After import actions.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function after() {

		// Processing

			self::front_and_blog_page();
			self::menu_locations();

	} // /after

	/**
	 * Setup front and blog page.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function front_and_blog_page() {

		// Processing

			update_option( 'show_on_front', 'page' );

			$page = get_page_by_path( 'home-1' );
			if ( $page ) {
				update_option( 'page_on_front', $page->ID );
			}

			$page = get_page_by_path( 'news' );
			if ( $page ) {
				update_option( 'page_for_posts', $page->ID );
			}

	} // /front_and_blog_page

	/**
	 * Setup navigation menu locations.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function menu_locations() {

		// Variables

			$menu            = array();
			$menu['primary'] = get_term_by( 'slug', 'primary-menu', 'nav_menu' );


		// Processing

			set_theme_mod( 'nav_menu_locations', array(
				'primary' => ( isset( $menu['primary']->term_id ) ) ? ( $menu['primary']->term_id ) : ( null ),
			) );

	} // /menu_locations

	/**
	 * OCDI plugin admin page styles.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function styles() {

		// Processing

			wp_add_inline_style(
				'ocdi-main-css',
				'.ocdi__content-container { max-width: 1024px; }'
			);

	} // /styles

}
