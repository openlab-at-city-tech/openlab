<?php /*
 Plugin Name: WDS CityTech
 Plugin URI: http://citytech.webdevstudios.com
 Description: Custom Functionality for CityTech BuddyPress Site.
 Version: 1.0
 Author: WebDevStudios
 Author URI: http://webdevstudios.com
 */

include 'wds-register.php';
include 'wds-docs.php';
include 'includes/oembed.php';

/**
 * Loading BP-specific stuff in the global scope will cause issues during activation and upgrades
 * Ensure that it's only loaded when BP is present.
 * See http://openlab.citytech.cuny.edu/redmine/issues/31
 */
function openlab_load_custom_bp_functions() {
	require ( dirname( __FILE__ ) . '/wds-citytech-bp.php' );
	require ( dirname( __FILE__ ) . '/includes/groupmeta-query.php' );
	require ( dirname( __FILE__ ) . '/includes/group-blogs.php' );
	require ( dirname( __FILE__ ) . '/includes/group-types.php' );
	require ( dirname( __FILE__ ) . '/includes/portfolios.php' );
	require ( dirname( __FILE__ ) . '/includes/search.php' );
}
add_action( 'bp_init', 'openlab_load_custom_bp_functions' );

global $wpdb;
date_default_timezone_set( 'America/New_York' );

function wds_add_default_member_avatar( $url = false ) {
	return WP_CONTENT_URL . '/img/bubbleavatar.jpg';
}
add_filter( 'bp_core_mysteryman_src', 'wds_add_default_member_avatar' );

function wds_default_signup_avatar( $img ) {
	if ( false !== strpos( $img, 'mystery-man' ) ) {
		$img = "<img src='" . wds_add_default_member_avatar() . "' width='200' height='200'>";
	}

	return $img;
}
add_filter( 'bp_get_signup_avatar', 'wds_default_signup_avatar' );

//
//   This function creates an excerpt of the string passed to the length specified and
//   breaks on a word boundary
//
function wds_content_excerpt( $text, $text_length ) {
	return bp_create_excerpt( $text, $text_length );
}

/**
 * Following filter is to correct Forum display of time since a post was written
 */
function openlab_get_the_topic_post_time_since( $current_time ) {
	global $topic_template;

	return bp_core_time_since( $topic_template->post->post_time );
}
//add_filter( 'bp_get_the_topic_post_time_since', 'openlab_get_the_topic_post_time_since' );

/**
 * Filtering the member last active value
 */
function openlab_get_last_activity( $last_activity, $last_activity_date, $string ) {
	if ( !is_numeric( $last_activity_date ) )
		$last_activity_date = strtotime( $last_activity_date );

	if ( !$last_activity_date || empty( $last_activity_date ) )
		$last_active = __( 'not recently active', 'buddypress' );
	else
		$last_active = sprintf( $string, bp_core_time_since( $last_activity_date ) );

	return $last_active;
}
add_filter( 'bp_core_get_last_activity', 'openlab_get_last_activity', 10, 3 );

/**
 * Filtering group last active value
 */
function openlab_get_group_last_active( $last_active ) {
	global $groups_template;

	if ( empty( $group ) )
		$group =& $groups_template->group;

	$last_active = $group->last_activity;

	if ( !$last_active )
		$last_active = groups_get_groupmeta( $group->id, 'last_activity' );

	if ( empty( $last_active ) ) {
		return __( 'not yet active', 'buddypress' );
	} else {
		return bp_core_time_since( strtotime( $last_active ) );
	}
}
add_filter( 'bp_get_group_last_active', 'openlab_get_group_last_active' );

/**
 * Filtering activity value
 */
function openlab_activity_time_since( $text, $activity ) {
	return '<span class="time-since">' . bp_core_time_since( strtotime( $activity->date_recorded ) ) . '</span>';
}
add_filter( 'bp_activity_time_since', 'openlab_activity_time_since', 10, 2 );

