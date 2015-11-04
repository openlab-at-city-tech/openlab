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

if (!function_exists('szgoogle_gplus_get_object')) {
	function szgoogle_gplus_get_object() { 
		if (!SZGoogleModule::getObject('SZGoogleModulePlus')) return false;
			else return SZGoogleModule::getObject('SZGoogleModulePlus');
	}
}

// Function to retrieve the HTML code of the badge profile
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_badge_profile')) {
	function szgoogle_gplus_get_badge_profile($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusProfileCode($options);
	}
}

// Function to retrieve the HTML code of the badge page
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_badge_page')) {
	function szgoogle_gplus_get_badge_page($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusPageCode($options);
	}
}

// Function to retrieve the HTML code of the badge community
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_badge_community')) {
	function szgoogle_gplus_get_badge_community($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusCommunityCode($options);
	}
}

// Function to retrieve the HTML code of the badge followers
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_badge_followers')) {
	function szgoogle_gplus_get_badge_followers($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusFollowersCode($options);
	}
}

// Function to retrieve the HTML code of the button +1
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_button_one')) {
	function szgoogle_gplus_get_button_one($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusPlusoneCode($options);
	}
}

// Function to retrieve the HTML code of the button share
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_button_share')) {
	function szgoogle_gplus_get_button_share($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusShareCode($options);
	}
}

// Function to retrieve the HTML code of the button follow
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_button_follow')) {
	function szgoogle_gplus_get_button_follow($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusFollowCode($options);
	}
}

// Function to retrieve the HTML code of the comments
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_comments')) {
	function szgoogle_gplus_get_comments($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusCommentsCode($options);
	}
}

// Function to retrieve value of cutom field contact page
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_contact_page')) {
	function szgoogle_gplus_get_contact_page($userid=null) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusContactPage($userid);
	}
}

// Function to retrieve value of cutom field contact community
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_contact_community')) {
	function szgoogle_gplus_get_contact_community($userid=null) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusContactCommunity($userid);
	}
}

// Function to retrieve value of cutom field best post
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_contact_bestpost')) {
	function szgoogle_gplus_get_contact_bestpost($userid=null) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusContactBestpost($userid);
	}
}

// Function to retrieve the HTML code of the embedded post
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_post')) {
	function szgoogle_gplus_get_post($options=array()) {
		if (!$object = szgoogle_gplus_get_object()) return false;
			else return $object->getPlusPostCode($options);
	}
}

// Function to retrieve the HTML code of the badge author 
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_gplus_get_badge_author')) {
	function szgoogle_gplus_get_badge_author($options=array()) {
		if (!$object = new SZGoogleActionPlusAuthorBadge()) return false;
			else return $object->getHTMLCode($options);
	}
}