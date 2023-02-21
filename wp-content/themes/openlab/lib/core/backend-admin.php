<?php

/**
 * Custom admin functionality
 */

/**
 * Hooking into @admin_menu
 */
function openlab_custom_admin_menu_actions() {

    openlab_custom_admin_menu_items();
}

add_action('admin_menu', 'openlab_custom_admin_menu_actions');

/**
 * Custom admin menu items
 */
function openlab_custom_admin_menu_items() {

    add_submenu_page(
            'edit.php?post_type=help', 'Footer Links', 'Footer Links', 'manage_options', 'help-footer-links', 'openlab_footer_links'
    );
}

/**
 * Loads Markup for Footer Links sub menu page
 */
function openlab_footer_links() {
    $footer_links_mup = '';

    ob_start();
    include(locate_template('parts/admin/footer-links-admin.php'));
    $footer_links_mup = ob_get_clean();

    echo $footer_links_mup;
}

/**
 * Processes incoming $_POST variables and responds accordingly
 * Also checks for links storage in options table
 * @return string
 */
function openlab_process_footer_links() {
    $links_data_out = array();
    $submit = filter_input(INPUT_POST, 'submit');

    //retrieve data from storage
    $accessibility_info_val = get_option('footer_link_accessibility_help_post');
    if ($accessibility_info_val) {
        $accessibility_info_obj = get_post($accessibility_info_val);
        $links_data_out['accessibility_info_id'] = $accessibility_info_val;
        $links_data_out['accessibility_info_title'] = $accessibility_info_obj->post_title;
    }

    if (!$submit) {
        return $links_data_out;
    }

    $accessibility_info_val = filter_input(INPUT_POST, 'accessibility_info_val');
    $accessibility_info_name = filter_input(INPUT_POST, 'accessibility_info_name');

    if ( $accessibility_info_val && !empty( $accessibility_info_name ) ) {
        $accessibility_info_obj = get_post($accessibility_info_val);
        $links_data_out['accessibility_info_id'] = $accessibility_info_val;
        $links_data_out['accessibility_info_title'] = $accessibility_info_obj->post_title;

        update_option('footer_link_accessibility_help_post', $accessibility_info_val);
        $links_data_out['sucess_message'] = 'Help post selection for the accessibility info link updated.';
    } else {

        $links_data_out['accessibility_info_id'] = 0;
        $links_data_out['accessibility_info_title'] = '';

        update_option('footer_link_accessibility_help_post', 0);

        $links_data_out['error_message'] = 'Help post selection for the accessibility info link returned a zero value. This will hide that link in the footer. If this was not your intention, please try again or contact support.';
    }

    return $links_data_out;
}
