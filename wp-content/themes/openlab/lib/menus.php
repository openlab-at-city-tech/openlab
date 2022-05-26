<?php
/* menu functions - current includes
  -register_nav_menus for custom menu locations
  -help pages menu - adding categories
  -profile pages sub menus
 */

//custom menu locations for OpenLab
register_nav_menus(array(
    'main' => __('Main Menu', 'cuny'),
    'aboutmenu' => __('About Menu', 'cuny'),
    'helpmenu' => __('Help Menu', 'cuny'),
    'helpmenusec' => __('Help Menu Secondary', 'cuny')
));

/**
 * Using @wp_nav_menu_objects for fine-grained menu customizations
 * @global type $post
 * @param type $items
 * @param type $args
 * @return type
 */
function openlab_wp_menu_customizations($items, $args) {
    global $post;

    if (false !== strpos($args->theme_location, 'about')) {

        $calendar_page_obj = get_page_by_path('about/calendar');
        $upcoming_page_obj = get_page_by_path('about/calendar/upcoming');

        //default order is at the end of the current set of items
        $order = count($items);
        $new_items = array();

        //add a mobile verison of the OpenLab Calendar menu item
        //first iterate through the current menu items and figure out where this new mobile menu item will go
        foreach ($items as $key => $item) {

            if (false === strpos($item->url, bp_get_root_domain())) {
                $items[$key]->classes[] = 'external-link';
            }

            if ($item->title === 'OpenLab Calendar') {

                $items[$key]->classes[] = 'hidden-xs';

                if ($post->post_parent === $calendar_page_obj->ID || $post->post_type === 'event') {
                    $items[$key]->classes[] = 'current-menu-item';
                }

                $order = $item->menu_order + 1;
            }

            if ($item->menu_order >= $order) {
                $items[$key]->menu_order = $item->menu_order + 1;
                $new_items[$key + 1] = $item;
            } else {
                $new_items[$key] = $item;
            }
        }

        //then we create the menu item and inject it into the menu items array
        $new_menu_item = openlab_custom_nav_menu_item('OpenLab Calendar', get_permalink($upcoming_page_obj->ID), $order, 0, array('visible-xs'));

        $new_items[$order] = $new_menu_item;
        ksort($new_items);
        $items = $new_items;
    }

    return $items;
}

add_filter('wp_nav_menu_objects', 'openlab_wp_menu_customizations', 11, 2);

/**
 * Reach into the item nav menu and remove stuff as necessary
 *
 * Hooked to bp_screens at 1 because apparently BP is broken??
 */
function openlab_modify_options_nav() {
    if (bp_is_group() && openlab_is_portfolio() && !bp_is_group_create()) {
        buddypress()->groups->nav->edit_nav(array(
            'name' => 'Profile',
                ), 'home', bp_get_current_group_slug());

        buddypress()->groups->nav->edit_nav(array(
            'name' => 'Settings',
                ), 'admin', bp_get_current_group_slug());

        // Keep the following tabs as-is
        $keepers = array( 'home', 'admin', 'members', 'forum', 'docs', 'files' );
        $nav_items = buddypress()->groups->nav->get_secondary(array('parent_slug' => bp_get_current_group_slug()));
        foreach ($nav_items as $nav_item) {
            if (!in_array($nav_item->slug, $keepers)) {
                buddypress()->groups->nav->delete_nav($nav_item->slug, bp_get_current_group_slug());
            }
        }
    }

    if (bp_is_group() && !bp_is_group_create()) {
        buddypress()->groups->nav->edit_nav(array(
            'position' => 15,
                ), 'admin', bp_get_current_group_slug());
    }

    if (bp_is_group() && !bp_is_group_create()) {
        $nav_items = buddypress()->groups->nav->get_secondary(array('parent_slug' => bp_get_current_group_slug()));
        foreach ($nav_items as $nav_item) {

            if ($nav_item->slug === 'events') {

                $new_option_args = array(
                    'name' => $nav_item->name,
                    'slug' => $nav_item->slug . '-mobile',
                    'parent_slug' => $nav_item->parent_slug,
                    'parent_url' => trailingslashit(bp_get_group_permalink(groups_get_current_group())),
                    'link' => trailingslashit($nav_item->link) . 'upcoming/',
                    'position' => intval($nav_item->position) + 1,
                    'item_css_id' => $nav_item->css_id . '-mobile',
                    'screen_function' => $nav_item->screen_function,
                    'user_has_access' => $nav_item->user_has_access,
                    'no_access_url' => $nav_item->no_access_url,
                );

                $status = bp_core_create_subnav_link($new_option_args, 'groups');
            }
        }
    }
}

add_action('bp_screens', 'openlab_modify_options_nav', 1);

/**
 * Help Sidebar menu: includes categories and sub-categories.
 *
 * @global type $post
 * @param string $items
 * @param type $args
 * @return string
 */
