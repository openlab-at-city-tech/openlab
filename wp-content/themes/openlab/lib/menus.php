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
 * Ensure that external links in the help menu get the external-link glyph
 */
function openlab_help_menu_external_glyph($items, $args) {
    if (false !== strpos($args->theme_location, 'about')) {
        foreach ($items as $key => $item) {
            if (false === strpos($item->url, bp_get_root_domain())) {
                $items[$key]->classes[] = 'external-link';
            }
        }
    }
    return $items;
}

add_filter('wp_nav_menu_objects', 'openlab_help_menu_external_glyph', 10, 2);

/**
 * Reach into the item nav menu and remove stuff as necessary
 *
 * Hooked to bp_screens at 1 because apparently BP is broken??
 */
function openlab_modify_options_nav() {
    global $bp;

    if (bp_is_group() && openlab_is_portfolio()) {
        // Keep the following tabs as-is
        $keepers = array('members');
        foreach ($bp->bp_options_nav[$bp->current_item] as $key => $item) {
            if ('home' == $key) {
                $bp->bp_options_nav[$bp->current_item][$key]['name'] = 'Profile';
            } else if ('admin' == $key) {
                $bp->bp_options_nav[$bp->current_item][$key]['name'] = 'Settings';
            } else if (!in_array($key, $keepers)) {
                unset($bp->bp_options_nav[$bp->current_item][$key]);
            }
        }
    }

    if (bp_is_group()) {
        $bp->bp_options_nav[bp_get_current_group_slug()]['admin']['position'] = 15;
        return;
    }
}

add_action('bp_screens', 'openlab_modify_options_nav', 1);

