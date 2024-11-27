<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Sydney
 */

get_header(); ?>

<?php do_action('sydney_before_content'); ?>

<?php do_action( 'sydney_page_content' ); ?>

<?php do_action('sydney_after_content'); ?>	

<?php do_action( 'sydney_get_sidebar' ); ?>

<?php get_footer(); ?>