function openlab_help_categories_menu($items, $args) {
    global $post;

    if ($args->theme_location == 'helpmenu') {
        $term = get_query_var('term');
        $parent_term = get_term_by('slug', $term, 'help_category');
        $current_term = false;

        if ($parent_term == false) {
            $child_terms = get_the_terms($post->ID, 'help_category');
            $term = array();

            if (!empty($child_terms)) {
                foreach ($child_terms as $child_term) {
                    $term[] = $child_term;
                }

                $parent_term = get_term_by('id', $term[0]->parent, 'help_category');
                $current_term = get_term_by('id', $term[0]->term_id, 'help_category');
            }
        }

        //for child term archive pages
        if ($parent_term !== false && $parent_term->parent != 0) {
            $current_term = $parent_term;
            $parent_term = get_term_by('id', $current_term->parent, 'help_category');
        }

        $help_args = array(
            'hide_empty' => false,
            'orderby' => 'term_order',
            'hide_empty' => false
        );
        $help_cats = get_terms('help_category', $help_args);

        // for post level identifying of current menu item
        $post_cats_array = array();

        if ($post->post_type == 'help') {
            $post_cats = get_the_terms($post->id, 'help_category');

            if ($post_cats) {
                foreach ($post_cats as $post_cat) {
                    // no children cats in menu
                    if ($post_cat->parent == 0) {
                        $post_cats_array[] = $post_cat->term_id;
                    }
                }
            }
        }

        $help_cat_list = "";
        foreach ($help_cats as $help_cat) {
            // eliminate children cats from the menu list
            if ($help_cat->parent == 0) {
                $help_classes = "help-cat menu-item";

                $highlight_active_state = get_query_var('taxonomy') != 'help_tags' && empty($_GET['help-search']);

                // see if this is the current menu item; if not, this could be a post,
                // so we'll check against an array of cat ids for this post
                if ($highlight_active_state) {
                    if ($parent_term !== false && $help_cat->term_id == $parent_term->term_id) {
                        $help_classes .= " current-menu-item";
                    } else if ($post->post_type == 'help') {
                        if (in_array($help_cat->term_id, $post_cats_array)) {
                            $help_classes .= " current-menu-item";
                        }
                    }
                }

                // a special case just for the glossary page
                if ($help_cat->name == "Help Glossary") {
                    $help_cat->name = "Glossary";
                }

                $help_cat_list .= '<li class="' . $help_classes . '"><a href="' . get_term_link($help_cat) . '">' . $help_cat->name . '</a>';

                // check for child terms
                $child_cat_check = get_term_children($help_cat->term_id, 'help_category');

                // list child terms, if any
                if (count($child_cat_check) > 0) {
                    $help_cat_list .= '<ul>';

                    $child_args = array(
                        'hide_empty' => false,
                        'orderby' => 'term_order',
                        'hide_empty' => false,
                        'parent' => $help_cat->term_id
                    );
                    $child_cats = get_terms('help_category', $child_args);
                    foreach ($child_cats as $child_cat) {

                        $child_classes = "help-cat menu-item";
                        if ($highlight_active_state) {
                            if ($current_term !== false && $child_cat->term_id == $current_term->term_id) {
                                $child_classes .= " current-menu-item";
                            } else if ($post->post_type == 'help') {
                                if (in_array($child_cat->term_id, $post_cats_array)) {
                                    $child_classes .= " current-menu-item";
                                }
                            }
                        }

                        $help_cat_list .= '<li class="' . $child_classes . '"><a href="' . get_term_link($child_cat) . '">' . $child_cat->name . '</a></li>';
                    }

                    $help_cat_list .= '</ul>';
                }

                $help_cat_list .= '</li>';
            }
        }

        $items = $items . $help_cat_list;
    }

    return $items;
}

add_filter('wp_nav_menu_items', 'openlab_help_categories_menu', 10, 2);

/**
 * For a single help post: get the primary term for that post
 * @global type $post
 * @return type
 */
function openlab_get_primary_help_term_name() {
    global $post;
    $child_terms = get_the_terms($post->ID, 'help_category');
    $term = array();
    foreach ($child_terms as $child_term) {
        $term[] = $child_term;
    }

    $current_term = get_term_by('id', $term[0]->term_id, 'help_category');
    return $current_term;
}

/**
 * Getting all of the submenu wrapper markup in one place
 * @param type $type
 * @param type $opt_var
 * @return string
 */
function openlab_submenu_markup($type = '', $opt_var = NULL, $row_wrapper = true) {
    $submenu_text = '';

    $width = 'col-md-24';

    switch ($type) {
        case 'invitations':
            $submenu_text = 'My Invitations<span aria-hidden="true">:</span> ';
            $menu = openlab_my_invitations_submenu();
            break;
        case 'friends':
            $friends_menu = openlab_my_friends_submenu(false);

            $menu = $friends_menu['menu'] ?? '';
            $submenu_text = $friends_menu['submenu_text'] ?? '';

            $width = 'col-sm-24 has-menu-items is-mol-menu';

            break;
        case 'messages':
            $submenu_text = 'My Messages<span aria-hidden="true">:</span> ';
            $menu = openlab_my_messages_submenu();
            break;

        case 'my-activity':
            $submenu_text = 'My Activity<span aria-hidden="true">:</span> ';
            $menu = openlab_my_activity_submenu();
            break;

        case 'groups':
            $group_menu = openlab_my_groups_submenu($opt_var);
            $menu = $group_menu['menu'];
            $submenu_text = $group_menu['submenu_text'];

            $width = 'col-sm-19 is-mol-menu';

            if ($menu !== '') {
                $width .= ' has-menu-items group-item';
            }

            break;
        default:
            $submenu_text = 'My Settings<span aria-hidden="true">:</span> ';
            $menu = openlab_profile_settings_submenu();
    }

    $extras = openlab_get_submenu_extras();

    $submenu = '<div class="' . $width . '">';
    $submenu .= '<div class="submenu"><div class="submenu-text pull-left bold"><h2>' . $submenu_text . '</h2></div>' . $extras . $menu . '</div>';
    $submenu .= '</div>';

    if ($row_wrapper) {
        $submenu = '<div class="row">' . $submenu . '</div>';
    }

    return $submenu;
}

