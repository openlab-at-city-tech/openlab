<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: https://wordpress.org/plugins/google-document-embedder/
Description: Lets you embed PDF, MS Office, TIFF, and many other file types in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).
Author: Kevin Davis, Dan Lester
Author URI: https://wordpress.org/plugins/google-document-embedder/
Text Domain: google-document-embedder
Domain Path: /languages/
Version: 2.6.4
License: GPLv2
*/

/**
 * LICENSE
 * This file is part of Google Doc Embedder.
 *
 * Google Doc Embedder is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package    google-document-embedder
 * @author     Kevin Davis <wpp@tnw.org>
 * @copyright  Copyright 2014 Kevin Davis
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @link       https://wordpress.org/plugins/google-document-embedder/
 */

// boring init junk
$gde_ver 				= "2.6.4";
$gde_db_ver 			= "1.2";		// update also in gde_activate()

if ( ! defined( 'ABSPATH' ) ) {	exit; }


require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );
global $wp_version;

$pdata 					= gde_get_plugin_data();
$gdeoptions				= get_option( 'gde_options' );
$gdetypes				= gde_supported_types();		

// check for db health
$healthy = true;

// add admin functions only if needed
if ( is_admin() ) { require_once( GDE_PLUGIN_DIR . 'functions-admin.php' ); }


// activate plugin, allow clear dx log on deactivate
register_activation_hook( __FILE__, 'gde_activate' );
register_deactivation_hook( __FILE__, 'gde_deactivate' );

// bring the magic
add_action( 'plugins_loaded', 'gde_load' );
add_shortcode( 'gview', 'gde_do_shortcode' );

function gde_do_shortcode( $atts ) {
	global $gdeoptions;

	extract( shortcode_atts( array (
		'file' => '',
		'profile' => 1, // default profile is always ID 1
		'save' => '',
		'width' => '',
		'height' => ''
	), $atts ) );
	
	// get requested profile data (or default if doesn't exist)
	$term = $profile;
	if ( is_numeric( $term ) ) {
		// id-based lookup
		if ( ! $profile = gde_get_profiles( $term ) ) {
			gde_dx_log("Loading default profile instead");
			if ( ! $profile = gde_get_profiles( 1 ) ) {
				return gde_show_error( __('Unable to load requested profile.', 'google-document-embedder') );
			} else {
				$pid = 1;
			}
		} else {
			$pid = $term;
		}
	} else {
		// name-based lookup
		if ( ! $profile = gde_get_profiles( strtolower( $term ) ) ) {
			gde_dx_log("Loading default profile instead");
			if ( ! $profile = gde_get_profiles( 1 ) ) {
				return gde_show_error( __('Unable to load requested profile.', 'google-document-embedder') );
			} else {
				$pid = 1;
			}
		} else {
			$pid = $profile['profile_id'];
		}
	}
	
	// use profile defaults if shortcode override not defined
	if ( $save !== "0" ) {
		if ( empty( $save ) ) {
			$save = $profile['link_show'];
		}
	}

	if ( empty( $width ) ) {
		$width = $profile['default_width'];
	}
	if ( empty( $height ) ) {
		$height = $profile['default_height'];
	}
	$lang =  $profile['language'];

	// tweak the dimensions if necessary
	$width = gde_sanitize_dims( $width );
	$height = gde_sanitize_dims( $height );
	
	// add base url if needed
	if ( ! preg_match( "/^http/i", $file ) ) {
		if ( substr( $file, 0, 2 ) == "//" ) {
			// append dynamic protocol
			if ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) {
				$file = "https:" . $file;
			} else {
				$file = "http:" . $file;
			}
		} elseif ( isset( $profile['base_url'] ) ) {
			// not a full link, add base URL if available
			if ( substr( $file, 0, 1 ) == "/" ) {
				// remove any preceding slash from doc (base URL adds it)
				$file = ltrim( $file, '/' );
			}
			$file = $profile['base_url'] . $file;
		}
	}
	
	// capture file details
	$fn = basename( $file );
	$fnp = gde_split_filename( $fn );
	
	// check for missing required field
	if ( ! $file ) {
		return gde_show_error( __('File not specified, check shortcode syntax', 'google-document-embedder') );
	}
	
	// file validation
	if ( $gdeoptions['error_check'] == "no" ) {
		$force = true;
	} else {
		$force = false;
	}
	$status = gde_validate_file( str_replace( " ", "%20", $file ), $force );
	
	if ( ! isset( $code ) && ! is_array( $status ) && $status !== -1 ) {
		// validation failed
		$code = gde_show_error( $status );
	} elseif ( ! isset( $code ) ) {
		// validation passed or was skipped
		
		// check for max filesize
		$viewer = true;
		if ( $gdeoptions['file_maxsize'] > 0 && isset( $status['fsize'] ) ) {
			$maxbytes = (int) $gdeoptions['file_maxsize'] * 1024 * 1024;
			if ( $status['fsize'] > $maxbytes ) {
				$viewer = false;
			}
		}
		
		// check for failed secure doc
		if ( empty( $file ) ) {
			$code = gde_show_error( __('File name is empty', 'google-document-embedder') );
		} else {
		
			// which viewer?

			$lnk = "//docs.google.com/viewer?url=" . urlencode( $file  ) . "&hl=" . urlencode($lang);

			$lnk .= "&embedded=true";

			// build viewer
			if ( $viewer == false ) {
				// exceeds max filesize
				$vwr = '';
			} else {
				$vwr = '<iframe src="%U%" class="gde-frame" style="width:%W%; height:%H%; border: none;" scrolling="no"></iframe>';
				$vwr = str_replace("%U%", $lnk, $vwr);
				$vwr = str_replace("%W%", esc_attr($width), $vwr);
				$vwr = str_replace("%H%", esc_attr($height), $vwr);
			}
			
			// show download link?
			$allow_save = false;
			if ( $save == "all" || $save == "1" ) {
				$allow_save = true;
			} elseif ( $save == "users" && is_user_logged_in() ) {
				$allow_save = true;
			}

			if ( $allow_save ) {
				// build download link
				$linkcode = '<p class="gde-text"><a href="%LINK%" class="gde-link"%ATTRS%>%TXT%</a></p>';
				$linkcode = str_replace( "%LINK%", $file, $linkcode );
				
				// fix type
				$ftype = strtoupper( $fnp[1] );
				if ( $ftype == "TIF" ) { 
					$ftype = "TIFF";
				}
				
				// link attributes
				$attr[] = gde_ga_event( $file ); // GA integration
				$linkcode = str_replace("%ATTRS%", implode( '', $attr ), $linkcode);
				
				// link text
				if ( empty( $profile['link_text'] ) ) {
					$profile['link_text'] = __('Download', 'google-document-embedder');
				}
				
				$dltext = str_replace( "%FILE", $fn, $profile['link_text'] );
				$dltext = str_replace( "%TYPE", $ftype, $dltext );
				$dltext = str_replace( "%SIZE", gde_format_bytes( $status['fsize'] ), $dltext );
				
				$linkcode = str_replace( "%TXT%", $dltext, $linkcode );
			} else {
				$linkcode = '';
			}
			
			// link position
			if ( $profile['link_pos'] == "above" ) {
				$code = $linkcode . "\n" . $vwr;
			} else {
				$code = $vwr . "\n" . $linkcode;
			}
		}
	}
	
	return $code;
}

