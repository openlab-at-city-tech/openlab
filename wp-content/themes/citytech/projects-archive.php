<?php /* Template Name: Projects Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_do_course_archive_title');
function cuny_do_course_archive_title() {
	echo '<h1 class="entry-title">Projects on the OpenLab</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_project_archive' );
function cuny_project_archive() {

	global $wpdb,$bp;
	$ids="9999999";

	$sequence_type = $search_terms = $search_terms_raw = '';

	if ( !empty( $_GET['group_sequence'] ) ) {
		$sequence_type = "type=" . $_GET['group_sequence'] . "&";
	}
	if( !empty( $_POST['group_search'] ) ){
		$search_terms_raw = $_POST['group_search'];
		$search_terms     = "search_terms=" . $search_terms_raw . "&";
	}

	if( !empty( $_GET['search'] ) ){
		$search_terms_raw = $_GET['search'];
		$search_terms     = "search_terms=" . $search_terms_raw . "&";
	}

	$in_sql = openlab_get_groups_in_sql( $search_terms_raw );

	$rs = $wpdb->get_results( "SELECT a.group_id FROM {$bp->groups->table_name_groupmeta} a where a.meta_key = 'wds_group_type' and a.meta_value='project' {$in_sql}" );

	// Hack to fix pagination
	add_filter( 'bp_groups_get_total_groups_sql', create_function( '', 'return "SELECT ' . count($rs) . ' AS value;";' ) );

	foreach ( (array)$rs as $r ) $ids.= ",".$r->group_id;
	if ( bp_has_groups( $sequence_type.$search_terms.'include='.$ids.'&per_page=12' ) ) : ?>

    <div class="group-count"><?php cuny_groups_pagination_count('Projects'); ?></div>
    <div class="clearfloat"></div>
	<ul id="project-list" class="item-list">
	<?php $count = 1; ?>
		<?php while ( bp_groups() ) : bp_the_group(); ?>
			<li class="project<?php echo cuny_o_e_class($count) ?>">
				<div class="item-avatar alignleft">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 100, 'height' => 100 )) ?></a>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
					<?php
					     $len = strlen(bp_get_group_description());
					     if ($len > 135) {
						$this_description = substr(bp_get_group_description(),0,135);
						$this_description = str_replace("</p>","",$this_description);
						echo $this_description.'&hellip; <a href="'.bp_get_group_permalink().'">See&nbsp;More</a></p>';
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

	<div class="pagination-links" id="group-dir-pag-top">
		<?php bp_groups_pagination_links() ?>
	</div>
<?php else: ?>

	<div class="widget-error">
		<?php _e('There are no projects to display.', 'buddypress') ?>
	</div>

<?php endif; ?>

<?php
}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { ?>
	<?php global $bp;
	//if($bp->loggedin_user->id > 0){?>
    <!--<div class="generic-button"><a href="<?php //echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/create/step/group-details/?type=project&new=true' ?>"><?php //_e( 'Create a Project', 'buddypress' ) ?></a></div>-->
    <?php //} ?>

    <h2 class="sidebar-title">Find a Project</h2>
    <p>Narrow down your search using the filters or search box below.</p>
    
    <?php //determine class type for filtering
	  $square_class = "gray-square";
	  $select_class = "gray-text";

    if ( empty( $_GET['group_sequence'] ) ) {
	$_GET['group_sequence'] = "active";
	} else {
		//if filtering is active, change the classes on the select fields
		$square_class = "red-square";
	  	$select_class = "red-text";
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
<form id="group_seq_form" name="group_seq_form" action="#" method="get">
	<div class="<?php echo $square_class; ?>"></div>
	<select name="group_sequence" onchange="document.forms['group_seq_form'].submit();" class="last-select <?php echo $select_class; ?>">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
	</select>
</form>
<div class="clearfloat"></div>
</div><!--filter-->

    <div class="archive-search">
    <div class="gray-square"></div>
    <form method="get">
    <input id="search-terms" type="text" name="search" placeholder="Search" />
    <input id="search-submit" type="submit" value="Search" />
    </form>
    <div class="clearfloat"></div>
    </div><!--archive search-->
<?php
}

genesis();