/**
 * Extra items that need to be on the same line as the submenu
 */
function openlab_get_submenu_extras() {
    global $bp;
    $extras = '';

    if ($bp->current_action == 'my-friends') :
        if (bp_has_members(bp_ajax_querystring('members'))) :
            $count = '<div class="pull-left">' . bp_get_members_pagination_count() . '</div>';

            $extras = <<<HTML
            <div class="pull-right">
                <div class="clearfix">
                    {$count}
                </div>
            </div>
HTML;

        endif;
    endif;

    return $extras;
}

//sub-menus for profile pages - a series of functions, but all here in one place
//sub-menu for profile pages
function openlab_profile_settings_submenu() {
    global $bp;

    if (!$dud = bp_displayed_user_domain()) {
        $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
    }

    $settings_slug = $dud . bp_get_settings_slug();
    $menu_list = array(
        $dud . 'profile/edit' => 'Edit Profile',
        $dud . 'profile/change-avatar' => 'Change Avatar',
        $settings_slug => 'Account Settings',
        $dud . 'settings/notifications' => 'Email Notifications',
        $dud . 'settings/delete-account' => 'Delete Account',
    );
    return openlab_submenu_gen($menu_list, true);
}

//sub-menus for my-<groups> pages
function openlab_my_groups_submenu($group) {
    global $bp;
    $menu_out = array();
    $menu_list = array();
    $step_name = '';

    $group_link = $bp->root_domain . '/my-' . $group . 's/';
    $create_link = bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?type=' . $group . '&new=true';
    $no_link = 'no-link';

    $span_start = '<span class="bold">';
    $span_end = '</span>';

    //get account type to see if they're faculty
    $member_type = openlab_get_user_member_type( get_current_user_id() );

    $submenu_text = 'My ' . ucfirst($group) . 's';

    //if the current user is faculty or a super admin, they can create a course, otherwise no dice
	$can_create = true;
	if ( 'course' === $group ) {
        // determines if there are any courses - if not, only show "create"
        $filters['wds_group_type'] = openlab_page_slug_to_grouptype();

        if ( is_super_admin( get_current_user_id() ) || 'faculty' === $account_type ) {
			$can_create = true;
		} else {
			$can_create = false;
		}
	}

	if ( $can_create ) {
		$menu_list = array(
			$create_link => 'Create / Clone a ' . ucfirst( $group ),
		);
	}

    $menu_out['menu'] = openlab_submenu_gen($menu_list);
    $menu_out['submenu_text'] = $submenu_text;

    return $menu_out;
}

function openlab_create_group_menu($grouptype) {
    global $bp;

    //get group step
    $current_step = isset($bp->groups->current_create_step) ? $bp->groups->current_create_step : '';
    $step_name = '';

    switch ($current_step) {
        case 'group-details':
            $step_name = 'Step One: Create Profile & Site';
            break;
        case 'group-settings':
            $step_name = 'Step Two: Privacy & Member Role Settings';
            break;
        case 'group-avatar':
            $step_name = 'Step Three: Avatar';
            break;
        case 'invite-anyone' :
            $step_name = 'Step Four: Invite Members';
            break;
    }

    $menu_mup = <<<HTML
            <div class="submenu create-group-submenu">
                <ul class="nav nav-inline">
                    <li class="submenu-item item-create-clone-a-course current-menu-item bold">{$step_name}</li>
                </ul>
            </div>
HTML;

    return $menu_mup;
}

//sub-menus for my-friends pages
function openlab_my_friends_submenu($count = true) {
    global $bp;
    $menu_out = array();

    if (!$dud = bp_displayed_user_domain()) {
        $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
    }
    $request_ids = friends_get_friendship_request_user_ids(bp_loggedin_user_id());
    $request_count = intval(count((array) $request_ids));

    $my_friends = $dud . 'friends/';
    $friend_requests = $dud . 'friends/requests/';

    $action = $bp->current_action;
    $item = $bp->current_item;
    $component = $bp->current_component;

    $count_span = '';
    if ($count) {
        $count_span = openlab_get_menu_count_mup($count);
    }


    if ($bp->is_item_admin) {
        $menu_list = array(
            $friend_requests => 'Requests Received ' . $count_span,
                //'#' => $page_identify,
        );
    } else {
        return '';
    }

    $submenu_class = 'no-deco';

    if ($action !== 'my-friends') {
        $submenu_class = 'display-as-menu-item';
    }

    $menu_out['menu'] = openlab_submenu_gen($menu_list);
    $menu_out['submenu_text'] = '<a class="' . $submenu_class . '" href="' . $my_friends . '">My Friends</a>';

    return $menu_out;
}

