<?php
/**
 * Uninstall Easy Custom Sidebars Plugin
 *
 * Used to uninstall this plugin and remove any options
 * and transients from the database. Fired when the plugin
 * is uninstalled. Developers can add code to this file that
 * will run when the user uninstalls Easy Custom Sidebars
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
