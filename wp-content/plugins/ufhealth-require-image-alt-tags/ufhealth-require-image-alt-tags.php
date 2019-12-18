<?php
/**
 * Plugin Name: UF Health Require Image Alt Tags
 * Description: Forces users to add an ALT tag when adding images to WordPress posts and more.
 * Version: 1.2
 * Text Domain: ufhealth-require-image-alt-tags
 * Domain Path: /languages
 * Author: UF Health
 * Author URI: http://webservices.ufhealth.org
 * License: GPLv2
 *
 * @package UFHealth\require_image_alt_tags
 */

define( 'UFHEALTH_REQUIRE_IMAGE_ALT_TAGS_VERSION', '1.2' );
define( 'UFHEALTH_REQUIRE_IMAGE_ALT_TAGS_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', 'ufhealth_require_image_alt_tags_loader' );

/**
 * Load plugin functionality.
 */
function ufhealth_require_image_alt_tags_loader() {

	// Remember the text domain.
	load_plugin_textdomain( 'ufhealth-require-image-alt-tags', false, dirname( dirname( __FILE__ ) ) . '/languages' );

	require dirname( __FILE__ ) . '/includes/require-alt-tags.php';

}
