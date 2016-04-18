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

/**
 * Definition function to convert a string to uppercase also 
 * configured with different languages​​. Using if possible mb_()
 */

function SZGOOGLE_UPPER($string) 
{
	if (!function_exists('mb_strtoupper')) return strtoupper($string); 
		else return mb_strtoupper($string,'UTF-8');
}

/**
 * Definition function to convert a string to lowercase also 
 * configured with different languages​​. Using if possible mb_()
 */

function SZGOOGLE_LOWER($string) 
{
	if (!function_exists('mb_strtolower')) return strtolower($string); 
		else return mb_strtolower($string,'UTF-8');
}

/**
 * Definition function to convert a string to uppercase also 
 * configured with different languages​​. Using if possible mb_()
 */

function SZGOOGLE_UWORDS($string) 
{
	if (!function_exists('mb_convert_case')) return ucwords($string); 
		else return mb_convert_case($string,MB_CASE_TITLE,'UTF-8');
}