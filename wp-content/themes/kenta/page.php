<?php
/**
 * The template for displaying all pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Kenta
 */

get_header();

kenta_do_elementor_location( 'page', 'template-parts/special', 'page' );

get_footer();
