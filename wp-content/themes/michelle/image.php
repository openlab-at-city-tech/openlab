<?php
/**
 * Image attachment template.
 *
 * @link  https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

while ( have_posts() ) :
	the_post();

	get_template_part( 'templates/parts/content/content', 'attachment-image' );

endwhile;
