<?php
defined('ABSPATH') || die;

// Run when activate plugin
register_activation_hook(ADVANCED_GUTENBERG_PLUGIN, function () {
    // Check if Gutenberg is activated
    if (!function_exists('register_block_type')) {
        $gutenbergInstallUrl = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => 'install-plugin',
                    'plugin' => 'gutenberg'
                ),
                admin_url('update.php')
            ),
            'install-plugin_gutenberg'
        );

        wp_die(
            esc_html__('Gutenberg is not detected! Activate it or', 'advanced-gutenberg')
            . ': <a href="'. esc_attr($gutenbergInstallUrl) .'">'. esc_html__('Install Gutenberg Now!', 'advanced-gutenberg') .'</a>'
        );
        exit;
    }

    if (defined('GUTENBERG_VERSION')) {
        if (version_compare(GUTENBERG_VERSION, GUTENBERG_VERSION_REQUIRED, 'lt')) {
            wp_die(
                esc_html__('We require at least Gutenberg version ', 'advanced-gutenberg')
                . esc_html(GUTENBERG_VERSION_REQUIRED) . '. '.
                esc_html__('Please update Gutenberg then comeback later!', 'advanced-gutenberg')
            );
            exit;
        }
    }

    // Get all GB-ADV active profiles
    $args     = array(
        'post_type' => 'advgb_profiles',
        'publish'   => true
    );
    $profiles = new WP_Query($args);

    // Add default profiles if no profiles exist
    if (!$profiles->have_posts()) {
        $post_data = array(
            'post_title'  => 'Default',
            'post_type'   => 'advgb_profiles',
            'post_status' => 'publish',
            'meta_input'  => array(
                'blocks' => array('active_blocks'=>array(), 'inactive_blocks'=>array()),
                'roles_access'  => AdvancedGutenbergMain::$default_roles_access,
                'users_access'  => array(),
            )
        );
        wp_insert_post($post_data, true);
    }

    // Add default settings for first time install
    $saved_settings = get_option('advgb_settings');

    if ($saved_settings === false) {
        update_option('advgb_settings', array(
            'gallery_lightbox' => 1,
            'gallery_lightbox_caption' => 1,
            'blocks_icon_color' => '#5952de',
            'disable_wpautop' => 0,
            'enable_columns_visual_guide' => 1
        ));
    }

    // Add cap to users
    global $wp_roles;

    $wp_roles->add_cap('administrator', 'edit_advgb_profiles');
    $wp_roles->add_cap('administrator', 'edit_others_advgb_profiles');
    $wp_roles->add_cap('administrator', 'create_advgb_profiles');
    $wp_roles->add_cap('administrator', 'publish_advgb_profiles');
    $wp_roles->add_cap('administrator', 'delete_advgb_profiles');
    $wp_roles->add_cap('administrator', 'delete_others_advgb_profiles');
    $wp_roles->add_cap('administrator', 'read_advgb_profile');
    $wp_roles->add_cap('administrator', 'read_private_advgb_profiles');

    $wp_roles->add_cap('editor', 'read_advgb_profile');
    $wp_roles->add_cap('editor', 'read_private_advgb_profiles');
    $wp_roles->add_cap('editor', 'edit_advgb_profiles');

    $wp_roles->add_cap('author', 'edit_advgb_profiles');
    $wp_roles->add_cap('author', 'read_advgb_profile');
    $wp_roles->add_cap('author', 'read_private_advgb_profiles');

    $wp_roles->add_cap('contributor', 'read_advgb_profile');
    $wp_roles->add_cap('contributor', 'read_private_advgb_profiles');

    // Copy default custom styles if no custom styles file exist
    WP_Filesystem();
    global $wp_filesystem;
    $custom_styles_dir = wp_upload_dir();
    $custom_styles_dir = $custom_styles_dir['basedir'] . '/advgb/';
    $css_default_file = plugin_dir_path(__FILE__). 'assets/css/customstyles/custom_styles.css';
    $css_file = $custom_styles_dir . 'custom_styles.css';

    if (!$wp_filesystem->exists($custom_styles_dir)) {
        $wp_filesystem->mkdir($custom_styles_dir);
    }

    if (!$wp_filesystem->exists($css_file)) {
        $wp_filesystem->copy($css_default_file, $css_file);
    }
});

// Run the updates from here
$advgb_current_version = get_option('advgb_version', '0.0.0');
global $wpdb;

if (version_compare($advgb_current_version, '1.6.7', 'lt')) {
    $all_blocks_list = get_option('advgb_blocks_list');
    if (!is_array($all_blocks_list)) {
        $all_blocks_list     = array();
    }

    // Get all GB-ADV active profiles
    $profiles = $wpdb->get_results('SELECT * FROM '. $wpdb->prefix. 'posts
         WHERE post_type="advgb_profiles"');

    if (!empty($profiles)) {
        foreach ($profiles as $profile) {
            $active_blocks_saved = get_post_meta($profile->ID, 'active_blocks', true);
            $isNewProfile = get_post_meta($profile->ID, 'blocks', true);

            // Active all blocks from default profiles
            if (!is_array($active_blocks_saved)) {
                if ($active_blocks_saved === 'all' ||
                    $isNewProfile && isset($isNewProfile['active_blocks']) && count($isNewProfile['active_blocks']) < 1) {
                    update_post_meta($profile->ID, 'blocks', array('active_blocks'   => array(), 'inactive_blocks' => array()));
                    delete_post_meta($profile->ID, 'active_blocks');
                    continue;
                }
            }

            // Check if it already is a new profile, no need to update it
            if ($isNewProfile && isset($isNewProfile['active_blocks'])) {
                continue;
            }

            if (!is_array($active_blocks_saved)) {
                $active_blocks_saved     = array();
            }
            // Rewrite the $all_block_list array to a simple index array with only block name
            $all_blocks = array();
            foreach ($all_blocks_list as $all_block_list) {
                $all_blocks[] = $all_block_list['name'];
            }
            $inactive_blocks = array_diff($all_blocks, $active_blocks_saved);
            $inactive_blocks = array_values($inactive_blocks);

            update_post_meta($profile->ID, 'blocks', array('active_blocks'=>$active_blocks_saved, 'inactive_blocks'=>$inactive_blocks));
            delete_post_meta($profile->ID, 'active_blocks');
        }
    }

    // We don't use it anymore
    delete_option('advgb_categories_list');
}

// Set version if needed
if ($advgb_current_version !== ADVANCED_GUTENBERG_VERSION) {
    update_option('advgb_version', ADVANCED_GUTENBERG_VERSION);
}
