<?php

/**
 * Plugin Name: Shortcodes Ultimate
 * Plugin URI: https://getshortcodes.com/
 * Author: Vova Anokhin
 * Author URI: https://getshortcodes.com/
 * Description: Create tabs, accordions, buttons, sliders, boxes and reusable content elements with 50+ WordPress shortcodes
 * Text Domain: shortcodes-ultimate
 * Domain Path: /languages
 * License: GPLv3
 * Version: 7.9.2
 * Requires PHP: 7.0
 * Requires at least: 6.0
 * Tested up to: 7.1
 *
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'su_fs' ) ) {
    su_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'su_fs' ) ) {
        if ( !function_exists( 'su_fs' ) ) {
            // Create a helper function for easy SDK access.
            function su_fs() {
                global $su_fs;
                if ( !isset( $su_fs ) ) {
                    // Include Freemius SDK.
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                    $su_fs = fs_dynamic_init( array(
                        'id'                => '7180',
                        'slug'              => 'shortcodes-ultimate',
                        'premium_slug'      => 'shortcodes-ultimate-pro',
                        'type'              => 'plugin',
                        'public_key'        => 'pk_c9ecad02df10f17e67880ac6bd8fc',
                        'is_premium'        => false,
                        'premium_suffix'    => 'Pro',
                        'has_addons'        => false,
                        'has_paid_plans'    => true,
                        'menu'              => array(
                            'slug'       => 'shortcodes-ultimate',
                            'first-path' => 'admin.php?page=shortcodes-ultimate',
                            'contact'    => false,
                            'support'    => false,
                        ),
                        'opt_in_moderation' => array(
                            'new'       => 100,
                            'updates'   => 0,
                            'localhost' => true,
                        ),
                        'is_live'           => true,
                        'is_org_compliant'  => true,
                    ) );
                }
                return $su_fs;
            }

            // Init Freemius.
            su_fs();
            // Signal that SDK was initiated.
            do_action( 'su_fs_loaded' );
        }
    }
    define( 'SU_PLUGIN_FILE', __FILE__ );
    define( 'SU_PLUGIN_VERSION', '7.9.2' );
    require_once dirname( __FILE__ ) . '/plugin.php';
}