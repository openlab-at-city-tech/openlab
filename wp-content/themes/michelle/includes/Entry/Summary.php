<?php
/**
 * Entry summary component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.5.1
 */

namespace WebManDesign\Michelle\Entry;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Setup;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Summary implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.5.1
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Filters

				add_filter( 'the_excerpt', __CLASS__ . '::get_excerpt', 20 );

				add_filter( 'get_the_excerpt', __CLASS__ . '::wrapper', 20 );
				add_filter( 'get_the_excerpt', __CLASS__ . '::continue_reading', 30, 2 );

				add_filter( 'excerpt_length', __CLASS__ . '::excerpt_length' );

				add_filter( 'excerpt_more', __CLASS__ . '::excerpt_more' );

				add_filter( 'pre_render_block', __CLASS__ . '::pre_render_block', 10, 2 );

	} // /init

	/**
	 * Get modified excerpt.
	 *
	 * Displays the excerpt properly.
	 * If the post is password protected, display a message.
	 * If the post has more tag, display the content appropriately.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $post_excerpt
	 *
	 * @return  string
	 */
	public static function get_excerpt( string $post_excerpt = '' ): string {

		// Variables

			$post_id = get_the_ID();


		// Requirements check

			if ( post_password_required( $post_id ) ) {
				if ( ! is_single( $post_id ) ) {
					return
						esc_html__( 'This content is password protected.', 'michelle' )
						. ' <a href="' . esc_url( get_permalink() ) . '">'
						. esc_html__( 'Enter the password to view it.', 'michelle' )
						. '</a>';
				}
				return '';
			}


		// Processing

			if (
				! is_single( $post_id )
				&& Component::has_more_tag()
			) {

				if ( has_excerpt( $post_id ) ) {
					$post_excerpt = str_replace(
						'entry-summary',
						'entry-summary has-more-tag',
						$post_excerpt
					);
				} else {
					$post_excerpt = '';
				}

				$post_excerpt = (string) apply_filters( 'the_content', $post_excerpt . get_the_content( '' ) . self::get_continue_reading_html() );
			}


		// Output

			return $post_excerpt;

	} // /get_excerpt

	/**
	 * Wrap excerpt within a `div.entry-summary`.
	 *
	 * Line breaks are required for proper functionality of `wpautop()` later on.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $post_excerpt
	 *
	 * @return  string
	 */
	public static function wrapper( string $post_excerpt = '' ): string {

		// Requirements check

			if ( empty( $post_excerpt ) ) {
				return $post_excerpt;
			}


		// Output

			return '<div class="entry-summary">' . PHP_EOL . $post_excerpt . PHP_EOL . '</div>';

	} // /wrapper

	/**
	 * Excerpt length.
	 *
	 * The number of words. Default 55.
	 *
	 * @since  1.0.0
	 *
	 * @return  int
	 */
	public static function excerpt_length(): int {

		// Output

			return 32;

	} // /excerpt_length

	/**
	 * Excerpt more.
	 *
	 * @since  1.0.0
	 *
	 * @return  string
	 */
	public static function excerpt_more(): string {

		// Output

			return '&hellip;';

	} // /excerpt_more

	/**
	 * Adding "Continue reading" link to excerpt.
	 *
	 * @since  1.0.0
	 *
	 * @param  string           $post_excerpt  The post excerpt.
	 * @param  null|int|WP_Post $post          Post object.
	 *
	 * @return  string
	 */
	public static function continue_reading( string $post_excerpt = '', $post = null ): string {

		// Requirements check

			if (
				! post_password_required( $post )
				&& ! Component::is_singular( get_the_ID() )
				&& ! Component::has_more_tag()
				&& in_array(
					get_post_type( $post ),
					(array) Setup\Post_Type::get_feature( 'continue_reading' )
				)
			) {
				$post_excerpt .= self::get_continue_reading_html( $post );
			}


		// Output

			return $post_excerpt;

	} // /continue_reading

	/**
	 * Get "Continue reading" HTML.
	 *
	 * @since  1.0.0
	 *
	 * @param  null|int|WP_Post $post     Post object.
	 * @param  string           $context  Optional identification of specific "Continue reading" text for better filtering.
	 *
	 * @return  string
	 */
	public static function get_continue_reading_html( $post = null, string $context = '' ): string {

		// Pre

			/**
			 * Bypass filter for WebManDesign\Michelle\Entry\Summary::get_continue_reading_html().
			 *
			 * Returning a non-false value will short-circuit the method,
			 * returning the passed value instead.
			 *
			 * @since  1.0.0
			 *
			 * @param  mixed            $pre      Default: false. If not false, method returns this value.
			 * @param  null|int|WP_Post $post     Post object.
			 * @param  string           $context  Optional context.
			 */
			$pre = apply_filters( 'pre/michelle/entry/summary/get_continue_reading_html', false, $post, $context );

			if ( false !== $pre ) {
				return $pre;
			}


		// Variables

			$html     = '';
			$template = 'templates/parts/component/link-more';


		// Processing

			ob_start();

			if ( $context && locate_template( $template . '-' . $context . '.php' ) ) {
				get_template_part( $template, $context );
			} else {
				get_template_part( $template, get_post_type() );
			}

			/**
			 * Stripping all new line and tab characters to prevent `wpautop()` messing things up later.
			 *
			 * "\t" - a tab.
			 * "\n" - a new line (line feed).
			 * "\r" - a carriage return.
			 * "\x0B" - a vertical tab.
			 */
			$html = str_replace(
				array( "\t", "\n", "\r", "\x0B" ),
				'',
				ob_get_clean()
			);


		// Output

			/**
			 * Filters "Continue reading" link HTML output.
			 *
			 * @since  1.0.0
			 *
			 * @param  string           $html
			 * @param  null|int|WP_Post $post     Post object.
			 * @param  string           $context  Optional context.
			 */
			return (string) apply_filters( 'michelle/entry/summary/get_continue_reading_html', $html, $post, $context );

	} // /get_continue_reading_html

	/**
	 * Block output modification: No need for theme "Continue reading" in Excerpt block.
	 *
	 * @since  1.5.1
	 *
	 * @param  string|null $pre_render  The rendered content. Default null.
	 * @param  array       $block       The block being rendered.
	 *
	 * @return  string|null
	 */
	public static function pre_render_block( $pre_render, array $block ) {

		// Processing

			if ( 'core/post-excerpt' === $block['blockName'] ) {
				remove_filter( 'get_the_excerpt', __CLASS__ . '::continue_reading', 30 );
			}


		// Output

			return $pre_render;

	} // /pre_render_block

}
