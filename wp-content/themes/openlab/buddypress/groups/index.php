<?php /* Template Name: My Group Template */
get_header();
global $bp;?>

	<div id="content" class="hfeed row">
            <div class="col-sm-9 my-groups-grid">
    	<h1 class="entry-title mol-title"><?php echo $bp->loggedin_user->fullname.'&rsquo;s'; ?> Profile</h1>
    	<?php get_template_part('buddypress/groups/groups','loop'); ?>
            </div>

    <div id="sidebar" class="sidebar widget-area col-sm-3">
		<?php get_template_part('buddypress/members/single/sidebar'); ?>
    </div>
        </div><!--content-->

<?php get_footer();
