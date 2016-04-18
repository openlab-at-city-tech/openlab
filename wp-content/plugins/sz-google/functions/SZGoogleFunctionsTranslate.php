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

if (!function_exists('szgoogle_translate_get_object')) {
	function szgoogle_translate_get_object() { 
		if (!SZGoogleModule::getObject('SZGoogleModuleTranslate')) return false;
			else return SZGoogleModule::getObject('SZGoogleModuleTranslate');
	}
}

// Function to retrieve the HTML code for monitoring
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_translate_get_code')) {
	function szgoogle_translate_get_code($options=array()) {
		if (!$object = szgoogle_translate_get_object()) return false;
			else return $object->getTranslateCode($options);
	}
}

// Function to retrieve the HTML code for section META
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_translate_get_meta')) {
	function szgoogle_translate_get_meta() {
		if (!$object = szgoogle_translate_get_object()) return false;
			else return $object->getTranslateMeta();
	}
}

// Function to retrieve the HTML code for section META ID
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_translate_get_meta_ID')) {
	function szgoogle_translate_get_meta_ID() {
		if (!$object = szgoogle_translate_get_object()) return false;
			else return $object->getTranslateMetaID();
	}
}