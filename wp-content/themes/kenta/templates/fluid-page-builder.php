<?php
/**
 * Page Template for Kenta.
 *
 * Template name: Kenta: Fluid Page Builder Template
 * Template Post Type: post, page, product
 */

// template override settings
set_query_var( 'site-container-style', 'fluid' );
set_query_var( 'site-container-layout', 'normal' );
set_query_var( 'disable-article-header', 'yes' );
set_query_var( 'disable-content-area-spacing', 'yes' );

get_header();

kenta_do_elementor_location( 'page', 'template-parts/special', 'page' );

get_footer();
