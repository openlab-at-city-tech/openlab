<?php /* Template Name: My Group Template */
get_header();
global $bp;?>

	<div id="content" class="hfeed">
    	<h1 class="entry-title mol-title"><?php echo $bp->loggedin_user->fullname.'&rsquo;s'; ?> Profile</h1>
    	<?php get_template_part('buddypress/groups/group','loop'); ?>
    </div><!--content-->

    <div id="sidebar" class="sidebar widget-area">
		<?php get_template_part('buddypress/members/single/sidebar'); ?>
    </div>

<?php get_footer();
