<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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

	get_template_part( 'templates/parts/component/page-header', 'index' );
	get_template_part( 'templates/parts/loop/loop', 'index' );

else :

	get_template_part( 'templates/parts/content/content', 'none' );

endif;
