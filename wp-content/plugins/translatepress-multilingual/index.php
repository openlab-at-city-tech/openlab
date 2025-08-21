<?php
/*
Plugin Name: TranslatePress - Multilingual
Plugin URI: https://translatepress.com/
Description: Experience a better way of translating your WordPress site using a visual front-end translation editor, with full support for WooCommerce and site builders.
Version: 2.9.19
Author: Cozmoslabs, Razvan Mocanu, Madalin Ungureanu, Cristophor Hurduban
Author URI: https://cozmoslabs.com/
Text Domain: translatepress-multilingual
Domain Path: /languages
License: GPL2
WC requires at least: 2.5.0
WC tested up to: 9.9.4

== Copyright ==
Copyright 2017 Cozmoslabs (www.cozmoslabs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();


function trp_enable_translatepress(){
	$enable_translatepress = true;
	$current_php_version = apply_filters( 'trp_php_version', phpversion() );

	// 5.6.20 is the minimum version supported by WordPress
	if ( $current_php_version !== false && version_compare( $current_php_version, '5.6.20', '<' ) ){
		$enable_translatepress = false;
		add_action( 'admin_menu', 'trp_translatepress_disabled_notice' );
	}

	return apply_filters( 'trp_enable_translatepress', $enable_translatepress );
}

if ( trp_enable_translatepress() ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-translate-press.php';

	/** License classes includes here
	 * Since version 1.4.6
	 * It need to be outside of a hook so it load before the classes that are in the addons, that we are trying to phase out
	 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-edd-sl-plugin-updater.php';

	/* make sure we execute our plugin before other plugins so the changes we make apply across the board */
	add_action( 'plugins_loaded', 'trp_run_translatepress_hooks', 1 );
}
function trp_run_translatepress_hooks(){
	$trp = TRP_Translate_Press::get_trp_instance();
	$trp->run();
}

function trp_translatepress_disabled_notice(){
	echo '<div class="notice notice-error"><p>' . wp_kses( sprintf( __( '<strong>TranslatePress</strong> requires at least PHP version 5.6.20+ to run. It is the <a href="%s">minimum requirement of the latest WordPress version</a>. Please contact your server administrator to update your PHP version.','translatepress-multilingual' ), 'https://wordpress.org/about/requirements/' ), array( 'a' => array( 'href' => array() ), 'strong' => array() ) ) . '</p></div>';
}

/**
 * Redirect users to the settings page on plugin activation
 */
add_action( 'activated_plugin', 'trp_plugin_activation_redirect' );
function trp_plugin_activation_redirect( $plugin ){

	if( !wp_doing_ajax() && $plugin == plugin_basename( __FILE__ ) ) {
		wp_safe_redirect( admin_url( 'options-general.php?page=translate-press' ) );
		exit();
	}

}

//This is for the DEV version
if( file_exists(plugin_dir_path( __FILE__ ) . '/index-dev.php') )
    include_once( plugin_dir_path( __FILE__ ) . '/index-dev.php');
