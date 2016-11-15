<?php

/**
 * BP Customizable Group Categories is a highly modified version of BP Groups Taxo,
 * (https://github.com/imath/bp-groups-taxo) centered around categories (instead of tags) 
 * for specific groups
 *
 * @link              http://early-adopter.com/
 * @since             1.0.0
 * @package           Bp_Customizable_Group_Categories
 *
 * @wordpress-plugin
 * Plugin Name:       BP Customizable Group Categories
 * Plugin URI:        https://github.com/livinglab/openlab
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Joe Unander
 * Author URI:        http://early-adopter.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-customizable-group-categories
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Constants
 */
if (!defined('CUSTOCG_BASE_FILE')) {
    define('CUSTOCG_BASE_FILE', __FILE__);
}
if (!defined('CUSTOCG_BASE_DIR')) {
    define('CUSTOCG_BASE_DIR', dirname(CUSTOCG_BASE_FILE));
}
if (!defined('CUSTOCG_PLUGIN_URL')) {
    define('CUSTOCG_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bp-customizable-group-categories-activator.php
 */
function activate_bp_customizable_group_categories() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-bp-customizable-group-categories-activator.php';
    Bp_Customizable_Group_Categories_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bp-customizable-group-categories-deactivator.php
 */
function deactivate_bp_customizable_group_categories() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-bp-customizable-group-categories-deactivator.php';
    Bp_Customizable_Group_Categories_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_bp_customizable_group_categories');
register_deactivation_hook(__FILE__, 'deactivate_bp_customizable_group_categories');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-bp-customizable-group-categories.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_customizable_group_categories() {

    $plugin = new Bp_Customizable_Group_Categories();
    $plugin->run();
}

add_action('bp_loaded', 'run_bp_customizable_group_categories', 1);
