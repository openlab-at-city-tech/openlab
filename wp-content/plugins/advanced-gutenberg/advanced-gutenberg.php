<?php
/**
 * Plugin Name: PublishPress Blocks
 * Plugin URI: https://publishpress.com/blocks/
 * Description: Enhanced tools for Gutenberg editor
 * Version: 2.5.6
 * Tested up to: 5.6.2
 * Author: PublishPress
 * Author URI: https://publishpress.com/
 * License: GPL2
 * Text Domain: advanced-gutenberg
 * Domain Path: /languages
 */

/**
 * Copyright
 *
 * @copyright 2014-2020  Joomunited
 * @copyright 2020       Advanced Gutenberg. help@advancedgutenberg.com
 * @copyright 2020-2021  PublishPress. help@publishpress.com
 *
 *  Original development of this plugin was kindly funded by Joomunited
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') || die;

//Check plugin requirements
if (version_compare(PHP_VERSION, '5.6.20', '<')) {
    if (! function_exists('advgb_disable_plugin')) {
        /**
         * Disable plugin
         *
         * @return void
         */
        function advgb_disable_plugin()
        {
            if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(__FILE__);
                unset($_GET['activate']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
            }
        }
    }

    if (! function_exists('advgb_show_error')) {
        /**
         * Show error
         *
         * @return void
         */
        function advgb_show_error()
        {
            echo '<div class="error"><p><strong>PublishPress Blocks</strong> needs at least PHP 5.6.20 version, please update php before installing the plugin.</p></div>';
        }
    }

    //Add actions
    add_action('admin_init', 'advgb_disable_plugin');
    add_action('admin_notices', 'advgb_show_error');

    //Do not load anything more
    return;
}

if (! defined('ADVANCED_GUTENBERG_VERSION')) {
    define('ADVANCED_GUTENBERG_VERSION', '2.5.6');
}

if (! defined('ADVANCED_GUTENBERG_PLUGIN')) {
    define('ADVANCED_GUTENBERG_PLUGIN', __FILE__);
}

if (!defined('GUTENBERG_VERSION_REQUIRED')) {
    define('GUTENBERG_VERSION_REQUIRED', '5.7.0');
}

require_once(plugin_dir_path(__FILE__) . '/install.php');
require_once(plugin_dir_path(__FILE__) . '/incl/advanced-gutenberg-main.php');
new AdvancedGutenbergMain();

if (! function_exists('advg_language_domain_init')) {
    /**
     * Load language translations
     *
     * @return void
     */
    function advg_language_domain_init()
    {
        // First, unload textdomain - Based on https://core.trac.wordpress.org/ticket/34213#comment:26
        unload_textdomain('advanced-gutenberg');

        // Load override language file first if available from version 2.3.11 and older
        if (file_exists(WP_LANG_DIR . '/plugins/' . 'advanced-gutenberg' . '-' . get_locale() . '.override.mo')) {
            load_textdomain(
                'advanced-gutenberg',
                WP_LANG_DIR . '/plugins/' . 'advanced-gutenberg' . '-' . get_locale() . '.override.mo'
            );
        }

        // Call the core translations from plugins languages/ folder
        if (file_exists(plugin_dir_path(__FILE__) . 'languages/' . 'advanced-gutenberg' . '-' . get_locale() . '.mo')) {
            load_textdomain(
                'advanced-gutenberg',
                plugin_dir_path(__FILE__) . 'languages/' . 'advanced-gutenberg' . '-' . get_locale() . '.mo'
            );
        }

        wp_set_script_translations(
            'editor',
            'advanced-gutenberg',
            plugin_dir_path( __FILE__ ) . 'languages'
        );
    }
}
add_action( 'init', 'advg_language_domain_init' );

// Include jufeedback helpers
require_once('jufeedback'. DIRECTORY_SEPARATOR . 'jufeedback.php');
call_user_func(
    '\Joomunited\ADVGB\Jufeedback\Jufeedback::init',
    __FILE__,
    'advgb',
    'advanced-gutenberg',
    'PublishPress Blocks',
    'advanced-gutenberg'
);
