<?php

/**
 * Boot check
 *
 * @package WP Link Status
 * @subpackage Core
 */

// Check class or constants conflict relative to other active plugins
if (class_exists('WPLNST_Core_Alive') || defined('WPLNST_VERSION') || defined('WPLNST_FILE') || defined('WPLNST_PATH')) {

	// Prepare message
	$wplnst_message = '<h1>Multiple Versions Detected</h1>
	<p>This plugin cannot be activated because there is a previous version activated.</p>
	<p>Please <strong>deactivate any other WP Link Status plugin</strong> in order to activate this version.</p>
	<p>Sorry for the inconvenience.</p>';

	// Check go back link
	if (function_exists('admin_url')) {
		$wplnst_message .= "\n".'<p><a href="'.esc_url(admin_url('plugins.php')).'">&larr; Go back</a></p>';
	}

	// No execution allowed
	wp_die($wplnst_message);

// Check WP version via checking version expected function, because $wp_version may have been overwritten
} elseif (function_exists('add_action') && !function_exists('is_main_query')) {

	// No compatible WP version
	trigger_error(__('Sorry, this version of WP Link Status requires WordPress 3.3 or later.', 'wplnst'), E_USER_ERROR);
}
