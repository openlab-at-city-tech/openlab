<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php get_header(); ?>
<div class="span-<?php modularity_sidebar_class(); ?>">
<div class="content">
		<h2><?php _e( 'Whoops!  Whatever you are looking for cannot be found.', 'modularity' ); ?></h2>
		<?php get_search_form(); ?>
	</div>
	</div>
	<?php get_sidebar(); ?>
<!-- Begin Footer -->
<?php get_footer(); ?>