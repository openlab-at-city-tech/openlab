<?php

/**
 *
 * @link              https://www.bouncingsprout.com
 * @since             1.0.0
 * @package           BP_Toolkit
 *
 * @wordpress-plugin
 * Plugin Name:       Block, Suspend, Report for BuddyPress
 * Plugin URI:        https://www.bouncingsprout.com/plugins/block-suspend-report-for-buddypress/
 * Description:       Block, Suspend, Report for BuddyPress provides enhanced moderation for your BuddyPress site.
 * Version:           3.6.1
 * Author:            Bouncingsprout Studio
 * Author URI:        https://www.bouncingsprout.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-toolkit
 * Domain Path:       /languages/
 *
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'bptk_fs' ) ) {
    bptk_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'bptk_fs' ) ) {
        // Create a helper function for easy SDK access.
        function bptk_fs()
        {
            global  $bptk_fs ;
            
            if ( !isset( $bptk_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $bptk_fs = fs_dynamic_init( array(
                    'id'              => '3579',
                    'slug'            => 'bp-toolkit',
                    'premium_slug'    => 'bp-toolkit-pro',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_26445578ae5fe6e0fe33c04760789',
                    'is_premium'      => false,
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                    'days'               => 14,
                    'is_require_payment' => true,
                ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                    'slug'        => 'bp-toolkit',
                    'first-path'  => 'plugins.php',
                    'support'     => false,
                    'affiliation' => false,
                ),
                    'is_live'         => true,
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
    define( 'BP_TOOLKIT_VERSION', '3.6.1' );
    /**
     * Define a constant to hold our plugin page
     */
    define( "BP_TOOLKIT_HOMEPAGE", "https://www.bouncingsprout.com/plugins/block-suspend-report-for-buddypress/" );
    /**
     * Define a constant to hold our support page
     */
    define( "BP_TOOLKIT_SUPPORT", "https://www.bouncingsprout.com/support/block-suspend-report-for-buddypress/" );
    /**
     * Define the capability for administrators.
     */
    define( 'BPTK_ADMIN_CAP', 'manage_options' );
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
        
        if ( class_exists( 'BuddyPress' ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-toolkit-activator.php';
            BP_Toolkit_Activator::activate();
        } else {
            add_action( 'admin_notices', 'deactivated_buddypress_error' );
        }
    
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
    
    bptk_fs()->add_filter( 'show_affiliate_program_notice', '__return_false' );
    register_uninstall_hook( __FILE__, 'uninstall_bp_toolkit' );
    register_activation_hook( __FILE__, 'activate_bp_toolkit' );
    register_deactivation_hook( __FILE__, 'deactivate_bp_toolkit' );
    include plugin_dir_path( __FILE__ ) . 'includes/bp-toolkit-general-functions.php';
    include plugin_dir_path( __FILE__ ) . 'includes/bp-toolkit-automod-functions.php';
    include plugin_dir_path( __FILE__ ) . 'includes/bp-toolkit-email-functions.php';
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
        
        if ( class_exists( 'BuddyPress' ) ) {
            $plugin = new BP_Toolkit();
            $plugin->run();
            $plugin->bptk_init();
        } else {
            add_action( 'admin_notices', 'deactivated_buddypress_error' );
        }
    
    }
    
    add_action( 'init', 'run_bp_toolkit' );
}

function deactivated_buddypress_error()
{
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php 
    _e( 'Block, Suspend, Report for BuddyPress requires BuddyPress or BuddyBoss to run. Please ensure it is activated.', 'bp-toolkit' );
    ?></p>
    </div>
	<?php 
}
