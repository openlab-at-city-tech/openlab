<?php
/*
* Function for displaying simplywordpres
* Version: 2.0.6
*/
global $wpdb, $wp_version, $bws_plugin_info, $bstwbsftwppdtplgns_options;
if ( ! function_exists ( 'bws_admin_enqueue_scripts' ) )
	require_once( dirname( __FILE__ ) . '/bws_functions.php' );

$bws_plugins_category = array();

$bws_plugins = array();

$themes = array();