//sub-menus for my-messages pages
function openlab_my_messages_submenu() {
    global $bp;
    if (!$dud = bp_displayed_user_domain()) {
        $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
    }

    $menu_list = array(
        $dud . 'messages/inbox/' => 'Inbox',
        $dud . 'messages/sentbox/' => 'Sent',
        $dud . 'messages/compose' => 'Compose',
    );
    return openlab_submenu_gen($menu_list);
}

function openlab_my_activity_submenu() {
	$base_url = bp_loggedin_user_domain() . 'my-activity';

	$current_item = $base_url;
	if ( ! empty( $_GET['type'] ) && in_array( $_GET['type'], [ 'mine', 'favorites', 'mentions', 'pins' ], true ) ) {
		$current_item .= '?type=' . $_GET['type'];

	}

	$menu_list = [
		$base_url                     => 'All',
		$base_url . '?type=mine'      => 'Mine',
		$base_url . '?type=favorites' => 'Favorites',
		$base_url . '?type=mentions'  => '@Mentions',
		$base_url . '?type=pins'      => 'Pins',
	];

	return openlab_submenu_gen( $menu_list, false, $current_item  );
}

//sub-menus for my-invites pages
function openlab_my_invitations_submenu() {
    global $bp;
    if (!$dud = bp_displayed_user_domain()) {
        $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
    }

    $menu_list = array(
        $dud . 'groups/invites/' => 'Invitations Received',
        $dud . 'invite-anyone/' => 'Invite New Members',
        $dud . 'invite-anyone/sent-invites/' => 'Sent Invitations',
    );
    return openlab_submenu_gen($menu_list);
}

function openlab_submenu_gen( $items, $timestamp = false, $current_item = null ) {
    global $bp, $post;

    if (empty($items)) {
        return '';
    }

    //get $items length so we know how many menu items there are ( for tagging the "last-item" class )
    $item_count = count($items);

    //determining if this is the current page or not - checks to see if this is an action page first; if not, checks the component of the page
    $action = $bp->current_action;
    $component = $bp->current_component;
    $page_slug = $post->post_name;

    if ($action) {
        $page_identify = $action;
    } else if ($component) {
        $page_identify = $component;
    } else if ($page_slug) {
        $page_identify = $page_slug;
    }

    //counter
    $i = 1;

    $submenu_classes = 'nav nav-inline';

    $submenu = '<ul class="' . $submenu_classes . '"><!--';

    foreach ($items as $item => $title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $slug);
        //class variable for each item
        $item_classes = "submenu-item item-" . $slug;

        //now search the slug for this item to see if the page identifier is there - if it is, this is the current page
        $current_check = false;

		if ( $current_item ) {
			$current_check = $current_item === $item;
		} elseif ( $page_identify ) {
			$current_check = strpos($item, $page_identify);
		}

        //special case for send invitations page hitting the same time as invitations received
        if ($page_identify == "invites" && $title == "Sent Invitations") {
            $current_check = false;
        }

        //adding the current-menu-item class - also includes special cases, parsed out to make them easier to identify
        if ($current_check !== false) {
            $item_classes .= " current-menu-item";
        } else if ($page_identify == "general" && $title == "Account Settings") {
            //special case just for account settings page
            $item_classes .= " current-menu-item";
        } else if ($page_identify == "my-friends" && $title == "My Friends") {
            //special case just for my friends page
            $item_classes .= " current-menu-item bold";
        } else if ($page_identify == "invite-new-members" && $title == "Invite New Members") {
            //special case just for Invite New Members page
            $item_classes .= " current-menu-item";
        } else if ($page_identify == 'my-groups') {
            //special case for my-<groups> pages
            if (isset($_GET['type'])) {
                $type = $_GET['type'];
				$type_title = '';
				if ( in_array( $type, openlab_group_types(), 1 ) ) {
					$type_title = esc_html( 'My ' . ucfirst( str_replace( '-', ' ', $type ) ) . 's' );
				}

                if ( $title == $type_title ) {
                    $item_classes .= " current-menu-item";
                }
            }
        }

        //checks to see if this is the last item or first item
        if ($item_count == $i) {
            $item_classes .= " last-item";
        } else if ($i == 1) {
            $item_classes .= " first-item";
        }

        //this is just to make styling the "delete" and "create" buttons easier
        //also added a class for the "no-link" submenu items that indicate the step in group creation
        if (strpos($item_classes, "delete")) {
            $item_classes .= " delete-button";
        } else if (strpos($item_classes, "create")) {
            $item_classes .= " create-button";
        } else if ($item == 'no-link') {
            $item_classes .= " no-link";
        }

        $submenu .= '--><li class="' . $item_classes . '">';

        //for delete
        $submenu .= (strstr($slug, 'delete-') > -1 ? '<span class="fa fa-minus-circle"></span>' : '');
        $submenu .= (strstr($slug, 'create-') > -1 ? '<span class="fa fa-plus-circle"></span>' : '');

        $submenu .= ( $item == 'no-link' ? '' : '<a href="' . $item . '">' );
        $submenu .= $title;
        $submenu .= ( $item == 'no-link' ? '' : '</a>' );
        $submenu .= '</li><!--';

        //increment counter
        $i++;
    }

    if ($timestamp) {
        $submenu .= '--><li class="info-line pull-right visible-lg"><span class="timestamp info-line-timestamp">' . bp_get_last_activity(bp_displayed_user_id()) . '</span></li><!--';
    }

    $submenu .= '--></ul>';

    return $submenu;
}

