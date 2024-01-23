<?php
/**
 * Pagination component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.5.0
 */

namespace WebManDesign\Michelle\Loop;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Pagination implements Component_Interface {

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

				add_action( 'michelle/postslist/after', __CLASS__ . '::posts' );

			// Filters

				add_filter( 'navigation_markup_template', __CLASS__ . '::comments', 10, 2 );

	} // /init

	/**
	 * Get filtered pagination arguments.
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $args
	 * @param  string $context
	 *
	 * @return  array
	 */
	public static function get_args_filtered( array $args = array(), string $context = '' ): array {

		// Output

			/**
			 * Filters theme pagination arguments.
			 *
			 * @since  1.0.0
			 *
			 * @param  array  $args     See paginate_links() function args. Default: array().
			 * @param  string $context  Optional context. Default: ''.
			 */
			return (array) apply_filters( 'michelle/loop/pagination/get_args_filtered', $args, $context );

	} // /get_args_filtered

	/**
	 * Pagination.
	 *
	 * @since    1.0.0
	 * @version  1.5.0
	 *
	 * @return  void
	 */
	public static function posts() {

		// Pre

			/**
			 * Bypass filter for WebManDesign\Michelle\Loop\Pagination::posts().
			 *
			 * Returning a non-false value will short-circuit the method.
			 * Null is returned by the short-circuit all the time.
			 * If filtered value is string, it will be echoed before the
			 * null is returned.
			 *
			 * @since  1.0.0
			 *
			 * @param  mixed $pre  Default: false. If value is string, method echo the value.
			 */
			$pre = apply_filters( 'pre/michelle/loop/pagination/posts', false );

			if ( false !== $pre ) {
				if ( is_string( $pre ) ) {
					echo $pre; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				return null;
			}


		// Variables

			$args = self::get_args_filtered(
				array(
					'prev_text' =>
						esc_html_x( '&laquo;', 'Pagination text (visible): previous.', 'michelle' ) . '<span class="screen-reader-text"> '
						. esc_html_x( 'Previous page', 'Pagination text (hidden): previous.', 'michelle' ) . '</span>',
					'next_text' =>
						'<span class="screen-reader-text">' . esc_html_x( 'Next page', 'Pagination text (hidden): next.', 'michelle' )
						. ' </span>' . esc_html_x( '&raquo;', 'Pagination text (visible): next.', 'michelle' ),
				),
				'loop'
			);

			$pagination = paginate_links( $args );


		// Processing

			if ( $pagination ) {
				$total   = ( isset( $GLOBALS['wp_query']->max_num_pages ) ) ? ( $GLOBALS['wp_query']->max_num_pages ) : ( 1 );
				$current = absint( max( get_query_var( 'paged' ), 1 ) );

				if (
					$total > 0
					&& $current > 0
				) {

					// Improving accessibility of page numbers.
					preg_match_all(
						'/>\d+</',
						$pagination,
						$matches,
						PREG_SET_ORDER
					);
					foreach ( $matches as $match ) {
						$pagination = str_replace(
							$match[0],
							// The HTML here is correct:
							'><span class="screen-reader-text">' . esc_html_x( 'Page:', 'Pagination page number screen reader label.', 'michelle' ) . ' </span' . $match[0],
							$pagination
						);
					}

					// Make current page focusable for screen readers.
					$pagination = str_replace(
						' aria-current=',
						' tabindex="0" aria-current=',
						$pagination
					);

					// Container improvements.
					$pagination =
						'<nav'
						. ' class="pagination"'
						. ' aria-label="'
							. esc_attr( sprintf(
								/* translators: 1: current page number, 2: total page count. */
								__( 'Posts navigation, page %1$d of %2$d', 'michelle' ),
								$current,
								$total
							) ) . '"'
						. ' data-page="' . esc_attr( sprintf(
								/* translators: 1: current page number, 2: total page count. */
								__( 'Page %1$d of %2$d', 'michelle' ),
								$current,
								$total
							) ) . '"'
						. ' data-current="' . esc_attr( $current ) . '"'
						. ' data-total="' . esc_attr( $total ) . '"'
						. '>'
						. $pagination
						. '</nav>';
				}
			}


		// Output

			echo $pagination; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	} // /posts

	/**
	 * Comments pagination.
	 *
	 * From simple next/previous links to full pagination.
	 *
	 * @since    1.0.0
	 * @version  1.5.0
	 *
	 * @param  string $template  The default template.
	 * @param  string $class     The class passed by the calling function.
	 *
	 * @return  string
	 */
	public static function comments( string $template, string $class ): string {

		// Requirements check

			if ( 'comment-navigation' !== $class ) {
				return $template;
			}


		// Variables

			$args = self::get_args_filtered(
				array(
					'prev_text' =>
						esc_html_x( '&laquo;', 'Pagination text (visible): previous.', 'michelle' ) . '<span class="screen-reader-text"> '
						. esc_html_x( 'Previous page', 'Pagination text (hidden): previous.', 'michelle' ) . '</span>',
					'next_text' =>
						'<span class="screen-reader-text">' . esc_html_x( 'Next page', 'Pagination text (hidden): next.', 'michelle' )
						. ' </span>' . esc_html_x( '&raquo;', 'Pagination text (visible): next.', 'michelle' ),
				),
				'comments'
			);

			$pagination = paginate_comments_links( array_merge( $args, array( 'echo' => false ) ) );

			$total   = get_comment_pages_count();
			$current = ( get_query_var( 'cpage' ) ) ? ( absint( get_query_var( 'cpage' ) ) ) : ( 1 );


		// Processing

			// Improving accessibility of page numbers.
			preg_match_all(
				'/>\d+</',
				$pagination,
				$matches,
				PREG_SET_ORDER
			);
			foreach ( $matches as $match ) {
				$pagination = str_replace(
					$match[0],
					// The HTML here is correct:
					'><span class="screen-reader-text">' . esc_html_x( 'Page:', 'Pagination page number screen reader label.', 'michelle' ) . ' </span' . $match[0],
					$pagination
				);
			}

			// Make current page focusable for screen readers.
			$pagination = str_replace(
				' aria-current=',
				' tabindex="0" aria-current=',
				$pagination
			);

			// Modifying navigation wrapper classes.
			$template = str_replace(
				'<nav class="navigation',
				'<nav class="navigation pagination comment-pagination',
				$template
			);

			// Adding responsive view HTML helper attributes.
			$template = str_replace( ' aria-label=', ' data-aria-label=', $template );
			$template = str_replace(
				'<nav',
				'<nav'
				. ' aria-label="'
					. esc_attr( sprintf(
						/* translators: 1: current page number, 2: total page count. */
						__( 'Comments navigation, page %1$d of %2$d', 'michelle' ),
						$current,
						$total
					) ) . '"'
				. ' data-current="' . esc_attr( $current ) . '"'
				. ' data-total="' . esc_attr( $total ) . '"',
				$template
			);

			// Displaying pagination HTML in the template.
			$template = str_replace(
				'<div class="nav-links">%3$s</div>',
				'<div'
				. ' class="nav-links"'
				. ' data-page="' . esc_attr( sprintf(
						/* translators: 1: current page number, 2: total page count. */
						__( 'Page %1$d of %2$d', 'michelle' ),
						$current,
						$total
					) ) . '"'
				. '>'
				. $pagination
				. '</div>',
				$template
			);


		// Output

			return (string) $template;

	} // /comments

}
