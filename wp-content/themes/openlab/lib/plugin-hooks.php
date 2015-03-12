<?php
/**
 * Plugin hooks
 * Complete archive of plugin hooking for openlab theme, wds-citytech plugin, and mu-plugins
 * Includes actual hooks, related includes, and references for folder/file overwrites and hooks that need to stay elsewhere
 */
/**
 * Invite Anyone
 * See also: openlab/buddypress/members/single/invite-anyone.php for template overrides
 */
require_once( STYLESHEETPATH . '/lib/plugin-mods/invite-funcs.php' );

/**
 * Plugin: Invite Anyone
 * Don't send friend requests when accepting Invite Anyone invitations
 *
 * @see #666
 */
add_filter('invite_anyone_send_friend_requests_on_acceptance', '__return_false');

/**
 * Buddypress Group Documents
 * See also: mu-plugins/openlab-group-documents-privacy.php
 */
require_once( STYLESHEETPATH . '/lib/plugin-mods/files-funcs.php' );

/**
 * Plugin: BuddyPress Docs
 * See also: openlab/buddypress/groups/single/docs for template overrides
 */
/**
 * Plugin: BuddyPress Docs
 * Don't allow BuddyPress Docs to use its own theme compatibility layer
 */
add_filter('bp_docs_do_theme_compat', '__return_false');

/**
 * Plugin: BuddyPress Docs
 * Overriding the BP Docs header file to clean up sub menus
 * @param type $menu_template
 * @return string
 */
function openlab_hide_docs_native_menu($menu_template) {

    $path = STYLESHEETPATH . '/buddypress/groups/single/docs/docs-header.php';

    return $path;
}

add_filter('bp_docs_header_template', 'openlab_hide_docs_native_menu');

/**
 * Plugin: BuddyPress Docs
 * Custom templates for BP Docs pages
 * Allows for layout control and Bootstrap injection
 * @param type $path
 * @param type $template
 * @return type
 */
function openlab_custom_docs_templates($path, $template) {

    if ($template->current_view == 'list') {
        $path = bp_locate_template('groups/single/docs/docs-loop.php', false);
    } else if ($template->current_view == 'create' || $template->current_view == 'edit') {
        $path = bp_locate_template('groups/single/docs/edit-doc.php', false);
    } else if ($template->current_view == 'single') {
        $path = bp_locate_template('groups/single/docs/single-doc.php', false);
    }

    return $path;
}

add_filter('bp_docs_template', 'openlab_custom_docs_templates', 10, 2);

/**
 * Allow super admins to edit any BuddyPress Doc
 * @global type $bp
 * @param type $user_can
 * @param type $action
 * @return boolean
 */
function openlab_allow_super_admins_to_edit_bp_docs($user_can, $action) {
    global $bp;

    if ('edit' == $action) {
        if (is_super_admin() || bp_loggedin_user_id() == get_the_author_meta('ID') || $user_can) {
            $user_can = true;
            $bp->bp_docs->current_user_can[$action] = 'yes';
        } else {
            $user_can = false;
            $bp->bp_docs->current_user_can[$action] = 'no';
        }
    }

    return $user_can;
}

add_filter('bp_docs_current_user_can', 'openlab_allow_super_admins_to_edit_bp_docs', 10, 2);

/**
 * Hack alert! Allow group avatars to be deleted
 *
 * There is a bug in BuddyPress Docs that blocks group avatar deletion, because
 * BP Docs is too greedy about setting its current view, and thinks that you're
 * trying to delete a Doc instead. Instead of fixing that, which I have no
 * patience for at the moment, I'm just going to override BP Docs's current
 * view in the case of deleting an avatar.
 */
function openlab_fix_avatar_delete($view) {
    if (bp_is_group_admin_page()) {
        $view = '';
    }

    return $view;
}

add_filter('bp_docs_get_current_view', 'openlab_fix_avatar_delete', 9999);

/**
 * BuddyPress Group Email Subscription
 * See also: openlab/buddypress/groups/single/notifications.php for template overrides
 */
require_once( STYLESHEETPATH . '/lib/plugin-mods/email-funcs.php' );

/**
 * Plugin: BuddyPress Group Email Subscription
 * This function overwrites the email status output from the buddypress group email subscription plugin
 * Allows for layout control and Bootstrap injection
 * @global type $members_template
 * @global type $groups_template
 * @param type $user_id
 * @param type $group
 * @return type
 */
