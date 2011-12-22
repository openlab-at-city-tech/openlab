<?php /* Template Name: Courses Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_do_course_archive_title');
function cuny_do_course_archive_title() {
	echo '<h1 class="entry-title">Courses on the OpenLab</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_course_archive' );
function cuny_course_archive() {
global $wpdb,$bp,$groups_template;
if ( empty( $_GET['group_sequence'] ) ) {
	$_GET['group_sequence'] = "alphabetical";
}
switch ($_GET['group_sequence']) {
	case "alphabetical":
		$display_option = "Alphabetical";
		$option_value = "alphabetical";
		break;
	case "newest":
		$display_option = "Newest";
		$option_value = "newest";
		break;
	case "active":
		$display_option = "Last Active";
		$option_value = "active";
		break;
	default: 
		$display_option = "Select Desired Sequence";
		$option_value = "";
		break;
}
?>
<form id="group_seq_form" name="group_seq_form" action="#" method="get">
	<select name="group_sequence" onchange="document.forms['group_seq_form'].submit();">
		<option value="<?php echo $option_value; ?>"><?php echo $display_option; ?></option>
		<option value='alphabetical'>Alphabetical</option>
		<option value='newest'>Newest</option>
		<option value='active'>Last Active</option>
	</select>
	<input type="submit" name="group_seq_submit" value="Sequence">
</form>
<?php
if ( !empty( $_GET['group_sequence'] ) ) {
	$sequence_type = "type=" . $_GET['group_sequence'] . "&";
}

$search_terms = '';

if ( !empty( $_POST['group_search'] ) ) {
	$search_terms="search_terms=".$_POST['group_search']."&";
}
if ( !empty( $_GET['search'] ) ){
	$search_terms="search_terms=".$_GET['search']."&";
}

if ( !empty( $_GET['school'] ) ) {
	$school=$_GET['school'];
	if($school=="tech"){
		$school="School of Technology & Design";
	}elseif($school=="studies"){
		$school="School of Professional Studies";
	}elseif($school=="arts"){
		$school="School of Arts & Sciences";
	}
}

if ( !empty( $_GET['department'] ) ) {
	$department=str_replace("-"," ",$_GET['department']);
	$department=ucwords($department);
}

if( !empty( $_GET['school'] ) && !empty( $_GET['department'] ) ) {
	echo "<h3>".$school."<br>";
	echo "Department: ".$department."</h3>";
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b, {$bp->groups->table_name_groupmeta} c where a.group_id=b.group_id and a.group_id=c.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_group_school' and b.meta_value like '%".$_GET['school']."%' and c.meta_key='wds_departments' and c.meta_value like '%".$department."%'";
} elseif( !empty( $_GET['school'] ) ) {
	echo "<h3>".$school."</h3>";
	$sql="SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b where a.group_id=b.group_id and a.meta_key='wds_group_type' and a.meta_value='course' and b.meta_key='wds_group_school' and b.meta_value like '%".$_GET['school']."%'";
}else{
	$sql="SELECT group_id FROM {$bp->groups->table_name_groupmeta} where meta_key='wds_group_type' and meta_value='course'";
}
$ids="9999999";
$rs = $wpdb->get_results( $sql );
foreach ( (array)$rs as $r ) $ids.= ",".$r->group_id;
if ( bp_has_groups( $sequence_type.$search_terms.'include='.$ids.'&per_page=12' ) ) : ?>
	<ul id="course-list" class="item-list">
		<?php 
		$count = 1;
		while ( bp_groups() ) : bp_the_group(); 
			$group_id=bp_get_group_id();?>
			<li class="course<?php echo cuny_o_e_class($count) ?>">
				<div class="item-avatar alignleft">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 100, 'height' => 100 )) ?></a>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
					<?php
					$admins = groups_get_group_admins( $group_id );
//					echo "<br />Admins: <pre>";print_r($admins);echo "</pre>";
					$faculty_id = $admins[0]->user_id;
//					$faculty_id = $bp->groups->current_group->admins[0]->user_id;
					$first_name= ucfirst(xprofile_get_field_data( 'First Name', $faculty_id));
					$last_name= ucfirst(xprofile_get_field_data( 'Last Name', $faculty_id));
					$wds_faculty= $first_name . " " . $last_name;;
//					$wds_faculty=groups_get_groupmeta($group_id, 'wds_faculty' );
					$wds_course_code=groups_get_groupmeta($group_id, 'wds_course_code' );
					$wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
		  			$wds_year=groups_get_groupmeta($group_id, 'wds_year' );
		  			$wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
					?>
                    <div class="info-line"><?php echo $wds_faculty; ?> | <?php echo $wds_departments;?> | <?php echo $wds_course_code;?><br /> <?php echo $wds_semester;?> <?php echo $wds_year;?></div>
					<?php
					     $len = strlen(bp_get_group_description());
					     if ($len > 135) {
						$this_description = substr(bp_get_group_description(),0,135);
						$this_description = str_replace("</p>","",$this_description);
						echo $this_description.'&hellip; (<a href="'.bp_get_group_permalink().'">View More</a>)</p>';
					     } else {
						bp_group_description();
					     }
					?>
				</div>
				
			</li>
			<?php if ( $count % 2 == 0 ) { echo '<hr style="clear:both;" />'; } ?>
			<?php $count++ ?>
		<?php endwhile; ?>
	</ul>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links() ?>
		</div>
<?php else: ?>

	<div class="widget-error">
		<?php _e('There are no courses to display.', 'buddypress') ?>
	</div>

<?php endif; ?>
		<?php

}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { 
$faculty=xprofile_get_field_data( 'Account Type', get_current_user_id() );
if ( is_super_admin( get_current_user_id() ) || $faculty=="Faculty" ) {?>
	<div class="generic-button"><a href="<?php echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/create/step/group-details/?type=course&new=true' ?>"><?php _e( 'Create a Course', 'buddypress' ) ?></a></div>
<?php } ?>
     <div class="archive-search">
    <form method="post">
    <?php $group_search = isset( $_POST['group_search'] ) ? $_POST['group_search'] : '' ?>
    <input type="text" name="group_search" value="<?php echo $group_search ?>" />
    <input type="submit" name="group_search_go" value="Search" />
    </form>
    </div>
<?php
}

genesis();