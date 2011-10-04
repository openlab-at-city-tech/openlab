<?php do_action( 'bp_before_group_home_content' ) ?>
<div id="single-course-header">
<?php
/* Populate Fields */
global $bp;
$group_id = $bp->groups->current_group->id;
$faculty = groups_get_groupmeta($group_id, 'wds_faculty');
$html = groups_get_groupmeta($group_id, 'wds_course_html');
?>
	<h1 class="title">Courses Offered</h1>
	<div class="header-image"><img src="<?php bloginfo('stylesheet_directory') ?>/images/sample/headerImage.jpg" alt="<?php bp_group_name() ?>"></div><!-- .header-image -->
	<div class="header-content">
		<h2 class="course-title"><?php bp_group_name() ?><a href="<?php bp_group_permalink() ?>/feed" class="rss"><img src="<?php bloginfo('stylesheet_directory') ?>/images/icon-RSS.png" alt="Subscribe To <?php bp_group_name() ?>'s Feeds"></a></h2>
		<div class="course-byline">
			<span class="faculty-name"><b>Faculty:</b> <?php echo $faculty ?></span> | 
			<span class="days-offered"><?php printf( __( '%s Days offered', 'buddypress' ), bp_get_group_last_active() ) ?></span>
		</div><!-- .course-info -->
		<?php //do_action( 'bp_before_group_header_meta' ) ?>
		<div class="course-description">
		<?php bp_group_description() ?>
		</div>
		<?php //do_action( 'bp_group_header_meta' ) ?>
		<div class="course-html-block">
			<?php echo $html ?>
		</div>
	</div><!-- .header-content -->
	
	<?php do_action( 'bp_after_group_header' ) ?>
	
	<?php do_action( 'template_notices' ) ?>
	
</div><!-- #single-course-header -->