<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.8 Plugin: WP-Polls 2.61										|
|	Copyright (c) 2009 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- Polls AJAX For Admin Backend												|
|	- wp-content/plugins/wp-polls/polls-admin-ajax.php					|
|																							|
+----------------------------------------------------------------+
*/


### Include wp-config.php
$wp_root = '../../..';
if (file_exists($wp_root.'/wp-load.php')) {
	require_once($wp_root.'/wp-load.php');
} else {
	require_once($wp_root.'/wp-config.php');
}


### Check Whether User Can Manage Polls
if(!current_user_can('manage_polls')) {
	die('Access Denied');
}


### Form Processing 
if(!empty($_POST['do'])) {
	// Set Header
	header('Content-Type: text/html; charset='.get_option('blog_charset').'');

	// Decide What To Do
	switch($_POST['do']) {
		// Delete Polls Logs
		case __('Delete All Logs', 'wp-polls'):
			check_ajax_referer('wp-polls_delete-polls-logs');
			if(trim($_POST['delete_logs_yes']) == 'yes') {
				$delete_logs = $wpdb->query("DELETE FROM $wpdb->pollsip");
				if($delete_logs) {
					echo '<p style="color: green;">'.__('All Polls Logs Have Been Deleted.', 'wp-polls').'</p>';
				} else {
					echo '<p style="color: red;">'.__('An Error Has Occured While Deleting All Polls Logs.', 'wp-polls').'</p>';
				}
			}
			break;
		// Delete Poll Logs For Individual Poll
		case __('Delete Logs For This Poll Only', 'wp-polls'):
			check_ajax_referer('wp-polls_delete-poll-logs');
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = $wpdb->get_var("SELECT pollq_question FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			if(trim($_POST['delete_logs_yes']) == 'yes') {
				$delete_logs = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $pollq_id");
				if($delete_logs) {
					echo '<p style="color: green;">'.sprintf(__('All Logs For \'%s\' Has Been Deleted.', 'wp-polls'), stripslashes($pollq_question)).'</p>';
				} else {
					echo '<p style="color: red;">'.sprintf(__('An Error Has Occured While Deleting All Logs For \'%s\'', 'wp-polls'), stripslashes($pollq_question)).'</p>';
				}
			}
			break;
		// Delete Poll's Answer
		case __('Delete Poll Answer', 'wp-polls'):
			check_ajax_referer('wp-polls_delete-poll-answer');
			$pollq_id  = intval($_POST['pollq_id']);
			$polla_aid = intval($_POST['polla_aid']);
			$poll_answers = $wpdb->get_row("SELECT polla_votes, polla_answers FROM $wpdb->pollsa WHERE polla_aid = $polla_aid AND polla_qid = $pollq_id");
			$polla_votes = intval($poll_answers->polla_votes);
			$polla_answers = stripslashes(trim($poll_answers->polla_answers));
			$delete_polla_answers = $wpdb->query("DELETE FROM $wpdb->pollsa WHERE polla_aid = $polla_aid AND polla_qid = $pollq_id");
			$delete_pollip = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $pollq_id AND pollip_aid = $polla_aid");
			$update_pollq_totalvotes = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_totalvotes = (pollq_totalvotes-$polla_votes) WHERE pollq_id = $pollq_id");
			if($delete_polla_answers) {
				echo '<p style="color: green;">'.sprintf(__('Poll Answer \'%s\' Deleted Successfully.', 'wp-polls'), $polla_answers).'</p>';
			} else {
				echo '<p style="color: red;">'.sprintf(__('Error In Deleting Poll Answer \'%s\'.', 'wp-polls'), $polla_answers).'</p>';
			}
			break;
		// Open Poll
		case __('Open Poll', 'wp-polls'):
			check_ajax_referer('wp-polls_open-poll');
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = $wpdb->get_var("SELECT pollq_question FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			$open_poll = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_active = 1 WHERE pollq_id = $pollq_id;");
			if($open_poll) {
				echo '<p style="color: green;">'.sprintf(__('Poll \'%s\' Is Now Opened', 'wp-polls'), stripslashes($pollq_question)).'</p>';
			} else {
				echo '<p style="color: red;">'.sprintf(__('Error Opening Poll \'%s\'', 'wp-polls'), stripslashes($pollq_question)).'</p>';
			}
			break;
		// Close Poll
		case __('Close Poll', 'wp-polls'):
			check_ajax_referer('wp-polls_close-poll');
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = $wpdb->get_var("SELECT pollq_question FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			$close_poll = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_active = 0 WHERE pollq_id = $pollq_id;");
			if($close_poll) {
				echo '<p style="color: green;">'.sprintf(__('Poll \'%s\' Is Now Closed', 'wp-polls'), stripslashes($pollq_question)).'</p>';
			} else {
				echo '<p style="color: red;">'.sprintf(__('Error Closing Poll \'%s\'', 'wp-polls'), stripslashes($pollq_question)).'</p>';
			}
			break;
		// Delete Poll
		case __('Delete Poll', 'wp-polls'):
			check_ajax_referer('wp-polls_delete-poll');
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = $wpdb->get_var("SELECT pollq_question FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			$delete_poll_question = $wpdb->query("DELETE FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			$delete_poll_answers =  $wpdb->query("DELETE FROM $wpdb->pollsa WHERE polla_qid = $pollq_id");
			$delete_poll_ip = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $pollq_id");
			$poll_option_lastestpoll = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'poll_latestpoll'");
			if(!$delete_poll_question) {
				echo '<p style="color: red;">'.sprintf(__('Error In Deleting Poll \'%s\' Question', 'wp-polls'), stripslashes($pollq_question)).'</p>';
			} 
			if(empty($text)) {
				echo '<p style="color: green;">'.sprintf(__('Poll \'%s\' Deleted Successfully', 'wp-polls'), stripslashes($pollq_question)).'</p>';
			}
			// Update Lastest Poll ID To Poll Options
			$latest_pollid = polls_latest_id();
			$update_latestpoll = update_option('poll_latestpoll', $latest_pollid);
			break;
	}
	exit();
}
?>