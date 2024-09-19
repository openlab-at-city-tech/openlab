<?php
/**
 * Plugin Name: Name Directory
 * Plugin URI: https://jeroenpeters.dev/wordpress-plugin-name-directory/
 * Description: A Name Directory, i.e. for animal names or to create a glossary. Visitors can add, search or just browse all names.
 * Version: 1.29.1
 * Author: Jeroen Peters
 * Author URI: https://jeroenpeters.dev
 * Text Domain: name-directory
 * Domain Path: /translation
 * License: GPL2
 */
/*  Copyright 2013-2024  Jeroen Peters (email: jeroenpeters1986@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Make sure we don't expose any info if called directly
if (! function_exists('add_action'))
{
    echo 'Nothing to see here. Move along now people.';
    exit;
}

global $wpdb;

global $name_directory_db_version;
$name_directory_db_version = '1.29.1';

global $name_directory_table_directory;
$name_directory_table_directory = $wpdb->prefix . "name_directory";

global $name_directory_table_directory_name;
$name_directory_table_directory_name = $wpdb->prefix . "name_directory_name";


/* The helpers and the shortcode are responsible for everything that happens on the frontend */
require_once dirname( __FILE__ ) . '/helpers.php';
require_once dirname( __FILE__ ) . '/shortcode.php';


/**
 * Database update check, run provisioning whenever the DB version has a mismatch
 */
function name_directory_post_update()
{
    global $name_directory_db_version;

    /* Update the database if there is a new version */
    if (get_option('name_directory_db_version') != $name_directory_db_version)
    {
        name_directory_db_tables();
        name_directory_db_post_update_actions();

        if(! name_directory_is_multibyte_supported())
        {
            add_action('admin_notices', 'name_directory_notice_mb_string_not_installed');
        }
    }
}


/**
 * Register a capability for the popular Members plugin
 */
function name_directory_register_capabilities()
{
    /* This should not been called without the Members plugin, but better safe than sorry */
    if ( function_exists( 'members_register_cap' ) ) {
        members_register_cap(
            'manage_name_directory',
            array(
                'label' => __('Can manage Name Directory', 'name-directory'),
            )
        );
    }
}
add_action( 'members_register_caps', 'name_directory_register_capabilities' );


/**
 * We only need admin functionality and database setup when we are in the WP-admin
 */
if ( is_admin() )
{
    require_once dirname( __FILE__ ) . '/admin.php';
    require_once dirname( __FILE__ ) . '/admin_general_settings.php';
    require_once dirname( __FILE__ ) . '/database.php';

    /* These register_activation_hooks run after install */
    register_activation_hook( __FILE__, 'name_directory_db_tables' );
    register_activation_hook( __FILE__, 'name_directory_db_install_demo_data' );

    /* This hook is for updates */
    add_action( 'plugins_loaded', 'name_directory_post_update' );
}


/**
 * Initialize the plugin
 * Ready.. set.. go!
 */
function name_directory_init()
{
    $plugin_dir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('name-directory', false, $plugin_dir . '/translation/');
}
add_action('plugins_loaded', 'name_directory_init');
