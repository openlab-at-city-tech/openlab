<?php
/**
 * HTML body class component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Header;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize\Mod;
use WebManDesign\Michelle\Content;
use WebManDesign\Michelle\Loop;
use WP_Post;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Body_Class implements Component_Interface {

	/**
	 * Soft cache for body classes array.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string
	 */
	private static $body_classes = array();

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

				add_filter( 'body_class', __CLASS__ . '::body_class', 98 );

				add_filter( 'admin_body_class', __CLASS__ . '::body_class_admin' );

	} // /init

	/**
	 * HTML body classes.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $classes
	 *
	 * @return  array
	 */
	public static function body_class( array $classes ): array {

		// Processing

			// JS fallback.
			$classes[] = 'no-js';

			// Is mobile navigation enabled?
			if ( Mod::get( 'navigation_mobile' ) ) {
				$classes[] = 'has-navigation-mobile';
			}

			// Is sticky site header on mobile screens?
			if ( Mod::get( 'header_mobile_sticky' ) ) {
				$classes[] = 'has-sticky-header-mobile';
			}

			// Is site title text hidden?
			if ( ! Mod::get( 'display_site_title' ) ) {
				$classes[] = 'is-hidden-site-title';
			}

			// Singular entry?
			if ( is_singular() ) {
				$classes[] = 'is-singular';

				// Has featured image?
				if ( has_post_thumbnail() ) {
					$classes[] = 'has-post-thumbnail';
				}
			} else {

				// Add a class of hfeed to non-singular pages.
				$classes[] = 'hfeed';
			}

			// Has more than 1 published author?
			if ( is_multi_author() ) {
				$classes[] = 'group-blog';
			}

			// Is primary title displayed?
			if ( Content\Component::show_primary_title( $classes ) ) {
				$classes[] = 'has-primary-title';
			} else {
				$classes[] = 'no-primary-title';
			}

			// Site title position.
			$classes[] = sanitize_html_class( 'has-site-title-' . Mod::get( 'site_title_position' ) );

			// Enable header search form modal.
			$classes[] = 'has-search-form-modal';

			// Sort classes alphabetically.
			asort( $classes );


		// Output

			return array_unique( $classes );

	} // /body_class

	/**
	 * HTML body classes in admin area.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  string $classes
	 *
	 * @return  string
	 */
	public static function body_class_admin( string $classes ): string {

		// Requirements check

			global $post;

			if (
				! is_admin()
				|| ! $post instanceof WP_Post
			) {
				return $classes;
			}


		// Processing

			// Dark editor theme class.

				$content_color = sanitize_hex_color_no_hash( get_background_color() );

				/**
				 * Color darkness code inspiration:
				 * @link  https://github.com/mexitek/phpColors
				 */
				if ( 6 === strlen( $content_color ) ) {
					$r = hexdec( $content_color[0] . $content_color[1] );
					$g = hexdec( $content_color[2] . $content_color[3] );
					$b = hexdec( $content_color[4] . $content_color[5] );

					$content_color_darkness = ( $r * 299 + $g * 587 + $b * 114 ) / 1000;

					if ( 130 >= $content_color_darkness ) {
						$classes .= ' is-dark-theme';
					}
				}

			// "Editing footer" reusable block class.

				if ( Content\Block_Area::get_post_type() === get_post_type( $post ) ) {
					switch ( get_the_ID( $post ) ) {

						case get_theme_mod( 'block_area_site_footer' ):
							$classes .= ' editing-block-area-footer';
							break;

						case get_theme_mod( 'block_area_error_404' ):
							$classes .= ' editing-block-area-404';
							break;

						default:
							break;

					}
				}


		// Output

			return $classes;

	} // /body_class_admin

	/**
	 * Retrieves soft cached array of body classes.
	 *
	 * @since  1.0.0
	 *
	 * @param  mixed $classes  Optional additional classes.
	 *
	 * @return  array
	 */
	public static function get_body_class( $classes = array() ): array {

		// Variables

			if ( ! is_array( $classes ) ) {
				$classes = array();
			}

			if ( empty( self::$body_classes ) ) {
				if ( ! doing_filter( 'body_class' ) ) {
					self::$body_classes = get_body_class();
				}
			}


		// Output

			if ( empty( $classes ) ) {
				return self::$body_classes;
			} else {
				return array_unique( array_merge( self::$body_classes, $classes ) );
			}

	} // /get_body_class

}
