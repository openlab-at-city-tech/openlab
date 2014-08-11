<?php /* Template Name: My Group Template */
get_header();
global $bp;?>

	<div id="content" class="hfeed row">
            <div class="col-sm-18 my-groups-grid">
    	<h1 class="entry-title mol-title"><?php echo $bp->loggedin_user->fullname.'&rsquo;s'; ?> Profile</h1>
    	<?php bp_get_template_part('groups/groups','loop'); ?>
            </div>

	<?php openlab_bp_sidebar('members'); ?>
        </div><!--content-->

<?php get_footer();
