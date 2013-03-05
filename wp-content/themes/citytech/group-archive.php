<?php /* Template Name: Group Archive */
/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
    	<div <?php post_class(); ?>>
        	<h1 class="entry-title"><?php echo ucfirst(openlab_page_slug_to_grouptype()).'s'; ?> on the OpenLab</h1>
            <div class="entry-content">
				<?php openlab_group_archive(); ?>
            </div><!--entry-content-->
        </div><!--hentry-->
    </div><!--content-->
    <div id="sidebar" class="sidebar widget-area">
		<?php get_sidebar('group-archive'); ?>
    </div>

<?php get_footer();
/**end layout**/