<?php

/*
 * Media-oriented functionality
 */

/**
 * Custom mysteryman
 * @return type
 */
function openlab_new_mysteryman() {
    return get_stylesheet_directory_uri() . '/images/default-avatar.jpg';
}

add_filter('bp_core_mysteryman_src', 'openlab_new_mysteryman', 2, 7);

/**
 * Custom default avatar
 * @param string $url
 * @param type $params
 * @return string
 */
function openlab_default_get_group_avatar($url, $params) {
    
    if(strstr($url,'default-avatar')){
        $url = get_stylesheet_directory_uri() . '/images/default-avatar.jpg';
    }
    
    return $url;
}

add_filter('bp_core_fetch_avatar_url', 'openlab_default_get_group_avatar', 10, 2);

/**
 * WordPress adds dimensions to embedded images; this is totally not responsive WordPress
 * @param type $html
 * @return type
 */
function openlab_remove_thumbnail_dimensions($html) {
    
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    
    return $html;
}

add_filter('post_thumbnail_html', 'openlab_remove_thumbnail_dimensions', 10);
add_filter('image_send_to_editor', 'openlab_remove_thumbnail_dimensions', 10);
add_filter('the_content', 'openlab_remove_thumbnail_dimensions', 10);
