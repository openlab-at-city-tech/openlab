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

    // Add default settings for first time install
    $saved_settings = get_option('advgb_settings');

    if ($saved_settings === false) {
        update_option('advgb_settings', array(
            'gallery_lightbox' => 1,
            'gallery_lightbox_caption' => '1',
            'blocks_icon_color' => '#655997',
            'disable_wpautop' => 0,
            'enable_columns_visual_guide' => 1,
            'enable_block_access' => 1,
            'enable_custom_styles' => 1,
            'enable_advgb_blocks' => 1,
            'enable_pp_branding' => 1,
            'enable_core_blocks_features' => 1,
            'block_controls' => 1,
            'block_extend' => 0,
            'reusable_blocks' => 1
        ));
    }

    // Delete deprecated options
    delete_option( 'advgb_jureview_installation_time' );
    delete_option( 'advgb_jufeedback_version' );
    delete_option( 'ppb_reviews_installed_on' ); // Added in 2.10.4 and disabled in 2.10.5
    delete_option( 'advgb_reviews_installed_on' ); // Added in 2.11.0 and disabled in 2.11.1
});



if ( !function_exists('advgb_some_specific_updates') ) {

    function advgb_some_specific_updates() {

        // Run the updates from here
        $advgb_current_version = get_option('advgb_version', '0.0.0');
        global $wpdb;

        // Migrate to Block Access by User Roles
        if( version_compare($advgb_current_version, '2.10.2', 'lt') && !get_option('advgb_blocks_user_roles') ) {

            // Migrate Block Access Profiles to Block Access by Roles
            global $wpdb;
            $profiles = $wpdb->get_results(
                'SELECT * FROM '. $wpdb->prefix. 'posts
                WHERE post_type="advgb_profiles" AND post_status="publish" ORDER BY post_date_gmt DESC'
            );

            if( !empty( $profiles ) ) {

                // Let's extract the user roles associated to Block Access profiles (we can't get all the user roles with regular WP way)
                $user_role_accesses = array();
                foreach ($profiles as $profile) {
                    $postID                 = $profile->ID;
                    $user_role_accesses[]   = get_post_meta( $postID, 'roles_access', true );
                }

                $user_role_accesses = call_user_func_array( 'array_merge', $user_role_accesses );
                $user_role_accesses = array_unique( $user_role_accesses );

                // Find the most recent profile of each user role
                $blocks_by_role_access = array();
                foreach( $user_role_accesses as $user_role_access ) {

                    $profiles = $wpdb->get_results(
                        'SELECT * FROM '. $wpdb->prefix. 'posts
                        WHERE post_type="advgb_profiles" AND post_status="publish" ORDER BY post_date_gmt DESC'
                    );

                    if( !empty( $profiles ) ) {
                        $centinel[$user_role_access] = false; // A boolean to get the first profile (newest) and skip the rest
                        foreach ($profiles as $profile) {
                            if( $centinel[$user_role_access] === false ) {
                                $postID         = $profile->ID;
                                $roles_access   = get_post_meta( $postID, 'roles_access', true );
                                $blocks         = get_post_meta( $postID, 'blocks', true );

                                if( in_array( $user_role_access, $roles_access ) ) {
                                    $blocks_by_role_access[$user_role_access] = $blocks;
                                    $centinel[$user_role_access] = true;
                                }
                            }
                        }
                    }
                }

                // Migrate Block Access by Profile to Block Access by Role
                if( $blocks_by_role_access ) {
                    update_option( 'advgb_blocks_user_roles', $blocks_by_role_access, false );
                }
            }
        }

        // Set version if needed
        if ($advgb_current_version !== ADVANCED_GUTENBERG_VERSION) {
            update_option('advgb_version', ADVANCED_GUTENBERG_VERSION);
        }
    }
    add_action('init', 'advgb_some_specific_updates');
}
