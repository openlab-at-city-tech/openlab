<?php 
/**
 * Plugin Name: Simple Drop Cap
 * Plugin URI: http://wordpress.org/plugins/simple-drop-cap/
 * Description: Simple drop cap plugin. Transform the first letter of a word into a drop cap or initial letter simply by wrapping the word with shortcode [dropcap].
 * Author: Yudhistira Mauris
 * Author URI: http://www.yudhistiramauris.com
 * Text Domain: simple-drop-cap
 * Version: 1.2.8
 * License: GPLv2
 * Domain Path: languages
 */

/**  Copyright 2014-2015 Yudhistira Mauris (email: mauris@yudhistiramauris.com)
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License, version 2, as 
 *   published by the Free Software Foundation.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/************************
 * GLOBAL CONSTANTS & VARIABLES
 ***********************/

if ( ! defined( 'WPSDC_PLUGIN_FILE' ) ) {
	define( 'WPSDC_PLUGIN_FILE', __FILE__ );
}

/************************
 * INCLUDES
 ***********************/

include ('includes/load-translation.php');
include ('includes/register-shortcode.php');
include ('includes/register-tinymce-button.php');
include ('includes/create-setting-menu-page.php');
include ('includes/register-settings.php');
include ('includes/scripts.php');