<?php
/*
Plugin URI: http://calendarscripts.info/watupro/
Author: Kiboko Labs
*/

global $wpdb;

if(!defined('WP_UNINSTALL_PLUGIN') or !WP_UNINSTALL_PLUGIN) exit;
    
// drop tables
if(get_option('watupro_delete_db')==1 and get_option('watupro_really_delete_db') === 'yes') {
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_master");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_question");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_answer");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_grading");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_taken_exams");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_student_answers");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_certificates");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_qcats");
   $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_groups");
   $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_cats");
   $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_dependencies");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_user_certificates");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}watupro_payments");
	    
	// clean options
	delete_option('watupro_show_answers');
	delete_option('watupro_single_page');
	delete_option('watupro_answer_type');
	delete_option('watupro_db_version');
	delete_option('watupro_delete_db');
	delete_option('watupro_currency');
	delete_option('watupro_errorlog');
	delete_option('watupro_manual_grade_subject');
	delete_option('watupro_manual_grade_message');
}