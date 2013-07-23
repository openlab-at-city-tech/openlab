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

//adding help categories (custom taxonomy) to menu for help page
function help_categories_menu($items, $args) {
    global $post;
    if ($args->theme_location == 'helpmenu') {
        $term = get_query_var('term');
        $parent_term = get_term_by('slug', $term, 'help_category');

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

        //using a plugin now - if it works out, will deprecate this
        // Temp: We have to reorder the cats to be in our desired order
        // This worked out accidentally on the staging site, because of
        // the order created in the database
        /* $ordered_cats = array();
          $cat_order = array(
          'OpenLab Help',
          'Getting Started',
          'People on the OpenLab',
          'Courses, Projects, and Clubs',
          'Sites on the OpenLab',
          'Best Practices',
          'Troubleshooting',
          'Portfolios and ePortfolios'
          );

          foreach( $help_cats as $hc_key => $hc ) {
          $ordered_key = array_search( $hc->name, $cat_order );

          if ( false !== $ordered_key ) {
          $ordered_cats[$ordered_key] = $hc;

          // Unset from the main $help_cats array, so we
          // know which have been used
          unset( $help_cats[$hc_key] );
          }
          }
          ksort( $ordered_cats );

          // Whatever's left should get tacked onto the end, so we don't
          // lose cats added in the future
          $help_cats = array_merge( $ordered_cats, $help_cats );
          // END Boone's temp sort code */

        $help_cat_list = "";
        foreach ($help_cats as $help_cat) {
            //eliminate children cats from the menu list
            if ($help_cat->parent == 0) {

                $help_classes = "help-cat menu-item";

                //see if this is the current menu item; if not, this could be a post,
                //so we'll check against an array of cat ids for this post
                if ($help_cat->term_id == $parent_term->term_id) {
                    $help_classes .= " current-menu-item";
                } else if ($post->post_type == 'help') {
                    if (in_array($help_cat->term_id, $post_cats_array)) {
                        $help_classes .= " current-menu-item";
                    }
                }

                //a special case just for the glossary page
                if ($help_cat->name == "Help Glossary") {
                    $help_cat->name = "Glossary";
                }

                $help_cat_list .= '<li class="' . $help_classes . '"><a href="' . get_term_link($help_cat) . '">' . $help_cat->name . '</a></li>';
            }
        }
        $items = $items . $help_cat_list;
    }
    return $items;
}

add_filter('wp_nav_menu_items', 'help_categories_menu', 10, 2);

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
    $create_link = BP_GROUPS_SLUG . '/create/step/group-details/?type=' . $group . '&new=true';

    //get account type to see if they're faculty
    $faculty = xprofile_get_field_data('Account Type', get_current_user_id());

    //if the current user is faculty or a super admin, they can create a course, otherwise no dice
    if ($group == "course") {
        if (is_super_admin(get_current_user_id()) || $faculty == "Faculty") {
            $menu_list = array(
                $group_link => 'My ' . ucfirst($group) . 's',
                $create_link => 'Create a ' . ucfirst($group),
            );
        } else {
            $menu_list = array(
                $group_link => 'My ' . ucfirst($group) . 's',
            );
        }
    } else {
        $menu_list = array(
            $group_link => 'My ' . ucfirst($group) . 's',
            $create_link => 'Create a ' . ucfirst($group),
        );
    }

    return openlab_submenu_gen($menu_list);
}

