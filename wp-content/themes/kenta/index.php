<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Kenta
 */

get_header();

if ( is_home() || is_archive() || is_search() ) {
	kenta_do_elementor_location( 'archive', 'template-parts/special', 'archive' );
} else if ( is_singular() ) {
	kenta_do_elementor_location( 'single', 'template-parts/special', 'single' );
} else {
	kenta_do_elementor_location( 'single', 'template-parts/content', 'none' );
}

get_footer();
