<?php /* Template Name: Group Archive */
/**begin layout**/
get_header(); ?>

<!-- Note: the sidebar comes first in the markup, but appears on the right. This makes responsive styling easier -->
<div id="sidebar" class="sidebar widget-area">
	<?php get_sidebar( 'group-archive' ); ?>
</div>

<div id="content" class="hfeed">
	<div <?php post_class(); ?>>
		<h1 class="entry-title"><?php echo ucfirst(openlab_page_slug_to_grouptype()).'s'; ?> on the OpenLab</h1>

		<div class="entry-content">
			<?php openlab_group_archive(); ?>
		</div><!--entry-content-->
	</div><!--hentry-->
</div><!--content-->

<?php get_footer();
/**end layout**/
