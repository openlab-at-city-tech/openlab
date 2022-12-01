<?php
/**
 * Starter content: Excerpt.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.9
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! is_customize_preview() ) {
	return;
}

?>

<p>
	<a href="https://webmandesign.github.io/docs/michelle/#posts-excerpt"><?php echo esc_html_x( 'This is a page/post excerpt text.', 'Theme starter content', 'michelle' ); ?></a>
	Lorem dolor sit amet adipiscing elit gravida, enim curabitur sagittis magna molestie rutrum nostra viverra.
</p>
