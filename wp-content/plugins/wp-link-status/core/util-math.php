<?php

/**
 * Util Math functions
 *
 * @package WP Link Status
 * @subpackage Core
 */



/**
 * Format size from bytes to KB, MB, GB or TB
 */
function wplnst_format_bytes($bytes, $precision = 3, $number_format = true) {

	$units = array('B', 'KB', 'MB', 'GB', 'TB');

	$bytes = max($bytes, 0);
	$pow = floor(($bytes? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	// Uncomment one of the following alternatives
	$bytes /= pow(1024, $pow);
	if (0 == $pow) {
		$pow = 1;
		if ($bytes > 0) {
			$bytes /= 1024;
		}
	}

	$value = round($bytes, $precision);
	if ($number_format && function_exists('number_format_i18n')) {
		$value = number_format_i18n($value, $precision);
	}

	return $value . ' ' . $units[$pow];
}