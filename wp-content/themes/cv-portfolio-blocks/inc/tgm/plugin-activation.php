<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme Skincare Product Store for publication on WordPress.org
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */
require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'cv_portfolio_blocks_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function cv_portfolio_blocks_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'      => esc_html__( 'WordClever – AI Content Writer', 'cv-portfolio-blocks' ),
			'slug'      => 'wordclever-ai-content-writer',
			'required'  => false,
		)
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'cv-portfolio-blocks',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}


// WordClever – AI Content Writer plugin activation
add_action('wp_ajax_install_and_activate_wordclever_plugin', 'install_and_activate_wordclever_plugin');

function install_and_activate_wordclever_plugin() {
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'install_activate_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed.']);
    }

    // Define plugin slugs and file paths
    $cv_portfolio_blocks_woocommerce_slug = 'woocommerce';
    $cv_portfolio_blocks_woocommerce_file = 'woocommerce/woocommerce.php';
    $cv_portfolio_blocks_woocommerce_url  = 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip';

    $cv_portfolio_blocks_wordclever_slug = 'wordclever-ai-content-writer';
    $cv_portfolio_blocks_wordclever_file = 'wordclever-ai-content-writer/wordclever.php';
    $cv_portfolio_blocks_wordclever_url  = 'https://downloads.wordpress.org/plugin/wordclever-ai-content-writer.latest-stable.zip';

    // Include necessary WordPress files
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/misc.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    $cv_portfolio_blocks_upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());

    // Step 1: Install and activate WooCommerce if not active
    if (!is_plugin_active($cv_portfolio_blocks_woocommerce_file)) {
        $cv_portfolio_blocks_installed_plugins = get_plugins();

        if (!isset($cv_portfolio_blocks_installed_plugins[$cv_portfolio_blocks_woocommerce_file])) {
            // Install WooCommerce
            $cv_portfolio_blocks_install_wc = $cv_portfolio_blocks_upgrader->install($cv_portfolio_blocks_woocommerce_url);
            if (is_wp_error($cv_portfolio_blocks_install_wc)) {
                wp_send_json_error(['message' => 'WooCommerce installation failed.']);
            }
        }

        // Activate WooCommerce
        $cv_portfolio_blocks_activate_wc = activate_plugin($cv_portfolio_blocks_woocommerce_file);
        if (is_wp_error($cv_portfolio_blocks_activate_wc)) {
            wp_send_json_error(['message' => 'WooCommerce activation failed.', 'error' => $cv_portfolio_blocks_activate_wc->get_error_message()]);
        }
    }

    // Step 2: Install and activate WordClever plugin
    if (!is_plugin_active($cv_portfolio_blocks_wordclever_file)) {
        $cv_portfolio_blocks_installed_plugins = get_plugins();

        if (!isset($cv_portfolio_blocks_installed_plugins[$cv_portfolio_blocks_wordclever_file])) {
            // Install WordClever plugin
            $cv_portfolio_blocks_install_wc_plugin = $cv_portfolio_blocks_upgrader->install($cv_portfolio_blocks_wordclever_url);
            if (is_wp_error($cv_portfolio_blocks_install_wc_plugin)) {
                wp_send_json_error(['message' => 'WordClever plugin installation failed.']);
            }
        }

        // Activate WordClever plugin
        $cv_portfolio_blocks_activate_wc_plugin = activate_plugin($cv_portfolio_blocks_wordclever_file);
        if (is_wp_error($cv_portfolio_blocks_activate_wc_plugin)) {
            wp_send_json_error(['message' => 'WordClever plugin activation failed.', 'error' => $cv_portfolio_blocks_activate_wc_plugin->get_error_message()]);
        }
    }

    // Success response
    wp_send_json_success(['message' => 'WooCommerce and WordClever plugins are activated successfully.']);
}


