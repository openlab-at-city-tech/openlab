<?php
/**
 * Page template component.
 *
 * Not using `is_page_template()` but rather checking for a page template
 * filename portion in body classes to make the functionality much more
 * flexible (also for custom page templates, for example).
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Entry;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Header\Body_Class;
use WP_Theme;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Page_Template implements Component_Interface {

	/**
	 * Name of cached data transient.
	 *
	 * @since   1.0.10
	 * @access  public
	 * @var     string
	 */
	public static $transient_cache_body_class = 'michelle_cache_template_body_class';

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'michelle/upgrade', __CLASS__ . '::body_class_cache_flush' );

			// Filters

				add_filter( 'body_class', __CLASS__ . '::body_class' );

				add_filter( 'theme_templates', __CLASS__ . '::post_templates', 5, 4 );

				add_filter( 'michelle/header/is_disabled', __CLASS__ . '::is_content_only' );
				add_filter( 'michelle/footer/is_disabled', __CLASS__ . '::is_content_only' );

	} // /init

	/**
	 * Enable post templates for public post types.
	 *
	 * @since  1.0.0
	 *
	 * @param array        $post_templates  Array of page templates. Keys are filenames, values are translated names.
	 * @param WP_Theme     $theme           The theme object.
	 * @param WP_Post|null $post            The post being edited, provided for context, or null.
	 * @param string       $post_type       Post type to get the templates for.
	 *
	 * @return  array
	 */
	public static function post_templates( array $post_templates, WP_Theme $theme, $post, string $post_type ): array {

		// Requirements check

			if ( ! get_post_type_object( $post_type )->public ) {
				return $post_templates;
			}


		// Variables

			$registered_post_templates = $theme->get_post_templates();

			if ( isset( $registered_post_templates['public-post-types'] ) ) {
				$registered_post_templates = $registered_post_templates['public-post-types'];
			} else {
				$registered_post_templates = array();
			}


		// Output

			return array_filter( array_merge( $post_templates, $registered_post_templates ) );

	} // /post_templates

	/**
	 * HTML body classes.
	 *
	 * Allows setting up page template custom body class(es).
	 *
	 * To set body class(es) for the page template, put similar code
	 * before actual page template content output:
	 * @example
	 * 	if ( doing_filter( 'body_class' ) ) {
	 *  	$body_classes = array( 'template-body-class-name' );
	 *   	return;
	 *  }
	 *
	 * @since  1.0.10
	 *
	 * @param  array $classes
	 *
	 * @return  array
	 */
	public static function body_class( array $classes ): array {

		// Processing

			if (
				is_singular()
				&& is_page_template()
			) {

				$template         = get_page_template_slug( get_the_ID() );
				$template_classes = array_filter( (array) get_transient( self::$transient_cache_body_class ) );

				if ( ! isset( $template_classes[ $template ] ) ) {
					// This variable should be set in the page template file!
					$body_classes = array();

					// Using buffer to prevent page content rendering.
					ob_start();
					include_once get_theme_file_path( $template ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
					$_not_used = ob_get_clean();

					$template_classes[ $template ] = array_filter( (array) $body_classes );

					// Caching classes to speed things up.
					set_transient( self::$transient_cache_body_class, $template_classes );
				}

				// Why bother if the page template has no body classes set.
				if ( empty( $template_classes[ $template ] ) ) {
					return $classes;
				}

				foreach ( $template_classes[ $template ] as $class ) {
					$classes[] = sanitize_html_class( $class );
				}
			}


		// Output

			return array_unique( $classes );

	} // /body_class

	/**
	 * Flush templates body classes transient cache.
	 *
	 * @since  1.0.10
	 *
	 * @return  void
	 */
	public static function body_class_cache_flush() {

		// Processing

			delete_transient( self::$transient_cache_body_class );

	} // /body_class_cache_flush

	// Page templates with content only.

		/**
		 * Is page template: Content only?
		 *
		 * @since  1.0.0
		 *
		 * @param  mixed $body_classes  Optional forced array of body classes when using the method within `body_class` hook.
		 *
		 * @return  bool
		 */
		public static function is_content_only( $body_classes = array() ): bool {

			// Variables

				$check_body_class = stripos( implode( ' ', Body_Class::get_body_class( $body_classes ) ), '-content-only' );


			// Output

				/**
				 * Filters whether we should display only page content.
				 *
				 * @since  1.0.0
				 *
				 * @param  bool $check_body_class  By default it checks for a specific body class name portion.
				 */
				return (bool) apply_filters( 'michelle/entry/page_template/is_content_only', (bool) $check_body_class );

		} // /is_content_only

}
