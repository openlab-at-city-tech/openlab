<?php /* Template Name: People Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_members_title' );
function cuny_members_title() {
	global $wp_query;
	$post_obj = $wp_query->get_queried_object();
	echo '<h1 class="entry-title">'.$post_obj->post_title.' on the OpenLab</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_members_index' );
function cuny_members_index() {
	global $wp_query;
	$post_obj = $wp_query->get_queried_object();
	$type=$post_obj->post_title;
	echo '<div id="people-listing">';
		  cuny_list_members('more' );
	echo '</div>';
}
//
//     New parameter "view" - 'more' - tells it to format a "View More" link for that member type
//                            'page' - tells it to perform normal member pagination so they can 'page' through the members
//
function cuny_list_members($view) {
global $wpdb, $bp, $members_template;
   if ( !empty( $_GET['usertype'] ) ) {
    	$user_type=$_GET['usertype'];
    	$user_type=ucwords($user_type);
    }
    if( !empty( $_GET['usertype'] ) ) {
    	echo '<h3 id="bread-crumb">'.$user_type.'</h3>';
    	$rs = $wpdb->get_results( "SELECT user_id FROM {$bp->profile->table_name_data} where field_id=7 and value='".$user_type."'" );
    } else {
        $rs = $wpdb->get_results( "SELECT user_id FROM {$bp->profile->table_name_data} where field_id=7" ); 
    }

    if ($_GET['group_sequence'] != "") {
		$sequence_type = "type=" . $_GET['group_sequence'] . "&";
	}
	$search_terms = '';
	if(!empty($_POST['people_search'])){
		$search_terms="search_terms=".$_POST['people_search']."&";
	}
	if(!empty($_GET['search'])){
		$search_terms="search_terms=".$_GET['search']."&";
	}		
	$avatar_args = array (
			'type' => 'full',
			'width' => 72,
			'height' => 62,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' )
		);

	$ids="9999999";
	foreach ( (array)$rs as $r ){ $ids.= ",".$r->user_id ;}
	//bp_has_members was not playing nice with both include and type, so I left in type - then $ids are checked against the array
	if ( bp_has_members( $sequence_type.$search_terms.'&per_page=48') ) : ?>
	<div class="group-count"><?php cuny_members_pagination_count('members'); ?></div>
	<div class="clearfloat"></div>
			<div class="avatar-block">
				<?php while ( bp_members() ) : bp_the_member(); 
               //the following checks the current $id agains the passed list from the query
               $member_id = $members_template->member->id;
	            $is_listed = strpos($ids,$member_id);
	            if ($is_listed === false)
	            {}else{
					$registered=$members_template->member->user_registered; ?>
					<div class="person-block">
						<div class="item-avatar">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
						</div>
						<div class="cuny-member-info">
							<a class="member-name" href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>
							<span class="member-since-line">Member since <?php echo $registered; ?></span>
                            <?php if ( bp_get_member_latest_update() ) : ?>
								<span class="update"><?php bp_member_latest_update( 'length=10' ) ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php } //end if for is_listed ?>
				<?php endwhile; ?>
			</div>
					<div id="pag-top" class="pagination">
				
						<div class="pag-count" id="member-dir-count-top">
							<?php bp_members_pagination_count() ?>
						</div>
				
						<div class="pagination-links" id="member-dir-pag-top">
							<?php bp_members_pagination_links() ?>
						</div>
				
					</div>

		<?php else: 
			if($type=="Student"){
				$type="students";
			}?>

			<div class="widget-error">
				<p><?php _e( 'No '.strtolower($type).' were found.', 'buddypress' ) ?></p>
			</div>

		<?php endif;

}


add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { ?>
<h2 class="sidebar-title">Find People</h2>
    <p>Narrow down your search using the filters or search box below.</p>
    
    <?php 
    //user type
if ( empty( $_GET['semester'] ) ) {
	$_GET['semester'] = "active";
}
switch ($_GET['semester']) {
	case "student":
		$user_display_option = "Student";
		$user_option_value = "student";
		break;
	case "faculty":
		$user_isplay_option = "Faculty";
		$user_option_value = "faculty";
		break;
	case "staff":
		$user_display_option = "Faculty";
		$user_option_value = "faculty";
		break;
	default: 
		$user_display_option = "Select User Type";
		$user_option_value = "";
		break;
} 
    
    //sequencing
    if ($_GET['group_sequence'] == "") {
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
<form id="group_seq_form" name="group_seq_form" action="#" method="get">
	<div class="red-square"></div>
	<select name="usertype" class="last-select">
		<option value="<?php echo $user_option_value; ?>"><?php echo $user_display_option; ?></option>
		<option value='student'>Student</option>
		<option value='faculty'>Faculty</option>
		<option value='staff'>Staff</option>
	</select>
    <div class="red-square"></div>
	<select name="group_sequence" class="last-select">
		<option value="<?php echo $option_value; ?>"><?php echo $display_option; ?></option>
		<option value='alphabetical'>Alphabetical</option>
		<option value='newest'>Newest</option>
		<option value='active'>Last Active</option>
	</select>
	<input type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
</form>
<div class="clearfloat"></div>
</div><!--filter-->

    <div class="archive-search">
    <div class="gray-square"></div>
    <form method="post">
    <input id="search-terms" type="text" name="group_search" value="Search" />
    <input id="search-submit" type="submit" name="group_search_go" value="Search" />
    </form>
    <div class="clearfloat"></div>
    </div><!--archive search-->
<?php
}
genesis();