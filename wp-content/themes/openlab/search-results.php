<?php
/*
 * Template Name: Search Results
 */

// Register the search results mobile drawer.
$drawer_id = 'archive-search-drawer';
$drawer_title = 'Search';
openlab_register_archive_mobile_drawer( $drawer_id, $drawer_title );
?>

<?php get_header(); ?>

<div id="content" class="hfeed row">
    <?php openlab_bp_sidebar( 'groups', true ); ?>
	<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
		<div id="openlab-main-content"  class="content-wrapper openlab-search-results" tabindex="-1">
			<h1 class="entry-title">OpenLab Search Results<button class="drawer-toggle mobile-toggle pull-right visible-xs" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $drawer_id ); ?>" data-drawer-toggle="<?php echo esc_attr( $drawer_id ); ?>"><span class="toggle-icon"></span><span class="sr-only">Search</span></button></h1>

			<div class="entry-content">
				<?php bp_get_template_part( 'groups/groups-loop' ); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
