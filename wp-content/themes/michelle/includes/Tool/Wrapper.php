<?php
/**
 * (Template) Wrapper.
 *
 * Introducing a `wrapper.php` template for not repeating
 * `get_header()` and `get_footer()` template tags over and over.
 *
 * @link  http://scribu.net/wordpress/theme-wrappers.html
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Tool;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Wrapper implements Component_Interface {

	/**
	 * Base name of the template file; e.g. 'archive-post-type' for 'archive-post-type.php' etc.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public static $base;

	/**
	 * Base core name of the template file; e.g. 'archive' for 'archive-post-type.php' etc.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public static $base_core;

	/**
	 * Full path to the main template file; e.g. 'archive-post-type.php' etc.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public static $path;

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

				add_filter( 'template_include', __CLASS__ . '::template_include', 9999 );

	} // /init

	/**
	 * Use wrapper template before loading other templates.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  string $template  The path of the template to include.
	 *
	 * @return  string
	 */
	public static function template_include( string $template ): string {

		// Variables

			$templates = array( 'wrapper.php' );

			self::$path = $template;
			self::$base = (string) basename( self::$path, '.php' );


		// Processing

			// Apply custom CSS class for non-theme template.
			if (
				! stripos( $template, trailingslashit( get_stylesheet() ) )
				&& ! stripos( $template, trailingslashit( get_template() ) )
			) {
				add_filter( 'body_class', function( $classes ) {
					$classes[] = 'is-custom-template-file';
					return $classes;
				}, 0 );
			}

			// Main wrapper functionality.
			if ( 'index' === self::$base ) {
				self::$base = false;
			} else {

				/**
				 * Include base core, such as `wrapper-archive.php`
				 * for `archive-post-type.php` template request.
				 */
				if ( stripos( self::$base, '-' ) ) {
					self::$base_core = explode( '-', self::$base );
					$templates[]     = sprintf( 'wrapper-%s.php', reset( self::$base_core ) );
				}

				/**
				 * Include base, such as `wrapper-archive-post-type.php`
				 * for `archive-post-type.php` template request.
				 */
				$templates[] = sprintf( 'wrapper-%s.php', self::$base );
			}


		// Output

			return locate_template( array_reverse( (array) $templates ) );

	} // /template_include

}
