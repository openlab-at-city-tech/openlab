<?php
/**
 * Template part for displaying pages.
 *
 * This is also required for plugins such as bbPress when no post ID can be
 * obtained and so there is no way to check whether previewing singular page.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( get_the_ID() ) {
	get_template_part( 'templates/parts/content/content' );
} else {
	get_template_part( 'templates/parts/content/content', 'full' );
}