if ( is_admin() ) {
	// add quick settings link to plugin list
	add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'gde_actlinks' );
	
	// notify if currently using a beta
	add_action( 'after_plugin_row', 'gde_warn_on_plugin_page' );
	
	// editor integration
	if ( ! isset( $gdeoptions['ed_disable'] ) || $gdeoptions['ed_disable'] == "no" ) {

		// add tinymce button
		add_action( 'admin_init','gde_mce_addbuttons' );
		
		// extend media upload support to natively unsupported mime types
		if ( $gdeoptions['ed_extend_upload'] == "yes" ) {
			add_filter( 'upload_mimes', 'gde_upload_mimes' );
		}
		
		// embed shortcode instead of link from media library for supported types
		add_filter( 'media_send_to_editor', 'gde_media_insert', 20, 3 );
	}
	
	// add local settings page
	add_action( 'admin_menu', 'gde_option_page' );

}

/**
 * Activate the plugin
 *
 * @since   0.2
 * @return  void
 * @note	This function must remain in this file
 */
function gde_activate( $network_wide ) {
	// check for sufficient php version (minimum supports json_encode)
	if ( ! ( phpversion() >= '5.2.0' ) ) {
		wp_die( 'Your server is running PHP version ' . phpversion() . ' but this plugin requires at least 5.2.0' );
	}
	
	// set db schema version for this release - global not available here
	$gde_db_ver = "1.2";
	
	// check for network-wide activation (currently not supported)
	if ( $network_wide ) {
		wp_die("Network activation is not supported at this time. Please activate individually until an update is available.");
	}
	
	require_once( plugin_dir_path( __FILE__ ) . 'libs/lib-setup.php' );
	
	// create/update profile db, if necessary
	if ( gde_db_tables( $gde_db_ver ) ) {
		gde_setup();
	} else {
		gde_dx_log("Table creation failed; setup halted");
		wp_die( __("Setup wasn't able to create the required database tables.", 'google-document-embedder') );
	}
}

/**
 * Remove dx log on deactivation
 *
 * @since   2.5.2.1
 * @return  void
 */
function gde_deactivate() {
	global $wpdb;
	
	$table = $wpdb->base_prefix . 'gde_dx_log';
	if ( is_multisite() ) {
		$blogid = get_current_blog_id();
		$wpdb->query("DELETE FROM $table WHERE blogid = '$blogid'");
	} else {
		$wpdb->query("DROP TABLE IF EXISTS $table");
	}
}

/**
 * Actions to perform when plugins have finished loading (before init)
 *
 * @since   2.5.2.1
 * @return  void
 */
function gde_load() {
	// localization
	load_plugin_textdomain( 'google-document-embedder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

?>
