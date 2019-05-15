<?php
/**
 * Plugin Name: Genesis Responsive Slider
 * Plugin URI: https://www.studiopress.com
 * Description: A responsive featured slider for the Genesis Framework.
 * Version: 1.0.0
 * Author: StudioPress
 * Author URI: https://www.studiopress.com
 * License: GNU General Public License v2.0 (or later)
 * License URI: https://www.opensource.org/licenses/gpl-license.php

 * Text Domain: genesis-responsive-slider
 * Domain Path: /languages
 *
 * @package genesis-responsive-slider
 */

/**
 * Props to Rafal Tomal, Nick Croft, Nathan Rice, Ron Rennick, Josh Byers and Brian Gardner for collaboratively writing this plugin.
 */

/**
 * Thanks to Tyler Smith for creating the awesome jquery FlexSlider plugin - http://flex.madebymufffin.com/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD', 'genesis_responsive_slider_settings' );
define( 'GENESIS_RESPONSIVE_SLIDER_VERSION', '1.0.0' );
define( 'GENESIS_RESPONSIVE_SLIDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GENESIS_RESPONSIVE_SLIDER_PLUGIN_URL', plugins_url( '', __FILE__ ) );


require_once GENESIS_RESPONSIVE_SLIDER_PLUGIN_DIR . '/includes/class-genesis-responsive-slider-widget.php';
require_once GENESIS_RESPONSIVE_SLIDER_PLUGIN_DIR . '/includes/class-genesis-responsive-slider.php';

register_uninstall_hook( __FILE__, array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_plugin_uninstall' ) );

add_action( 'after_setup_theme', array( 'Genesis_Responsive_Slider', 'init' ), 15 );

/** Include Admin file */
if ( is_admin() ) {
	require_once GENESIS_RESPONSIVE_SLIDER_PLUGIN_DIR . '/includes/class-genesis-responsive-slider-admin.php';

	add_action( 'init', array( 'Genesis_Responsive_Slider_Admin', 'init' ) );
}
