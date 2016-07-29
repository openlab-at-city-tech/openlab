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

    $options = '<option value="dept_all">All</option>';

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
