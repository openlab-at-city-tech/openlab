<?php
/*
Plugin Name: Watu PRO
Plugin URI: http://calendarscripts.info/watupro
Description: Create exams and quizzes and display the result immediately after the user takes the exam. More information at <a href="http://calendarscripts.info/watupro">Watu PRO Site</a>
Go to <a href="admin.php?page=watupro_options">Watu PRO Settings</a> or <a href="admin.php?page=watupro_exams">Manage Your Exams</a> 
Version: 6.6.2
Author: Kiboko Labs
Author URI: http://kibokolabs.com
License: GPLv2 or later
*/

define( 'WATUPRO_PATH', dirname( __FILE__ ) );
define( 'WATUPRO_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'WATUPRO_URL', plugin_dir_url( __FILE__ ));

// include libraries and controllers
include WATUPRO_PATH.'/lib/watupro.php';
include WATUPRO_PATH.'/controllers/init.php';
include WATUPRO_PATH.'/controllers/import-export.php';
include WATUPRO_PATH.'/controllers/cats.php';
include WATUPRO_PATH.'/controllers/groups.php';
include WATUPRO_PATH.'/controllers/certificates.php';
include WATUPRO_PATH.'/controllers/modules.php';
include WATUPRO_PATH.'/controllers/shortcodes.php';
include WATUPRO_PATH.'/controllers/help.php';
include WATUPRO_PATH.'/controllers/exam.php';
include WATUPRO_PATH.'/controllers/questions.php';
include WATUPRO_PATH.'/controllers/exams.php';
include WATUPRO_PATH.'/controllers/options.php';
include WATUPRO_PATH.'/controllers/takings.php';
include WATUPRO_PATH.'/controllers/live-result.php';
include WATUPRO_PATH.'/controllers/grades.php';
include WATUPRO_PATH.'/controllers/hints.php';
include WATUPRO_PATH.'/controllers/files.php';
include WATUPRO_PATH.'/controllers/ajax.php';
include WATUPRO_PATH.'/controllers/social-sharing.php';
include WATUPRO_PATH.'/controllers/timer.php';
include WATUPRO_PATH.'/controllers/diff-levels.php';
include WATUPRO_PATH.'/controllers/widgets.php';
include WATUPRO_PATH."/controllers/shortcode-generator.php";
include WATUPRO_PATH."/controllers/woocommerce.php";
include WATUPRO_PATH."/controllers/webhooks.php";
include WATUPRO_PATH.'/models/exam.php';
include WATUPRO_PATH.'/models/user.php';
include WATUPRO_PATH."/models/question.php";
include WATUPRO_PATH."/models/certificate.php";
include WATUPRO_PATH."/models/category.php";
include WATUPRO_PATH."/models/taking.php";
include WATUPRO_PATH."/helpers/csvhelper.php";
include WATUPRO_PATH."/helpers/text-captcha.php";
include WATUPRO_PATH."/models/record.php";
include WATUPRO_PATH."/models/grade.php";
include WATUPRO_PATH."/models/question-enhanced.php";
include WATUPRO_PATH."/models/flashcard.php";


// intelligence code
if(watupro_intel()) {
	include WATUPRO_PATH.'/i/controllers/practice.php';
	include WATUPRO_PATH.'/i/controllers/init.php';
	include_once(WATUPRO_PATH."/i/models/exam_intel.php");
	add_action('wp_ajax_watupro_practice_submit', array("WatuPracticeController", "submit"));
	add_action('wp_ajax_nopriv_watupro_practice_submit', array("WatuPracticeController", "submit"));
}

// when students register
if(!empty($_GET['watupro_register'])) {
    add_action('register_form','watupro_role_field'); // this is probably not needed anymore
}

register_activation_hook( __FILE__, 'watupro_activate' );

add_action('user_register', 'watupro_register_role');
add_action('user_register', 'watupro_register_group');
add_action('init', 'watupro_init');
add_action( 'admin_menu', 'watupro_add_menu_links' );
add_action('wp_ajax_watupro_taking_details', 'watupro_taking_details');
add_action('wp_ajax_watupro_delete_taking', 'watupro_delete_taking');

// ajax for show_exam /user end
add_action('wp_ajax_watupro_submit', 'watupro_submit');
add_action('wp_ajax_nopriv_watupro_submit', 'watupro_submit');
add_action('wp_ajax_watupro_initialize_timer', 'watupro_initialize_timer');
add_action('wp_ajax_nopriv_watupro_initialize_timer', 'watupro_initialize_timer');
add_action('wp_ajax_watupro_store_details', 'watupro_store_details');
add_action('wp_ajax_nopriv_watupro_store_details', 'watupro_store_details');
add_action('wp_ajax_watupro_liveresult', 'watupro_liveresult');
add_action('wp_ajax_nopriv_watupro_liveresult', 'watupro_liveresult');
add_action('wp_ajax_watupro_store_all', 'watupro_store_all');
add_action('wp_ajax_watupro_get_hints', 'watupro_get_hints');
add_action('wp_ajax_nopriv_watupro_get_hints', 'watupro_get_hints');
add_action('wp_ajax_watupro_ajax', 'watupro_ajax');
add_action('wp_ajax_nopriv_watupro_ajax', 'watupro_ajax'); // misc ajax handler to avoid adding more ajax actions


// user groups - functions in controllers/groups.php
add_action( 'show_user_profile', 'watupro_user_fields' );
add_action( 'edit_user_profile', 'watupro_user_fields' );
add_action( 'user_new_form', 'watupro_user_fields' );
// add_action( 'um_after_register_fields', 'watupro_user_fields' );
add_action( 'personal_options_update', 'watupro_save_extra_user_fields' );
add_action( 'edit_user_profile_update', 'watupro_save_extra_user_fields' );
add_action( 'user_register', 'watupro_save_extra_user_fields' );
add_filter( 'plugin_action_links', 'watupro_plugin_action_links', 10, 2);

// extra columns in user profile
add_filter('manage_users_columns', array('WTPUser', 'add_custom_column'));
add_action('manage_users_custom_column', array('WTPUser','manage_custom_column'), 10, 3); 

// hook personal data eraser
add_filter('wp_privacy_personal_data_erasers', array('WTPUser', 'register_eraser'), 10);

if(watupro_module("reports")) require(WATUPRO_PATH."/modules/reports/controllers/init.php");

function watupro_get_version() {
    $plugin_data = get_plugin_data( __FILE__ );
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}