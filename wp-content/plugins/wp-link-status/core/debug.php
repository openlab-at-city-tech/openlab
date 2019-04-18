<?php

/**
 * Debug functions
 *
 * @package WP Link Status
 * @subpackage Core
 */



// Load dependencies
require_once dirname(dirname(__FILE__)).'/constants.php';



/**
 * Check debug flag
 */
function wplnst_is_debug() {
	return defined('WPLNST_DEBUG') && WPLNST_DEBUG;
}



/**
 * Output plugin debug
 */
function wplnst_debug($message, $tag = '') {

	// Check debug
	if (!wplnst_is_debug()) {
		return;
	}

	// Check output mode
	$output = defined('WPLNST_DEBUG_OUTPUT')? WPLNST_DEBUG_OUTPUT : false;
	if (empty($output) || !in_array($output, ['error_log', 'trace'])) {
		$output = 'error_log';
	}

	// Default output
	if ('error_log' == $output) {
		error_log('WPLNST'.(empty($tag)? '' : ' ['.$tag.']').' - '.$message);

	// Trace output
	} elseif ('trace' == $output) {
		wplnst_trace($message, $tag);
	}
}



/**
 * Appends a message to the trace file
 */
function wplnst_trace($message, $tag) {
	$path = dirname(dirname(dirname(dirname(__FILE__)))).'/wplnst_trace.txt';
	@file_put_contents($path, date('Y-m-d h:i:s').' '.(empty($tag)? '' : '['.trim($tag).'] ').trim($message)."\n", FILE_APPEND);
}