//sub-menus for my-friends pages
function openlab_my_friends_submenu() {
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

    $menu_list = array(
        $my_friends => 'My Friends',
        $friend_requests => 'Requests Received <span class="mol-count count-' . $request_count . '">' . $request_count . '</span>',
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

    //get $items length so we know how many menu items there are (for tagging the "last-item" class)
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

    $submenu = '<ul>';

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

        //adding the selected-page class - also includes special cases, parsed out to make them easier to identify
        if ($current_check !== false) {
            $item_classes .= " selected-page";
        } else if ($page_identify == "general" && $title == "Account Settings") {
            //special case just for account settings page
            $item_classes .= " selected-page";
        } else if ($page_identify == "my-friends" && $title == "My Friends") {
            //special case just for my friends page
            $item_classes .= " selected-page";
        } else if ($page_identify == "invite-new-members" && $title == "Invite New Members") {
            //special case just for Invite New Members page
            $item_classes .= " selected-page";
        } else if ($page_identify == 'my-groups') {
            //special case for my-<groups> pages
            if (isset($_GET['type'])) {
                $type = $_GET['type'];
                $type_title = 'My ' . ucfirst(str_replace('-', ' ', $type)) . 's';
                if ($title == $type_title) {
                    $item_classes .= " selected-page";
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
        if (strpos($item_classes, "delete")) {
            $item_classes .= " delete-button";
        } else if (strpos($item_classes, "create")) {
            $item_classes .= " create-button";
        }

        $submenu .= '<li class="' . $item_classes . '">';
        $submenu .= '<a href="' . $item . '">';
        $submenu .= $title;
        $submenu .= '</a>';
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
    return $new_item;
}

add_filter('bp_get_options_nav_admin', 'openlab_filter_subnav_admin');

function openlab_filter_subnav_admin($subnav_item) {
    global $bp;
    $group_type = openlab_get_group_type(bp_get_current_group_id());
    $new_item = str_replace("Admin", ucfirst($group_type) . " Settings", $subnav_item);
    //this is to stop the course settings menu item from getting a current class on membership pages
    if ($bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' || $bp->action_variables[0] == 'membership-requests') {
        $new_item = str_replace("current selected", " ", $new_item);
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

    //filtering for current status on membership menu item when in membership submenu
    if (bp_action_variable(0) && ( $bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' || $bp->current_action == 'notifications' || $bp->action_variables[0] == 'membership-requests' || $wp_query->query_vars['pagename'] == 'invite-anyone')) {
        $new_item = str_replace('id="members-groups-li"', 'id="members-groups-li" class="current selected"', $new_item);
    }

    //get total member count
    $total_mem = bp_core_number_format(groups_get_groupmeta(bp_get_current_group_id(), 'total_member_count'));

    //added classes to span
    $new_item = str_replace('<span>' . $total_mem . '</span>', '<span class="mol-count count-' . $total_mem . '">' . $total_mem . '</span>', $new_item);

    return $new_item;
}

add_filter('bp_get_options_nav_nav-docs', 'openlab_filter_subnav_docs');

function openlab_filter_subnav_docs($subnav_item) {
    global $bp;

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

    $new_item = str_replace('<span>' . $total_doc_count . '</span>', '<span class="mol-count count-' . $total_doc_count . '">' . $total_doc_count . '</span>', $subnav_item);
    return $new_item;
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

    //get the current item menu
    $nav_items = $bp->bp_options_nav[$bp->current_item];

    //manual sorting of current item menu
    if ($nav_items) {
        foreach ($nav_items as $nav_key => $nav_item) {
            switch ($nav_items) {
                case ($nav_item['slug'] == 'home'):
                    $nav_item['position'] = 10;
                    break;
                case ($nav_item['slug'] == 'admin'):
                    $nav_item['position'] = 15;
                    break;
                case ($nav_item['slug'] == 'members'):
                    $nav_item['position'] = 35;
                    break;
                case ($nav_item['slug'] == 'files'):
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

/*
 * Hide "Join Group/Leave Group" button on Portfolios and select group profile pages
 */

function openlab_no_join_on_portfolios($button) {
    global $bp;

    if (openlab_is_portfolio()) {
        $button = "";
    }
    //fix for files, docs, and membership pages in group profile - hiding join button
    else if ($bp->current_action == 'files' || $bp->current_action == 'docs' || $bp->current_action == 'invite-anyone' || $bp->current_action == 'notifications') {
        $button = "";
    }

    return $button;
}

add_filter('bp_get_group_join_button', 'openlab_no_join_on_portfolios');

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
            <li<?php if ('edit-details' == $current_tab || empty($current_tab)) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/edit-details">Edit Profile</a></li>
        <?php } ?>

        <?php if (!(int) bp_get_option('bp-disable-avatar-uploads')) : ?>
            <li<?php if ('group-avatar' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-avatar">Change Avatar</a></li>
        <?php endif; ?>

        <li<?php if ('group-settings' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-settings">Privacy Settings</a></li>

        <?php /* Only show the Edit Access List tab for non-public groups */ ?>
        <?php if ('public' != $group->status) : ?>
            <li<?php if ('access-list' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/access-list">Edit Access List</a></li>
        <?php endif ?>

        <?php
        $account_type = xprofile_get_field_data('Account Type', $bp->loggedin_user->id);
        if ($account_type == "Student") {
            $profile = "ePortfolio";
        } else {
            $profile = "Portfolio";
        }
        ?>

        <li class="delete-button <?php if ('delete-group' == $current_tab) : ?> current<?php endif; ?>" ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/delete-group">Delete <?php echo $profile; ?></a></li>

    <?php else : ?>

        <?php if ($bp->is_item_admin || $bp->is_item_mod) { ?>
            <li<?php if ('edit-details' == $current_tab || empty($current_tab)) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/edit-details"><?php _e('Edit Profile', 'buddypress'); ?></a></li>
        <?php } ?>

        <?php
        if (!$bp->is_item_admin)
            return false;
        ?>

        <li<?php if ('group-avatar' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-avatar"><?php _e('Change Avatar', 'buddypress'); ?></a></li>

        <li<?php if ('group-settings' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/group-settings"><?php _e('Settings', 'buddypress'); ?></a></li>

        <?php //do_action( 'groups_admin_tabs', $current_tab, $group->slug ) ?>

        <li class="delete-button <?php if ('delete-group' == $current_tab) : ?>current<?php endif; ?>" ><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/delete-group"><?php _e('Delete ' . ucfirst($group_type), 'buddypress'); ?></a></li>

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
        <li<?php if ('manage-members' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/manage-members"><?php _e('Membership', 'buddypress'); ?></a></li>

        <?php if ($group->status == 'private'): ?>
            <li<?php if ('membership-requests' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/membership-requests"><?php _e('Member Requests', 'buddypress'); ?></a></li>
        <?php endif; ?>
    <?php else: ?>
        <li<?php if ($bp->current_action == 'members') : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/members"><?php _e('Membership', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php if (bp_group_is_member()): ?>
        <li<?php if ($bp->current_action == 'invite-anyone') : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/invite-anyone"><?php _e('Invite New Members', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php if ($bp->is_item_admin || $bp->is_item_mod): ?>
        <li<?php if ('notifications' == $current_tab) : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/admin/notifications"><?php _e('Email Members', 'buddypress'); ?></a></li>
    <?php endif; ?>

    <?php if (bp_group_is_member()): ?>
        <li<?php if ($bp->current_action == 'notifications') : ?> class="current"<?php endif; ?>><a href="<?php echo bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug ?>/notifications"><?php _e('Your Email Options', 'buddypress'); ?></a></li>
    <?php endif; ?>

<?php
}
