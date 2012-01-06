<?php /* Template Name: Projects Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_do_course_archive_title');
function cuny_do_course_archive_title() {
	echo '<h1 class="entry-title">Projects in Our Community</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_project_archive' );
function cuny_project_archive() {

	global $wpdb,$bp;
	$ids="9999999";
	if ($_GET['group_sequence'] != "") {
		$sequence_type = "type=" . $_GET['group_sequence'] . "&";
	}
	if( !empty( $_POST['group_search'] ) ){
		$search_terms="search_terms=".$_POST['group_search']."&";
	}
	
	$search_terms = '';
	if( !empty( $_GET['search'] ) ){
		$search_terms="search_terms=".$_GET['search']."&";
	}
	
	$rs = $wpdb->get_results( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} where meta_key='wds_group_type' and meta_value='project'" );
	foreach ( (array)$rs as $r ) $ids.= ",".$r->group_id;
	if ( bp_has_groups( $sequence_type.$search_terms.'include='.$ids.'&per_page=12&max=' . $instance['max_groups'] ) ) : ?>
	
    <p class="group-count"><?php bp_groups_pagination_count(); ?></p>
	<ul id="project-list" class="item-list">
	<?php $count = 1; ?>
		<?php while ( bp_groups() ) : bp_the_group(); ?>
			<li class="project<?php echo cuny_o_e_class($count) ?>">
				<div class="item-avatar alignleft">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 100, 'height' => 100 )) ?></a>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
					<div class="info-line">Project Information:</div>
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
		<?php $count++; ?>
		<?php endwhile; ?>
	</ul>

<?php else: ?>

	<div class="widget-error">
		<?php _e('There are no projects to display.', 'buddypress') ?>
	</div>

<?php endif; ?>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links() ?>
		</div><?php

}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { ?>
	<?php global $bp;
	//if($bp->loggedin_user->id > 0){?>
    <!--<div class="generic-button"><a href="<?php //echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/create/step/group-details/?type=project&new=true' ?>"><?php //_e( 'Create a Project', 'buddypress' ) ?></a></div>-->
    <?php //} ?>
    
    <h2 class="sidebar-title">Find a Club</h2>
    <p>Narrow down your search using the filters or search box below.</p>
    
    <?php if ($_GET['group_sequence'] == "") {
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
<div class="filter">
<div class="red-square"></div>
<form id="group_seq_form" name="group_seq_form" action="#" method="get">
	<select name="group_sequence" onchange="document.forms['group_seq_form'].submit();" class="last-select">
		<option value="<?php echo $option_value; ?>"><?php echo $display_option; ?></option>
		<option value='alphabetical'>Alphabetical</option>
		<option value='newest'>Newest</option>
		<option value='active'>Last Active</option>
	</select>
</form>
<div class="clearfloat"></div>
</div><!--filter-->

    <div class="archive-search">
    <div class="gray-square"></div>
    <form method="post">
    <input id="search-terms" type="text" name="group_search" value="<?php echo $_POST['group_search'];?>" />
    <input id="search-submit" type="submit" name="group_search_go" value="Search" />
    </form>
    <div class="clearfloat"></div>
    </div><!--archive search-->
<?php 
}

genesis();
