<?php
/**
 * Template Name: Overlay header (dark)
 * Template Post Type: public-post-types
 *
 * Overlays header, making it dark color.
 * Removes page/post intro.
 * Works with all public post types.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/* translators: Custom page template name. */
__( 'Overlay header (dark)', 'michelle' );

if ( is_page( get_the_ID() ) ) {
	get_template_part( 'page' );
} else {
	get_template_part( 'single', get_post_type() );
}
