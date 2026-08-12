<?php

//ajax based functions

/**
 * This function process the department dropdown on the Courses archive page
 *
 */
function openlab_ajax_return_course_list() {
    if (!wp_verify_nonce($_GET['nonce'], 'dept_select_nonce')) {
        exit('exit');
    }

    $school = $_GET['school'];

    $depts = openlab_get_department_list($school, 'short');

    $options = '<option value="dept_all">All Departments</option>';

    foreach ($depts as $dept_name => $dept_label) {
        $options .= '<option value="' . esc_attr($dept_name) . '">' . esc_attr($dept_label) . '</option>';
    }

    die($options);
}

add_action('wp_ajax_nopriv_openlab_ajax_return_course_list', 'openlab_ajax_return_course_list');
add_action('wp_ajax_openlab_ajax_return_course_list', 'openlab_ajax_return_course_list');

function openlab_ajax_return_latest_activity() {
    if (!wp_verify_nonce($_GET['nonce'], 'request-nonce')) {
        exit('exit');
    }

    $whats_happening = openlab_whats_happening();

    die($whats_happening);
}

add_action('wp_ajax_nopriv_openlab_ajax_return_latest_activity', 'openlab_ajax_return_latest_activity');
add_action('wp_ajax_openlab_ajax_return_latest_activity', 'openlab_ajax_return_latest_activity');

function openlab_ajax_unique_login_check() {
    if (!isset($_GET['login'])) {
        status_header(500);
        die();
    }

    $login = urldecode(wp_unslash($_GET['login']));

    if (username_exists($login)) {
        status_header(400);
    } else {
        status_header(200);
    }

    die();
}

add_action('wp_ajax_nopriv_openlab_unique_login_check', 'openlab_ajax_unique_login_check');

/**
 * Retrieves any help posts that have a 'like' match to the term query via post_title
 * @global type $wpdb
 */
function openlab_ajax_help_post_autocomplete() {
    global $wpdb;
    $posts_out = array();

    $nonce = filter_input(INPUT_GET, 'nonce');

    if (!wp_verify_nonce($nonce, 'request-nonce')) {
        exit('exit');
    }

    $term = filter_input(INPUT_GET, 'term');

    if (!term || empty($term)) {
        exit('exit');
    }

    $prepared_query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_title LIKE '%s'", 'help', 'publish', '%' . $term . '%');
    $posts = $wpdb->get_results($prepared_query);

    if ( ! $posts ) {
        $posts_out[0] = array(
            'label' => 'No Results',
            'value' => 'No Results',
            'id' => 0
        );
        die(json_encode($posts_out));
    }

    foreach ($posts as $key => $post){

        $posts_out[$key] = array(
            'label' => $post->post_title,
            'value' => $post->post_title,
            'id' => $post->ID
        );
    }

    die(json_encode($posts_out));
}

add_action('wp_ajax_openlab_ajax_help_post_autocomplete', 'openlab_ajax_help_post_autocomplete');
