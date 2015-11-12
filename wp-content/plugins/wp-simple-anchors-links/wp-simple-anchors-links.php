<?php
/*
Plugin Name: WP Simple Anchors Links
Plugin URI: http://www.kilukrumedia.com
Description: Insert simply many anchors to pages, posts and custom post type.
Version: 1.0.0
Author: Kilukru Media
Author URI: http://www.kilukrumedia.com
*/


/*
Copyright (C) 2012-2014 Kilukru Media, kilukrumedia.com (info AT kilukrumedia DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !session_id() ){ session_start(); } // Start session just in case

if ( ! defined( 'WPSIMPLEANCHORSLINKS_VERSION' ) )
{	define( 'WPSIMPLEANCHORSLINKS_VERSION', '1.0.0' ); }

if ( ! defined( 'WPSIMPLEANCHORSLINKS_VERSION_NUMERIC' ) )
{	define( 'WPSIMPLEANCHORSLINKS_VERSION_NUMERIC', '1000000' ); }

if ( ! defined( 'WPSIMPLEANCHORSLINKS_VERSION_FILETIME' ) )
{	define( 'WPSIMPLEANCHORSLINKS_VERSION_FILETIME', '1390505570' ); } //Set by echo time();

if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_DIR' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); }

if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_BASENAME' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }

if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_DIRNAME' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_DIRNAME', dirname( WPSIMPLEANCHORSLINKS_PLUGIN_BASENAME ) ); }

if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_URL' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); }

if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_CSS_URL' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_CSS_URL', WPSIMPLEANCHORSLINKS_PLUGIN_URL . 'css/' ); }
if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_IMAGES_URL' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_IMAGES_URL', WPSIMPLEANCHORSLINKS_PLUGIN_URL . 'images/' ); }
if ( ! defined( 'WPSIMPLEANCHORSLINKS_PLUGIN_JS_URL' ) )
{	define( 'WPSIMPLEANCHORSLINKS_PLUGIN_JS_URL', WPSIMPLEANCHORSLINKS_PLUGIN_URL . 'js/' ); }

if ( ! defined( 'WP_CONTENT_URL' ) )
{	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' ); }
if ( ! defined( 'WP_ADMIN_URL' ) )
{	define( 'WP_ADMIN_URL', get_option( 'siteurl' ) . '/wp-admin' ); }
if ( ! defined( 'WP_CONTENT_DIR' ) )
{	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); }
if ( ! defined( 'WP_PLUGIN_URL' ) )
{	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. DIRECTORY_SEPARATOR . 'plugins' ); }
if ( ! defined( 'WP_PLUGIN_DIR' ) )
{	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'plugins' ); }


/**
 * Options to disabled elements
 */
//WPSIMPLEANCHORSLINKS_DISABLED_FRONTEND_CSS


if ( class_exists( 'WP_Simple_Anchors_Links' ) ) {
	add_action( 'activation_notice', 'wpsimpleanchorslinks_class_defined_error' );
	return;
}

// Require functions before Class
require_once( WPSIMPLEANCHORSLINKS_PLUGIN_DIR . 'wpsimpleanchorslinks_functions.php');
require_once( WPSIMPLEANCHORSLINKS_PLUGIN_DIR . 'wpsimpleanchorslinks_class.php');

global $mblzr, $wpsimpleanchorslinks_options, $wpsimpleanchorslinks_activation;

$wpsimpleanchorslinks_activation = false;
$wpsimpleanchorslinks = new WP_Simple_Anchors_Links();

////checking to see if things need to be updated

register_activation_hook( __FILE__, 'wpsimpleanchorslinks_activate' );

add_action( 'init', 'wpsimpleanchorslinks_update_settings_check' );

////end checking to see if things need to be updated


