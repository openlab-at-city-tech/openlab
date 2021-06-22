<?php
/*
Plugin URI: http://wordpress.org/plugins/watu/
Author: Kiboko Labs
*/

global $wpdb;

if(!defined('WP_UNINSTALL_PLUGIN') or !WP_UNINSTALL_PLUGIN) exit;
    
$delDb = get_option('watu_delete_db');
	
delete_option('watu_show_answers');
delete_option('watu_single_page');
delete_option('watu_answer_type');
delete_option( 'watu_db_tables' );

if( $delDb == 'checked="checked"' and get_option('watu_delete_db_confirm') == 'yes') {
	$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_master ");
	$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_question ");
	$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_answer ");
	$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_grading ");
	$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_takings ");
}