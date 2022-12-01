<?php
/**
 * Post meta: Author.
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/user.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<span class="entry-meta-item byline author vcard">
	<svg class="svg-icon" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8,8c1.7,0,3-1.3,3-3S9.7,2,8,2S5,3.3,5,5S6.3,8,8,8z M10,9H6c-1.7,0-3,1.3-3,3v2h10v-2C13,10.3,11.7,9,10,9z"/></svg>

	<span class="entry-meta-description"><?php echo esc_html_x( 'Written by:', 'Post meta info description: author name.', 'michelle' ); ?></span>
	<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="url fn n" rel="author"><?php the_author(); ?></a>
</span>