/**
 * Help Sidebar menu: includes categories and sub-categories
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
        if ($parent_term == false) {
            $child_terms = get_the_terms($post->ID, 'help_category');
            $term = array();
            foreach ($child_terms as $child_term) {
                $term[] = $child_term;
            }

            $parent_term = get_term_by('id', $term[0]->parent, 'help_category');
            $current_term = get_term_by('id', $term[0]->term_id, 'help_category');
        }

        //for child term archive pages
        if ($parent_term->parent != 0) {
            $current_term = $parent_term;
            $parent_term = get_term_by('id', $current_term->parent, 'help_category');
        }

        $help_args = array(
            'hide_empty' => false,
            'orderby' => 'term_order',
            'hide_empty' => false
        );
        $help_cats = get_terms('help_category', $help_args);

        //for post level identifying of current menu item

        $post_cats_array = array();

        if ($post->post_type == 'help') {
            $post_cats = get_the_terms($post->id, 'help_category');

            if ($post_cats) {
                foreach ($post_cats as $post_cat) {
                    //no children cats in menu
                    if ($post_cat->parent == 0) {
                        $post_cats_array[] = $post_cat->term_id;
                    }
                }
            }
        }

        $help_cat_list = "";
        foreach ($help_cats as $help_cat) {
            //eliminate children cats from the menu list
            if ($help_cat->parent == 0) {

                $help_classes = "help-cat menu-item";

                //see if this is the current menu item; if not, this could be a post,
                //so we'll check against an array of cat ids for this post
                if (get_query_var('taxonomy') != 'help_tags') {
                    if ($help_cat->term_id == $parent_term->term_id) {
                        $help_classes .= " current-menu-item";
                    } else if ($post->post_type == 'help') {
                        if (in_array($help_cat->term_id, $post_cats_array)) {
                            $help_classes .= " current-menu-item";
                        }
                    }
                }

                //a special case just for the glossary page
                if ($help_cat->name == "Help Glossary") {
                    $help_cat->name = "Glossary";
                }

                $help_cat_list .= '<li class="' . $help_classes . '"><a href="' . get_term_link($help_cat) . '">' . $help_cat->name . '</a>';

                //check for child terms
                $child_cat_check = get_term_children($help_cat->term_id, 'help_category');

                //list child terms, if any
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
                        if (get_query_var('taxonomy') != 'help_tags') {
                            if ($child_cat->term_id == $current_term->term_id) {
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
function openlab_submenu_markup($type = '', $opt_var = NULL) {
    $submenu_text = '';

    $width = 'col-md-24';
    $row_wrapper = true;

    switch ($type) {
        case 'invitations':
            $submenu_text = 'My Invitations: ';
            $menu = openlab_my_invitations_submenu();
            break;
        case 'friends':
            $menu = openlab_my_friends_submenu(false);
            break;
        case 'messages':
            $submenu_text = 'My Messages: ';
            $menu = openlab_my_messages_submenu();
            break;
        case 'groups':
            $menu = openlab_my_groups_submenu($opt_var);
            $width = 'col-sm-19';
            $row_wrapper = false;
            break;
        default:
            $submenu_text = 'My Settings: ';
            $menu = openlab_profile_settings_submenu();
    }

    $submenu = '<div class="' . $width . '">';
    $submenu .= '<div class="submenu"><div class="submenu-text pull-left bold">' . $submenu_text . '</div>' . $menu . '</div>';
    $submenu .= '</div>';

    if ($row_wrapper) {
        $submenu = '<div class="row">' . $submenu . '</div>';
    }

    return $submenu;
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
    return openlab_submenu_gen($menu_list);
}

//sub-menus for my-<groups> pages
function openlab_my_groups_submenu($group) {
    global $bp;
    $group_link = $bp->root_domain . '/my-' . $group . 's/';
    $create_link = bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?type=' . $group . '&new=true';
    $no_link = 'no-link';

    //get account type to see if they're faculty
    $faculty = xprofile_get_field_data('Account Type', get_current_user_id());

    //get group step
    $current_step = isset($bp->groups->current_create_step) ? $bp->groups->current_create_step : '';
    $step_name = '';

    switch ($current_step) {
        case 'group-details':
            $step_name = 'Step One: Profile';
            break;
        case 'group-settings':
            $step_name = 'Step Two: Privacy Settings';
            break;
        case 'group-avatar':
            $step_name = 'Step Three: Avatar';
            break;
        case 'invite-anyone' :
            $step_name = 'Step Four: Invite Members';
            break;
    }

    //if the current user is faculty or a super admin, they can create a course, otherwise no dice
    if ($group == "course") {

        //determines if there are any courses - if not, only show "create"
        $filters['wds_group_type'] = openlab_page_slug_to_grouptype();

        $course_text = 'Create / Clone a ';

        if (is_super_admin(get_current_user_id()) || $faculty == "Faculty") {
            //have to add extra conditional in here for submenus on editing pages
            if ($step_name == '') {
                $menu_list = array(
                    $group_link => 'My ' . ucfirst($group) . 's',
                    $create_link => $course_text . ucfirst($group),
                );
            } else {
                $menu_list = array(
                    $group_link => 'My ' . ucfirst($group) . 's',
                    $create_link => $course_text . ucfirst($group),
                    $no_link => $step_name,
                );
            }
        } else {
            $menu_list = array(
                $group_link => 'My ' . ucfirst($group) . 's',
            );
        }
    } else {
        //have to add extra conditional in here for submenus on editing pages
        if ($step_name == '') {
            $menu_list = array(
                $group_link => 'My ' . ucfirst($group) . 's',
                $create_link => 'Create a ' . ucfirst($group),
            );
        } else {
            $menu_list = array(
                $group_link => 'My ' . ucfirst($group) . 's',
                $create_link => 'Create a ' . ucfirst($group),
                $no_link => $step_name,
            );
        }
    }

    return openlab_submenu_gen($menu_list);
}

//sub-menus for my-friends pages
function openlab_my_friends_submenu($count = true) {
    global $bp;
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
        $count_span = '<span class="mol-count pull-right count-' . $request_count . '">' . $request_count . '</span>';
    }

    $menu_list = array(
        $my_friends => 'My Friends',
        $friend_requests => 'Requests Received ' . $count_span,
            //'#' => $page_identify,
    );
    return openlab_submenu_gen($menu_list);
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

function openlab_submenu_gen($items) {
    global $bp, $post;

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

    $submenu = '<ul class="nav nav-inline">';

    foreach ($items as $item => $title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $slug);
        //class variable for each item
        $item_classes = "submenu-item item-" . $slug;

        //now search the slug for this item to see if the page identifier is there - if it is, this is the current page
        $current_check = false;

        if ($page_identify) {
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
            $item_classes .= " current-menu-item";
        } else if ($page_identify == "invite-new-members" && $title == "Invite New Members") {
            //special case just for Invite New Members page
            $item_classes .= " current-menu-item";
        } else if ($page_identify == 'my-groups') {
            //special case for my-<groups> pages
            if (isset($_GET['type'])) {
                $type = $_GET['type'];
                $type_title = 'My ' . ucfirst(str_replace('-', ' ', $type)) . 's';
                if ($title == $type_title) {
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

        $submenu .= '<li class="' . $item_classes . '">';

        //for delete
        $submenu .= (strstr($slug, 'delete-') > -1 ? '<span class="fa fa-minus-circle"></span>' : '');
        $submenu .= (strstr($slug, 'create-') > -1 ? '<span class="fa fa-plus-circle"></span>' : '');

        $submenu .= ( $item == 'no-link' ? '' : '<a href="' . $item . '">' );
        $submenu .= $title;
        $submenu .= ( $item == 'no-link' ? '' : '</a>' );
        $submenu .= '</li>';

        //increment counter
        $i++;
    }
    $submenu .= '</ul>';

    return $submenu;
}

/**
 * bp_get_options_nav filtering
 *
 */
