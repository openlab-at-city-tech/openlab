<?php

/**
 * Util functions
 *
 * @package WP Link Status
 * @subpackage Core
 */



// Load dependencies
require_once dirname(__FILE__).'/debug.php';
require_once dirname(__FILE__).'/settings.php';



/**
 * Requires a plugin file
 */
function wplnst_require($section, $filename) {
	require_once(WPLNST_PATH.'/'.$section.'/'.$filename.'.php');
}



/**
 * Requires multiple files for the same section
 */
function wplnst_require_section($section, $filenames) {
	foreach ($filenames as $filename) {
		wplnst_require($section, $filename);
	}
}



/**
 * Return a numeric setting
 */
function wplnst_get_nsetting($name, $value = 0) {
	return WPLNST_Core_Settings::get_nsetting($name, $value);
}



/**
 * Return a boolean setting
 */
function wplnst_get_bsetting($name, $default = false) {
	return WPLNST_Core_Settings::get_bsetting($name, $default);
}



/**
 * Return a text setting
 */
function wplnst_get_tsetting($name, $default = true) {
	return WPLNST_Core_Settings::get_tsetting($name, $default);
}



/**
 * Check if cURL is enabled in this system
 */
function wplnst_is_curl_enabled() {

	// Last status
	static $is_enabled;
	if (isset($is_enabled)) {
		return $is_enabled;
	}

	// Simple check, but it may have been overwritten
	if (!function_exists('curl_version')) {
		$is_enabled = false;
		return false;
	}

	// Check extension
	$extensions = @get_loaded_extensions();
	if (!empty($extensions) && is_array($extensions) && in_array('curl', $extensions)) {
		$is_enabled = true;
		return true;
	}

	// Not found
	$is_enabled = false;
	return false;
}