add_action( 'bp_before_group_forum_topic_posts', 'wds_forum_topic_next_prev' );
function wds_forum_topic_next_prev() {
	global $groups_template, $wpdb;

	$forum_id = groups_get_groupmeta( $groups_template->group->id, 'forum_id' );
	$topic_id = bp_get_the_topic_id();
	$group_slug = bp_get_group_slug();
	$next_topic = $wpdb->get_results( "SELECT * FROM wp_bb_topics
				                 WHERE forum_id='$forum_id' AND topic_id > '$topic_id' AND topic_status='0'
						 ORDER BY topic_id ASC LIMIT 1", 'ARRAY_A' );
	$next_topic_slug = isset( $next_topic[0]['topic_slug'] ) ? $next_topic[0]['topic_slug'] : '';
	//echo "<br />Next Topic ID: " . $next_topic[0]['topic_id'];
	$previous_topic = $wpdb->get_results( "SELECT * FROM wp_bb_topics
				                 WHERE forum_id='$forum_id' AND topic_id < '$topic_id' AND topic_status='0'
						 ORDER BY topic_id DESC LIMIT 1", 'ARRAY_A' );
	$previous_topic_slug = isset( $previous_topic[0]['topic_slug'] ) ? $previous_topic[0]['topic_slug'] : '';
	if ( $previous_topic_slug != '' ) {
		echo "<a href='" . site_url() . "/groups/$group_slug/forum/topic/$previous_topic_slug'><<< Previous Topic &nbsp;&nbsp;&nbsp&nbsp;</a>";
	}
	if ( $next_topic_slug != '' ) {
		echo "<a href='" . site_url() . "/groups/$group_slug/forum/topic/$next_topic_slug'> Next Topic >>></a>";
	}
	/*
	echo "<br />Previous Topic ID: " . $previous_topic[0]['topic_id'];
	echo "<br />Next Topic / Previous Topic ";
	echo "<br />Forum ID: " . $forum_id;
	echo "<br />Topic ID: " . bp_get_the_topic_id();
	*/
}

/**
 * On activation, copies the BP first/last name profile field data into the WP 'first_name' and
 * 'last_name' fields.
 *
 * @todo This should probably be moved to a different hook. This $last_user lookup is hackish and
 *       may fail in some edge cases. I believe the hook bp_activated_user is correct.
 */
add_action( 'bp_after_activation_page', 'wds_bp_complete_signup' );
function wds_bp_complete_signup() {
		global $bp, $wpdb, $user_ID;

	$last_user = $wpdb->get_results( "SELECT * FROM wp_users ORDER BY ID DESC LIMIT 1", 'ARRAY_A' );
//       echo "<br />Last User ID: " . $last_user[0]['ID'] . " Last Login name: " . $last_user[0]['user_login'];
	$user_id = $last_user[0]['ID'];
	$first_name= xprofile_get_field_data( 'First Name', $user_id );
	$last_name=  xprofile_get_field_data( 'Last Name', $user_id );
//	echo "<br />User ID: $user_id First : $first_name Last: $last_name";
	$update_user_first = update_user_meta( $user_id, 'first_name', $first_name );
	$update_user_last = update_user_meta( $user_id, 'last_name', $last_name );
}

//child theme privacy - if corresponding group is private or hidden restrict access to site
/*add_action( 'init','wds_check_blog_privacy' );
function wds_check_blog_privacy() {
	global $bp, $wpdb, $blog_id, $user_ID;
	if ( $blog_id! = 1 ) {
		$wds_bp_group_id = get_option( 'wds_bp_group_id' );
		if ( $wds_bp_group_id ) {
			$group = new BP_Groups_Group( $wds_bp_group_id );
			$status = $group->status;
			if ( $status! = "public" ) {
				//check memeber
				if ( !is_user_member_of_blog( $user_ID, $blog_id ) ) {
					echo "<center><img src='http://openlab.citytech.cuny.edu/wp-content/mu-plugins/css/images/cuny-sw-logo.png'><h1>";
					echo "This is a private website, ";
					if ( $user_ID == 0 ) {
						echo "please login to gain access.";
					} else {
						echo "you do not have access.";
					}
					echo "</h1></center>";
					exit();
				}
			}
		}
	}
}*/

/**
 * On secondary sites, add our additional buttons to the site nav
 *
 * This function filters wp_page_menu, which is what shows up when no custom
 * menu has been selected. See cuny_add_group_menu_items() for the
 * corresponding method for custom menus.
 */
function my_page_menu_filter( $menu ) {
	global $bp, $wpdb;

	if ( strpos( $menu, 'Home' ) !== false ) {
		$menu = str_replace( 'Site Home', 'Home', $menu );
		$menu = str_replace( 'Home', 'Site Home', $menu );
	} else {
		$menu = str_replace( '<div class="menu"><ul>','<div class="menu"><ul><li><a title="Site Home" href="' . site_url() . '">Site Home</a></li>', $menu );
	}
	$menu = str_replace( 'Site Site Home', 'Site Home', $menu );

	// Only say 'Home' on the ePortfolio theme
	// @todo: This will probably get extended to all sites
	$menu = str_replace( 'Site Home', 'Home', $menu );

	$wds_bp_group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

	if ( $wds_bp_group_id  ) {
		$group_type = ucfirst( groups_get_groupmeta( $wds_bp_group_id, 'wds_group_type' ) );
		$group = new BP_Groups_Group( $wds_bp_group_id, true );
		$menu_a = explode( '<ul>', $menu );
		$menu_a = array(
			$menu_a[0],
			'<ul>',
			'<li id="group-profile-link"><a title="Site" href="' . bp_get_root_domain() . '/groups/' . $group->slug . '/">' . $group_type . ' Profile</a></li>',
			$menu_a[1],
		);
		$menu = implode( '', $menu_a );
	}
	return $menu;
}
add_filter( 'wp_page_menu', 'my_page_menu_filter' );

//child theme menu filter to link to website
function cuny_add_group_menu_items( $items, $args ) {
		// The Sliding Door theme shouldn't get any added items
		// See http://openlab.citytech.cuny.edu/redmine/issues/772
		if ( 'custom-sliding-menu' == $args->theme_location ) {
				return $items;
		}

	if ( ! bp_is_root_blog() ) {
		if ( ( strpos( $items, 'Contact' ) ) ) {
		} else {
			$items = '<li><a title="Home" href="' . site_url() . '">Home</a></li>' . $items;
		}
		$items = cuny_group_menu_items() . $items;
	}

	return $items;
}
add_filter( 'wp_nav_menu_items','cuny_add_group_menu_items', 10, 2 );

function cuny_group_menu_items() {
	global $bp, $wpdb;

	$wds_bp_group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

	if ( $wds_bp_group_id ) {
		$group_type = ucfirst( groups_get_groupmeta( $wds_bp_group_id, 'wds_group_type' ) );
		$group = new BP_Groups_Group( $wds_bp_group_id, true );

		$tab = '<li id="group-profile-link"><a title="Site" href="' . bp_get_root_domain() . '/groups/'.$group->slug.'/">'.$group_type.' Profile</a></li>';
		$tabs = $tab;
	} else {
		$tabs = '';
	}

	return $tabs;
}

//add breadcrumbs for buddypress pages
add_action( 'wp_footer','wds_footer_breadcrumbs' );
function wds_footer_breadcrumbs() {
	global $bp, $bp_current;
	if ( bp_is_group() ) {
		$group_id = $bp->groups->current_group->id;
		$b2 = $bp->groups->current_group->name;
		$group_type = groups_get_groupmeta( $bp->groups->current_group->id, 'wds_group_type' );
		if ( $group_type == "course" ) {
			$b1 = '<a href="'.site_url().'/courses/">Courses</a>';
		} elseif ( $group_type == "project" ) {
			$b1 = '<a href="'.site_url().'/projects/">Projects</a>';
		} elseif ( $group_type == "club" ) {
			$b1 ='<a href="'.site_url().'/clubs/">Clubs</a>';
		} else {
			$b1 = '<a href="'.site_url().'/groups/">Groups</a>';
		}

	}
	if ( !empty( $bp->displayed_user->id ) ) {
		$account_type = xprofile_get_field_data( 'Account Type', $bp->displayed_user->id );
		if ( $account_type == "Staff" ) {
			$b1 = '<a href="'.site_url().'/people/">People</a> / <a href="'.site_url().'/people/staff/">Staff</a>';
		} elseif ( $account_type == "Faculty" ) {
			$b1 = '<a href="'.site_url().'/people/">People</a> / <a href="'.site_url().'/people/faculty/">Faculty</a>';
		} elseif ( $account_type == "Student" ) {
			$b1 = '<a href="'.site_url().'/people/">People</a> / <a href="'.site_url().'/people/students/">Students</a>';
		} else {
			$b1 = '<a href="'.site_url().'/people/">People</a>';
		}
		$last_name= xprofile_get_field_data( 'Last Name', $bp->displayed_user->id );
		$b2 = ucfirst( $bp->displayed_user->fullname );//.''.ucfirst( $last_name )
	}
	if ( bp_is_group() || !empty( $bp->displayed_user->id ) ) {
		$breadcrumb = '<div class="breadcrumb">You are here:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="View Home" href="http://openlab.citytech.cuny.edu/">Home</a> / '.$b1.' / '.$b2.'</div>';
		$breadcrumb = str_replace( "'","\'", $breadcrumb );?>
    	<script>document.getElementById( 'breadcrumb-container' ).innerHTML='<?php echo $breadcrumb; ?>';</script>
	<?php
	}
}




//Filter bp members full name
//add_filter( 'bp_get_member_name', 'wds_bp_the_site_member_realname' );
//add_filter( 'bp_member_name', 'wds_bp_the_site_member_realname' );
//add_filter( 'bp_get_displayed_user_fullname', 'wds_bp_the_site_member_realname' );
//add_filter( 'bp_displayed_user_fullname', 'wds_bp_the_site_member_realname' );
//add_filter( 'bp_get_loggedin_user_fullname', 'wds_bp_the_site_member_realname' );
//add_filter( 'bp_loggedin_user_fullname', 'wds_bp_the_site_member_realname' );
function wds_bp_the_site_member_realname() {
	global $bp;
	global $members_template;
	$members_template->member->fullname = $members_template->member->display_name;
	$user_id = $members_template->member->id;
	$first_name = xprofile_get_field_data( 'Name', $user_id );
	$last_name = xprofile_get_field_data( 'Last Name', $user_id );
	return ucfirst( $first_name )." ".ucfirst( $last_name );
}

//filter names in activity
/*add_filter( 'bp_get_activity_action', 'wds_bp_the_site_member_realname_activity' );
add_filter( 'bp_get_activity_user_link', 'wds_bp_the_site_member_realname_activity' );
function wds_bp_the_site_member_realname_activity() {
	global $bp;
	global $activities_template;
	print_r( $activities_template );
	$action = $activities_template->activity->action;
	echo "<hr><xmp>".$action."</xmp>";
	return $action;
	$user_id = $activities_template->activity->user_id;
	$first_name= xprofile_get_field_data( 'Name', $user_id );
	$last_name= xprofile_get_field_data( 'Last Name', $user_id );
	$activities_template->activity->user_nicename = "rr";
	$link = bp_core_get_user_domain( $activities_template->activity->user_id, $activities_template->activity->user_nicename, $activities_template->activity->user_login );
	return "werwe";
}*/

//Default BP Avatar Full
if ( !defined( 'BP_AVATAR_FULL_WIDTH' ) )
define( 'BP_AVATAR_FULL_WIDTH', 225 );
if ( !defined( 'BP_AVATAR_FULL_HEIGHT' ) )
define( 'BP_AVATAR_FULL_HEIGHT', 225 );


/**
 * Don't let child blogs use bp-default or a child thereof
 *
 * @todo Why isn't this done by network disabling BP Default and its child themes?
 * @todo Why isn't BP_DISABLE_ADMIN_BAR defined somewhere like bp-custom.php?
 */
function wds_default_theme() {
	global $wpdb, $blog_id;
	if ( $blog_id>1 ) {
		define( 'BP_DISABLE_ADMIN_BAR', true );
		$theme = get_option( 'template' );
		if ( $theme == "bp-default" ) {
			switch_theme( "twentyten", "twentyten" );
			wp_redirect( home_url() );
			exit();
		}
	}
}
add_action( 'init', 'wds_default_theme' );

//register.php -hook for new div to show account type fields
add_action( 'bp_after_signup_profile_fields', 'wds__bp_after_signup_profile_fields' );
function wds__bp_after_signup_profile_fields() {?>
<div class="editfield"><div id="wds-account-type"></div></div>
<?php
}


add_action( 'wp_head', 'wds_registration_ajax' );
function wds_registration_ajax() {
	wp_print_scripts( array( 'sack' ) );
	$sack = 'var isack = new sack( "'.get_bloginfo( 'wpurl' ).'/wp-admin/admin-ajax.php" );';
	$loading = '<img src="'.get_bloginfo( 'template_directory' ).'/_inc/images/ajax-loader.gif">';?>
	<script type="text/javascript">
		//<![CDATA[

		//load register account type
		function wds_load_account_type( id,default_type ) {
			<?php echo $sack;?>
			//document.getElementById( 'save-pad' ).innerHTML='<?php echo $loading; ?>';
			if ( default_type != "" ) {
			 selected_value = default_type;
			} else {
			   var select_box=document.getElementById( id );
			   var selected_index=select_box.selectedIndex;
			   var selected_value = select_box.options[selected_index].value;
			}

			if ( selected_value != "" ) {
				document.getElementById( 'signup_submit' ).style.display='';
			} else {
				document.getElementById( 'signup_submit' ).style.display='none';
			}

			isack.execute = 1;
			isack.method = 'POST';
			isack.setVar( "action", "wds_load_account_type" );
			isack.setVar( "account_type", selected_value );
			isack.runAJAX();
			return true;
		}


		//]]>
	</script>
	<?php
}
add_action( 'bp_after_registration_submit_buttons' , 'wds_load_default_account_type' );
function wds_load_default_account_type() {
 		    $return = '<script type="text/javascript">';

		    $account_type = isset( $_POST['field_7'] ) ? $_POST['field_7'] : '';
		    $type = '';
		    $selected_index = '';

		    if ( $account_type == "Student" ) {
			$type = "Student";
			$selected_index = 1;
		    }
		    if ( $account_type == "Faculty" ) {
			$type = "Faculty";
			$selected_index = 2;
		    }
		    if ( $account_type == "Staff" ) {
			$type = "Staff";
			$selected_index = 3;
		    }

		    if ( $type && $selected_index ) {
			$return .=  'var select_box=document.getElementById( \'field_7\' );';
			$return .=  'select_box.selectedIndex = ' . $selected_index . ';';
			$return .= "wds_load_account_type( 'field_7','$type' );";
		    }
		    $return .= '</script>';
		    echo $return;

}

function wds_load_account_type() {
	global $wpdb, $bp;
	$return = '';
	$account_type = $_POST['account_type'];
	if ( $account_type ) {
		$return .= wds_get_register_fields( $account_type );
	} else {
		$return = "Please select an Account Type.";
	}
	$return = str_replace( "'","\'", $return );
	die( "document.getElementById( 'wds-account-type' ).innerHTML='$return'" );
}
add_action( 'wp_ajax_wds_load_account_type', 'wds_load_account_type' );
add_action( 'wp_ajax_nopriv_wds_load_account_type', 'wds_load_account_type' );

function wds_bp_profile_group_tabs() {
	global $bp, $group_name;
	if ( !$groups = wp_cache_get( 'xprofile_groups_inc_empty', 'bp' ) ) {
		$groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
		wp_cache_set( 'xprofile_groups_inc_empty', $groups, 'bp' );
	}
	if ( empty( $group_name ) )
		$group_name = bp_profile_group_name( false );

	for ( $i = 0; $i < count( $groups ); $i++ ) {
		if ( $group_name == $groups[$i]->name ) {
			$selected = ' class="current"';
		} else {
			$selected = '';
		}
		$account_type = bp_get_profile_field_data( 'field=Account Type' );
		if ( $groups[$i]->fields ) {
			echo '<li' . $selected . '><a href="' . $bp->displayed_user->domain . $bp->profile->slug . '/edit/group/' . $groups[$i]->id . '">' . esc_attr( $groups[$i]->name ) . '</a></li>';
		}
	}
	do_action( 'xprofile_profile_group_tabs' );
}
//Group Stuff
add_action( 'wp_head', 'wds_groups_ajax' );
function wds_groups_ajax() {
	global $bp;
	wp_print_scripts( array( 'sack' ) );
	$sack = 'var isack = new sack( "'.get_bloginfo( 'wpurl' ).'/wp-admin/admin-ajax.php" );';
	$loading = '<img src="'.get_bloginfo( 'template_directory' ).'/_inc/images/ajax-loader.gif">';?>
	<script type="text/javascript">
		//<![CDATA[
		function wds_load_group_type( id ) {
			<?php echo $sack;?>
			var select_box=document.getElementById( id );
			var selected_index=select_box.selectedIndex;
			var selected_value = select_box.options[selected_index].value;
			isack.execute = 1;
			isack.method = 'POST';
			isack.setVar( "action", "wds_load_group_type" );
			isack.setVar( "group_type", selected_value );
			isack.runAJAX();
			return true;
		}

		function wds_load_group_departments( id ) {
			<?php $group= bp_get_current_group_id();
			echo $sack;?>
			var schools="0";
			if ( document.getElementById( 'school_tech' ).checked ) {
				schools=schools+","+document.getElementById( 'school_tech' ).value;
			}
			if ( document.getElementById( 'school_studies' ).checked ) {
				schools=schools+","+document.getElementById( 'school_studies' ).value;
			}
			if ( document.getElementById( 'school_arts' ).checked ) {
				schools=schools+","+document.getElementById( 'school_arts' ).value;
			}
			var group_type = jQuery( 'input[name="group_type"]' ).val();
			isack.execute = 1;
			isack.method = 'POST';
			isack.setVar( "action", "wds_load_group_departments" );
			isack.setVar( "schools", schools );
			isack.setVar( "group", "<?php echo $group;?>" );
			isack.setVar( "is_group_create", "<?php echo intval( bp_is_group_create() ) ?>" );
			isack.setVar( "group_type", group_type );
			isack.runAJAX();
			return true;
		}
		//]]>
	</script>
	<?php
}

add_action( 'wp_ajax_wds_load_group_departments', 'wds_load_group_departments' );
add_action( 'wp_ajax_nopriv_wds_load_group_departments', 'wds_load_group_departments' );
function wds_load_group_departments() {
	global $wpdb, $bp;
	$group = $_POST['group'];
	$schools = $_POST['schools'];
	$group_type = $_POST['group_type'];
	$is_group_create = ( bool ) $_POST['is_group_create'];
	$schools = str_replace( "0,","", $schools );
	$schools = explode( ",", $schools );


	$departments_tech    = openlab_get_department_list( 'tech' );
	$departments_studies = openlab_get_department_list( 'studies' );
	$departments_arts    = openlab_get_department_list( 'arts' );

	// We want to prefill the School and Dept fields, which means we have
	// to prefetch the dept field and figure out School backward
	if ( 'portfolio' == strtolower( $group_type ) && $is_group_create ) {
		$account_type = strtolower( bp_get_profile_field_data( array(
			'field' => 'Account Type',
			'user_id' => bp_loggedin_user_id()
		) ) );
		$dept_field = 'student' == $account_type ? 'Major Program of Study' : 'Department';

		$wds_departments = (array) bp_get_profile_field_data( array(
			'field' => $dept_field,
			'user_id' => bp_loggedin_user_id()
		) );

		foreach ( $wds_departments as $d ) {
			if ( in_array( $d, $departments_tech ) )
				$schools[] = 'tech';

			if ( in_array( $d, $departments_studies ) )
				$schools[] = 'studies';

			if ( in_array( $d, $departments_arts ) )
				$schools[] = 'art';
		}
	}

	$departments = array();
	if ( in_array( "tech", $schools ) ) {
		$departments = array_merge_recursive( $departments, $departments_tech );
	}
	if ( in_array( "studies", $schools ) ) {
		$departments = array_merge_recursive( $departments, $departments_studies );
	}
	if ( in_array( "arts", $schools ) ) {
		$departments = array_merge_recursive( $departments, $departments_arts );
	}
	sort( $departments );

	if ( 'portfolio' == strtolower( $group_type ) && $is_group_create ) {
		$wds_departments = (array) bp_get_profile_field_data( array(
			'field' => $dept_field,
			'user_id' => bp_loggedin_user_id()
		) );
	} else {
		$wds_departments = groups_get_groupmeta( $group, 'wds_departments' );
		$wds_departments = explode( ",", $wds_departments );
	}

	$return = '<div class="department-list-container">';
	foreach ( $departments as $i => $value ) {
		$checked = "";
		if ( in_array( $value, $wds_departments ) ) {
			$checked = "checked";
		}
		$return .= "<input type='checkbox' name='wds_departments[]' value='".$value."' ".$checked."> ".$value."<br>";
	}
	$return .= "</div>";
	$return = str_replace( "'","\'", $return );
	die( "document.getElementById( 'departments_html' ).innerHTML='$return'" );
}

/**
 * Get a list of schools
 */
function openlab_get_school_list() {
	return array(
		'tech'       => 'Technology & Design',
		'studies'    => 'Professional Studies',
		'arts'       => 'Arts & Sciences'
	);
}

/**
 * Get a list of departments
 *
 * @param str Optional. Leave out to get all departments
 */
function openlab_get_department_list( $school = '', $label_type = 'full' ) {

	// Sanitize school name
	$schools = openlab_get_school_list();
	if ( isset( $schools[$school] ) ) {
		$school = $school;
	} else if ( in_array( $school, $schools ) ) {
		$school = array_search( $school, $schools );
	}

	$all_departments = array(
		'tech' => array(
			'advertising-design-and-graphic-arts' => array(
				'label' => 'Advertising Design and Graphic Arts',
			),
			'architectural-technology' => array(
				'label' => 'Architectural Technology',
			),
			'computer-engineering-technology' => array(
				'label' => 'Computer Engineering Technology',
			),
			'computer-systems-technology' => array(
				'label' => 'Computer Systems Technology',
			),
			'construction-management-and-civil-engineering-technology' => array(
				'label' => 'Construction Management and Civil Engineering Technology',
				'short_label' => 'Construction & Civil Engineering Tech',
			),
			'electrical-and-telecommunications-engineering-technology' => array(
				'label' => 'Electrical and Telecommunications Engineering Technology',
				'short_label' => 'Electrical & Telecom Engineering Tech',
			),
			'entertainment-technology' => array(
				'label' => 'Entertainment Technology',
			),
			'environmental-control-technology' => array(
				'label' => 'Environmental Control Technology',
			),
			'mechanical-engineering-technology' => array(
				'label' => 'Mechanical Engineering Technology'
			),
		),
		'studies' => array(
			'business' => array(
				'label' => 'Business',
			),
			'career-and-technology-teacher-education' => array(
				'label' => 'Career and Technology Teacher Education',
			),
			'dental-hygiene' => array(
				'label' => 'Dental Hygiene',
			),
			'health-services-administration' => array(
				'label' => 'Health Services Administration',
			),
			'hospitality-management' => array(
				'label' => 'Hospitality Management',
			),
			'human-services' => array(
				'label' => 'Human Services',
			),
			'law-and-paralegal-studies' => array(
				'label' => 'Law and Paralegal Studies',
			),
			'nursing' => array(
				'label' => 'Nursing',
			),
			'radiologic-technology-and-medical-imaging' => array(
				'label' => 'Radiologic Technology and Medical Imaging',
			),
			'restorative-dentistry' => array(
				'label' => 'Restorative Dentistry',
			),
			'vision-care-technology' => array(
				'label' => 'Vision Care Technology',
			),
		),
		'arts' => array(
			'african-american-studies' => array(
				'label' => 'African American Studies',
			),
			'biological-sciences' => array(
				'label' => 'Biological Sciences',
			),
			'chemistry' => array(
				'label' => 'Chemistry',
			),
			'english' => array(
				'label' => 'English',
			),
			'humanities' => array(
				'label' => 'Humanities',
			),
			'library' => array(
				'label' => 'Library',
			),
			'mathematics' => array(
				'label' => 'Mathematics',
			),
			'physics' => array(
				'label' => 'Physics',
			),
			'social-science' => array(
				'label' => 'Social Science',
			),
		),
	);

	// Lazy - I didn't feel like manually converting to key-value structure
	$departments_sorted = array();
	foreach ( $schools as $s_key => $s_label ) {
		// Skip if we only want one school
		if ( $school && $s_key != $school ) {
			continue;
		}

		$departments_sorted[$s_key] = array();
	}

	foreach ( $all_departments as $s_key => $depts ) {
		// Skip if we only want one school
		if ( $school && $s_key != $school ) {
			continue;
		}

		foreach ( $depts as $dept_name => $dept ) {
			if ( 'short' == $label_type ) {
				$d_label = isset( $dept['short_label'] ) ? $dept['short_label'] : $dept['label'];
			} else {
				$d_label = $dept['label'];
			}

			$departments_sorted[ $s_key ][ $dept_name ] = $d_label;
		}
	}

	if ( $school ) {
		$departments_sorted = $departments_sorted[$school];
	}

	return $departments_sorted;
}

add_action( 'init', 'wds_new_group_type' );
function wds_new_group_type() {
  if ( isset( $_GET['new'] ) && $_GET['new'] == "true" && isset( $_GET['type'] ) ) {
	  global $bp;
	  unset( $bp->groups->current_create_step );
	  unset( $bp->groups->completed_create_steps );

	  setcookie( 'bp_new_group_id', false, time() - 1000, COOKIEPATH );
	  setcookie( 'bp_completed_create_steps', false, time() - 1000, COOKIEPATH );
	  setcookie( 'wds_bp_group_type', $_GET['type'], time() + 20000, COOKIEPATH );
	  bp_core_redirect( $bp->root_domain . '/' . $bp->groups->slug . '/create/step/group-details/?type='.$_GET['type'] );
  }
}

add_action( 'wp_ajax_wds_load_group_type', 'wds_load_group_type' );
add_action( 'wp_ajax_nopriv_wds_load_group_type', 'wds_load_group_type' );
function wds_load_group_type( $group_type ) {
	global $wpdb, $bp, $user_ID;

	$return = '';

	if ( $group_type ) {
		$echo = true;
		$return = '<input type="hidden" name="group_type" value="' . ucfirst( $group_type ) . '">';
	} else {
		$group_type = $_POST['group_type'];
	}

	$wds_group_school = groups_get_groupmeta( bp_get_current_group_id(), 'wds_group_school' );
	$wds_group_school = explode( ",", $wds_group_school );

	$account_type = xprofile_get_field_data( 'Account Type', bp_loggedin_user_id() );

	$return .= '<table>';

	$return .= '<tr class="schools">';

	$return .= '<td class="block-title" colspan="2">School(s)';
	if ( openlab_is_school_required_for_group_type( $group_type ) && ( 'staff' != strtolower( $account_type ) || is_super_admin( get_current_user_id() ) ) ) {
		$return .= ' <span class="required">(required)</span>';
	}
	$return .= '</td></tr>';

		$return .= '<tr class="school-tooltip"><td colspan="2">';

		// associated school/dept tooltip
	switch ( $group_type ) {
		case 'course' :
			$return .= '<p class="ol-tooltip">If your course is associated with one or more of the college’s schools or departments, please select from the checkboxes below.</p>';
			break;
		case 'portfolio' :
			$return .= '<p class="ol-tooltip">If your ' . openlab_get_portfolio_label() . ' is associated with one or more of the college’s schools or departments, please select from the checkboxes below.</p>';
			break;
		case 'project' :
			$return .= '<p class="ol-tooltip">Is your Project associated with one or more of the college\'s schools?</p>';
			break;
		case 'club' :
			$return .= '<p class="ol-tooltip">Is your Club associated with one or more of the college\'s schools?</p>';
			break;
	}

		$return .= '</td></tr>';

		$return .= '<tr><td class="school-inputs" colspan="2">';

	// If this is a Portfolio, we'll pre-check the school and department
	// of the logged-in user
	$checked_array = array( 'schools' => array(), 'departments' => array() );
	if ( 'portfolio' == $group_type && bp_is_group_create() ) {
		$account_type = strtolower( bp_get_profile_field_data( array(
			'field' => 'Account Type',
			'user_id' => bp_loggedin_user_id()
		) ) );
		$dept_field = 'student' == $account_type ? 'Major Program of Study' : 'Department';

		$user_department = bp_get_profile_field_data( array(
			'field'   => $dept_field,
			'user_id' => bp_loggedin_user_id()
		) );

		if ( $user_department ) {
			$all_departments = openlab_get_department_list();
			foreach ( $all_departments as $school => $depts ) {
				if ( in_array( $user_department, $depts ) ) {
					$checked_array['schools'][] = $school;
					$checked_array['departments'][] = array_search( $user_department, $depts );
					break;
				}
			}
		}
	} else {
		foreach ( (array) $wds_group_school as $school ) {
			$checked_array['schools'][] = $school;
		}
	}

	if ( $group_type == "course" || $group_type == 'portfolio' ) {
		$onclick = 'onclick="wds_load_group_departments();"';
	} else {
		$onclick = '';
	}

	$return.= '<label><input type="checkbox" id="school_tech" name="wds_group_school[]" value="tech" '.$onclick.' ' . checked( in_array( 'tech', $checked_array['schools'] ), true, false ) . '> Technology & Design</label>';

	$return.= '<label><input type="checkbox" id="school_studies" name="wds_group_school[]" value="studies" '.$onclick.' '. checked( in_array( 'studies', $checked_array['schools'] ), true, false ) .'> Professional Studies</label>';

	$return.= '<label><input type="checkbox" id="school_arts" name="wds_group_school[]" value="arts" '.$onclick.' '. checked( in_array( 'arts', $checked_array['schools'] ), true, false ) .'> Arts & Sciences</label>';

	$return .= '</td>';
	$return .= '</tr>';

	if ( 'course' == $group_type || 'portfolio' == $group_type ) {
		// For the love of Pete, it's not that hard to cast variables
		$wds_faculty = $wds_course_code = $wds_section_code = $wds_semester = $wds_year = $wds_course_html = '';

		if ( bp_get_current_group_id() ) {
			$wds_faculty      = groups_get_groupmeta( bp_get_current_group_id(), 'wds_faculty' );
			$wds_course_code  = groups_get_groupmeta( bp_get_current_group_id(),  'wds_course_code' );
			$wds_section_code = groups_get_groupmeta( bp_get_current_group_id(), 'wds_section_code' );
			$wds_semester     = groups_get_groupmeta( bp_get_current_group_id(), 'wds_semester' );
			$wds_year         = groups_get_groupmeta( bp_get_current_group_id(), 'wds_year' );
			$wds_course_html  = groups_get_groupmeta( bp_get_current_group_id(), 'wds_course_html' );
		}
		//$return. = '<tr>';
           //$return. = ' <td>Faculty:';
			//$return. = '<td><input type="text" name="wds_faculty" value="'.$bp->loggedin_user->fullname.'"></td>';
		//$return. = '</tr>';
		$last_name= xprofile_get_field_data( 'Last Name', $bp->loggedin_user->id );
		$return.= '<input type="hidden" name="wds_faculty" value="'.$bp->loggedin_user->fullname.' '.$last_name.'">';

		$return.= '<tr class="department-title">';

		$return .= '<td colspan="2" class="block-title">Department(s)';
		if ( openlab_is_school_required_for_group_type( $group_type ) && 'staff' != strtolower( $account_type ) ) {
			$return .= ' <span class="required">(required)</span>';
		}
		$return .= '</td></tr>';
			$return.= '<tr class="departments"><td id="departments_html" colspan="2"></td>';
		$return.= '</tr>';

		if ( 'course' == $group_type ) {

			$return .= '<tr><td colspan="2"><p class="ol-tooltip">The following fields are not required, but including this information will make it easier for others to find your Course.</p></td></tr>';

			$return .= '<tr class="additional-field course-code-field">';
			$return .= '<td class="additional-field-label">Course Code:</td>';
			$return .= '<td><input type="text" name="wds_course_code" value="' . $wds_course_code . '"></td>';
			$return .= '</tr>';

			$return .= '<tr class="additional-field section-code-field">';
			$return .= '<td class="additional-field-label">Section Code:';
			$return .= '<td><input type="text" name="wds_section_code" value="' . $wds_section_code . '"></td>';
			$return .= '</tr>';

			$return .= '<tr class="additional-field semester-field">';
			$return .= '<td class="additional-field-label">Semester:';
			$return .= '<td><select name="wds_semester">';
			$return .= '<option value="">--select one--';

			$checked = $Spring = $Summer = $Fall = $Winter = "";

			if ( $wds_semester == "Spring" ) {
				$Spring = "selected";
			} elseif ( $wds_semester == "Summer" ) {
				$Summer = "selected";
			} elseif ( $wds_semester == "Fall" ) {
				$Fall   = "selected";
			} elseif ( $wds_semester == "Winter" ) {
				$Winter = "selected";
			}

			$return .= '<option value="Spring" ' . $Spring . '>Spring';
			$return .= '<option value="Summer" ' . $Summer . '>Summer';
			$return .= '<option value="Fall" ' . $Fall . '>Fall';
			$return .= '<option value="Winter" ' . $Winter . '>Winter';
			$return .= '</select></td>';
			$return .= '</tr>';

			$return .= '<tr class="additional-field year-field">';
			$return .= '<td class="additional-field-label">Year:';
			$return .= '<td><input type="text" name="wds_year" value="' . $wds_year . '"></td>';
			$return .= '</tr>';

			$return .= '<tr class="additional-field additional-description-field">';
			$return .= '<td colspan="2" class="additional-field-label">Additional Description/HTML:</td></tr>';
			$return .= '<tr><td colspan="2"><textarea name="wds_course_html" id="additional-desc-html">' . $wds_course_html . '</textarea></td></tr>';
			$return.= '</tr>';
		}
	} elseif ( $group_type == "project" ) {

	} elseif ( $group_type == "club" ) {

	} else {
		$return = "Please select a Group Type.";
	}

	$return.= '</table>';

	if ( $group_type == "course" || 'portfolio' == $group_type ) {
		$return.= '<script>wds_load_group_departments();</script>';
	}
	if ( $echo ) {
		return $return;
	} else {
		$return = str_replace( "'","\'", $return );
		die( "document.getElementById( 'wds-group-type' ).innerHTML='$return'" );
	}
}

/**
 * Are School and Department required for this group type?
 */
function openlab_is_school_required_for_group_type( $group_type = '' ) {
	$req_types = array( 'course', 'portfolio' );

	return in_array( $group_type, $req_types );
}

/**
 * School and Department are required for courses and portfolios
 *
 * Hook in before BP's core function, so we get first dibs on returning errors
 */
function openlab_require_school_and_department_for_groups() {
	global $bp;

	// Only check at group creation and group admin
	if ( !bp_is_group_admin_page() && !bp_is_group_create() ) {
		return;
	}

	// Don't check at deletion time ( groan )
	if ( bp_is_group_admin_page( 'delete-group' ) ) {
		return;
	}

	// No payload, no check
	if ( empty( $_POST ) ) {
		return;
	}

	if ( bp_is_group_create() ) {
		$group_type = isset( $_GET['type'] ) ? $_GET['type'] : '';
		$redirect = bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/';
	} else {
		$group_type = openlab_get_current_group_type();
		$redirect = bp_get_group_permalink( groups_get_current_group() ) . 'admin/edit-details/';
	}

	$account_type = xprofile_get_field_data( 'Account Type', bp_loggedin_user_id() );
	if ( openlab_is_school_required_for_group_type( $group_type ) && bp_is_action_variable( 'group-details', 1 ) && 'staff' != strtolower( $account_type ) ) {

		if ( empty( $_POST['wds_group_school'] ) || empty( $_POST['wds_departments'] ) ) {
			bp_core_add_message( 'You must provide a school and department.', 'error' );
			bp_core_redirect( $redirect );
		}

	}

}
add_action( 'bp_actions', 'openlab_require_school_and_department_for_groups', 5 );


//Save Group Meta
add_action( 'groups_group_after_save', 'wds_bp_group_meta_save' );
function wds_bp_group_meta_save( $group ) {
	global $wpdb, $user_ID, $bp;

	if ( isset( $_POST['group_type'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_group_type', $_POST['group_type'] );

		if ( 'course' == $_POST['group_type'] ) {
			$is_course = true;
		}
	}

	if ( isset( $_POST['wds_faculty'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_faculty', $_POST['wds_faculty'] );
	}
	if ( isset( $_POST['wds_group_school'] ) ) {
		$wds_group_school = implode( ",", $_POST['wds_group_school'] );
		groups_update_groupmeta( $group->id, 'wds_group_school', $wds_group_school );
	}
	if ( isset( $_POST['wds_departments'] ) ) {
		$wds_departments = implode( ",", $_POST['wds_departments'] );
		groups_update_groupmeta( $group->id, 'wds_departments', $wds_departments );
	}
	if ( isset( $_POST['wds_course_code'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_course_code', $_POST['wds_course_code'] );
	}
	if ( isset( $_POST['wds_section_code'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_section_code', $_POST['wds_section_code'] );
	}
	if ( isset( $_POST['wds_semester'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_semester', $_POST['wds_semester'] );
	}
	if ( isset( $_POST['wds_year'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_year', $_POST['wds_year'] );
	}
	if ( isset( $_POST['wds_course_html'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_course_html', $_POST['wds_course_html'] );
	}
	if ( isset( $_POST['group_project_type'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_group_project_type', $_POST['group_project_type'] );
	}

	// Site association. Non-courses have the option of not having associated sites ( thus the
	// wds_website_check value ).
	if ( isset( $_POST['wds_website_check'] ) ||
	     openlab_is_course( $group->id ) ||
	     !empty( $is_course ) ||
	     openlab_is_portfolio( $group->id )
	) {

		if ( isset( $_POST['new_or_old'] ) && 'new' == $_POST['new_or_old'] ) {

			// Create a new site
			ra_copy_blog_page( $group->id );

		} elseif ( isset( $_POST['new_or_old'] ) && 'old' == $_POST['new_or_old'] && isset( $_POST['groupblog-blogid'] ) ) {

			// Associate an existing site
			groups_update_groupmeta( $group->id, 'wds_bp_group_site_id', (int) $_POST['groupblog-blogid'] );

		} elseif ( isset( $_POST['new_or_old'] ) && 'external' == $_POST['new_or_old'] && isset( $_POST['external-site-url'] ) ) {

			// External site

			// Some validation
			$url = openlab_validate_url( $_POST['external-site-url'] );
			groups_update_groupmeta( $group->id, 'external_site_url', $url );

			if ( !empty( $_POST['external-site-type'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_type', $_POST['external-site-type'] );
			}

			if ( !empty( $_POST['external-posts-url'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_posts_feed', $_POST['external-posts-url'] );
			}

			if ( !empty( $_POST['external-comments-url'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_comments_feed', $_POST['external-comments-url'] );
			}
		}

		if ( openlab_is_portfolio( $group->id ) ) {
			openlab_associate_portfolio_group_with_user( $group->id, bp_loggedin_user_id() );
		}
	}

	// Site privacy
	if ( isset( $_POST['blog_public'] ) ) {
		$blog_public = (float) $_POST['blog_public'];
		$site_id = openlab_get_site_id_by_group_id( $group->id );

		if ( $site_id ) {
			update_blog_option( $site_id, 'blog_public', $blog_public );
		}
	}

	// Portfolio list display
	if ( isset( $_POST['group-portfolio-list-heading'] ) ) {
		$enabled = ! empty( $_POST['group-show-portfolio-list'] ) ? 'yes' : 'no';
		groups_update_groupmeta( $group->id, 'portfolio_list_enabled', $enabled );

		groups_update_groupmeta( $group->id, 'portfolio_list_heading', strip_tags( stripslashes( $_POST['group-portfolio-list-heading'] ) ) );
	}

	// Feed URLs ( step two of group creation )
	if ( isset( $_POST['external-site-posts-feed'] ) || isset( $_POST['external-site-comments-feed'] ) ) {
		groups_update_groupmeta( $group->id, 'external_site_posts_feed', $_POST['external-site-posts-feed'] );
		groups_update_groupmeta( $group->id, 'external_site_comments_feed', $_POST['external-site-comments-feed'] );
	}
}

function wds_get_by_meta( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true, $meta_key = null, $meta_value = null ) {
	global $wpdb, $bp;

	if ( $limit && $page )
		$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit ), intval( $limit ) );
	else
		$pag_sql = '';

	if ( !is_user_logged_in() || ( !is_super_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
		$hidden_sql = " AND g.status != 'hidden'";
	else
		$hidden_sql = '';

	if ( $search_terms ) {
		$search_terms = like_escape( $wpdb->escape( $search_terms ) );
		$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
	} else {
		$search_sql = '';
	}

	if ( $user_id ) {
		$user_id = $wpdb->escape( $user_id );
		$paged_groups = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name_members} m, {$bp->groups->table_name} g WHERE gm3.meta_key='$meta_key' AND gm3.meta_value='$meta_value' AND g.id = m.group_id AND g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY g.name ASC {$pag_sql}" );
		$total_groups = $wpdb->get_var( "SELECT COUNT( DISTINCT m.group_id ) FROM {$bp->groups->table_name_members} m LEFT JOIN {$bp->groups->table_name_groupmeta} gm ON m.group_id = gm.group_id INNER JOIN {$bp->groups->table_name} g ON m.group_id = g.id WHERE gm.meta_key = 'last_activity' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
	} else {
		$paged_groups = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE gm3.meta_key='$meta_key' AND gm3.meta_value='$meta_value' AND g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY g.name ASC {$pag_sql}" );
		$total_groups = $wpdb->get_var( "SELECT COUNT( DISTINCT g.id ) FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE gm3.meta_key='$meta_key' AND gm3.meta_value='$meta_value' AND g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql}" );
	}
//echo $total_groups;
	if ( !empty( $populate_extras ) ) {
		foreach ( (array) $paged_groups as $group ) $group_ids[] = $group->id;
		$group_ids = $wpdb->escape( join( ',', (array) $group_ids ) );
		$paged_groups = BP_Groups_Group::get_group_extras( $paged_groups, $group_ids, 'newest' );
	}

	return array( 'groups' => $paged_groups, 'total' => $total_groups );
}

//Copy the group blog template
function ra_copy_blog_page( $group_id ) {
	global $bp, $wpdb, $current_site, $user_email, $base, $user_ID;
	$blog = isset( $_POST['blog'] ) ? $_POST['blog'] : array();

	if ( !empty( $blog['domain'] ) && $group_id ) {
	  $wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
	  if ( !defined( 'SUNRISE' ) || $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->dmtable}'" ) != $wpdb->dmtable ) {
		  $join = $where = '';
	  } else {
		  $join = "LEFT JOIN {$wpdb->dmtable} d ON d.blog_id = b.blog_id ";
		  $where = "AND d.domain IS NULL ";
	  }

	  $src_id = intval( $_POST['source_blog'] );

	  //$domain = sanitize_user( str_replace( '/', '', $blog[ 'domain' ] ) );
	  //$domain = str_replace( ".","", $domain );
	  $domain = friendly_url( $blog[ 'domain' ] );
	  $email = sanitize_email( $user_email );
	  $title = $_POST['group-name'];

	  if ( !$src_id ) {
		  $msg = __( 'Select a source blog.' );
	  } elseif ( empty( $domain ) || empty( $email ) ) {
		  $msg = __( 'Missing blog address or email address.' );
	  } elseif ( !is_email( $email ) ) {
		  $msg = __( 'Invalid email address' );
	  } else {
		  if ( constant( 'VHOST' ) == 'yes' ) {
			  $newdomain = $domain.".".$current_site->domain;
			  $path = $base;
		  } else {
			  $newdomain = $current_site->domain;
			  $path = $base.$domain.'/';
		  }

		  $password = 'N/A';
		  $user_id = email_exists( $email );
		  if ( !$user_id ) {
			  $password = generate_random_password();
			  $user_id = wpmu_create_user( $domain, $password, $email );
			  if ( false == $user_id ) {
				  $msg = __( 'There was an error creating the user' );
			  } else {
				  wp_new_user_notification( $user_id, $password );
			  }
		  }
		  $wpdb->hide_errors();
		  $new_id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( "public" => 1 ), $current_site->id );
		  $id = $new_id;
		  $wpdb->show_errors();
		  if ( !is_wp_error( $id ) ) { //if it dont already exists then move over everything

			  $current_user = get_userdata( bp_loggedin_user_id() );

			  groups_update_groupmeta( $group_id, 'wds_bp_group_site_id', $id );
			  /*if ( get_user_option( $user_id, 'primary_blog' ) == 1 )
				  update_user_option( $user_id, 'primary_blog', $id, true );*/
			  $content_mail = sprintf( __( "New site created by %1s\n\nAddress: http://%2s\nName: %3s" ), $current_user->user_login , $newdomain.$path, stripslashes( $title ) );
			  wp_mail( get_site_option( 'admin_email' ),  sprintf( __( '[%s] New Blog Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
			  wpmu_welcome_notification( $id, $user_id, $password, $title, array( "public" => 1 ) );
			  $msg = __( 'Site Created' );
			  // now copy
			  $blogtables = $wpdb->base_prefix . $src_id . "_";
			  $newtables = $wpdb->base_prefix . $new_id . "_";
			  $query = "SHOW TABLES LIKE '{$blogtables}%'";
  //				var_dump( $query );
			  $tables = $wpdb->get_results( $query, ARRAY_A );
			  if ( $tables ) {
				  reset( $tables );
				  $create = array();
				  $data = array();
				  $len = strlen( $blogtables );
				  $create_col = 'Create Table';
				  // add std wp tables to this array
				  $wptables = array( $blogtables . 'links', $blogtables . 'postmeta', $blogtables . 'posts',
					  $blogtables . 'terms', $blogtables . 'term_taxonomy', $blogtables . 'term_relationships' );
				  for ( $i = 0;$i < count( $tables );$i++ ) {
					  $table = current( $tables[$i] );
					  if ( substr( $table,0, $len ) == $blogtables ) {
						  if ( !( $table == $blogtables . 'options' || $table == $blogtables . 'comments' ) ) {
							  $create[$table] = $wpdb->get_row( "SHOW CREATE TABLE {$table}" );
							  $data[$table] = $wpdb->get_results( "SELECT * FROM {$table}", ARRAY_A );
						  }
					  }
				  }
  //					var_dump( $create );
				  if ( $data ) {
					  switch_to_blog( $src_id );
					  $src_url = get_option( 'siteurl' );
					  $option_query = "SELECT option_name, option_value FROM {$wpdb->options}";
					  restore_current_blog();
					  $new_url = get_blog_option( $new_id, 'siteurl' );
					  foreach ( $data as $k => $v ) {
						  $table = str_replace( $blogtables, $newtables, $k );
						  if ( in_array( $k, $wptables ) ) { // drop new blog table
							  $query = "DROP TABLE IF EXISTS {$table}";
							  $wpdb->query( $query );
						  }
						  $key = (array) $create[$k];
						  $query = str_replace( $blogtables, $newtables, $key[$create_col] );
						  $wpdb->query( $query );
						  $is_post = ( $k == $blogtables . 'posts' );
						  if ( $v ) {
							  foreach ( $v as $row ) {
								  if ( $is_post ) {
									  $row['guid'] = str_replace( $src_url, $new_url, $row['guid'] );
									  $row['post_content'] = str_replace( $src_url, $new_url, $row['post_content'] );
									  $row['post_author'] = $user_id;
								  }
								  $wpdb->insert( $table, $row );
							  }
						  }
					  }
					  // copy media
					  $cp_base = ABSPATH . '/' . UPLOADBLOGSDIR . '/';
					  $cp_cmd = 'cp -r ' . $cp_base . $src_id . ' ' . $cp_base . $new_id;
					  exec( $cp_cmd );
					  // update options
					  $skip_options = array( 'admin_email','blogname','blogdescription','cron','db_version','doing_cron',
						  'fileupload_url','home','new_admin_email','nonce_salt','random_seed','rewrite_rules','secret','siteurl','upload_path',
						  'upload_url_path', "{$wpdb->base_prefix}{$src_id}_user_roles" );
					  $options = $wpdb->get_results( $option_query );
					  //new blog stuff
					  if ( $options ) {
						  switch_to_blog( $new_id );
						  update_option( "wds_bp_group_id", $group_id );
						  foreach ( $options as $o ) {
  //								var_dump( $o );
							  if ( !in_array( $o->option_name, $skip_options ) && substr( $o->option_name,0,6 ) != '_trans' ) {
								  update_option( $o->option_name, maybe_unserialize( $o->option_value ) );
							  }
						  }
						  if ( version_compare( $GLOBALS['wp_version'], '2.8', '>' ) ) {
							  set_transient( 'rewrite_rules', '' );
						  } else {
							  update_option( 'rewrite_rules', '' );
						  }

						  restore_current_blog();
						  $msg = __( 'Blog Copied' );
					  }
				  }
			  }
		  } else {
			  $msg = $id->get_error_message();
		  }
	  }
	}
}

//this is a function for sanitizing the website name
//source http://cubiq.org/the-perfect-php-clean-url-generator
function friendly_url( $str, $replace = array(), $delimiter = '-' ) {
	if ( !empty( $replace ) ) {
		$str = str_replace( (array) $replace, ' ', $str );
	}

	if ( function_exists( 'iconv' ) ) {
		$clean = iconv( 'UTF-8', 'ASCII//TRANSLIT', $str );
	} else {
		$clean = $str;
	}

	$clean = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $clean );
	$clean = strtolower( trim( $clean, '-' ) );
	$clean = preg_replace( "/[\/_|+ -]+/", $delimiter, $clean );

	return $clean;
}

/**
 * Don't let anyone access the Create A Site page
 *
 * @see http://openlab.citytech.cuny.edu/redmine/issues/160
 */
function openlab_redirect_from_site_creation() {
	if ( bp_is_create_blog() ) {
		bp_core_redirect( bp_get_root_domain() );
	}
}
add_action( 'bp_actions', 'openlab_redirect_from_site_creation' );

/**
 * Load custom language file for BP Group Documents
 */
load_textdomain( 'bp-group-documents', WP_CONTENT_DIR . '/languages/buddypress-group-documents-en_CAC.mo' );

/**
 * Allow super admins to change user type on Dashboard
 */
class OpenLab_Change_User_Type {
	function init() {
		static $instance;

		if ( !is_super_admin() ) {
			return;
		}

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Change_User_Type;
		}
	}

	function __construct() {
		add_action( 'show_user_profile', array( $this, 'markup' ) );
		add_action( 'edit_user_profile', array( $this, 'markup' ) );

		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
	}

	function markup( $user ) {
		$account_type = xprofile_get_field_data( 'Account Type', $user->ID );

		$field_id = xprofile_get_field_id_from_name( 'Account Type' );
		$field = new BP_XProfile_Field( $field_id );
		$options = $field->get_children();

		?>

		<h3>OpenLab Account Type</h3>

		<table class="form-table">
			<tr>
				<th>
					<label for="openlab_account_type">Account Type</label>
				</th>

				<td>
					<?php foreach ( $options as $option ) : ?>
						<input type="radio" name="openlab_account_type" value="<?php echo $option->name ?>" <?php checked( $account_type, $option->name ) ?>> <?php echo $option->name ?><br />
					<?php endforeach ?>
				</td>
			</tr>
		</table>

		<?php
	}

	function save( $user_id ) {
		if ( isset( $_POST['openlab_account_type'] ) ) {
			xprofile_set_field_data( 'Account Type', $user_id, $_POST['openlab_account_type'] );
		}
	}

}
add_action( 'admin_init', array( 'OpenLab_Change_User_Type', 'init' ) );

/**
 * Only allow the site's faculty admin to see full names on the Dashboard
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/165
 */
function openlab_hide_fn_ln( $check, $object, $meta_key, $single ) {
	global $wpdb, $bp;

	if ( is_admin() && in_array( $meta_key, array( 'first_name', 'last_name' ) ) ) {

		// Faculty only
		$account_type = xprofile_get_field_data( 'Account Type', get_current_user_id() );
		if ( 'faculty' != strtolower( $account_type ) ) {
			return '';
		}

		// Make sure it's the right faculty member
		$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = '%d' LIMIT 1", get_current_blog_id() ) );

		if ( !empty( $group_id ) && !groups_is_user_admin( get_current_user_id(), (int) $group_id ) ) {
			return '';
		}

		// Make sure it's a course
		$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

		if ( 'course' != strtolower( $group_type ) ) {
			return '';
		}
	}

	return $check;
}
//add_filter( 'get_user_metadata', 'openlab_hide_fn_ln', 9999, 4 );

/**
 * No access redirects should happen from wp-login.php
 */
add_filter( 'bp_no_access_mode', create_function( '', 'return 2;' ) );

/**
 * Don't auto-link items in profiles
 * Hooked to bp_screens so that it gets fired late enough
 */
add_action( 'bp_screens', create_function( '', "remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );" ) );


//Change "Group" to something else
class buddypress_Translation_Mangler {
 /*
  * Filter the translation string before it is displayed.
  *
  * This function will choke if we try to load it when not viewing a group page or in a group loop
  * So we bail in cases where neither of those things is present, by checking $groups_template
  */
 static function filter_gettext( $translation, $text, $domain ) {
   global $bp, $groups_template;

   if ( 'buddypress' != $domain ) {
	   return $translation;
   }

   $group_id = 0;
   if ( !bp_is_group_create() ) {
	   if ( !empty( $groups_template->group->id ) ) {
		$group_id = $groups_template->group->id;
	   } else if ( !empty( $bp->groups->current_group->id ) ) {
		$group_id = $bp->groups->current_group->id;
	   }
   }

   if ( $group_id ) {
	$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
   } else if ( isset( $_GET['type'] ) ) {
   	$grouptype = $_GET['type'];
   } else {
   	return $translation;
   }

   $uc_grouptype = ucfirst( $grouptype );
   $translations = get_translations_for_domain( 'buddypress' );
   switch( $text ) {
	case "Forum":
		return $translations->translate( "Discussion" );
		break;
	case "Group Forum":
		return $translations->translate( "$uc_grouptype Discussion" );
		break;
	case "Group Forum Directory":
		return $translations->translate( "" );
		break;
	case "Group Forums Directory":
		return $translations->translate( "Group Discussions Directory" );
		break;
	case "Join Group":
		return $translations->translate( "Join Now!" );
		break;
	case "You successfully joined the group.":
		return $translations->translate( "You successfully joined!" );
		break;
	case "Recent Discussion":
		return $translations->translate( "Recent Forum Discussion" );
		break;
	case "said ":
		return $translations->translate( "" );
		break;
	case "Create a Group":
		return $translations->translate( "Create a " . $uc_grouptype );
		break;
  }
  return $translation;
 }
}

function openlab_launch_translator() {
	add_filter( 'gettext', array( 'buddypress_Translation_Mangler', 'filter_gettext' ), 10, 4 );
}
add_action( 'bp_setup_globals', 'openlab_launch_translator' );

class buddypress_ajax_Translation_Mangler {
 /*
  * Filter the translation string before it is displayed.
  */
 static function filter_gettext( $translation, $text, $domain ) {
   $translations = get_translations_for_domain( 'buddypress' );
   switch( $text ) {
	case "Friendship Requested":
	case "Add Friend":
		return $translations->translate( "Friend" );
		break;
  }
  return $translation;
 }
}
function openlab_launch_ajax_translator() {
	add_filter( 'gettext', array( 'buddypress_ajax_Translation_Mangler', 'filter_gettext' ), 10, 4 );
}
add_action( 'bp_setup_globals', 'openlab_launch_ajax_translator' );
/**
 * When a user attempts to visit a blog, check to see if the user is a member of the
 * blog's associated group. If so, ensure that the member has access.
 *
 * This function should be deprecated when a more elegant solution is found.
 * See http://openlab.citytech.cuny.edu/redmine/issues/317 for more discussion.
 */
function openlab_sync_blog_members_to_group() {
	global $wpdb, $bp;

	// No need to continue if the user is not logged in, if this is not an admin page, or if
	// the current blog is not private
	$blog_public = get_option( 'blog_public' );
	if ( !is_user_logged_in() || !is_admin() || (int) $blog_public < 0 ) {
		return;
	}

	$user_id = get_current_user_id();
	$userdata = get_userdata( $user_id );

	// Is the user already a member of the blog?
	if ( empty( $userdata->caps ) ) {

		// Is this blog associated with a group?
		$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

		if ( $group_id ) {

			// Is this user a member of the group?
			if ( groups_is_user_member( $user_id, $group_id ) ) {

				// Figure out the status
				if ( groups_is_user_admin( $user_id, $group_id ) ) {
					$status = 'administrator';
				} else if ( groups_is_user_mod( $user_id, $group_id ) ) {
					$status = 'editor';
				} else {
					$status = 'author';
				}

				// Add the user to the blog
				add_user_to_blog( get_current_blog_id(), $user_id, $status );

				// Redirect to avoid errors
				echo '<script type="text/javascript">window.location="' . $_SERVER['REQUEST_URI'] . '";</script>';
			}
		}
	}
}
//add_action( 'init', 'openlab_sync_blog_members_to_group', 999 ); // make sure BP is loaded

/**
 * Interfere in the comment posting process to allow for duplicates on the same post
 *
 * Borrowed from http://www.strangerstudios.com/blog/2010/10/duplicate-comment-detected-it-looks-as-though-you%E2%80%99ve-already-said-that/
 * See http://openlab.citytech.cuny.edu/redmine/issues/351
 */
function openlab_enable_duplicate_comments_preprocess_comment( $comment_data ) {
	if ( is_user_logged_in() ) {
		//add some random content to comment to keep dupe checker from finding it
		$random = md5( time() );
		$comment_data['comment_content'] .= "disabledupes{" . $random . "}disabledupes";
	}

	return $comment_data;
}
add_filter( 'preprocess_comment', 'openlab_enable_duplicate_comments_preprocess_comment' );

/**
 * Strips disabledupes string from comments. See previous function.
 */
function openlab_enable_duplicate_comments_comment_post( $comment_id ) {
	global $wpdb;

	if ( is_user_logged_in() ) {

		//remove the random content
		$comment_content = $wpdb->get_var( "SELECT comment_content FROM $wpdb->comments WHERE comment_ID = '$comment_id' LIMIT 1" );
		$comment_content = preg_replace( "/disabledupes\{.*\}disabledupes/", "", $comment_content );
		$wpdb->query( "UPDATE $wpdb->comments SET comment_content = '" . $wpdb->escape( $comment_content ) . "' WHERE comment_ID = '$comment_id' LIMIT 1" );
	}
}
add_action( 'comment_post', 'openlab_enable_duplicate_comments_comment_post', 1 );

/**
 * Adds the URL of the user profile to the New User Registration admin emails
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/334
 */
function openlab_newuser_notify_siteadmin( $message ) {

	// Due to WP lameness, we have to hack around to get the username
	preg_match( "|New User: ( .* )|", $message, $matches );

	if ( !empty( $matches ) ) {
		$user = get_user_by( 'login', $matches[1] );
		$profile_url = bp_core_get_user_domain( $user->ID );

		if ( $profile_url ) {
			$message_a = explode( 'Remote IP', $message );
			$message = $message_a[0] . 'Profile URL: ' . $profile_url . "\n" . 'Remote IP' . $message_a[1];
		}
	}

	return $message;
}
add_filter( 'newuser_notify_siteadmin', 'openlab_newuser_notify_siteadmin' );

/**
 * Get the word for a group type
 *
 * Groups fall into three categories: Project, Club, and Course. Use this function to get the word
 * corresponding to the group type, with the appropriate case and count.
 *
 * @param $case 'lower' ( course ), 'title' ( Course ), 'upper' ( COURSE )
 * @param $count 'single' ( course ), 'plural' ( courses )
 * @param $group_id Will default to the current group id
 */
function openlab_group_type( $case = 'lower', $count = 'single', $group_id = 0 ) {
	if ( !$case || !in_array( $case, array( 'lower', 'title', 'upper' ) ) ) {
		$case = 'lower';
	}

	if ( !$count || !in_array( $count, array( 'single', 'plural' ) ) ) {
		$case = 'single';
	}

	// Set a group id. The elseif statements allow for cascading logic; if the first is not
	// found, fall to the second, etc.
	$group_id = (int) $group_id;
	if      ( !$group_id && $group_id = bp_get_current_group_id() ) {} // current group
	else if ( !$group_id && $group_id = bp_get_new_group_id() ) {}     // new group
	else if ( !$group_id && $group_id = bp_get_group_id() ) {}         // group in loop

	$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

	if ( empty( $group_type ) ) {
		return '';
	}

	switch ( $case ) {
		case 'lower' :
			$group_type = strtolower( $group_type );
			break;

		case 'title' :
			$group_type = ucwords( $group_type );
			break;

		case 'upper' :
			$group_type = strtoupper( $group_type );
			break;
	}

	switch ( $count ) {
		case 'single' :
			break;

		case 'plural' :
			$group_type .= 's';
			break;
	}

	return $group_type;
}

/**
 * Utility function for getting a default user id when none has been passed to the function
 *
 * The logic is this: If there is a displayed user, return it. If not, check to see whether we're
 * in a members loop; if so, return the current member. If it's still 0, check to see whether
 * we're on a my-* page; if so, return the loggedin user id. Otherwise, return 0.
 *
 * Note that we have to manually check the $members_template variable, because
 * bp_get_member_user_id() doesn't do it properly.
 *
 * @return int
 */
function openlab_fallback_user() {
	global $members_template;

	$user_id = bp_displayed_user_id();

	if ( !$user_id && !empty( $members_template ) && isset( $members_template->member ) ) {
		$user_id = bp_get_member_user_id();
	}

	if ( !$user_id && ( is_page( 'my-courses' ) || is_page( 'my-clubs' ) || is_page( 'my-projects' ) || is_page( 'my-sites' ) ) ) {
		$user_id = bp_loggedin_user_id();
	}

	return (int) $user_id;
}

/**
 * Utility function for getting a default group id when none has been passed to the function
 *
 * The logic is this: If this is a group page, return the current group id. If this is the group
 * creation process, return the new_group_id. If this is a group loop, return the id of the group
 * show during this iteration
 *
 * @return int
 */
function openlab_fallback_group() {
	global $groups_template;

	$group_id = bp_get_current_group_id();

	if ( !$group_id && bp_is_group_create() ) {
		$group_id = bp_get_new_group_id();
	}

	if ( !$group_id && !empty( $groups_template ) && isset( $groups_template->group ) ) {
		$group_id = $groups_template->group->id;
	}

	return (int) $group_id;
}

/**
 * Is this my profile?
 *
 * We need a specialized function that returns true when bp_is_my_profile() does, or in addition,
 * when on a my-* page
 *
 * @return bool
 */
function openlab_is_my_profile() {
	global $bp;

	if ( !is_user_logged_in() ) {
		return false;
	}

	if ( bp_is_my_profile() ) {
		return true;
	}

	if ( is_page( 'my-courses' ) || is_page( 'my-clubs' ) || is_page( 'my-projects' ) || is_page( 'my-sites' )  ) {
		return true;
	}

	//for the group creating pages
	if ( $bp->current_action == "create" )
	{
		return true;
	}

	return false;
}

/**
 * On saving settings, save our additional fields
 */
function openlab_addl_settings_fields() {
	global $bp;

	$fname = isset( $_POST['fname'] ) ? $_POST['fname'] : '';
	$lname = isset( $_POST['lname'] ) ? $_POST['lname'] : '';

	// Don't let this continue if a password error was recorded
	if ( isset( $bp->template_message_type ) && 'error' == $bp->template_message_type && 'No changes were made to your account.' != $bp->template_message ) {
		return;
	}

	if ( empty( $fname ) || empty( $lname ) ) {
		bp_core_add_message( 'First Name and Last Name are required fields', 'error' );
	} else {
		xprofile_set_field_data( 'First Name', bp_displayed_user_id(), $fname );
		xprofile_set_field_data( 'Last Name', bp_displayed_user_id(), $lname );

		bp_core_add_message( __( 'Your settings have been saved.', 'buddypress' ), 'success' );
	}

	bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bp_get_settings_slug() . '/general' ) );
}
add_action( 'bp_core_general_settings_after_save', 'openlab_addl_settings_fields' );

/**
 * A small hack to ensure that the 'Create A New Site' option is disabled on my-sites.php
 */
function openlab_disable_new_site_link( $registration ) {
	if ( '/wp-admin/my-sites.php' == $_SERVER['SCRIPT_NAME'] ) {
		$registration = 'none';
	}

	return $registration;
}
add_filter( 'site_option_registration', 'openlab_disable_new_site_link' );

/**
 * Default subscription level for group emails should be All
 */
function openlab_default_group_subscription( $level ) {
	if ( ! $level ) {
		$level = 'supersub';
	}

	return $level;
}

add_filter( 'ass_default_subscription_level', 'openlab_default_group_subscription' );

function openlab_set_default_group_subscription_on_creation( $group_id ) {
	groups_update_groupmeta( $group_id, 'ass_default_subscription', 'supersub' );
}
add_action( 'groups_created_group', 'openlab_set_default_group_subscription_on_creation' );

/**
 * Brackets in password reset emails cause problems in some clients. Remove them
 */
function openlab_strip_brackets_from_pw_reset_email( $message ) {
	$message = preg_replace( '/<( http\S*? )>/', '$1', $message );
		return $message;
}
add_filter( 'retrieve_password_message', 'openlab_strip_brackets_from_pw_reset_email' );

/**
 * Don't allow non-super-admins to Add New Users on user-new.php
 *
 * This is a hack. user-new.php shows the Add New User section for any user
 * who has the 'create_users' cap. For some reason, Administrators have the
 * 'create_users' cap even on Multisite. Instead of doing a total removal
 * of this cap for Administrators ( which may break something ), I'm just
 * removing it on the user-new.php page.
 *
 */
function openlab_block_add_new_user( $allcaps, $cap, $args ) {
		if ( !in_array( 'create_users', $cap ) ) {
				return $allcaps;
		}

		if ( !is_admin() || false === strpos( $_SERVER['SCRIPT_NAME'], 'user-new.php' ) ) {
				return $allcaps;
		}

		if ( is_super_admin() ) {
               return $allcaps;
		}

		unset( $allcaps['create_users'] );

		return $allcaps;
}
add_filter( 'user_has_cap', 'openlab_block_add_new_user', 10, 3 );

/**
 * Hack alert! Allow group avatars to be deleted
 *
 * There is a bug in BuddyPress Docs that blocks group avatar deletion, because
 * BP Docs is too greedy about setting its current view, and thinks that you're
 * trying to delete a Doc instead. Instead of fixing that, which I have no
 * patience for at the moment, I'm just going to override BP Docs's current
 * view in the case of deleting an avatar.
 */
function openlab_fix_avatar_delete( $view ) {
	if ( bp_is_group_admin_page() ) {
		$view = '';
	}

	return $view;
}
add_filter( 'bp_docs_get_current_view', 'openlab_fix_avatar_delete', 9999 );

/**
 * Remove user from group blog when leaving group
 *
 * NOTE: This function should live in includes/group-blogs.php, but can't
 * because of AJAX load order
 */
function openlab_remove_user_from_groupblog( $group_id, $user_id ) {
	$blog_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );

	if ( $blog_id ) {
		remove_user_from_blog( $user_id, $blog_id );
	}
}
add_action( 'groups_leave_group', 'openlab_remove_user_from_groupblog', 10, 2 );

/**
 * Don't let Awesome Flickr plugin load colorbox if WP AJAX Edit Comments is active
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/363
 */
function openlab_fix_colorbox_conflict_1() {
	if ( ! function_exists( 'enqueue_afg_scripts' ) ) {
		return;
	}

	$is_wp_ajax_edit_comments_active = in_array( 'wp-ajax-edit-comments/wp-ajax-edit-comments.php', (array) get_option( 'active_plugins', array() ) );

	remove_action( 'wp_print_scripts', 'enqueue_afg_scripts' );

	if ( ! get_option( 'afg_disable_slideshow' ) ) {
		if ( get_option( 'afg_slideshow_option' ) == 'highslide' ) {
		    wp_enqueue_script( 'afg_highslide_js', BASE_URL . "/highslide/highslide-full.min.js" );
		}

		if ( get_option( 'afg_slideshow_option' ) == 'colorbox' && ! $is_wp_ajax_edit_comments_active ) {
		    wp_enqueue_script( 'jquery' );
		    wp_enqueue_script( 'afg_colorbox_script', BASE_URL . "/colorbox/jquery.colorbox-min.js" , array( 'jquery' ) );
		    wp_enqueue_script( 'afg_colorbox_js', BASE_URL . "/colorbox/mycolorbox.js" , array( 'jquery' ) );
		}
	}
}
add_action( 'wp_print_scripts', 'openlab_fix_colorbox_conflict_1', 1 );

/**
 * Prevent More Privacy Options from displaying its -2 message, and replace with our own
 *
 * See #775
 */
function openlab_swap_private_blog_message() {
	global $current_blog, $ds_more_privacy_options;

	if ( '-2' == $current_blog->public ) {
		remove_action( 'template_redirect', array( &$ds_more_privacy_options, 'ds_members_authenticator' ) );
		add_action( 'template_redirect', 'openlab_private_blog_message', 1 );
	}
}
add_action( 'wp', 'openlab_swap_private_blog_message' );

/**
 * Callback for our own "members only" blog message
 *
 * See #775
 */
function openlab_private_blog_message() {
	global $ds_more_privacy_options;

	$blog_id   = get_current_blog_id();
	$group_id  = openlab_get_group_id_by_blog_id( $blog_id );
	$group_url = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id, ) ) );
	$user_id   = get_current_user_id();

	if ( is_user_member_of_blog( $user_id, $blog_id ) || is_super_admin() ) {
		return;
	} else if ( is_user_logged_in() ) {
		openlab_ds_login_header(); ?>
		<form name="loginform" id="loginform" />
			<p>To become a member of this site, please request membership on <a href="<?php echo esc_attr( $group_url ) ?>">the profile page</a>.</p>
		</form>
		</div>
	</body>
</html>
	<?php
		exit();
	} else {
		if ( is_feed() ) {
			$ds_more_privacy_options->ds_feed_login();
		} else {
			nocache_headers();
			header( 'HTTP/1.1 302 Moved Temporarily' );
			header( 'Location: ' . wp_login_url() );
			header( 'Status: 302 Moved Temporarily' );
			exit();
		}
	}
}

/**
 * A version of the More Privacy Options login header without the redirect
 *
 * @see openlab_private_blog_message()
 */
function openlab_ds_login_header() {
	global $error, $is_iphone, $interim_login, $current_site;
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<title><?php _e( "Private Blog Message" ); ?></title>
				<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php
		wp_admin_css( 'login', true );
		wp_admin_css( 'colors-fresh', true );

	if ( $is_iphone ) { ?>
	<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
	<style type="text/css" media="screen">
	form { margin-left: 0px; }
	#login { margin-top: 20px; }
	</style>
<?php
	} elseif ( isset( $interim_login ) && $interim_login ) { ?>
	<style type="text/css" media="all">
	.login #login { margin: 20px auto; }
	</style>
<?php
	}

	do_action( 'login_head' ); ?>
</head>
			<body class="login">
				<div id="login">
					<h1><a href="<?php echo apply_filters( 'login_headerurl', 'http://' . $current_site->domain . $current_site->path ); ?>" title="<?php echo apply_filters( 'login_headertitle', $current_site->site_name ); ?>"><span class="hide"><?php bloginfo( 'name' ); ?></span></a></h1>
	<?php
	}

/**
 * Course member portfolio list widget
 *
 * This function is here ( rather than includes/portfolios.php ) because it needs
 * to run at 'widgets_init'.
 *
 * @todo Make sure it doesn't show up for non-courses. This can only be done
 * after BP is set up.
 */
class OpenLab_Course_Portfolios_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'openlab_course_portfolios_widget',
			'Portfolio List',
			array(
				'description' => 'Display a list of the Portfolios belonging to the members of this course.',
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'];

		$name_key = 'display_name' === $instance['sort_by'] ? 'user_display_name' : 'portfolio_title';
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$portfolios = openlab_get_group_member_portfolios( $group_id, $instance['sort_by'] );

		if ( '1' === $instance['display_as_dropdown'] ) {
			echo '<form action="" method="get">';
			echo '<select class="portfolio-goto" name="portfolio-goto">';
			echo '<option value="" selected="selected">Choose a Portfolio</option>';
			foreach ( $portfolios as $portfolio ) {
				echo '<option value="' . esc_attr( $portfolio['portfolio_url'] ) . '">' . esc_attr( $portfolio[ $name_key ] ) . '</option>';
			}
			echo '</select>';
			echo '<input class="openlab-portfolio-list-widget-submit" style="margin-top: .5em" type="submit" value="Go" />';
			wp_nonce_field( 'portfolio_goto', '_pnonce' );
			echo '</form>';
		} else {
			echo '<ul class="openlab-portfolio-links">';
			foreach ( $portfolios as $portfolio ) {
				echo '<li><a href="' . esc_url( $portfolio['portfolio_url'] ) . '">' . esc_html( $portfolio[ $name_key ] ) . '</a></li>';
			}
			echo '</ul>';
		}

		// Some lousy inline CSS
		?>
		<style type="text/css">
			.openlab-portfolio-list-widget-submit {
				margin-top: .5em;
			}
			body.js .openlab-portfolio-list-widget-submit {
				display: none;
			}
		</style>

		<?php

		echo $args['after_widget'];

		$this->enqueue_scripts();
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['display_as_dropdown'] = ! empty( $new_instance['display_as_dropdown'] ) ? '1' : '';
		$instance['sort_by'] = in_array( $new_instance['sort_by'], array( 'random', 'display_name', 'title' ) ) ? $new_instance['sort_by'] : 'display_name';
		$instance['num_links'] = isset( $new_instance['num_links'] ) ? (int) $new_instance['num_links'] : '';
		return $instance;
	}

	public function form( $instance ) {
		$settings = wp_parse_args( $instance, array(
			'title' => 'Member Portfolios',
			'display_as_dropdown' => '0',
			'sort_by' => 'title',
			'num_links' => false,
		) );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ) ?>">Title:</label><br />
			<input name="<?php echo $this->get_field_name( 'title' ) ?>" id="<?php echo $this->get_field_name( 'title' ) ?>" value="<?php echo esc_attr( $settings['title'] ) ?>" />
		</p>

		<p>
			<input name="<?php echo $this->get_field_name( 'display_as_dropdown' ) ?>" id="<?php echo $this->get_field_name( 'display_as_dropdown' ) ?>" value="1" <?php checked( $settings['display_as_dropdown'], '1' ) ?> type="checkbox" />
			<label for="<?php echo $this->get_field_id( 'display_as_dropdown' ) ?>">Display as dropdown</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'sort_by' ) ?>">Sort by:</label><br />
			<select name="<?php echo $this->get_field_name( 'sort_by' ) ?>" id="<?php echo $this->get_field_name( 'sort_by' ) ?>">
				<option value="title" <?php selected( $settings['sort_by'], 'title' ) ?>>Portfolio title</option>
				<option value="display_name" <?php selected( $settings['sort_by'], 'display_name' ) ?>>Member name</option>
				<option value="random" <?php selected( $settings['sort_by'], 'random' ) ?>>Random</option>
			</select>
		</p>

		<?php
	}

	protected function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );

		// poor man's dependency - jquery will be loaded by now
		add_action( 'wp_footer', array( $this, 'script' ), 1000 );
	}

	public function script() {
		?>
		<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			$( '.portfolio-goto' ).on( 'change', function() {
				var maybe_url = this.value;
				if ( maybe_url ) {
					document.location.href = maybe_url;
				}
			} );
		},( jQuery ) );
		</script>
		<?php
	}
}

/**
 * Register the Course Portfolios widget
 */
function openlab_register_portfolios_widget() {
	register_widget( 'OpenLab_Course_Portfolios_Widget' );
}
add_action( 'widgets_init', 'openlab_register_portfolios_widget' );

/**
 * Utility function for getting the xprofile exclude groups for a given account type
 */
function openlab_get_exclude_groups_for_account_type( $type ) {
	global $wpdb, $bp;
	$groups = $wpdb->get_results( "SELECT id, name FROM {$bp->profile->table_name_groups}" );

	// Reindex
	$gs = array();
	foreach ( $groups as $group ) {
		$gs[ $group->name ] = $group->id;
	}

	$exclude_groups = array();
	foreach ( $gs as $gname => $gid ) {
		// special case for alumni
		if ( 'Alumni' === $type && 'Student' === $gname ) {
			continue;
		}

		// otherwise, non-matches are excluded
		if ( $gname !== $type ) {
			$exclude_groups[] = $gid;
		}
	}

	return implode( ',', $exclude_groups );
}
