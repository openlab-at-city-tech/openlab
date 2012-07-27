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
 * main change here at the moment - changing "home" to "profile"
 *
 * @todo Clean up this godawful mess. There are filters for this stuff - bbg
 * @todo attempting to remedy - jwu
 */

add_filter('bp_get_options_nav_home','openlab_filter_home_groups');
 
function openlab_filter_home_groups($subnav_item)
{
	
	$subnav_item['name'] = "Profile";
	return $subnav_item;
	
} 
 
 
function cuny_get_options_nav_legacy() {
	global $bp;

	// If we are looking at a member profile, then the we can use the current component as an
	// index. Otherwise we need to use the component's root_slug
	$component_index = !empty( $bp->displayed_user ) ? $bp->current_component : bp_get_root_slug( $bp->current_component );

	if ( !bp_is_single_item() ) {
		if ( !isset( $bp->bp_options_nav[$component_index] ) || count( $bp->bp_options_nav[$component_index] ) < 1 ) {
			return false;
		} else {
			$the_index = $component_index;
		}
	} else {
		if ( !isset( $bp->bp_options_nav[$bp->current_item] ) || count( $bp->bp_options_nav[$bp->current_item] ) < 1 ) {
			return false;
		} else {
			$the_index = $bp->current_item;
		}
	}

	// Loop through each navigation item
	foreach ( (array)$bp->bp_options_nav[$the_index] as $subnav_item ) {
		
		if ( !$subnav_item['user_has_access'] )
			continue;

		// If the current action or an action variable matches the nav item id, then add a highlight CSS class.
		if ( $subnav_item['slug'] == $bp->current_action ) {
			$selected = ' class="current selected"';
		} else {
			$selected = '';
		}

		// List type depends on our current component
		$list_type = bp_is_group() ? 'groups' : 'personal';
		
		//name changes
		$group_type = openlab_get_group_type( bp_get_current_group_id());
		if ($subnav_item['name'] == 'Home')
		{
			$subnav_item['name'] = 'Profile';
			
		}else if ($subnav_item['name'] == 'Admin')
		{
			$subnav_item['name'] = ucfirst($group_type).' Settings';
		}

		// echo out the final list item
		$menu_output[$subnav_item['css_id']] =  apply_filters( 'bp_get_options_nav_' . $subnav_item['css_id'], '<li id="' . $subnav_item['css_id'] . '-' . $list_type . '-li" ' . $selected . '><a id="' . $subnav_item['css_id'] . '" href="' . $subnav_item['link'] . '">' . $subnav_item['name'] . '</a></li>', $subnav_item );
		echo apply_filters( 'bp_get_options_nav_' . $subnav_item['css_id'], '<li id="' . $subnav_item['css_id'] . '-' . $list_type . '-li" ' . $selected . '><a id="' . $subnav_item['css_id'] . '" href="' . $subnav_item['link'] . '">' . $subnav_item['name'] . '</a></li>', $subnav_item );
	}
	
	//re-orders menu
	foreach ($menu_output as $name => &$menu_item)
	{
		if ($name == "home")
		{
			$menu_final[0] = $menu_item;
		} else if ($name == "admin")
		{
			$menu_final[1] = $menu_item;
		} else if ($name == "members")
		{
			$menu_final[2] = $menu_item;
		} else if ($name == "forums")
		{
			$menu_final[3] = $menu_item;
		}else if ($name == "nav-docs")
		{
			$menu_final[4] = $menu_item;
		}else if ($name == "group-documents")
		{
			$menu_final[5] = $menu_item;
		}
	}
	
	ksort($menu_final);
	
	foreach ($menu_final as $final_menu_item)
	{
		echo $final_menu_item;
	}
	
}//end cuny_get_options_nav