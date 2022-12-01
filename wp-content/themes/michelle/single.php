<?php
/**
 * The template for displaying all single posts.
 *
 * @link  https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

while ( have_posts() ) :
	the_post();

	get_template_part( 'templates/parts/content/content', Content\Component::get_content_type( 'single' ) );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

endwhile;
