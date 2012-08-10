<?php
/*
Template Name: Search
*/
?>

<?php get_header(); ?>


<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>


		<div id="current-content" class="hfeed">

			<h2>Search</h2>
			<div id="search"><h4><?php _e('Search'); ?></h4>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</div>

		</div> <!-- #current-content -->


		<div id="dynamic-content"></div>
	</div> <!-- #primary -->
</div> <!-- #primary-wrapper -->

<div id="sidebar-home" class="secondary">
<?php	dynamic_sidebar('Search');  ?>
</div>
	
<?php get_footer(); ?>
