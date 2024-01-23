<?php
/**
 * The template for displaying archive pages.
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

if ( have_posts() ) :

	get_template_part( 'templates/parts/component/page-header', 'archive' );
	get_template_part( 'templates/parts/loop/loop', 'archive' );

else :

	get_template_part( 'templates/parts/content/content', 'none' );

endif;
