<?php

/**
 * WP Link Status Core Boot check
 *
 * @package WP Link Status
 * @subpackage WP Link Status Core
 */

// Check class or constants conflict relative to other active plugins
if (class_exists('WPLNST_Core_Alive') || defined('WPLNST_VERSION') || defined('WPLNST_FILE') || defined('WPLNST_PATH')) {
	
	// No execution allowed
	trigger_error(__('Detected another version of WP Link Status already active. Please deactivate it before and try again to activate this plugin.', 'wplnst'), E_USER_ERROR);

// Check WP version via checking version expected function, because $wp_version may have been overwritten
} elseif (function_exists('add_action') && !function_exists('is_main_query')) {
	
	// No compatible WP version
	trigger_error(__('Sorry, this version of WP Link Status requires WordPress 3.3 or later.', 'wplnst'), E_USER_ERROR);
}
