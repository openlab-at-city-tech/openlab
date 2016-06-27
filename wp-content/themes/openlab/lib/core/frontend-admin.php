<?php

/**
 * As most users do not have access to wp-admin on OpenLab (this file does not cover group sites)
 * this file covers functionality hooking into frontend admin actions
 */

/**
 * Catch data ahead of DB update
 * Primarily useful for validation
 * @param type $post_ID
 * @param type $data
 */
function openlab_pre_post_update_custom_actions($post_ID, $data) {

    //right now event validation is *only* client side
    //@todo - add server side validation
    if (get_post_type($post_ID) === 'event') {
        
    }
}

add_action('pre_post_update', 'openlab_pre_post_update_custom_actions', 10, 2);
