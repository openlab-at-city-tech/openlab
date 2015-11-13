<?php

/**
 * @package WP Simple Anchors Links
 * @link http://www.kilukrumedia.com
 * @copyright Copyright &copy; 2014, Kilukru Media
 * @version: 1.0.0
 */

if (!function_exists('wpsimpleanchorslinks_activate')) {
	function wpsimpleanchorslinks_activate() {
	  global $wpsimpleanchorslinks_activation;
	  $wpsimpleanchorslinks_activation = true;
	}
}

if (!function_exists('_print_r')) {
	function _print_r( $var ) {
		echo '<pre>';
		print_r( $var );
		echo '</pre>';
	}
}

if (!function_exists('wpsimpleanchorslinks_update_settings_check')) {
	function wpsimpleanchorslinks_update_settings_check() {

		//Set migrate function @todo
		//if(isset($_POST['wpsimpleanchorslinks_migrate'])) wpsimpleanchorslinks_migrate();

		//Set migrate options function @todo
		//if ( ( isset( $_POST['wpsimpleanchorslinks_migrate_options'] ) )  ||
		//	 ( !get_option('wpsimpleanchorslinks_options') ) ) {
		//}
	}
}

if (!function_exists('wpsimpleanchorslinks_class_defined_error')) {
	function wpsimpleanchorslinks_class_defined_error() {
		$wpsimpleanchorslinks_class_error = "The WP Simple Anchors Links class is already defined";
		if ( class_exists( 'ReflectionClass' ) ) {
			$r = new ReflectionClass( 'WP_Simple_Anchors_Links' );
			$wpsimpleanchorslinks_class_error .= " in " . $r->getFileName();
		}
		$wpsimpleanchorslinks_class_error .= ", preventing WP Simple Anchors Links from loading.";
		echo wpsimpleanchorslinks_show_essage($wpsimpleanchorslinks_class_error, true);
	}
}

if ( ! function_exists( 'shortcode_exists' ) ){
/**
 * Check if a shortcode is registered in WordPress.
 *
 * Examples: shortcode_exists( 'caption' ) - will return true.
 *           shortcode_exists( 'blah' )    - will return false.
 */
function shortcode_exists( $shortcode = false ) {
	global $shortcode_tags;

	if ( ! $shortcode )
		return false;

	if ( array_key_exists( $shortcode, $shortcode_tags ) )
		return true;

	return false;
}
}

