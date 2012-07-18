<?php //menu functions

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