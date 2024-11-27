<?php
/**
 * The template for displaying search results pages.
 *
 * @package Sydney
 */

get_header(); ?>

<?php do_action('sydney_before_content'); ?>

<?php do_action('sydney_search_content'); ?>

<?php do_action('sydney_after_content'); ?>

<?php do_action( 'sydney_get_sidebar' ); ?>
<?php get_footer(); ?>