function openlab_manage_members_email_status($user_id = '', $group = '') {
    global $members_template, $groups_template;

    // if group admins / mods cannot manage email subscription settings, stop now!
    if (get_option('ass-admin-can-edit-email') == 'no') {
        return;
    }

    // no user ID? fallback on members loop user ID if it exists
    if (!$user_id) {
        $user_id = !empty($members_template->member->user_id) ? $members_template->member->user_id : false;
    }

    // no user ID? fallback on group loop if it exists
    if (!$group) {
        $group = !empty($groups_template->group) ? $groups_template->group : false;
    }

    // no user or group? stop now!
    if (!$user_id || !is_object($group)) {
        return;
    }

    $user_id = (int) $user_id;

    $group_url = bp_get_group_permalink($group) . 'admin/manage-members/email';
    $sub_type = ass_get_group_subscription_status($user_id, $group->id);
    echo '<p class="italics no-margin no-margin-bottom"> ' . __('Email status:', 'bp-ass') . ' ' . ass_subscribe_translate($sub_type) . '.';
    echo ' ' . __('Change to:', 'bp-ass') . ' ';
    echo '</p>';
    echo '<a class="btn btn-primary link-btn btn-xs" href="' . wp_nonce_url($group_url . '/no/' . $user_id, 'ass_member_email_status') . '">' . __('No Email', 'bp-ass') . '</a>';
    echo '<a class="btn btn-primary link-btn btn-xs" href="' . wp_nonce_url($group_url . '/sum/' . $user_id, 'ass_member_email_status') . '">' . __('Weekly', 'bp-ass') . '</a>';
    echo '<a class="btn btn-primary link-btn btn-xs" href="' . wp_nonce_url($group_url . '/dig/' . $user_id, 'ass_member_email_status') . '">' . __('Daily', 'bp-ass') . '</a>';

    if (ass_get_forum_type()) {
        echo '<a class="btn btn-primary link-btn btn-xs" href="' . wp_nonce_url($group_url . '/sub/' . $user_id, 'ass_member_email_status') . '">' . __('New Topics', 'bp-ass') . '</a>';
    }

    echo '<a class="btn btn-primary link-btn btn-xs" href="' . wp_nonce_url($group_url . '/supersub/' . $user_id, 'ass_member_email_status') . '">' . __('All Email', 'bp-ass') . '</a>';
}

remove_action('bp_group_manage_members_admin_item', 'ass_manage_members_email_status');
add_action('bp_group_manage_members_admin_item', 'openlab_manage_members_email_status');

/**
 * Put the group type in email notification subject lines
 * @param type $subject
 * @return type
 */
function openlab_group_type_in_notification_subject($subject) {

    if (!empty($groups_template->group->id)) {
        $group_id = $groups_template->group->id;
    } else if (!empty($bp->groups->current_group->id)) {
        $group_id = $bp->groups->current_group->id;
    } else {
        return $subject;
    }


    if (isset($_COOKIE['wds_bp_group_type'])) {
        $grouptype = $_COOKIE['wds_bp_group_type'];
    } else {
        $grouptype = groups_get_groupmeta($group_id, 'wds_group_type');
    }

    return str_replace('in the group', 'in the ' . $grouptype, $subject);
}

add_filter('ass_clean_subject', 'openlab_group_type_in_notification_subject');

/**
 * Default subscription level for group emails should be All
 */
function openlab_default_group_subscription($level) {
    if (!$level) {
        $level = 'supersub';
    }

    return $level;
}

add_filter('ass_default_subscription_level', 'openlab_default_group_subscription');

/**
 * Bbpress
 * See also: openlab/bbpress for template overrides
 */

/**
 * Plugin: BBPress
 * Adding the forums submenu into the BBPress layout
 */
function openlab_forum_tabs_output() {
    ?>
    <ul class="nav nav-inline">
        <?php openlab_forum_tabs(); ?>
    </ul>
    <?php
}

add_action('bbp_before_group_forum_display', 'openlab_forum_tabs_output');

/**
 * Plugin: BBPress
 * Injectiong bootstrap classes into BBPress comment textarea field
 * @param type $output
 * @param type $args
 * @param type $post_content
 * @return type
 */
function openlab_custom_bbp_content($output, $args, $post_content) {

    if (strpos($output, 'textarea') !== false) {
        $output = str_replace('wp-editor-area', 'form-control', $output);
    }

    return $output;
}

add_filter('bbp_get_the_content', 'openlab_custom_bbp_content', 10, 3);

/**
 * Plugin: BBPress
 * Updating BBPress page navigation to include font awesome icons
 * @param type $pag_args
 * @return string
 */
