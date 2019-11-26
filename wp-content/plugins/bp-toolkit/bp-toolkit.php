<?php

/**
*
* @link              https://www.therealbenroberts.com
* @since             1.0.0
* @package           BP_Toolkit
*
* @wordpress-plugin
* Plugin Name:       Block, Suspend, Report for BuddyPress
* Plugin URI:        https://www.therealbenroberts.com/plugins
* Description:       Block, Suspend, Report for BuddyPress provides enhanced moderation for your BuddyPress site.
* Version:           1.0.5
* Author:            Ben Roberts
* Author URI:        https://www.therealbenroberts.com
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       bp-toolkit
* Domain Path:       /languages
*
*
*/
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'bptk_fs' ) ) {
    bptk_fs()->set_basename( false, __FILE__ );
    return;
}


if ( !function_exists( 'bptk_fs' ) ) {
    // Create a helper function for easy SDK access.
    function bptk_fs()
    {
        global  $bptk_fs ;
        
        if ( !isset( $bptk_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $bptk_fs = fs_dynamic_init( array(
                'id'             => '3579',
                'slug'           => 'bp-toolkit',
                'premium_slug'   => 'bp-toolkit-pro',
                'type'           => 'plugin',
                'public_key'     => 'pk_26445578ae5fe6e0fe33c04760789',
                'is_premium'     => false,
                'premium_suffix' => '(Pro)',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 14,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'    => 'bp-toolkit',
                'contact' => true,
                'support' => false,
                'parent'  => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $bptk_fs;
    }
    
    // Init Freemius.
    bptk_fs();
    // Signal that SDK was initiated.
    do_action( 'bptk_fs_loaded' );
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BP_TOOLKIT_VERSION', '1.0.5' );
/**
 * Define the capability for administrators.
 */
define( 'BPTK_ADMIN_CAP', 'edit_users' );
/**
 * Define the folder for our templates.
 */
define( 'BPTK_TEMPLATES', plugin_dir_path( __FILE__ ) . '/templates/' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bp-toolkit-activator.php
 */
function activate_bp_toolkit()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-toolkit-activator.php';
    BP_Toolkit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bp-toolkit-deactivator.php
 */
function deactivate_bp_toolkit()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-toolkit-deactivator.php';
    BP_Toolkit_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 */
function uninstall_bp_toolkit()
{
    bptk_fs()->add_action( 'after_uninstall', 'bptk_fs_uninstall_cleanup' );
}

register_uninstall_hook( __FILE__, 'uninstall_bp_toolkit' );
register_activation_hook( __FILE__, 'activate_bp_toolkit' );
register_deactivation_hook( __FILE__, 'deactivate_bp_toolkit' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bp-toolkit.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_toolkit()
{
    $plugin = new BP_Toolkit();
    $plugin->run();
    $plugin->bptk_init();
}

add_action( 'bp_include', 'run_bp_toolkit' );