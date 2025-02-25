<?php

/**
 * WordPress plugin "TablePress" main file, responsible for initiating the plugin.
 *
 * @package TablePress
 * @author Tobias Bäthge
 * @version 2.4.1
 *
 *
 * Plugin Name: TablePress (Premium)
 * Plugin URI: https://tablepress.org/
 * Description: Embed beautiful and interactive tables into your WordPress website’s posts and pages, without having to write code!
 * Version: 2.4.1
 * Update URI: https://api.freemius.com
 * Requires at least: 6.0
 * Requires PHP: 7.2
 * Author: Tobias Bäthge
 * Author URI: https://tablepress.org/
 * Author email: wordpress@tobias.baethge.com
 * License: GPL 2
 * Donate URI: https://tablepress.org/donate/
 *
 *
 * Copyright 2012-2024 Tobias Bäthge
 *
 * TablePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as published by
 * the Free Software Foundation.
 *
 * TablePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WordPress. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 *
 * Note: This file must not contain PHP code that does not run on PHP < 7.2!
 *
 * @fs_premium_only /modules/
 * @fs_ignore /libraries/
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
if ( !defined( 'TABLEPRESS_IS_PLAYGROUND_PREVIEW' ) ) {
    define( 'TABLEPRESS_IS_PLAYGROUND_PREVIEW', false );
}
if ( function_exists( 'tb_tp_fs' ) ) {
    tb_tp_fs()->set_basename( true, __FILE__ );
    // @phpstan-ignore-line (Wrong variable type in Freemius function docblock.)
} else {
    /**
     * Helper function for easier Freemius SDK access.
     *
     * @since 2.0.0
     *
     * @return Freemius Freemius SDK instance.
     */
    function tb_tp_fs() {
        global $tb_tp_fs;
        if ( !isset( $tb_tp_fs ) ) {
            // Include Freemius SDK.
            require_once __DIR__ . '/libraries/freemius/start.php';
            $tb_tp_fs = fs_dynamic_init( array(
                'id'                => '10340',
                'slug'              => 'tablepress',
                'type'              => 'plugin',
                'public_key'        => 'pk_b215ca1bb4041cf43ed137ae7665b',
                'is_premium'        => true,
                'has_addons'        => false,
                'has_paid_plans'    => true,
                'menu'              => array(
                    'slug'    => 'tablepress',
                    'contact' => false,
                    'support' => false,
                    'pricing' => false,
                ),
                'opt_in_moderation' => array(
                    'new'       => true,
                    'updates'   => false,
                    'localhost' => false,
                ),
                'anonymous_mode'    => TABLEPRESS_IS_PLAYGROUND_PREVIEW,
                'is_live'           => true,
            ) );
        }
        return $tb_tp_fs;
    }

    // Init Freemius.
    tb_tp_fs();
    // Load the TablePress plugin icon for the Freemius opt-in/activation screen.
    tb_tp_fs()->add_filter( 'plugin_icon', static function () {
        return __DIR__ . '/admin/img/tablepress.png';
    } );
    // Hide the tabs navigation on Freemius screens.
    tb_tp_fs()->add_filter( 'hide_account_tabs', '__return_true' );
    // Hide the Powered by Freemius tab from generated pages, like "Upgrade" or "Pricing".
    tb_tp_fs()->add_filter( 'hide_freemius_powered_by', '__return_true' );
    // Use different arrow icons in the admin menu.
    tb_tp_fs()->override_i18n( array(
        'symbol_arrow-left'  => '&larr;',
        'symbol_arrow-right' => '&rarr;',
    ) );
    // Signal that the SDK was initiated.
    do_action( 'tb_tp_fs_loaded' );
    /*
     * Define certain plugin variables as constants.
     */
    if ( !defined( 'TABLEPRESS_ABSPATH' ) ) {
        define( 'TABLEPRESS_ABSPATH', trailingslashit( __DIR__ ) );
    }
    if ( !defined( 'TABLEPRESS__FILE__' ) ) {
        define( 'TABLEPRESS__FILE__', __FILE__ );
    }
    if ( !defined( 'TABLEPRESS_BASENAME' ) ) {
        define( 'TABLEPRESS_BASENAME', plugin_basename( TABLEPRESS__FILE__ ) );
    }
    if ( !defined( 'TABLEPRESS_JSON_OPTIONS' ) ) {
        // JSON_UNESCAPED_SLASHES: Don't escape slashes, e.g. to make search/replace of URLs in the database easier.
        define( 'TABLEPRESS_JSON_OPTIONS', JSON_UNESCAPED_SLASHES );
    }
    /*
     * Check if the site environment fulfills the minimum requirements.
     */
    if ( !(require_once TABLEPRESS_ABSPATH . 'controllers/environment-checks.php') ) {
        return;
        // Exit early if the return value from the file is false.
    }
    /*
     * Load TablePress class, which holds common functions and variables.
     */
    require_once TABLEPRESS_ABSPATH . 'classes/class-tablepress.php';
    /*
     * Load TablePress premium modules.
     */
    if ( tb_tp_fs()->is__premium_only() ) {
        TablePress::load_class( 'TablePress_Modules_Loader', 'class-modules-loader.php', 'modules/classes' );
    }
    /*
     * Start up TablePress on WordPress's "init" action hook.
     */
    add_action( 'init', array('TablePress', 'run') );
}