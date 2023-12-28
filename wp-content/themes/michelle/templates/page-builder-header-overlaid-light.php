<?php // phpcs:ignore WPThemeReview.Templates.ReservedFileNamePrefix.ReservedTemplatePrefixFound
/**
 * Template Name: Page builder (overlay header light)
 * Template Post Type: public-post-types
 *
 * Prepares page/post content for using a page builder plugin. The default
 * content area layout can be set in customizer options.
 * Overlays header, making it light color.
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
__( 'Page builder (overlay header light)', 'michelle' );

if ( is_page( get_the_ID() ) ) {
	get_template_part( 'page' );
} else {
	get_template_part( 'single', get_post_type() );
}
