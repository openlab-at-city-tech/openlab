<?php
/**
 * The home template file.
 *
 * @package Sydney
 */

get_header(); ?>

	<?php do_action('sydney_before_content'); ?>

	<?php do_action('sydney_archive_content'); ?>

	<?php do_action('sydney_after_content'); ?>

<?php do_action( 'sydney_get_sidebar' ); ?>
<?php get_footer(); ?>