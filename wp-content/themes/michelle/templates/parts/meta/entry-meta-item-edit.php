<?php
/**
 * Post meta: Edit link.
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/edit.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$icon = '<svg class="svg-icon" width="1em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M12.6,6.9l0.5-0.5c0.8-0.8,0.8-2,0-2.8l-0.7-0.7c-0.8-0.8-2-0.8-2.8,0L9.1,3.4L12.6,6.9z"/><polygon points="8.4,4.1 2,10.5 2,14 5.5,14 11.9,7.6"/></svg>';

edit_post_link(
	sprintf(
		/* translators: %s: Name of current post. Only visible to screen readers. */
		esc_html__( 'Edit %s', 'michelle' ),
		'<span class="screen-reader-text">' . get_the_title() . '</span>'
	),
	'<span class="entry-meta-item edit-link">' . PHP_EOL . $icon . PHP_EOL,
	'</span>'
);
