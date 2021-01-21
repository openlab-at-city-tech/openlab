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
                'blocks' => array('active_blocks'=>array(), 'inactive_blocks'=>array('advgb/container')),
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
});

// Run the updates from here
$advgb_current_version = get_option('advgb_version', '0.0.0');
global $wpdb;

if (version_compare($advgb_current_version, '2.0.6', 'lt')) {
    // Get all GB-ADV active profiles
    $profiles = $wpdb->get_results('SELECT * FROM '. $wpdb->prefix. 'posts
         WHERE post_type="advgb_profiles"');

    if (!empty($profiles)) {
        foreach ($profiles as $profile) {
            $blocks_saved = get_post_meta($profile->ID, 'blocks', true);

            if (!is_array($blocks_saved)) {
                continue;
            }

            // Remove Container block from profile
            $key = array_search('advgb/container', $blocks_saved['active_blocks']);
            if ($key !== false) {
                unset($blocks_saved['active_blocks'][$key]);
            }

            $keyIA = array_search('advgb/container', $blocks_saved['inactive_blocks']);
            if ($keyIA === false) {
                array_push($blocks_saved['inactive_blocks'], 'advgb/container');
            }

            update_post_meta($profile->ID, 'blocks', $blocks_saved);
        }
    }
}

// Set version if needed
if ($advgb_current_version !== ADVANCED_GUTENBERG_VERSION) {
    update_option('advgb_version', ADVANCED_GUTENBERG_VERSION);
}

// Delete custom_styles.css if exists (created in 2.4.4 and older)
require_once ABSPATH . 'wp-admin/includes/file.php';

WP_Filesystem();
global $wp_filesystem;
$custom_styles_dir  = wp_upload_dir();
$custom_styles_file = $custom_styles_dir['basedir'] . '/advgb/custom_styles.css';

if ($wp_filesystem->exists($custom_styles_file)) {
    $wp_filesystem->delete($custom_styles_file);
}