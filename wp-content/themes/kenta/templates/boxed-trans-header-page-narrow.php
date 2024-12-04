<?php
/**
 * Page Template for Kenta.
 *
 * Template name: Kenta: Template with Transparent Header and Narrow Content
 * Template Post Type: post, page, product
 */

// template override settings
set_query_var( 'site-container-style', 'boxed' );
set_query_var( 'site-container-layout', 'narrow' );
set_query_var( 'site-transparent-header', 'enable' );
set_query_var( 'disable-article-header', 'yes' );
set_query_var( 'disable-content-area-spacing', 'yes' );

get_header();

kenta_do_elementor_location( 'page', 'template-parts/special', 'page' );

get_footer();
