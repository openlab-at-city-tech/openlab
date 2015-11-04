<?php

/**
 * Definition of the PHP functions that can be called directly 
 * by a theme or a plugin for customizations without use shortcode
 *
 * @package SZGoogle
 * @subpackage Functions
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Definition of the call wrapper functions for modules
// With these features, you can customize themes and other plugins

if (!function_exists('szgoogle_youtube_get_object')) {
	function szgoogle_youtube_get_object() { 
		if (!SZGoogleModule::getObject('SZGoogleModuleYoutube')) return false;
			else return SZGoogleModule::getObject('SZGoogleModuleYoutube');
	}
}

// Function to retrieve the HTML code of a youtube video
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_youtube_get_code_video')) {
	function szgoogle_youtube_get_code_video($options=array()) {
		if (!$object = new SZGoogleActionYoutubeVideo()) return false;
			else return $object->getHTMLCode($options);
	}
}

// Function to retrieve the HTML code of a youtube playlist
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_youtube_get_code_playlist')) {
	function szgoogle_youtube_get_code_playlist($options=array()) {
		if (!$object = new SZGoogleActionYoutubePlaylist()) return false;
			else return $object->getHTMLCode($options);
	}
}

// Function to retrieve the HTML code of a youtube badge
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_youtube_get_code_badge')) {
	function szgoogle_youtube_get_code_badge($options=array()) {
		if (!$object = new SZGoogleActionYoutubeBadge()) return false;
			else return $object->getHTMLCode($options);
	}
}

// Function to control the correct code of channel youtube
// connected to the basic functions as shotcodes and widgets

if (!function_exists('sz_google_module_youtube_check_channel')) {
	function sz_google_module_youtube_check_channel($options=array()) {
		if (!$object = szgoogle_youtube_get_object()) return false;
			else return $object->youtubeCheckChannel($options);
	}
}

// Function to retrieve the HTML code of a youtube button
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_youtube_get_code_button')) {
	function szgoogle_youtube_get_code_button($options=array()) {
		if (!$object = szgoogle_youtube_get_object()) return false;
			else return $object->getYoutubeButtonCode($options);
	}
}

// Function to retrieve the HTML code of a youtube link
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_youtube_get_code_link')) {
	function szgoogle_youtube_get_code_link($options=array()) {
		if (!$object = szgoogle_youtube_get_object()) return false;
			else return $object->getYoutubeLinkCode($options);
	}
}