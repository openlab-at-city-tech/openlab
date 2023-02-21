<?php

/**
 * Handling custom pages and core page functionality
 */

/**
 * Create a custom page
 * @param type $slug
 * @param type $title
 * @return type
 */
function openlab_custom_page($slug, $title, $parent_obj = NULL) {

    $post_id = -1;
    $author_id = 1;
    $check_path = $slug;

	$parent_id = 0;
    if ( $parent_obj ) {
        $ancestor_path = array();
        foreach ( $parent_obj as $parent ) {
            if ( $parent ) {
                $ancestor_path[] = $parent->post_name;
                $parent_id = $parent->ID;
            }
        }

        $check_path = implode('/', $ancestor_path) . '/' . $slug;
    }

    if (NULL === get_page_by_path($check_path)) {

        $post_id = wp_insert_post(
                array(
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_author' => $author_id,
                    'post_name' => $slug,
                    'post_title' => $title,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_parent' => $parent_id,
                )
        );
    } else {
        $post_id = -2;
    }

    return $post_id;
}

/**
 * Call custom pages to be created
 */
function openlab_custom_pages() {

    $about_page_obj = get_page_by_path('about');
    openlab_custom_page('calendar', 'OpenLab Calendar', array($about_page_obj));

    $calendar_page_obj = get_page_by_path('about/calendar');
    openlab_custom_page('upcoming', 'OpenLab Calendar: Upcoming', array($about_page_obj, $calendar_page_obj));
}

add_filter('after_setup_theme', 'openlab_custom_pages');
