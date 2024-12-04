<?php
/**
 * The template for archive page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Kenta
 */

// show archive header
kenta_show_archive_header();

// show posts loop
get_template_part( 'template-parts/special', 'loop' );
