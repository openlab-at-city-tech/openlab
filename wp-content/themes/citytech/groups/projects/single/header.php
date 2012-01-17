<?php do_action( 'bp_before_group_home_content' ) ?>
<div id="single-course-header">
<?php
/* Populate Fields */
global $bp;
//echo '<pre>';
//	print_r($bp);
//echo '</pre>';

$group_id = $bp->groups->current_group->id;
$group_name = $bp->groups->current_group->name;
$group_description = $bp->groups->current_group->description;
$faculty = groups_get_groupmeta($group_id, 'wds_faculty');
$html = groups_get_groupmeta($group_id, 'wds_course_html');
?>
	<h1 class="entry-title">Projects on the OpenLab</h1>
	 <div id="course-header-avatar" class="alignleft">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php bp_group_avatar('type=full&width=225&height=225') ?>
		</a>
	</div><!-- #course-header-avatar -->	
	<div id="course-header-content" class="alignleft">
		<h2 class="course-title"><?php echo $group_name; ?><a href="<?php bp_group_permalink() ?>/feed" class="rss"><img src="<?php bloginfo('stylesheet_directory') ?>/images/icon-RSS.png" alt="Subscribe To <?php echo $group_name; ?>'s Feeds"></a></h2>
		<div class="info-line"><span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s ago', 'buddypress' ), bp_get_group_last_active() ) ?></span></div>
		<div class="course-byline">
			<span class="faculty-name"><b>Faculty:</b> <?php echo $faculty ?></span> | 
			<span class="days-offered"><?php printf( __( '%s Days offered', 'buddypress' ), bp_core_time_since( $bp->groups->current_group->last_activity ) ) ?></span>
		</div><!-- .course-info -->
		<?php //do_action( 'bp_before_group_header_meta' ) ?>
		<div class="course-description">
		<?php echo apply_filters('the_content', $group_description );
		?>
		</div>
		<?php //do_action( 'bp_group_header_meta' ) ?>
		<div class="course-html-block">
			<?php echo $html ?>
		</div>
	</div><!-- .header-content -->
	
	<?php do_action( 'bp_after_group_header' ) ?>
	
	<?php do_action( 'template_notices' ) ?>
	
</div><!-- #single-course-header -->