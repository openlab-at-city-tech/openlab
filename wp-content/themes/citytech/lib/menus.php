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
	$settings_slug = bp_get_settings_slug();
	$menu_list = array(
					   'profile/edit'=> 'Edit Profile',
					   'profile/change-avatar' => 'Change Avatar',
					   $settings_slug => 'Account Settings', 
					   'settings/notifications' => 'Email Notifications',
					   'settings/delete-account' => 'Delete Account',
					   );
	return openlab_submenu_gen($menu_list);
} 

function openlab_submenu_gen($items)
{
	if ( !$dud = bp_displayed_user_domain() ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}
	
	$submenu = '<ul>';
		
		foreach ($items as $item => $title)
		{
			$submenu .= '<li class="submenu-item">';
				$submenu .= '<a href="'.$dud.$item.'">';
				$submenu .= $title;
				$submenu .= '</a>';
			$submenu .= '</li>';
		}	
	$submenu .= '</ul>';
	
	return $submenu;
}

?>