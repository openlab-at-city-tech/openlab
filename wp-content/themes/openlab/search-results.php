<?php
/*
 * Template Name: Search Results
 */
?>

<?php get_header(); ?>

<div id="content" class="hfeed row">
    <?php openlab_bp_sidebar( 'groups', true ); ?>
	<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
		<div id="openlab-main-content"  class="content-wrapper">
			<h1 class="entry-title">OpenLab Search Results</h1>

			<div class="entry-content">
				<?php bp_get_template_part( 'groups/groups-loop' ); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
