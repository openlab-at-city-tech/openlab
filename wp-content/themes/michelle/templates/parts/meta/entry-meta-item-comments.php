<?php
/**
 * Post meta: Comments count.
 *
 * Not using `comments_popup_link()` due to we have a different HTML for
 * easier support of icons via CSS and for adding a hover title on link.
 * @link  https://developer.wordpress.org/reference/functions/comments_popup_link/
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/chat.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if (
	post_password_required()
	|| ! comments_open( get_the_ID() )
) {
	return;
}

$comments_number = get_comments_number( get_the_ID() );

?>

<span class="entry-meta-item comments-link">
	<svg class="svg-icon" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M7,3H3C1.9,3,1,3.9,1,5v7l2.4-2.4C3.8,9.2,4.3,9,4.8,9H6V8c0-1.7,1.3-3,3-3C9,3.9,8.1,3,7,3z M13,6H9C7.9,6,7,6.9,7,8v2c0,1.1,0.9,2,2,2h2.2c0.5,0,1,0.2,1.4,0.6L15,15V8C15,6.9,14.1,6,13,6z"/></svg>

	<a href="<?php the_permalink(); ?>#comments" title="<?php echo esc_attr( sprintf(
		/* translators: %d: number of comments. */
		esc_html__( 'Comments: %d', 'michelle' ),
		number_format_i18n( $comments_number )
	) ); ?>">
		<span class="entry-meta-description"><?php echo esc_html_x( 'Comments:', 'Post meta info description: comments count.', 'michelle' ); ?></span>
		<span class="comments-count"><?php echo number_format_i18n( $comments_number ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></span>
	</a>
</span>
