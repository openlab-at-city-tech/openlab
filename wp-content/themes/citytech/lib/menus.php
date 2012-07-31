<?php /*menu functions - current includes
-register_nav_menus for custom menu locations
-help pages menu - adding categories
-profile pages sub menus
*/

//custom menu locations for OpenLab
register_nav_menus( array(
	'main' => __('Main Menu', 'cuny'),
	'aboutmenu' => __('About Menu', 'cuny'),
	'helpmenu' => __('Help Menu', 'cuny'),
	'helpmenusec' => __('Help Menu Secondary', 'cuny')
) );

//adding help categories (custom taxonomy) to menu for help page
function help_categories_menu($items, $args) {
	global $post;
    if ($args->theme_location == 'helpmenu')
	{
		$term = get_query_var('term');
		$parent_term = get_term_by( 'slug' , $term , 'help_category' );
		
		$help_args = array(
						   'hide_empty' => false,
						   'orderby' => 'id'
						   );
		$help_cats = get_terms('help_category', $help_args);
		$help_cat_list = "";
		foreach ($help_cats as $help_cat)
		{
			//eliminate children cats from the menu list
			if ($help_cat->parent == 0)
			{
			
				$help_classes = "help-cat menu-item";
				
				//see if this is the current menu item
				if ($help_cat->term_id == $parent_term->term_id)
				{
					$help_classes .= " current-menu-item";
				}
				
				$help_cat_list .=  '<li class="'.$help_classes.'"><a href="' . get_term_link($help_cat) . '">' . $help_cat->name . '</a></li>';
			}
		}
		$items = $items.$help_cat_list;
	}
    	return $items;
}
add_filter( 'wp_nav_menu_items', 'help_categories_menu', 10, 2 );

//sub-menus for profile pages - a series of functions, but all here in one place
function openlab_profile_settings_submenu()
{
	global $bp;
	
	if ( !$dud = bp_displayed_user_domain() ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}
	
	$settings_slug = bp_get_settings_slug();
	$menu_list = array(
					   $dud.'profile/edit'=> 'Edit Profile',
					   $dud.'profile/change-avatar' => 'Change Avatar',
					   $settings_slug => 'Account Settings', 
					   $dud.'settings/notifications' => 'Email Notifications',
					   $dud.'settings/delete-account' => 'Delete Account',
					   );
	return openlab_submenu_gen($menu_list);
} 

//sub-menus for my-<groups> pages
function openlab_my_groups_submenu($group)
{
	global $bp;
	$group_link = $bp->root_domain.'/my-'.$group.'s/';
	$create_link = BP_GROUPS_SLUG . '/create/step/group-details/?type='.$group.'&new=true';

	$menu_list = array(
					   $group_link => 'My '.ucfirst($group).'s',
					   $create_link => 'Create '.ucfirst($group),
					   );
	return openlab_submenu_gen($menu_list);
} 

//sub-menus for my-friends pages
function openlab_my_friends_submenu()
{
	global $bp;
	if ( !$dud = bp_displayed_user_domain() ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}
	$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
	$request_count = intval( count( (array) $request_ids ) );
	
	$my_friends = $dud.'friends/';
	$friend_requests = $dud.'friends/requests/';
	
	$action = $bp->current_action;
	$item = $bp->current_item;
	$component = $bp->current_component;

	$menu_list = array(
					   $my_friends => 'My Friends',
					   $friend_requests => 'Requests Received <span class="mol-count count-'.$request_count.'">'.$request_count.'</span>',
					   //'#' => $page_identify,
					   );
	return openlab_submenu_gen($menu_list);
} 

//sub-menus for my-messages pages
function openlab_my_messages_submenu()
{
	global $bp;
	if ( !$dud = bp_displayed_user_domain() ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$menu_list = array(
					   $dud.'messages/inbox/' => 'Inbox',
					   $dud.'messages/sentbox/' => 'Sent',
					   $dud.'messages/compose' => 'Compose',
					   );
	return openlab_submenu_gen($menu_list);
} 

//sub-menus for my-invites pages
function openlab_my_invitations_submenu()
{
	global $bp;
	if ( !$dud = bp_displayed_user_domain() ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$menu_list = array(
					   $dud.'groups/invites/' => 'Invitations Received',
					   $dud.'invite-anyone/' => 'Invite New Members',
					   $dud.'invite-anyone/sent-invites/' => 'Sent Invitations',
					   );
	return openlab_submenu_gen($menu_list);
}

function openlab_submenu_gen($items)
{
	global $bp, $post;
	
	//determining if this is the current page or not - checks to see if this is an action page first; if not, checks the component of the page
	$action = $bp->current_action;
	$component = $bp->current_component;
	$page_slug = $post->post_name;
	
	if ($action)
	{
		$page_identify = $action;
	} else if ($component) {
		$page_identify = $component;
	} else if ($page_slug) {
		$page_identify = $page_slug;
	}
	
	$submenu = '<ul>';
		
		foreach ($items as $item => $title)
		{
			$slug = strtolower($title);
			$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $slug);
			//class variable for each item
			$item_classes = "submenu-item item-".$slug;
				
				//now search the slug for this item to see if the page identifier is there - if it is, this is the current page
				$current_check = false;
				
				if ($page_identify)
				{
				$current_check = strpos($item,$page_identify);
				}
				
				if ($current_check !== false)
				{
					$item_classes .= " selected-page";
				} else if ($page_identify == "general" && $title == "Account Settings")
				{
					//special case just for account settings page
					$item_classes .= " selected-page";
				}
			
			$submenu .= '<li class="'.$item_classes.'">';
				$submenu .= '<a href="'.$item.'">';
				$submenu .= $title;
				$submenu .= '</a>';
			$submenu .= '</li>';
		}	
	$submenu .= '</ul>';
	
	return $submenu;
}

/**
 * a variation on bp_get_options_nav to match the design
 * main change here at the moment - changing "home" to "profile" - now deprecated
 *
 * @todo Clean up this godawful mess. There are filters for this stuff - bbg
 * @todo attempting to remedy - jwu
 */

add_filter('bp_get_options_nav_home','openlab_filter_subnav_home');
 
function openlab_filter_subnav_home($subnav_item)
{
	$new_item = str_replace("Home","Profile",$subnav_item);
	return $new_item;
}

add_filter('bp_get_options_nav_admin','openlab_filter_subnav_admin');

function openlab_filter_subnav_admin($subnav_item)
{
	$group_type = openlab_get_group_type( bp_get_current_group_id());
	$new_item = str_replace("Admin",ucfirst($group_type)." Settings",$subnav_item);
	return $new_item;
}

add_filter('bp_get_options_nav_members','openlab_filter_subnav_members');

function openlab_filter_subnav_members($subnav_item)
{
	$new_item = str_replace("Members","Membership",$subnav_item);
	return $new_item;
}

add_filter('bp_get_options_nav_nav-invite-anyone','openlab_filter_subnav_nav_invite_anyone');

function openlab_filter_subnav_nav_invite_anyone($suvbnav_item)
{
	return "";
}

add_filter('bp_get_options_nav_nav-notifications','openlab_filter_subnav_nav_notifications');

function openlab_filter_subnav_nav_notifications($suvbnav_item)
{
	return "";
}