/**
 * bp_get_options_nav filtering
 *
 */
//submenu nav renaming
add_filter('bp_get_options_nav_home', 'openlab_filter_subnav_home');

function openlab_filter_subnav_home($subnav_item) {
    global $bp;

    $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();
    $group_label = openlab_get_group_type_label('case=upper');
    $new_label = '<span class="inline-visible-xs">' . $group_label . '</span> Profile';

    $new_item = str_replace("Home", $new_label, $subnav_item);

    //update "current" class to "current-menu-item" to unify site identification of current menu page
    $new_item = str_replace("current selected", "current-menu-item", $new_item);

    //for mobile menu add course site and site dashboard (if available)
    $group_id = bp_get_current_group_id();

    $group_site_settings = openlab_get_group_site_settings($group_id);

    $site_link = '';

    if (!empty($group_site_settings['site_url']) && $group_site_settings['is_visible']) {
        $site_link = '<li id="site-groups-li" class="visible-xs"><a href="' . trailingslashit(esc_attr($group_site_settings['site_url'])) . '" id="site">' . $group_label . ' Site</a></li>';
        if (
            $group_site_settings['is_local'] &&
            (
                (openlab_is_portfolio() && openlab_is_my_portfolio())
                || ( !openlab_is_portfolio() && groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() ) && current_user_can_for_blog( $group_site_settings['site_id'], 'edit_posts' ) )
                || $bp->is_item_admin
                || is_super_admin()
            )
        ) {

            $site_link .= '<li id="site-admin-groups-li" class="visible-xs"><a href="' . trailingslashit(esc_attr($group_site_settings['site_url'])) . 'wp-admin/" id="site-admin">Site Dashboard</a></li>';
        }
    }

    return $new_item . $site_link;
}

add_filter('bp_get_options_nav_admin', 'openlab_filter_subnav_admin');

function openlab_filter_subnav_admin($subnav_item) {
    global $bp;
    $group_label = openlab_get_group_type_label('case=upper');
    $new_item = $subnav_item;
    //this is to stop the course settings menu item from getting a current class on membership pages
    if (bp_action_variable(0)) {
        if ($bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' || $bp->action_variables[0] == 'membership-requests') {
            $new_item = str_replace("current selected", " ", $new_item);
        } else {
            //update "current" class to "current-menu-item" to unify site identification of current menu page
            $new_item = str_replace("current selected", "current-menu-item", $new_item);
        }
    }

    return $new_item;
}

add_filter('bp_get_options_nav_members', 'openlab_filter_subnav_members');