function openlab_bbp_pagination($pag_args) {

    $pag_args['prev_text'] = __('<i class="fa fa-angle-left"></i>');
    $pag_args['next_text'] = __('<i class="fa fa-angle-right"></i>');
    $pag_args['type'] = 'list';

    return $pag_args;
}

add_filter('bbp_topic_pagination', 'openlab_bbp_pagination');

/**
 * Plugin: BBPress
 * Injecting classes into pagination container to unify pagination styling
 * @param type $pagination
 * @return type
 */
function openlab_bbp_paginatin_custom_markup($pagination) {

    $pagination = str_replace('page-numbers', 'page-numbers pagination', $pagination);

    return $pagination;
}

add_filter('bbp_get_forum_pagination_links', 'openlab_bbp_paginatin_custom_markup');

/**
 * Plugin: BBpress
 * Injecting bootstrap and site standard button classes into subscription toggle button
 * @param type $html
 * @param type $r
 * @param type $user_id
 * @param type $topic_id
 * @return type
 */
function openlab_style_bbp_subscribe_link($html, $r, $user_id, $topic_id) {

    if (!bbp_is_single_topic()) {
        $html = str_replace('class="subscription-toggle"', 'class="subscription-toggle btn btn-primary btn-margin btn-margin-top no-deco"', $html);
    }

    return $html;
}

add_filter('bbp_get_user_subscribe_link', 'openlab_style_bbp_subscribe_link', 10, 4);

/**
 * More generous cap mapping for bbPress topic posting.
 *
 * bbPress maps everything onto Participant. We don't want to have to use that.
 */
function openlab_bbp_map_group_forum_meta_caps($caps = array(), $cap = '', $user_id = 0, $args = array()) {
    if (!bp_is_group()) {
        return $caps;
    }
    switch ($cap) {
// If user is a group mmember, allow them to create content.
        case 'read_forum' :
        case 'publish_replies' :
        case 'publish_topics' :
        case 'read_hidden_forums' :
        case 'read_private_forums' :
            if (bbp_group_is_member() || bbp_group_is_mod() || bbp_group_is_admin()) {
                $caps = array('exist');
            }
            break;
// If user is a group mod ar admin, map to participate cap.
        case 'moderate' :
        case 'edit_topic' :
        case 'edit_reply' :
        case 'view_trash' :
        case 'edit_others_replies' :
        case 'edit_others_topics' :
            if (bbp_group_is_mod() || bbp_group_is_admin()) {
                $caps = array('exist');
            }
            break;
// If user is a group admin, allow them to delete topics and replies.
        case 'delete_topic' :
        case 'delete_reply' :
            if (bbp_group_is_admin()) {
                $caps = array('exist');
            }
            break;
    }
    return apply_filters('bbp_map_group_forum_topic_meta_caps', $caps, $cap, $user_id, $args);
}

add_filter('bbp_map_meta_caps', 'openlab_bbp_map_group_forum_meta_caps', 10, 4);

/**
 * Force bbPress to display all forums (ie don't hide any hidden forums during bbp_has_forums() queries).
 *
 * We manage visibility ourselves.
 *
 * See #1299.
 */
add_filter('bbp_include_all_forums', '__return_true');

/**
 * Force bbp_has_forums() to show all post statuses.
 *
 * As above, I have no idea why bbPress makes some items hidden, but it appears
 * incompatible with BuddyPress groups.
 */
function openlab_bbp_force_all_forum_statuses($r) {
    $r['post_status'] = array(bbp_get_public_status_id(), bbp_get_private_status_id(), bbp_get_hidden_status_id());
    return $r;
}

add_filter('bbp_before_has_forums_parse_args', 'openlab_bbp_force_all_forum_statuses');

/**
 * Ensure that post results for bbPres forum queries are never marked hidden.
 *
 * Working with bbPress is really exhausting.
 */
function openlab_bbp_force_forums_to_public($posts, $query) {
    if (!function_exists('bp_is_group') || !bp_is_group()) {
        return $posts;
    }
    if ('forum' !== $query->get('post_type')) {
        return $posts;
    }
    foreach ($posts as &$post) {
        $post->post_status = 'publish';
    }
    return $posts;
}

add_filter('posts_results', 'openlab_bbp_force_forums_to_public', 10, 2);

/**
 * Force site public to 1 for bbPress.
 *
 * Otherwise activity is not posted.
 */
function openlab_bbp_force_site_public_to_1($public, $site_id) {
    if (1 == $site_id) {
        $public = 1;
    }
    return $public;
}

add_filter('bbp_is_site_public', 'openlab_bbp_force_site_public_to_1', 10, 2);
