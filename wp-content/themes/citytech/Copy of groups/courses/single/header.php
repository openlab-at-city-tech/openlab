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
// $faculty = groups_get_groupmeta($group_id, 'wds_faculty');
$faculty_id = $bp->groups->current_group->admins[0]->user_id;
$first_name= ucfirst(xprofile_get_field_data( 'First Name', $faculty_id));
$last_name= ucfirst(xprofile_get_field_data( 'Last Name', $faculty_id));
$group_type = openlab_get_group_type( bp_get_current_group_id());

/*
$member_arg = Array("exclude_admins_mods"=>false); 
if ( bp_group_has_members($member_arg) ) : 
	  while ( bp_group_members() ) : bp_group_the_member();
	      echo "<br />Member ID: " . bp_get_group_member_id();
	      echo "<br />BP: <pre>";print_r($bp);echo "</pre>";
	      if(bp_group_is_admin() == "1") {
		     $faculty_id = bp_get_group_member_id();
		     echo "<br />Faculty ID: $faculty_id";
		     $first_name= xprofile_get_field_data( 'First Name', $faculty_id);
		     $last_name= xprofile_get_field_data( 'Last Name', $faculty_id);

	      }
          endwhile;
endif;
*/
$section = groups_get_groupmeta($group_id, 'wds_section_code');
$html = groups_get_groupmeta($group_id, 'wds_course_html');
?>
<h1 class="entry-title"><?php echo $group_name; ?> Profile</h1>
     <?php if ($bp->current_action == "home"): ?>
     <div id="course-header-avatar" class="alignleft">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php bp_group_avatar('type=full&width=225') ?>
		</a>
       <?php if (is_user_logged_in() && $bp->is_item_admin): ?>
         <div id="group-action-wrapper">
					<div id="action-edit-group"><a href="<?php echo bp_group_permalink(). 'admin/edit-details/'; ?>">Edit Profile</a></div>
            		<div id="action-edit-avatar"><a href="<?php echo bp_group_permalink(). 'admin/group-avatar/'; ?>">Change Avatar</a></div>
         </div>
		<?php elseif (is_user_logged_in()): ?>
		<div id="group-action-wrapper">
				<?php do_action( 'bp_group_header_actions' ); ?>
        </div>
	 	<?php endif; ?>
	</div><!-- #course-header-avatar -->	
	<div id="course-header-content" class="alignleft">
		<h2 class="course-title"><?php echo $group_name; ?> <a href="<?php bp_group_permalink() ?>/feed" class="rss"><img src="<?php bloginfo('stylesheet_directory') ?>/images/icon-RSS.png" alt="Subscribe To <?php echo $group_name; ?>'s Feeds"></a></h2>
		<div class="info-line"><span class="highlight"><?php if ($section != "") {echo "Section Code: $section";} ?></span></div>
		<div class="info-line"><span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ) ?></span></div>
		<div class="course-byline">
			<span class="faculty-name"><b>Faculty:</b> <?php echo $first_name . " " . $last_name; ?></span>  
		<!--	<span class="days-offered"><?php // printf( __( '%s Days offered', 'buddypress' ), bp_core_time_since( $bp->groups->current_group->last_activity ) ) ?></span> -->
			<?php
			$wds_course_code=groups_get_groupmeta($group_id, 'wds_course_code' );
			$wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
			$wds_year=groups_get_groupmeta($group_id, 'wds_year' );
			$wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
			?>
            <div class="info-line" style="margin-top:2px;"><?php echo $wds_course_code;?> | <?php echo $wds_departments;?> | <?php echo $wds_semester;?> <?php echo $wds_year;?></div>
		
		</div><!-- .course-info -->
		<?php //do_action( 'bp_before_group_header_meta' ) ?>
		<div class="course-description">
		<?php echo apply_filters('the_content', $group_description ); ?>
		</div>
		<?php //do_action( 'bp_group_header_meta' ) ?>
		
        <?php if ($html): ?>
        <div class="course-html-block">
			<?php echo $html; ?>
		</div>
        <?php endif; ?>
	</div><!-- .header-content -->
	
	<?php do_action( 'bp_after_group_header' ) ?>
	
	<?php do_action( 'template_notices' ) ?>

    <?php endif; ?>
    </div><!-- #single-course-header -->	
