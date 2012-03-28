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
	echo '<div id="people-listing">';
		  cuny_list_members('more' );
	echo '</div>';
}
//
//     New parameter "view" - 'more' - tells it to format a "See More" link for that member type
//                            'page' - tells it to perform normal member pagination so they can 'page' through the members
//
function cuny_list_members($view) {
	global $wpdb, $bp, $members_template, $wp_query;

	// Set up variables

	// There are two ways to specify user type: through the page name, or a URL param
	$user_type = $sequence_type = $search_terms = '';
	if ( !empty( $_GET['usertype'] ) && $_GET['usertype'] != 'all' ) {
		$user_type = $_GET['usertype'];
		$user_type = ucwords( $user_type );
	} else {
		$post_obj  = $wp_query->get_queried_object();
		$post_title = !empty( $post_obj->post_title ) ? ucwords( $post_obj->post_title ) : '';

		if ( in_array( $post_title, array( 'Staff', 'Faculty', 'Students' ) ) ) {
			if ( 'Students' == $post_title ) {
				$user_type = 'Student';
			} else {
				$user_type = $post_title;
			}
		}
	}

	if ( !empty( $_GET['group_sequence'] ) ) {
		$sequence_type = $_GET['group_sequence'];
	}

	if( !empty($_POST['people_search'] ) ){
		$search_terms = $_POST['people_search'];
	} else if( !empty($_GET['search'] ) ) {
		$search_terms = $_GET['search'];
	} else if ( !empty( $_POST['group_search'] ) ) {
		$search_terms = $_POST['group_search'];
	}

    	if ( $user_type ) {
    		echo '<h3 id="bread-crumb">'.$user_type.'</h3>';
    	}

	// Set up the bp_has_members() arguments
	// Note that we're not taking user_type into account. We'll do that with a query filter
	$args = array( 'per_page' => 48 );

	if ( $sequence_type ) {
		$args['type'] = $sequence_type;
	}

	if ( $search_terms ) {
		// Filter the sql query so that we ignore the first name and last name fields
		$first_name_field_id = xprofile_get_field_id_from_name( 'First Name' );
		$last_name_field_id  = xprofile_get_field_id_from_name( 'Last Name' );

		// These are the same runtime-created functions, created separately so I don't have
		// to toss globals around. If you change one, change them both!
		add_filter( 'bp_core_get_paged_users_sql', create_function( '$sql', '
			$ex = explode( " AND ", $sql );
			array_splice( $ex, 1, 0, "spd.field_id NOT IN (' . $first_name_field_id . ',' . $last_name_field_id . ')" );
			$ex = implode( " AND ", $ex );

			return $ex;
		' ) );

		add_filter( 'bp_core_get_total_users_sql', create_function( '$sql', '
			$ex = explode( " AND ", $sql );
			array_splice( $ex, 1, 0, "spd.field_id NOT IN (' . $first_name_field_id . ',' . $last_name_field_id . ')" );
			$ex = implode( " AND ", $ex );

			return $ex;
		' ) );

		$args['search_terms'] = $search_terms;
	}

	// I don't love doing this
	if ( $user_type ) {
		// These are the same runtime-created functions, created separately so I don't have
		// to toss globals around. If you change one, change them both!
		add_filter( 'bp_core_get_paged_users_sql', create_function( '$sql', '
			// Join to profile table for user type
			$ex = explode( " LEFT JOIN ", $sql );
			array_splice( $ex, 1, 0, "' . $bp->profile->table_name_data . ' ut ON ut.user_id = u.ID" );
			$ex = implode( " LEFT JOIN ", $ex );

			// Add the necessary where clause
			$ex = explode( " AND ", $ex );
			array_splice( $ex, 1, 0, "ut.field_id = 7 AND ut.value = \'' . $user_type . '\'" );
			$ex = implode( " AND ", $ex );

			return $ex;
		' ) );

		add_filter( 'bp_core_get_total_users_sql', create_function( '$sql', '
			// Join to profile table for user type
			$ex = explode( " LEFT JOIN ", $sql );
			array_splice( $ex, 1, 0, "' . $bp->profile->table_name_data . ' ut ON ut.user_id = u.ID" );
			$ex = implode( " LEFT JOIN ", $ex );

			// Add the necessary where clause
			$ex = explode( " AND ", $ex );
			array_splice( $ex, 1, 0, "ut.field_id = 7 AND ut.value = \'' . $user_type . '\'" );
			$ex = implode( " AND ", $ex );

			return $ex;
		' ) );
    	}

	$avatar_args = array (
			'type' => 'full',
			'width' => 72,
			'height' => 72,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' )
		);


	if ( bp_has_members( $args ) ) :


	?>
	<div class="group-count"><?php cuny_members_pagination_count('members'); ?></div>
	<div class="clearfloat"></div>
			<div class="avatar-block">
				<?php while ( bp_members() ) : bp_the_member();
               //the following checks the current $id agains the passed list from the query
               $member_id = $members_template->member->id;


					$registered = bp_format_time( strtotime( $members_template->member->user_registered ), true ) ?>
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
			if($user_type=="Student"){
				$user_type="students";
			}?>

			<div class="widget-error">
				<p><?php _e( 'No '.strtolower($user_type).' were found.', 'buddypress' ) ?></p>
			</div>

		<?php endif;

}


add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { ?>
<h2 class="sidebar-title">Find People</h2>
    <p>Narrow down your search using the filters or search box below.</p>

    <?php
    //user type
if ( empty( $_GET['usertype'] ) ) {
	$_GET['usertype'] = "";
}
switch ($_GET['usertype']) {
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
	case "all":
		$user_display_option = "All";
		$user_option_value = "all";
		break;
	default:
		$user_display_option = "Select User Type";
		$user_option_value = "";
		break;
}

    //sequencing
if ( empty( $_GET['group_sequence'] ) ) {
	$_GET['group_sequence'] = "active";
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
		<option value='all'>All</option>
	</select>
    <div class="red-square"></div>
	<select name="group_sequence" class="last-select">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
	</select>
    <input type="button" value="Reset" onClick="window.location.href = '<?php bp_root_domain() ?>/people/'">
	<input type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
</form>
<div class="clearfloat"></div>
</div><!--filter-->

    <div class="archive-search">
    <div class="gray-square"></div>
    <form method="post">
    <input id="search-terms" type="text" name="group_search" placeholder="Search" />
    <input id="search-submit" type="submit" name="group_search_go" value="Search" />
    </form>
    <div class="clearfloat"></div>
    </div><!--archive search-->
<?php
}
genesis();