function openlab_filter_subnav_members($subnav_item) {
    global $bp;
    global $wp_query;

    //string replace menu name
    $new_item = str_replace("Members", "Membership", $subnav_item);

    // Switch slugs based on user role.
    if ( $bp->is_item_admin ) {
        $new_item = str_replace( '/members/', '/admin/manage-members', $new_item );
	}

    $uri = $bp->unfiltered_uri;
    $check_uri = array('groups', 'notifications');
    $notification_status = false;
    if (count(array_intersect($uri, $check_uri)) == count($check_uri)) {
        $notification_status = true;
    }

    //filtering for current status on membership menu item when in membership submenu
    if ((bp_action_variable(0) && ( $bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' || $bp->current_action == 'notifications' || $bp->action_variables[0] == 'membership-requests' || $wp_query->query_vars['pagename'] == 'invite-anyone' )) || $notification_status || ( bp_is_group() && bp_is_current_action( 'invite-anyone' ) ) ) {
        $new_item = str_replace('id="members-groups-li"', 'id="members-groups-li" class="current-menu-item"', $new_item);
    } else {
        //update "current" class to "current-menu-item" to unify site identification of current menu page
        $new_item = str_replace("current selected", "current-menu-item", $new_item);
    }

    //get total member count
    $total_mem = bp_core_number_format(groups_get_groupmeta(bp_get_current_group_id(), 'total_member_count'));

    //added classes to span
    if ($total_mem > 0) {
        $new_item = str_replace('<span>' . $total_mem . '</span>', '<span class="mol-count pull-right count-' . $total_mem . ' gray">' . $total_mem . '</span>', $new_item);
    } else {
        $new_item = str_replace('<span>' . $total_mem . '</span>', '', $new_item);
    }

    return $new_item;
}

add_filter('bp_get_options_nav_nav-docs', 'openlab_filter_subnav_docs');

function openlab_filter_subnav_docs($subnav_item) {
    global $bp;

    // No Docs item if disabled.
    if ( ! openlab_is_docs_enabled_for_group( bp_get_current_group_id() ) ) {
        return '';
    }

    $group_slug = bp_get_group_slug();

	$query_builder = new BP_Docs_Query(
		[
			'group_id' => bp_get_current_group_id(),
		]
	);
	$wp_query      = $query_builder->get_wp_query();

    $total_doc_count = ! empty( $wp_query->found_posts ) ? $wp_query->found_posts : 0;

    if ( $total_doc_count > 0 ) {
        $new_item = str_replace(
			'</a></li>',
			'<span class="mol-count pull-right count-' . esc_attr( $total_doc_count ) . ' gray">' . esc_html( $total_doc_count ) . '</span></a></li>',
			$subnav_item
		);
    } else {
		$new_item = $subnav_item;
	}

    //update "current" class to "current-menu-item" to unify site identification of current menu page
    $new_item = str_replace( "current selected", "current-menu-item", $new_item );

    return $new_item;
}

/**
 * Modify the Documents subnav item in group contexts.
 */
function openlab_filter_subnav_nav_group_documents($subnav_item) {
    if ( ! openlab_is_files_enabled_for_group( bp_get_current_group_id() ) ) {
        return '';
    }

    //update "current" class to "current-menu-item" to unify site identification of current menu page
    $subnav_item = str_replace("current selected", "current-menu-item", $subnav_item);

    // Add count. @todo Better caching.
    $count = BP_Group_Documents::get_total(bp_get_current_group_id());
    if ($count) {
        $span = sprintf('<span class="mol-count pull-right count-%s gray">%s</span>', intval($count), esc_html(number_format_i18n($count)));
        $subnav_item = str_replace('</a>', ' ' . $span . '</a>', $subnav_item);
    }

	$subnav_item = str_replace( 'Documents', 'File Library', $subnav_item );
	$subnav_item = str_replace( 'Files', 'File Library', $subnav_item );

    return $subnav_item;
}

add_filter('bp_get_options_nav_group-documents', 'openlab_filter_subnav_nav_group_documents');


add_filter('bp_get_options_nav_nav-forum', 'openlab_filter_subnav_forums');

/**
 * Modify the Discussion subnav item in group contexts.
 */
function openlab_filter_subnav_forums($subnav_item) {
    // update "current" class to "current-menu-item" to unify site identification of current menu page
    $subnav_item = str_replace('current selected', 'current-menu-item', $subnav_item);
    $subnav_item = str_replace('Forum', 'Discussion', $subnav_item);

    // Add count.
    $count = 0;
    $forum_ids = bbp_get_group_forum_ids(bp_get_current_group_id());
    if ($forum_ids) {
        // bbPress function bbp_get_forum_topic_count is broken. @todo fix or cache.
        $topic_ids = get_posts(array(
            'post_type' => bbp_get_topic_post_type(),
            'post_parent' => $forum_ids[0],
            'fields' => 'ids',
            'posts_per_page' => -1,
        ));
        $count = count($topic_ids);
    }

    if ($count) {
        $span = sprintf('<span class="mol-count pull-right count-%s gray">%s</span>', intval($count), esc_html(number_format_i18n($count)));
        $subnav_item = str_replace('</a>', ' ' . $span . '</a>', $subnav_item);
    }

    return $subnav_item;
}

add_filter('bp_get_options_nav_nav-invite-anyone', 'openlab_filter_subnav_nav_invite_anyone');

function openlab_filter_subnav_nav_invite_anyone($subnav_item) {
    return "";
}

add_filter('bp_get_options_nav_nav-notifications', 'openlab_filter_subnav_nav_notifications');

function openlab_filter_subnav_nav_notifications($subnav_item) {
    return "";
}

add_filter('bp_get_options_nav_request-membership', 'openlab_filter_subnav_nav_request_membership');

function openlab_filter_subnav_nav_request_membership($subnav_item) {
    return "";
}

add_filter('bp_get_options_nav_nav-events', 'openlab_filter_subnav_nav_events');
add_filter('bp_get_options_nav_nav-events-mobile', 'openlab_filter_subnav_nav_events');

function openlab_filter_subnav_nav_events($subnav_item) {
    $subnav_item = str_replace('Events', 'Calendar', $subnav_item);

    //for some reason group events page is not registering this nav element as current
    $current = '';
    if (bp_current_action() === 'events' || bp_current_component() === 'events') {
        $current = " current-menu-item";
    }

    if (strpos($subnav_item, 'nav-events-mobile') !== false) {
        $class = "visible-xs$current";
    } else {
        $class = "hidden-xs$current";
    }

    $subnav_item = str_replace("<li", "<li class='$class'", $subnav_item);

    return $subnav_item;
}

add_filter('bp_get_options_nav_calendar', 'openlab_filter_subnav_nav_calendar');

function openlab_filter_subnav_nav_calendar($subnav_item) {
    $subnav_item = str_replace('Calendar', 'All Events', $subnav_item);

    $subnav_item = str_replace("current selected", "current-menu-item", $subnav_item);

    return $subnav_item;
}

add_filter('bp_get_options_nav_upcoming', 'openlab_filter_subnav_nav_upcoming');

function openlab_filter_subnav_nav_upcoming($subnav_item) {

    $subnav_item = str_replace("current selected", "current-menu-item", $subnav_item);

    return $subnav_item;
}

add_filter('bp_get_options_nav_new-event', 'openlab_filter_subnav_nav_new_event');

function openlab_filter_subnav_nav_new_event($subnav_item) {

    $subnav_item = str_replace("current selected", "current-menu-item", $subnav_item);

    //check the group calendar access setting to see if the current user has the right privileges
	$event_create_access = openlab_get_group_event_create_access_setting( bp_get_current_group_id() );

    if ($event_create_access === 'admin' && !bp_is_item_admin() && !bp_is_item_mod()) {
        return "";
    }

    return $subnav_item;
}

//submenu navigation re-ordering
function openlab_group_submenu_nav() {
    if (!bp_is_group() || bp_is_group_create()) {
        return;
    }

	$positions = array(
		'home'      => 10,
		'nav-forum' => 25,
		'docs'      => 60,
		'files'     => 70,
		'events'    => 80,
		'members'   => 90,
		'admin'     => 100,
	);

    foreach ($positions as $slug => $position) {
        buddypress()->groups->nav->edit_nav(array(
            'position' => $position,
                ), $slug, bp_get_current_group_slug());
    }
}

add_action('bp_screens', 'openlab_group_submenu_nav', 1);

/**
 * Markup for group admin tabs
 */
function openlab_group_admin_tabs($group = false) {
	global $bp, $groups_template;

	if ( !$group ) {
		$group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;
	}

	$current_tab = bp_action_variable(0);

	$group_type = groups_get_groupmeta($bp->groups->current_group->id, 'wds_group_type');

	// Portfolio tabs look different from other groups
	?>

	<?php if (openlab_is_portfolio()) : ?>
		<?php if ( $bp->is_item_admin ) { ?>
			<li <?php if ('edit-details' == $current_tab || empty($current_tab)) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/edit-details">Edit Profile</a></li>
		<?php } ?>

		<?php if (!(int) bp_get_option('bp-disable-avatar-uploads')) : ?>
			<li <?php if ('group-avatar' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-avatar">Change Avatar</a></li>
		<?php endif; ?>

		<li <?php if ('group-settings' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-settings">Settings</a></li>

		<?php
		$profile = openlab_get_portfolio_label(
			[
				'user_id' => bp_loggedin_user_id(),
			]
		);
		?>

		<li class="delete-button <?php if ('delete-group' == $current_tab) : ?> current-menu-item<?php endif; ?>" ><span class="fa fa-minus-circle"></span><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/delete-group">Delete <?php echo $profile; ?></a></li>

	<?php else : ?>

		<?php if ( $bp->is_item_admin ) { ?>
			<li <?php if ('edit-details' == $current_tab || empty($current_tab)) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/edit-details"><?php _e('Edit Profile', 'buddypress'); ?></a></li>
		<?php } ?>

		<?php
			if ( !$bp->is_item_admin ) {
				return false;
			}
		?>

		<li <?php if ('group-avatar' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-avatar"><?php _e('Change Avatar', 'buddypress'); ?></a></li>

		<li <?php if ('group-settings' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-settings"><?php _e('Settings', 'buddypress'); ?></a></li>

		<?php //do_action( 'groups_admin_tabs', $current_tab, $group->slug ); ?>

		<li class="clone-button <?php if ('clone-group' == $current_tab) : ?>current-menu-item<?php endif; ?>" ><span class="fa fa-plus-circle"></span><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details?type=' . $group_type . '&clone=' . bp_get_current_group_id() ?>"><?php _e('Clone ' . ucfirst($group_type), 'buddypress'); ?></a></li>

		<li class="delete-button last-item <?php if ('delete-group' == $current_tab) : ?>current-menu-item<?php endif; ?>" ><span class="fa fa-minus-circle"></span><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/delete-group"><?php _e('Delete ' . ucfirst($group_type), 'buddypress'); ?></a></li>

		<?php if ($group_type == "portfolio") : ?>
			<li class="portfolio-displayname pull-right"><span class="highlight"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></span></li>
		<?php else : ?>
			<li class="info-line pull-right"><span class="timestamp info-line-timestamp visible-lg"><span class="fa fa-undo"></span> <?php printf(__('active %s', 'buddypress'), bp_get_group_last_active()) ?></span></li>
		<?php endif; ?>

	<?php endif ?>

	<?php
}

/**
 * Markup for Member Tabs
 */
function openlab_group_membership_tabs($group = false) {
    global $bp, $groups_template;

    if (!$group)
        $group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;

    $current_tab = bp_action_variable(0);

    $group_type = groups_get_groupmeta($bp->groups->current_group->id, 'wds_group_type');
    ?>
    <!--
    <?php if ( $bp->is_item_admin ) : ?>
        --><li<?php if ($current_tab == 'manage-members') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/manage-members"><?php _e('Membership', 'buddypress'); ?></a></li><!--

        <?php if ($group->status == 'private'): ?>
            --><li<?php if ('membership-requests' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/membership-requests"><?php _e('Member Requests', 'buddypress'); ?></a></li><!--
        <?php endif; ?>
    <?php else: ?>
        --><li<?php if ($bp->current_action == 'members') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/members"><?php _e('Membership', 'buddypress'); ?></a></li><!--
    <?php endif; ?>

    <?php if (bp_group_is_member() && invite_anyone_access_test() && openlab_is_admin_truly_member()): ?>
        --><li<?php if ($bp->current_action == 'invite-anyone') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/invite-anyone"><?php _e('Invite New Members', 'buddypress'); ?></a></li><!--
    <?php endif; ?>

    <?php if ( $bp->is_item_admin ) : ?>
        --><li<?php if ('notifications' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/notifications"><?php _e('Email Members', 'buddypress'); ?></a></li><!--
    <?php endif; ?>

    <?php if (bp_group_is_member() && openlab_is_admin_truly_member()): ?>
        --><li<?php if ($bp->current_action == 'notifications') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/notifications"><?php _e('Your Email Options', 'buddypress'); ?></a></li><!--
    <?php endif; ?>
    -->
    <?php
}

function openlab_docs_tabs() {
    global $bp, $groups_template;

    $group = null;
    if (bp_is_group()) {
        $group = groups_get_current_group();
    } elseif (!empty($groups_template->group)) {
        $group = $groups_template->group;
    }
    ?>

    <li <?php echo (bp_docs_current_view() == 'list' ? 'class="current-menu-item"' : ''); ?> ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/docs/">View Docs</a></li><!--
    <?php if (groups_is_user_member(get_current_user_id(), bp_get_group_id())): ?>
        --><li <?php echo (bp_docs_current_view() == 'create' ? 'class="current-menu-item"' : ''); ?> ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/docs/create">New Doc</a></li><!--
    <?php endif; ?>
    <?php if ((bp_docs_current_view() == 'edit' || bp_docs_current_view() == 'single') && bp_docs_is_existing_doc()): ?>
        <?php $doc_obj = bp_docs_get_current_doc(); ?>
        --><li class="current-menu-item"><?php echo $doc_obj->post_title; ?></li><!--
    <?php endif; ?>
    -->
    <?php
}

function openlab_forum_tabs() {
    global $bp, $groups_template, $wp_query;
    $group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;
    // Load up bbPress once
    $bbp = bbpress();

    /** Query Resets ***************************************************** */
    // Forum data
    $forum_ids = bbp_get_group_forum_ids(bp_get_current_group_id());
    $forum_id = array_shift($forum_ids);
    $offset = 0;

    $bbp->current_forum_id = $forum_id;

    bbp_set_query_name('bbp_single_forum');

    // Get the topic
    bbp_has_topics(array(
        'name' => bp_action_variable($offset + 1),
        'posts_per_page' => 1,
        'show_stickies' => false
    ));

    // Setup the topic
    bbp_the_topic();
    ?>

    <li <?php echo (!bp_action_variable() ? 'class="current-menu-item"' : ''); ?> ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/forum/">Discussion</a></li><!--
    <?php if (bp_action_variable() == 'topic'): ?>
        --><li class="current-menu-item hyphenate"><span><?php bbp_topic_title() ?></span></li><!--
            <?php endif; ?>
    -->
    <?php
}

function openlab_is_create_group($group_type) {
    global $bp;
    $return = NULL;

    //get group step
    $current_step = isset($bp->groups->current_create_step) ? $bp->groups->current_create_step : '';

    $steps = array('group-details', 'group-settings', 'group-avatar', 'invite-anyone');

    if (openlab_get_group_type() == $group_type && in_array($current_step, $steps) && bp_current_action() == 'create') {
        $return = true;
    }

    return $return;
}

function openlab_get_group_profile_mobile_anchor_links() {
    $links = '';
    $group_id = bp_get_current_group_id();

    // Non-public groups shouldn't show this to non-members.
    $group = groups_get_current_group();
    if ('public' !== $group->status && empty($group->user_has_access)) {
        return $links;
    }

	if ( groups_get_groupmeta( $group_id, 'openlab_related_links_list_enable' ) ) {
		$related_links = openlab_get_group_related_links($group_id);
		if (!empty($related_links)) {

			$links .= '<li id="related-links-groups-li" class="visible-xs mobile-anchor-link"><a href="#group-related-links-sidebar-widget" id="related-links">Related Sites</a></li>';
		}
	}

    if (openlab_portfolio_list_enabled_for_group()) {
        $portfolio_data = openlab_get_group_member_portfolios($group_id);
        if (!empty($portfolio_data)) {
            $links .= '<li id="portfolios-groups-li" class="visible-xs mobile-anchor-link"><a href="#group-member-portfolio-sidebar-widget" id="portfolios">Portfolios</a></li>';
        }
    }

    return $links;
}

function openlab_calendar_submenu() {
    global $post;

    $links_out = array(
        array(
            'name' => 'All Events',
            'slug' => 'calendar',
            'link' => get_site_url() . '/about/calendar/',
            'class' => $post->post_name === 'calendar' ? 'current-menu-item' : ''
        ),
        array(
            'name' => 'Upcoming',
            'slug' => 'upcoming',
            'link' => get_site_url() . '/about/calendar/upcoming/',
            'class' => $post->post_name === 'upcoming' ? 'current-menu-item' : ''
        )
    );

    return $links_out;
}

/**
 * Function for dynamically injection menu items
 * @param type $title
 * @param type $url
 * @param type $order
 * @param type $parent
 * @return \stdClass
 */
function openlab_custom_nav_menu_item($title, $url, $order, $parent = 0, $classes = array()) {
    $item = new stdClass();
    $item->ID = 1000000 + $order + $parent;
    $item->db_id = $item->ID;
    $item->title = $title;
    $item->url = $url;
    $item->menu_order = $order;
    $item->menu_item_parent = $parent;
    $item->type = '';
    $item->object = '';
    $item->object_id = '';
    $item->classes = $classes;
    $item->target = '';
    $item->attr_title = '';
    $item->description = '';
    $item->xfn = '';
    $item->status = '';
    return $item;
}
