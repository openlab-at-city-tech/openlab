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

    if (!empty($bp_first_name)) {
        $user_meta['first_name'] = $bp_first_name;
    }

    if (!empty($bp_last_name)) {
        $user_meta['last_name'] = $bp_last_name;
    }

    return $user_meta;
}

add_filter('oplb_gradebook_user_meta', 'openlab_oplb_gradebook_user_meta', 10, 2);

function openlab_oplb_gradebook_show_user_widget($status) {

    //only show widget is user is member of group
    $blog_id = get_current_blog_id();

    $group_id = openlab_get_group_id_by_blog_id($blog_id);

    if(!$group_id){
        return false;
    }

    $current_user = wp_get_current_user();

    if (!groups_is_user_member($current_user->ID, $group_id) && !groups_is_user_admin($current_user->ID, $group_id) && !groups_is_user_mod($current_user->ID, $group_id)) {
        return false;
    }

    return $status;
}

add_filter('oplb_gradebook_show_user_widget', 'openlab_oplb_gradebook_show_user_widget', 10);

function openlab_oplb_gradebook_gradebook_init_placeholder($placeholder){

    $blog_id = get_current_blog_id();

    $group_id = openlab_get_group_id_by_blog_id($blog_id);

    if(!$group_id){
        return false;
    }

    $this_group = groups_get_group(
        array(
             'group_id' => $group_id,
        )
    );

    if ( ! empty( $this_group->name ) ) {
        return $this_group->name;
    }

    return $placeholder;

}

add_filter('oplb_gradebook_gradebook_init_placeholder','openlab_oplb_gradebook_gradebook_init_placeholder');

function openlab_oplb_gradebook_students_list($students, $blog_id){
    global $oplb_gradebook_api;

    //reset outgoing array
    $students_out = array();

    $group_id = openlab_get_group_id_by_blog_id($blog_id);

    if(!$group_id){
        return $students_out;
    }

    $member_arg = array(
        'group_id' => $group_id,
        'exclude_admins_mods' => true,
        'per_page' => false,
    );

    if (bp_group_has_members($member_arg)) :

        while (bp_group_members()) : bp_group_the_member();

            global $members_template;
            $member = $members_template->member;
            $this_student = new stdClass;

            $user_meta = $oplb_gradebook_api->oplb_gradebook_get_user_meta($member);
            $this_student->first_name = $user_meta['first_name'];
            $this_student->last_name = $user_meta['last_name'];
            $this_student->user_login = $member->user_login;

            array_push($students_out, $this_student);

        endwhile;

    else:

        //if there's an error set, return the error
        if(isset($students['error'])){
            $students_out['error'] = $students['error'];
        } else {
            //no students to return
            $students_out['error'] = 'no students';
        }

    endif;

    return $students_out;
}

add_filter('oplb_gradebook_students_list', 'openlab_oplb_gradebook_students_list', 10, 2);
