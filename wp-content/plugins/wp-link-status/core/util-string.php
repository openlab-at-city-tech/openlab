<?php

/**
 * Util String functions
 *
 * @package WP Link Status
 * @subpackage Core
 */



/**
 * Text cropping
 */
function wplnst_crop_text($text, $chars, $dots = '...') {

	// Remove HTML tags
	$text = strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $text));

	// All text
	$text = trim($text);
	if (strlen($text) <= $chars) {
		return $text;
	}

	// Crop text
	$text = substr($text, 0, $chars);

	// Remove punctuation
	$text = rtrim($text, '.');
	$text = rtrim($text, ',');
	$text = rtrim($text, ';');
	$text = rtrim($text, ':');

	// Check last char
	if (mb_substr($text, -1) != ' ') {
		$pos = strrpos($text, ' ');
		if ($pos > 0) {
			$text = substr($text, 0, $pos);
		}
	}

	// Last chars
	$text = rtrim($text, '.');
	$text = rtrim($text, ',');
	$text = rtrim($text, ';');
	$text = rtrim($text, ':');

	// Done
	return $text.$dots;
}