//submenu nav renaming
add_filter('bp_get_options_nav_home', 'openlab_filter_subnav_home');

function openlab_filter_subnav_home($subnav_item) {
    $new_item = str_replace("Home", "Profile", $subnav_item);

    //update "current" class to "current-menu-item" to unify site identification of current menu page
    $new_item = str_replace("current selected", "current-menu-item", $new_item);

    return $new_item;
}

add_filter('bp_get_options_nav_admin', 'openlab_filter_subnav_admin');

function openlab_filter_subnav_admin($subnav_item) {
    global $bp;
    $group_label = openlab_get_group_type_label('case=upper');
    $new_item = str_replace('Admin', $group_label . ' Settings', $subnav_item);
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

    //switch slugs based on user role
    if ($bp->is_item_admin || $bp->is_item_mod):
        $new_item = str_replace("/members/", "/admin/manage-members", $new_item);
    endif;

    $uri = $bp->unfiltered_uri;
    $check_uri = array('groups', 'notifications');
    $notification_status = false;
    if (count(array_intersect($uri, $check_uri)) == count($check_uri)) {
        $notification_status = true;
    }

    //filtering for current status on membership menu item when in membership submenu
    if ((bp_action_variable(0) && ( $bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' || $bp->current_action == 'notifications' || $bp->action_variables[0] == 'membership-requests' || $wp_query->query_vars['pagename'] == 'invite-anyone' )) || $notification_status) {
        $new_item = str_replace('id="members-groups-li"', 'id="members-groups-li" class="current-menu-item"', $new_item);
    } else {
        //update "current" class to "current-menu-item" to unify site identification of current menu page
        $new_item = str_replace("current selected", "current-menu-item", $new_item);
    }

    //get total member count
    $total_mem = bp_core_number_format(groups_get_groupmeta(bp_get_current_group_id(), 'total_member_count'));

    //added classes to span
    $new_item = str_replace('<span>' . $total_mem . '</span>', '<span class="mol-count pull-right count-' . $total_mem . '">' . $total_mem . '</span>', $new_item);

    return $new_item;
}

add_filter('bp_get_options_nav_nav-docs', 'openlab_filter_subnav_docs');

function openlab_filter_subnav_docs($subnav_item) {
    global $bp;

    //no docs if we're on the portfolio page
    if (openlab_is_portfolio()) {
        return '';
    }

    $group_slug = bp_get_group_slug();

    $docs_arg = Array("posts_per_page" => "3",
        "post_type" => "bp_doc",
        "tax_query" =>
        Array(Array("taxonomy" => "bp_docs_associated_item",
                "field" => "slug",
                "terms" => $group_slug)));
    $query = new WP_Query($docs_arg);

    $total_doc_count = !empty($query->found_posts) ? $query->found_posts : 0;

    wp_reset_query();

    $new_item = str_replace('<span>' . $total_doc_count . '</span>', '<span class="mol-count pull-right count-' . $total_doc_count . '">' . $total_doc_count . '</span>', $subnav_item);

    //update "current" class to "current-menu-item" to unify site identification of current menu page
    $new_item = str_replace("current selected", "current-menu-item", $new_item);

    return $new_item;
}

add_filter('bp_get_options_nav_group-documents', 'openlab_filter_subnav_nav_group_documents');

function openlab_filter_subnav_nav_group_documents($subnav_item) {
    //no files if we're on the portfolio page
    if (openlab_is_portfolio()) {
        return '';
    } else {

        //update "current" class to "current-menu-item" to unify site identification of current menu page
        $subnav_item = str_replace("current selected", "current-menu-item", $subnav_item);

        return $subnav_item;
    }
}

add_filter('bp_get_options_nav_forums', 'openlab_filter_subnav_forums');

function openlab_filter_subnav_forums($subnav_item) {
    //update "current" class to "current-menu-item" to unify site identification of current menu page
    $subnav_item = str_replace("current selected", "current-menu-item", $subnav_item);

    return $subnav_item;
}

add_filter('bp_get_options_nav_nav-invite-anyone', 'openlab_filter_subnav_nav_invite_anyone');

function openlab_filter_subnav_nav_invite_anyone($suvbnav_item) {
    return "";
}

add_filter('bp_get_options_nav_nav-notifications', 'openlab_filter_subnav_nav_notifications');

function openlab_filter_subnav_nav_notifications($suvbnav_item) {
    return "";
}

//submenu navigation re-ordering
function openlab_group_submenu_nav() {
    global $bp;

    $current_item = isset($bp->current_item) ? $bp->current_item : '';

    if (!$current_item) {
        return;
    }

    //get the current item menu
    $nav_items = $bp->bp_options_nav[$current_item];

    //manual sorting of current item menu
    if ($nav_items) {
        foreach ($nav_items as $nav_key => $nav_item) {
            switch ($nav_items) {
                case ( $nav_item['slug'] == 'home' ):
                    $nav_item['position'] = 10;
                    break;
                case ( $nav_item['slug'] == 'admin' ):
                    $nav_item['position'] = 15;
                    break;
                case ( $nav_item['slug'] == 'members' ):
                    $nav_item['position'] = 35;
                    break;
                case ( $nav_item['slug'] == 'files' ):
                    $nav_item['position'] = 60;
                    break;
                default:
                    $nav_item['position'] = $nav_item['position'];
            }
            $final_nav[$nav_key] = $nav_item;
        }
    }

    $bp->bp_options_nav[$bp->current_item] = $final_nav;
}

add_action('bp_actions', 'openlab_group_submenu_nav', 1);

/**
 * Markup for group admin tabs
 */
function openlab_group_admin_tabs($group = false) {
    global $bp, $groups_template;

    if (!$group)
        $group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;

    $current_tab = bp_action_variable(0);

    $group_type = groups_get_groupmeta($bp->groups->current_group->id, 'wds_group_type');

    // Portfolio tabs look different from other groups
    ?>

    <?php if (openlab_is_portfolio()) : ?>

        <?php if ($bp->is_item_admin || $bp->is_item_mod) { ?>
            <li<?php if ('edit-details' == $current_tab || empty($current_tab)) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/edit-details">Edit Profile</a></li>
        <?php } ?>

        <?php if (!(int) bp_get_option('bp-disable-avatar-uploads')) : ?>
            <li<?php if ('group-avatar' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-avatar">Change Avatar</a></li>
        <?php endif; ?>

        <li<?php if ('group-settings' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-settings">Privacy Settings</a></li>

        <?php
        $account_type = xprofile_get_field_data('Account Type', $bp->loggedin_user->id);
        if ($account_type == "Student") {
            $profile = "ePortfolio";
        } else {
            $profile = "Portfolio";
        }
        ?>

        <li class="delete-button <?php if ('delete-group' == $current_tab) : ?> current-menu-item<?php endif; ?>" ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/delete-group">Delete <?php echo $profile; ?></a></li>

    <?php else : ?>

        <?php if ($bp->is_item_admin || $bp->is_item_mod) { ?>
            <li<?php if ('edit-details' == $current_tab || empty($current_tab)) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/edit-details"><?php _e('Edit Profile', 'buddypress'); ?></a></li>
        <?php } ?>

        <?php
        if (!$bp->is_item_admin)
            return false;
        ?>

        <li<?php if ('group-avatar' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-avatar"><?php _e('Change Avatar', 'buddypress'); ?></a></li>

        <li<?php if ('group-settings' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-settings"><?php _e('Settings', 'buddypress'); ?></a></li>

        <?php //do_action( 'groups_admin_tabs', $current_tab, $group->slug )     ?>

        <?php if ('course' === openlab_get_group_type(bp_get_current_group_id())) : ?>
            <li class="clone-button <?php if ('clone-group' == $current_tab) : ?>current-menu-item<?php endif; ?>" ><span class="fa fa-plus-circle"></span> <a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details?type=course&clone=' . bp_get_current_group_id() ?>"><?php _e('Clone ' . ucfirst($group_type), 'buddypress'); ?></a></li>
        <?php endif ?>

        <li class="delete-button <?php if ('delete-group' == $current_tab) : ?>current-menu-item<?php endif; ?>" ><span class="fa fa-minus-circle"></span> <a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/delete-group"><?php _e('Delete ' . ucfirst($group_type), 'buddypress'); ?></a></li>

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

    <?php if ($bp->is_item_admin || $bp->is_item_mod): ?>
        <li<?php if ('manage-members' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/manage-members"><?php _e('Membership', 'buddypress'); ?></a></li>

        <?php if ($group->status == 'private'): ?>
            <li<?php if ('membership-requests' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/membership-requests"><?php _e('Member Requests', 'buddypress'); ?></a></li>
        <?php endif; ?>
    <?php else: ?>
        <li<?php if ($bp->current_action == 'members') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/members"><?php _e('Membership', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php if (bp_group_is_member() && invite_anyone_access_test() && openlab_is_admin_truly_member()): ?>
        <li<?php if ($bp->current_action == 'invite-anyone') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/invite-anyone"><?php _e('Invite New Member', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php if ($bp->is_item_admin || $bp->is_item_mod): ?>
        <li<?php if ('notifications' == $current_tab) : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/notifications"><?php _e('Email Members', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php if (bp_group_is_member() && openlab_is_admin_truly_member()): ?>
        <li<?php if ($bp->current_action == 'notifications') : ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/notifications"><?php _e('Your Email Options', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php
}

function openlab_docs_tabs() {
    global $bp;
    $group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;
    ?>

    <li <?php echo (bp_docs_current_view() == 'list' ? 'class="current-menu-item"' : ''); ?> ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/docs/">View Docs</a></li>
    <li <?php echo (bp_docs_current_view() == 'create' ? 'class="current-menu-item"' : ''); ?> ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/docs/create">New Doc</a></li>
        <?php if ((bp_docs_current_view() == 'edit' || bp_docs_current_view() == 'single') && bp_docs_is_existing_doc()): ?>
        <li class="current-menu-item"><?php the_title() ?></li>
        <?php endif; ?>

    <?php
}
