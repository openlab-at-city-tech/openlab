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

if (!function_exists('szgoogle_drive_get_embed')) {
	function szgoogle_drive_get_embed($options=array()) {
		if (!$object = new SZGoogleActionDriveEmbed()) return false;
			else return $object->getHTMLCode($options);
	}
}

// Function to retrieve the HTML code of google viewer
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_drive_get_viewer')) {
	function szgoogle_drive_get_viewer($options=array()) {
		if (!$object = new SZGoogleActionDriveViewer()) return false;
			else return $object->getHTMLCode($options);
	}
}

// Function to retrieve the HTML code of drive save button
// connected to the basic functions as shotcodes and widgets

if (!function_exists('szgoogle_drive_get_savebutton')) {
	function szgoogle_drive_get_savebutton($options=array()) {
		if (!$object = new SZGoogleActionDriveSave()) return false;
			else return $object->getHTMLCode($options);
	}
}