<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
if(get_option('ays_quiz_maker_upgrade_plugin','false') === 'false'){
    global $wpdb;
    $quiz_categories_table      =   $wpdb->prefix . 'aysquiz_quizcategories';
    $quizes_table               =   $wpdb->prefix . 'aysquiz_quizes';
    $questions_table            =   $wpdb->prefix . 'aysquiz_questions';
    $question_categories_table  =   $wpdb->prefix . 'aysquiz_categories';
    $question_tags_table        =   $wpdb->prefix . 'aysquiz_question_tags';
    $answers_table              =   $wpdb->prefix . 'aysquiz_answers';
    $reports_table              =   $wpdb->prefix . 'aysquiz_reports';
    $rates_table                =   $wpdb->prefix . 'aysquiz_rates';
    $themes_table               =   $wpdb->prefix . 'aysquiz_themes';
    $attributes_table           =   $wpdb->prefix . 'aysquiz_attributes';
    $orders_table               =   $wpdb->prefix . 'aysquiz_orders';
    $settings_table             =   $wpdb->prefix . 'aysquiz_settings';
    $question_reports_table     =   $wpdb->prefix . 'aysquiz_question_reports';


    $wpdb->query("DROP TABLE IF EXISTS `".$answers_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$questions_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$quizes_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$reports_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$rates_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$themes_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$quiz_categories_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$question_categories_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$question_tags_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$attributes_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$orders_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$settings_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$question_reports_table."`");

    delete_option( "ays_quiz_db_version");
    delete_option( "ays_quiz_maker_upgrade_plugin");
}
