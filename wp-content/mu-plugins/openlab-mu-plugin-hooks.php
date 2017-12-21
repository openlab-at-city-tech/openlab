<?php

/*
 * For plugin hooks that need to happen on a group site level
 */

/**
 * Plugin: OpenLab Gradebook
 */

/**
 * Filter OpenLab Gradebook user meta and add Xprofile fields if they are avaiable
 * @param type $user_meta
 * @return type
 */
function openlab_oplb_gradebook_user_meta($user_meta, $user) {

    $bp_first_name = xprofile_get_field_data('First Name', $user->ID);
    $bp_last_name = xprofile_get_field_data('Last Name', $user->ID);

    if ($bp_first_name
            && !empty($bp_first_name)) {
        $user_meta['first_name'] = $bp_first_name;
    }

    if ($bp_last_name
            && !empty($bp_last_name)) {
        $user_meta['last_name'] = $bp_last_name;
    }

    return $user_meta;
}

add_filter('oplb_gradebook_user_meta', 'openlab_oplb_gradebook_user_meta', 10, 2);

function openlab_oplb_gradebook_show_user_widget($status) {
    global $wpdb;

    //only show widget is user is member of group
    $blog_id = get_current_blog_id();

    $query = $wpdb->prepare("SELECT group_id FROM {$wpdb->groupmeta} WHERE meta_key = %s AND meta_value = %d", 'wds_bp_group_site_id', $blog_id);
    $results = $wpdb->get_results($query);

    if (!$results || empty($results)) {
        return false;
    }

    $group_id = intval($results[0]->group_id);

    $member_arg = array(
        'group_id' => $group_id,
    );
    
    $current_user = wp_get_current_user();
    
    if (!groups_is_user_member($current_user->ID, $group_id) && !groups_is_user_admin($current_user->ID, $group_id) && !groups_is_user_mod($current_user->ID, $group_id)) {
        return false;
    }

    return $status;
}

add_filter('oplb_gradebook_show_user_widget', 'openlab_oplb_gradebook_show_user_widget', 10);
