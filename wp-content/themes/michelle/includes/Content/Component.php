<?php
/**
 * Content component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.2.0
 */

namespace WebManDesign\Michelle\Content;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Header\Body_Class;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.0.6
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Containers.
			Container::init();
			// Blocks.
			Block::init();
			Block_Area::init();
			Block_Patterns::init();
			Block_Styles::init();
			// Starter content.
			Starter::init();

	} // /init

	/**
	 * Get content type by context.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $context
	 *
	 * @return  string
	 */
	public static function get_content_type( string $context = '' ): string {

		// Variables

			$sub_context = '';
			$separator   = '/';

			if ( stripos( $context, $separator ) ) {
				$contexts = explode( $separator, $context );
				$context  = $contexts[0];
				unset( $contexts[0] );
				$sub_context = implode( $separator, $contexts );
			}


		// Output

			/**
			 * Filters the content type by context.
			 *
			 * @since  1.0.0
			 *
			 * @param  string $content_type    Default: get_post_type().
			 * @param  string $context         Optional context, such as "single" or "loop".
			 * @param  string $sub_context     Optional additional sub-context, such as a plugin-specific feature name.
			 */
			return (string) apply_filters( 'michelle/content/get_content_type', get_post_type(), $context, $sub_context );

	} // /get_content_type

	/**
	 * Get the paginated page number info as HTML.
	 *
	 * @since  1.0.0
	 *
	 * @return  string
	 */
	public static function get_paged_info(): string {

		// Pre

			/**
			 * Bypass filter for WebManDesign\Michelle\Content\Component::get_paged_info().
			 *
			 * Returning a non-false value will short-circuit the method,
			 * returning the passed value instead.
			 *
			 * @since  1.0.0
			 *
			 * @param  mixed $pre  Default: false. If not false, method returns this value.
			 */
			$pre = apply_filters( 'pre/michelle/content/get_paged_info', false );

			if ( false !== $pre ) {
				return $pre;
			}


		// Variables

			global $page, $paged;

			$output    = '';
			$paginated = max( absint( $page ), absint( $paged ) );


		// Processing

			if ( 1 < $paginated ) {
				$output = '<span class="page-number"> ' . sprintf(
					/* translators: Paginated content title suffix. %d: page number. */
					esc_html__( '(page %d)', 'michelle' ),
					number_format_i18n( $paginated )
				) . '</span>';
			}


		// Output

			return $output;

	} // /get_paged_info

	/**
	 * Do we need to display primary title?
	 *
	 * Used to enable/disable `#primary` section H1 title.
	 *
	 * @since    1.0.0
	 * @version  1.2.0
	 *
	 * @param  mixed $body_classes  Optional forced array of body classes when using the method within `body_class` hook.
	 *
	 * @return  bool
	 */
	public static function show_primary_title( $body_classes = array() ): bool {

		// Variables

			$is_blog_homepage        = is_front_page() && is_home();
			$body_classes            = implode( ' ', Body_Class::get_body_class( $body_classes ) );
			$has_no_intro_body_class = stripos( $body_classes, '-no-intro' ) || stripos( $body_classes, 'no-primary-title' );


		// Output

			/**
			 * Whether to show content primary title.
			 *
			 * @since  1.0.0
			 *
			 * @param  bool $show  Default: ! $is_blog_homepage && ! $has_no_intro_body_class.
			 */
			return (bool) apply_filters( 'michelle/content/show_primary_title', ! $is_blog_homepage && ! $has_no_intro_body_class );

	} // /show_primary_title

}
