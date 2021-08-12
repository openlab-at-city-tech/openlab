<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function watu_grades() {
   global $wpdb;
   
   // select quiz
   $quiz = $wpdb->get_row($wpdb->prepare("SELECT ID, name, advanced_settings FROM ".WATU_EXAMS." WHERE ID=%d", intval($_GET['quiz_id'])));
   if(empty($quiz->ID)) wp_die(__('Unrecognized quiz ID', 'watu'));
   
   if(!empty($_POST['add']) or !empty($_POST['save'])) {
   	$gtitle = sanitize_text_field($_POST['gtitle']);	   
	   $gfrom = floatval($_POST['gfrom']);
	   $gto = floatval($_POST['gto']);
	   $redirect_url = esc_url_raw($_POST['redirect_url']);
	   $moola = intval(@$_POST['moola']);
   }
   
   if(!empty($_POST['add']) and check_admin_referer('watu_grades')) {
   	$gdesc = watu_strip_tags($_POST['gdesc']);
   	$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_GRADES." SET
   		exam_id=%d, gtitle=%s, gdescription=%s, gfrom=%f, gto=%f, redirect_url=%s, moola=%d",
   		$quiz->ID, $gtitle, $gdesc, $gfrom, $gto, $redirect_url, $moola));   	
   	watu_redirect("admin.php?page=watu_grades&quiz_id=".$quiz->ID);	
   }
   
   if(!empty($_POST['save']) and check_admin_referer('watu_grades')) {
   	$gdesc = watu_strip_tags($_POST['gdesc'.$_POST['id']]);
   	$wpdb->query($wpdb->prepare("UPDATE ".WATU_GRADES." SET
   		gtitle=%s, gdescription=%s, gfrom=%f, gto=%f, redirect_url=%s, moola=%d WHERE ID=%d",
   		$gtitle, $gdesc, $gfrom, $gto, $redirect_url, $moola, intval($_POST['id'])));
   	watu_redirect("admin.php?page=watu_grades&quiz_id=".$quiz->ID);
   }
   
   if(!empty($_POST['del']) and check_admin_referer('watu_grades')) {
   	$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_GRADES." WHERE ID=%d", intval($_POST['id'])));
   }
   
   // select grades
   $grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_GRADES." WHERE exam_id=%d ORDER BY gto DESC", $quiz->ID));
   
   $integrate_moolamojo = get_option('watu_integrate_moolamojo');
   if($integrate_moolamojo) {   	
   	$advanced_settings = unserialize(stripslashes($quiz->advanced_settings));
   	$integrate_moolamojo = $advanced_settings['transfer_moola'];
   }
   
   if(@file_exists(get_stylesheet_directory().'/watu/grades.html.php')) include get_stylesheet_directory().'/watu/grades.html.php';
	else include(WATU_PATH . '/views/grades.html.php');  
} // end manage grades