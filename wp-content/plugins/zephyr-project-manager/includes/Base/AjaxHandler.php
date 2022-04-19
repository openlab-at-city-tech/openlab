<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Zephyr;
use Inc\Api\Emails;
use Inc\Core\Task;
use Inc\Core\Tasks;
use Inc\Core\File;
use Inc\Core\Message;
use Inc\Core\Members;
use Inc\Core\Projects;
use Inc\Core\Activity;
use Inc\Core\Utillities;
use Inc\Core\Categories;
use Inc\Api\ColorPickerApi;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager;
use Inc\ZephyrProjectManager\Kanban;
use Inc\Api\Callbacks\AdminCallbacks;


class AjaxHandler extends BaseController {

	/**
	* Registers the callback functions responsible for providing a response
	* to Ajax requests setup throughout the rest of the plugin
	* @since    1.0.0
	*/
	public function __construct() {
		
		/* Projects */
		add_action( 'wp_ajax_zpm_new_project', array( $this, 'new_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_new_project', array( $this, 'new_project' ) );
	    add_action( 'wp_ajax_zpm_remove_project', array( $this, 'remove_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_project', array( $this, 'remove_project' ) );
	    add_action( 'wp_ajax_zpm_get_project', array( $this, 'get_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_project', array( $this, 'get_project' ) );
		add_action( 'wp_ajax_zpm_get_projects', array( $this, 'get_projects' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_projects', array( $this, 'get_projects' ) );
	    add_action( 'wp_ajax_zpm_save_project', array( $this, 'save_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_save_project', array( $this, 'save_project' ) );
	    add_action( 'wp_ajax_zpm_update_project_status', array( $this, 'update_project_status' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_project_status', array( $this, 'update_project_status' ) );
	    add_action( 'wp_ajax_zpm_like_project', array( $this, 'like_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_like_project', array( $this, 'like_project' ) );
	    add_action( 'wp_ajax_zpm_copy_project', array( $this, 'copy_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_copy_project', array( $this, 'copy_project' ) );
	    add_action( 'wp_ajax_zpm_export_project', array( $this, 'export_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_export_project', array( $this, 'export_project' ) );
	    add_action( 'wp_ajax_zpm_print_project', array( $this, 'print_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_print_project', array( $this, 'print_project' ) );
	    add_action( 'wp_ajax_zpm_project_progress', array( $this, 'project_progress' ) );
	    add_action( 'wp_ajax_nopriv_zpm_project_progress', array( $this, 'project_progress' ) );
	    add_action( 'wp_ajax_zpm_add_project_to_dashboard', array( $this, 'add_project_to_dashboard' ) );
	    add_action( 'wp_ajax_nopriv_zpm_add_project_to_dashboard', array( $this, 'add_project_to_dashboard' ) );
	    add_action( 'wp_ajax_zpm_remove_project_from_dashboard', array( $this, 'remove_project_from_dashboard' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_project_from_dashboard', array( $this, 'remove_project_from_dashboard' ) );

	    add_action( 'wp_ajax_zpm_switch_project_type', array( $this, 'switch_project_type' ) );
	    add_action( 'wp_ajax_nopriv_zpm_switch_project_type', array( $this, 'switch_project_type' ) );

	    /* Tasks */

	    $ajax_actions = [
	    	'update_user_meta',
	    	'getUserData',
	    	'updateTaskDueDate',
	    	'updateFileProject',
	    	'newTaskModal',
	    	'editTaskModal',
	    	'newProjectModal',
	    	'editProjectModal',
	    	'subtaskEditModal',
	    	'getCalendarItems',
	    	'getSubtasks',
	    	'updateTaskStatus',
	    	'getStatus',
	    	'archiveProject',
	    	'updateMessage',
	    	'uploadAjaxFile',
	    	'getTaskComments',
	    	'uploadDefaultFile',
	    	'getMembers',
	    	'sendEmail',
	    	'updateProjectSetting',
	    	'loadProjectsFromCSV',
	    	'loadProjectsFromJSON',
	    	'exportProjectsToCSV',
	    	'exportTasksToCSV',
	    	'saveProjects',
	    	'saveTasks',
	    	'getTasksDateRange',
	    	'removeProjectFromDashboard',
	    	'getTaskPanelHTML',
	    	'getProjectPanelHTML'
	    ];

		$this->add_ajax_function( 'new_task' );
		$this->add_ajax_function( 'view_task' );
		$this->add_ajax_function( 'copy_task' );
		$this->add_ajax_function( 'export_task' );
		$this->add_ajax_function( 'export_tasks' );

		$this->add_ajax_function( 'upload_tasks' );
		$this->add_ajax_function( 'convert_to_project' );
		$this->add_ajax_function( 'update_task_completion' );
		$this->add_ajax_function( 'remove_task' );
		$this->add_ajax_function( 'save_task' );
		$this->add_ajax_function( 'get_task' );
		$this->add_ajax_function( 'get_tasks' );
		$this->add_ajax_function( 'filter_tasks' );
		$this->add_ajax_function( 'filter_projects' );
		$this->add_ajax_function( 'get_user_projects' );
		$this->add_ajax_function( 'team_members_list_html' );
		$this->add_ajax_function( 'get_user_progress' );
		
		foreach ($ajax_actions as $action) {
			$this->add_ajax_function( $action );
		}

	 //    add_action( 'wp_ajax_zpm_upload_tasks', array( $this, 'upload_tasks' ) );
	 //    add_action( 'wp_ajax_nopriv_zpm_upload_tasks', array( $this, 'upload_tasks' ) );
		// add_action( 'wp_ajax_zpm_convert_to_project', array( $this, 'convert_to_project' ) );
	 //    add_action( 'wp_ajax_nopriv_zpm_convert_to_project', array( $this, 'convert_to_project' ) );
	 //    add_action( 'wp_ajax_zpm_update_task_completion', array( $this, 'update_task_completion' ) );
	 //    add_action( 'wp_ajax_nopriv_zpm_update_task_completion', array( $this, 'update_task_completion' ) );
	 //    add_action( 'wp_ajax_zpm_remove_task', array( $this, 'remove_task' ) );
	 //    add_action( 'wp_ajax_nopriv_zpm_remove_task', array( $this, 'remove_task' ) );
	    // add_action( 'wp_ajax_zpm_save_task', array( $this, 'save_task' ) );
	    // add_action( 'wp_ajax_nopriv_zpm_save_task', array( $this, 'save_task' ) );
	    // add_action( 'wp_ajax_zpm_get_tasks', array( $this, 'get_tasks' ) );
	    // add_action( 'wp_ajax_nopriv_zpm_get_tasks', array( $this, 'get_tasks' ) );
	    // add_action( 'wp_ajax_zpm_get_task', array( $this, 'get_task' ) );
	    // add_action( 'wp_ajax_nopriv_zpm_get_task', array( $this, 'get_task' ) );
	    // add_action( 'wp_ajax_zpm_filter_tasks', array( $this, 'filter_tasks' ) );
	    // add_action( 'wp_ajax_nopriv_zpm_filter_tasks', array( $this, 'filter_tasks' ) );

	    // add_action( 'wp_ajax_zpm_filter_projects', array( $this, 'filter_projects' ) );
	    // add_action( 'wp_ajax_nopriv_zpm_filter_projects', array( $this, 'filter_projects' ) );

	    add_action( 'wp_ajax_zpm_filter_tasks_by', array( $this, 'filter_by' ) );
	    add_action( 'wp_ajax_nopriv_zpm_filter_tasks_by', array( $this, 'filter_by' ) );

	    add_action( 'wp_ajax_zpm_like_task', array( $this, 'like_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_like_task', array( $this, 'like_task' ) );
	    add_action( 'wp_ajax_zpm_follow_task', array( $this, 'follow_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_follow_task', array( $this, 'follow_task' ) );
	    //add_action( 'wp_ajax_zpm_update_subtasks', array( $this, 'update_subtasks' ) );
	    //add_action( 'wp_ajax_nopriv_zpm_update_subtasks', array( $this, 'update_subtasks' ) );

	    add_action( 'wp_ajax_zpm_update_project_members', array( $this, 'update_project_members' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_project_members', array( $this, 'update_project_members' ) );

	    add_action( 'wp_ajax_zpm_update_task_priority', array( $this, 'update_task_priority' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_task_priority', array( $this, 'update_task_priority' ) );

	    /* Categories */
	    add_action( 'wp_ajax_zpm_create_category', array( $this, 'create_category' ) );
	    add_action( 'wp_ajax_nopriv_zpm_create_category', array( $this, 'create_category' ) );
	    add_action( 'wp_ajax_zpm_remove_category', array( $this, 'remove_category' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_category', array( $this, 'remove_category' ) );
	    add_action( 'wp_ajax_zpm_update_category', array( $this, 'update_category' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_category', array( $this, 'update_category' ) );
	    add_action( 'wp_ajax_zpm_display_categories', array( $this, 'display_category_list' ) );
	    add_action( 'wp_ajax_nopriv_zpm_display_categories', array( $this, 'display_category_list' ) );

	    /* Comments & Messages */
	    add_action( 'wp_ajax_zpm_send_comment', array( $this, 'send_comment' ) );
	    add_action( 'wp_ajax_nopriv_zpm_send_comment', array( $this, 'send_comment' ) );
	    add_action( 'wp_ajax_zpm_remove_comment', array( $this, 'remove_comment' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_comment', array( $this, 'remove_comment' ) );

	    /* Activity */
	    add_action( 'wp_ajax_zpm_display_activities', array( $this, 'display_activities' ) );
	    add_action( 'wp_ajax_nopriv_zpm_display_activities', array( $this, 'display_activities' ) );

	    add_action( 'wp_ajax_zpm_dismiss_notice', array( $this, 'dismiss_notice' ) );
	    add_action( 'wp_ajax_nopriv_zpm_dismiss_notice', array( $this, 'dismiss_notice' ) );

	    add_action( 'wp_ajax_zpm_update_user_access', array( $this, 'update_user_access' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_user_access', array( $this, 'update_user_access' ) );

	    add_action( 'wp_ajax_zpm_add_team', array( $this, 'add_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_add_team', array( $this, 'add_team' ) );

	    add_action( 'wp_ajax_zpm_update_team', array( $this, 'update_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_team', array( $this, 'update_team' ) );

	    add_action( 'wp_ajax_zpm_get_team', array( $this, 'get_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_team', array( $this, 'get_team' ) );

	    add_action( 'wp_ajax_zpm_delete_team', array( $this, 'delete_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_delete_team', array( $this, 'delete_team' ) );

	    add_action( 'wp_ajax_zpm_get_all_tasks', array( $this, 'get_all_tasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_all_tasks', array( $this, 'get_all_tasks' ) );

	    add_action( 'wp_ajax_zpm_get_project_tasks', array( $this, 'get_project_tasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_project_tasks', array( $this, 'get_project_tasks' ) );

	    add_action( 'wp_ajax_zpm_deactivation_survey', array( $this, 'deactivation_survey' ) );
	    add_action( 'wp_ajax_nopriv_zpm_deactivation_survey', array( $this, 'deactivation_survey' ) );

	    add_action( 'wp_ajax_zpm_update_task_start_date', array( $this, 'update_task_start_date' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_task_start_date', array( $this, 'update_task_start_date' ) );

	    add_action( 'wp_ajax_zpm_update_task_end_date', array( $this, 'update_task_end_date' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_task_end_date', array( $this, 'update_task_end_date' ) );

	    $this->add_ajax_function( 'create_status' );
	    $this->add_ajax_function( 'update_status' );
	    $this->add_ajax_function( 'delete_status' );

	    // Users
	    $this->add_ajax_function( 'get_user_by_unique_id' );

	    // Projects
	    $this->add_ajax_function( 'complete_project' );
	    $this->add_ajax_function( 'view_project' );
	    $this->add_ajax_function( 'get_project_members' );
	    $this->add_ajax_function( 'project_task_progress' );
	    $this->add_ajax_function( 'get_paginated_projects' );

	    $this->add_ajax_function( 'get_members' );
	    $this->add_ajax_function( 'get_available_project_count' );

	    $this->add_ajax_function( 'uploadTaskFile' );
	}

	public function add_ajax_function( $function_name ) {
		add_action( 'wp_ajax_zpm_' . $function_name, array( $this, $function_name ) );
	    add_action( 'wp_ajax_nopriv_zpm_' . $function_name, array( $this, $function_name ) );
	}

	public function getTaskComments() {
		$task_id = isset($_POST['id']) ? $_POST['id'] : '';
		$task_data = Tasks::get_task($task_id);
		$task = new Task($task_data);
		$data = [
			'data' => $task_data
		];
		ob_start();
		?>
		<div class="zpm_task_comments" data-task-id="<?php echo $this_task->id; ?>">
			<?php $comments = $task->getComments(); ?>
			<?php foreach($comments as $comment) : ?>
				<?php echo $comment->html(); ?>
			<?php endforeach; ?>
		</div>
		<?php
		$data['html'] = ob_get_clean();
		echo json_encode($data);
		die();
	}

	public function getStatus() {
		$statusSlug = isset($_POST['status']) ? $_POST['status'] : '';
		$status = Utillities::get_status($statusSlug);
		echo json_encode($status);
		die();
	}

	public function updateTaskStatus() {
		$taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$status = isset($_POST['status']) ? $_POST['status'] : '';

		$statusSlug = Utillities::getStatusSlug($status);

		$args = [
			'status' => $statusSlug
		];

		do_action('zpm_task_status_changed', $taskId, $statusSlug);

		Tasks::update($taskId, $args);

		echo json_encode($args);
		die();
	}

	public function getSubtasks() {
		$results = [];
		$taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		if (!empty($taskId)) {
			$results = Tasks::get_subtasks($taskId);
		}

		echo json_encode($results);
		die();
	}

	/**
	* Ajax function for sending a comment/message/attachment
	* @return json
	*/
	public function send_comment() {
		global $wpdb;
		global $zpmMessages;

		$table_name = ZPM_MESSAGES_TABLE;
		$timezone = get_option( 'timezone_string' );
		if (!empty($timezone)) {
			date_default_timezone_set( $timezone );
		}
		
		$date =  date('Y-m-d H:i:s');
		$user_id = isset($_POST['user_id']) ? sanitize_text_field( $_POST['user_id']) : $this->get_user_id();
		$subject_id = isset($_POST['subject_id']) ? sanitize_text_field( $_POST['subject_id']) : '';
		$message = isset($_POST['message']) ? serialize( stripslashes($_POST['message']) ) : '';
		$type = isset($_POST['type']) ? serialize( $_POST['type']) : '';
		$parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
		$attachments = isset($_POST['attachments']) && !empty($_POST['attachments']) ? $_POST['attachments'] : false;
		$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
		$send_email = isset($_POST['send_email']) ? $_POST['send_email'] : true;

		$settings = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'subject_id' => $subject_id,
			'message' => $message,
			'date_created' => $date,
			'type' => $type,
			'parent_id' => $parent_id,
		);

		$args = $settings;
		$args['attachments'] = $attachments;

		do_action( 'zpm_new_comment', $args );

		if ($subject !== '') {
			$wpdb->insert($table_name, $settings);
			$last_comment = $wpdb->insert_id;
			$zpmMessages->addReadMessage($last_comment);
		} else {
			$last_comment = false;
		}

		if ($attachments) {
			$currentUserId = get_current_user_id();
			foreach ($attachments as $attachment) {
				$parent_id = (!$last_comment) ? '' : $last_comment;
				$attachment_type = isset($attachment['attachment_type']) ? $attachment['attachment_type'] : 'task';
				$subject_id = isset($attachment['subject_id']) ? $attachment['subject_id'] : $subject_id;
				$settings['user_id'] = $currentUserId;
				$settings['subject'] = $attachment_type;
				$settings['subject_id'] = $subject_id;
				$settings['parent_id'] = $parent_id;
				$settings['type'] = serialize('attachment');
				$settings['message'] = serialize($attachment['attachment_id']);
				$wpdb->insert($table_name, $settings);
			}
		}

		if ($subject == 'task') {
			$last_comment = Tasks::get_comment( $last_comment );
			$message = new Message($last_comment);
			$html = $message->html();
			$last_comment->message = maybe_unserialize( $last_comment->message );
			$last_comment->message = html_entity_decode( $last_comment->message );
			
			
			$attachments = Tasks::get_comment_attachments( $last_comment->id );
			$user = BaseController::get_project_manager_user( $last_comment->user_id );
			
			$last_comment->username = $user['name'];
			$attachments_array = [];

			foreach ($attachments as $attachment) {
				$this_attachment = wp_get_attachment_url( unserialize( $attachment->message ) );
				array_push( $attachments_array, $this_attachment );
			}

			$last_comment->attachments = $attachments_array;
			$task = Tasks::get_task( $subject_id );

			Utillities::sendMentionEmails($last_comment->message, 'task', $task);

			$files = Tasks::get_task_attachments($subject_id);
			ob_start();
			foreach($files as $attachment) : ?>
				<?php $file = new File($attachment); ?>
				<?php echo $file->html(); ?>
			<?php endforeach;
			$filesHtml = ob_get_clean();

			$response = array(
				'html' => $html,
				'subject_object' => $task,
				'comment' => $last_comment,
				'files_html' => $filesHtml
			);
			
			if ($send_email) {
				Emails::send_comment_notification( $last_comment, $task, 'task' );
			}
		} elseif ($subject == 'project') {
			$last_comment = Projects::get_comment($last_comment);
			$message = new Message($last_comment);
			$last_comment->message = maybe_unserialize( $last_comment->message );
			$last_comment->message = html_entity_decode( $last_comment->message );
			$html = $message->html();
			$attachments = Projects::get_comment_attachments( $last_comment->id );
			$user = BaseController::get_project_manager_user( $last_comment->user_id );
			
			$last_comment->username = $user['name'];
			$attachments_array = [];

			foreach ($attachments as $attachment) {
				$this_attachment = wp_get_attachment_url( unserialize( $attachment->message ) );
				array_push( $attachments_array, $this_attachment );
			}

			$last_comment->attachments = $attachments_array;

			$project = Projects::get_project( $subject_id );

			Utillities::sendMentionEmails($last_comment->message, 'project', $project);

			$response = array(
				'html' => $html,
				'subject_object' => $project,
				'comment' => $last_comment,
				'project' => true
			);

			if ($send_email) {
				Emails::send_comment_notification( $last_comment, $project, 'project' );
			}
			
		} else {
			$html = Projects::file_html($attachments[0]['attachment_id'], $wpdb->insert_id);
			$attachments[0]['url'] = wp_get_attachment_url( $attachments[0]['attachment_id'] );
			$attachments[0]['task_id'] = $subject_id;
			$response = [
				'html' => $html,
				'data' => $attachments[0]
			];
		}
		
		echo json_encode($response);
		die();
	}

	// Uploads a task file
	public function uploadTaskFile() {
		global $wpdb;
		$userId = isset($_POST['user_id']) ? $_POST['user_id'] : get_current_user_id();
		$taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$fileId = isset($_POST['file_id']) ? $_POST['file_id'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$parentId = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;

		$task = new Task( $taskId );
		$task->addFile( $fileId, $parentId, $type, $userId );
	
		echo json_encode(['success']);
		die();
	}

	/* Project Ajax functions */
	public function new_project() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );

		$manager = ZephyrProjectManager();
		$Project = new Projects();
		$data = array();
		if (isset($_POST['project_name'])) {
			$data['name'] = stripslashes(sanitize_text_field($_POST['project_name']));
		}
		if (isset($_POST['project_description'])) {
			$data['description'] = stripslashes(sanitize_textarea_field($_POST['project_description']));
		}
		if (isset($_POST['project_team'])) {
			$data['team'] = serialize($_POST['project_team']);
		} else {
			$data['team'] = get_current_user_id();
		}
		if (isset($_POST['project_categories'])) {
			$data['categories'] = serialize($_POST['project_categories']);
		}
		if (isset($_POST['project_start_date'])) {
			$data['date_start'] = sanitize_text_field($_POST['project_start_date']);
		}
		if (isset($_POST['project_due_date'])) {
			$data['date_due'] = serialize( sanitize_text_field($_POST['project_due_date']) );
		}
		if ( isset( $_POST['categories'] ) ) {
			$data['categories'] = serialize( $_POST['categories'] );
		}

		$data['type'] = isset($_POST['type']) ? sanitize_text_field ($_POST['type']) : 'list';
		$data['completed'] = '0';
		$data['priority'] = isset($_POST['priority']) ? sanitize_text_field ($_POST['priority']) : 'priority_none';

		$data = apply_filters( 'zpm_new_project_data', $data );

		$last_id = Projects::new_project( $data );
		$project = Projects::get_project( $last_id );
		$manager::add_project( $project );

		do_action( 'zpm_new_project', $project );
		Projects::update_progress($last_id);
		Emails::new_project_email($last_id);

		$username = Members::get_member_name( $project->user_id );

		$theme = isset($_POST['theme']) ? $_POST['theme'] : 'default';
		//$html = Projects::frontend_project_item($project, $theme);
		$html = Projects::new_project_cell($project);
		$response = array(
			'html' 			=> $html,
			'frontend_html' => $html,
			'project' 		=> $project,
			'username'		=> $username,
			'post'		    => $_POST
		);

		echo json_encode($response);
		die();
	}

	/**
	* Removes a project from the database
	*/
	public function remove_project() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project = Projects::get_project( $_POST['project_id'] );
		Projects::delete_project( $_POST['project_id'] );
		do_action( 'zpm_project_deleted', $project );

		//Emails::deleted_project_email( $_POST['project_id'] );
		$return = array( 
			'project_count' => Projects::project_count()
		);
		echo json_encode($return);
		die();
	}

	public function archiveProject() {
		$id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
		$archived = isset($_POST['archived']) ? $_POST['archived'] : true;

		$args = [
			'archived' => $archived
		];

		Projects::update($id, $args);
		echo json_encode([]);
		die();
	}

	/**
	* Ajax function to save changes made to a project settings
	*/
	public function save_project() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_PROJECTS_TABLE;
		$projectId = $_POST['project_id'];
		$old_project = Projects::get_project($projectId);
		$old_name = stripslashes($old_project->name);
		$old_description = $old_project->description;
		$date = date('Y-m-d H:i:s');
		$settings = array();
		$name = isset($_POST['project_name']) ? stripslashes(sanitize_text_field( $_POST['project_name'])) : '';
		$description = isset($_POST['project_description']) ? stripcslashes(sanitize_textarea_field( $_POST['project_description'])) : '';
		$start_date = isset($_POST['project_start_date']) ? sanitize_text_field( $_POST['project_start_date']) : '';
		$due_date = isset($_POST['project_due_date']) ? sanitize_text_field( $_POST['project_due_date']) : '';
		$categories = isset($_POST['project_categories']) ? serialize($_POST['project_categories']) : serialize([]);
		$priority = isset($_POST['priority']) ? $_POST['priority'] : 'priority_none';
		$projectId = $_POST['project_id'];
		$project = Projects::get_project($projectId);

		$settings = array(
			'name' 		  => $name,
			'description' => $description,
			'date_start'  => $start_date,
			'date_due'    => $due_date,
			'categories'  => $categories,
			'priority'    => $priority
		);

		if ( isset( $_POST['assignees'] ) ) {
			$settings['assignees'] = zpm_array_to_comma_string($_POST['assignees']);
		}

		$settings = apply_filters( 'zpm_update_project_data', $settings );

		if ( Zephyr::isPro() ) {
			$settings['custom_fields'] = isset($_POST['custom_fields']) ? serialize( $_POST['custom_fields']) : '';
		}

		$where = array(
			'id' => $_POST['project_id']
		);

		$wpdb->update( $table_name, $settings, $where );
		$last_id = $wpdb->insert_id;

		if ($old_name !== $settings['name']) {
			Activity::log_activity($this->get_user_id(), $_POST['project_id'], $old_name, $settings['name'], 'project', 'project_changed_name' );
		}

		if ($old_description !== $settings['description']) {
			Activity::log_activity($this->get_user_id(), $_POST['project_id'], '', $settings['name'], 'project', 'project_changed_description' );
		}

		$general_settings = Utillities::general_settings();
		$start_datetime = new DateTime( $settings['date_start'] );
		$due_datetime = new DateTime( $settings['date_due'] );
		$start_date = ($start_datetime->format('Y-m-d') !== '-0001-11-30') ? date_i18n($general_settings['date_format'], strtotime($settings['date_start'])) : __( 'Not set', 'zephyr-project-manager' );
		$due_date = ($due_datetime->format('Y-m-d') !== '-0001-11-30') ? date_i18n($general_settings['date_format'], strtotime($settings['date_due'])) : __( 'Not set', 'zephyr-project-manager' );

		$categories = isset($_POST['project_categories']) ? $_POST['project_categories'] : array();

		$status_slug = isset($_POST['status']) ? $_POST['status'] : '';
		$status = Utillities::get_status( $status_slug );
		$status_color = isset( $status['color'] ) ? $status['color'] : '';

		$data = array(
			'id' => $_POST['project_id'],
			'status' => $status['name'],
			'status_color' => $status_color
		);

		Projects::update_project_status( $_POST['project_id'], $status['name'], $status_slug );

		if (Projects::getStatus($project) !== $status_slug && $status_slug == 'completed') {
			do_action('zpm_project_completed', $project);
		}

		if ($settings['assignees'] !== $project->assignees) {
			$assignees = Projects::getAssignees($project);
			do_action('zpm_project_assigned', $project, $assignees);
		}

		do_action('zpm_project_status_changed', $data);

		$response = array (
			'response' => $settings,
			'categories' => $categories,
			'formatted_start_date' => $start_date,
			'formatted_due_date' => $due_date
		);

		ob_start();

		do_action( 'zpm_project_preview_fields', $old_project );

		if (isset($_POST['shortcode']) && $_POST['shortcode'] == true) {
			$response['shortcode_html'] = do_shortcode( '[zephyr_project id="' . $projectId . '"]' );
		}

		$response['custom_fields'] = ob_get_clean();

		echo json_encode( $response );
		die();
	}

	public function update_task_priority() {
		global $wpdb;

		$table_name = ZPM_TASKS_TABLE;

		$settings = array();

		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '-1';
		$task = Tasks::get_task( $task_id );

		if (isset($_POST['priority'])) {
			$settings['priority'] = $_POST['priority'];
		}
		
		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );

		$user_id = get_current_user_id();
		$member = Members::get_member( $user_id );
		$priority = isset($settings['priority']) ? $settings['priority'] : 'priority_none';
		$status = Utillities::get_status($priority);
		$priority_label = $status['name'];

		$event_message = '';

		if ( $task->priority !== $settings['priority'] ) {
			$event_message = sprintf( __( '%s updated the priority of %s to %s', 'zephyr-project-manager' ), $member['name'], $task->name, $priority_label );
		}

		$response = [
			'post' => $_POST,
			'event_message' => $event_message
		];
		echo json_encode( $response );
		die();
	}

	public function update_task_start_date() {
		global $wpdb;

		$table_name = ZPM_TASKS_TABLE;

		$settings = array();

		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '-1';


		if ( isset( $_POST['datetime'] ) ) {
			$format = 'Y-m-d H:i:s';
			$date = date($format, $_POST['datetime'] / 1000);
			$settings['date_start'] = $date;
		}
		
		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );

		echo json_encode( $settings );
		die();
	}


	public function update_task_end_date() {
		global $wpdb;

		$table_name = ZPM_TASKS_TABLE;

		$settings = array();

		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '-1';


		if ( isset( $_POST['datetime'] ) ) {
			$format = 'Y-m-d H:i:s';
			$date = date($format, $_POST['datetime'] / 1000);
			$settings['date_due'] = $date;
		}
		
		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );

		echo json_encode( $settings );
		die();
	}

	/**
	* Ajax function to save changes made to a project settings
	*/
	public function switch_project_type() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_PROJECTS_TABLE;
		$settings = array();

		$type = isset($_POST['type']) ? $_POST['type'] : 'list';

		$settings = array(
			'type'  => $type
		);

		$where = array(
			'id' => $_POST['project_id']
		);
		$wpdb->update( $table_name, $settings, $where );

		echo json_encode(array(
			'response' => 'success'
		));
		die();
	}

	public function update_project_status() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		
		$project_id = $_POST['project_id'];
		$status = isset($_POST['status']) ? sanitize_textarea_field($_POST['status']) : '';
		$status_color = isset($_POST['status_color']) ? sanitize_text_field($_POST['status_color']) : '';

		$data = array(
			'id' => $project_id,
			'status' => $status,
			'status_color' => $status_color
		);

		Projects::update_project_status($project_id, $status, $status_color);

		do_action('zpm_project_status_changed', $data);

		echo json_encode(array(
			'status' => 'success',
			'data' => $data
		));
		die();
	}

	public function update_project_members() {
		global $wpdb;
		$project_id = $_POST['project_id'];
		$members = $_POST['members'];
		Projects::update_members( $project_id, $members );
		echo json_encode( $members );
		die();
	}

	/**
	* Export a project to CSV or JSON and download the file
	* @return json
	*/
	public function export_project() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project = Projects::get_project($_POST['project_id']);
		$upload_dir = wp_upload_dir();

		if (isset($_POST['export_to']) && $_POST['export_to'] == 'json') {
			$tasks = Tasks::get_project_tasks($_POST['project_id'], true);
			$project->tasks = $tasks;
			$formattedData = json_encode($project);
			$filename = $upload_dir['basedir'] . '/Project - ' . stripslashes($project->name) . '.json';
			$handle = fopen($filename,'w+');
			fwrite($handle, $formattedData);
			fclose($handle);
			$filename = $upload_dir['baseurl'] . '/Project - ' . stripslashes($project->name) . '.json';
			$response = array(
				'file_name' => 'Project - ' . stripslashes($project->name) . '.json',
				'file_url'  => $filename
			);
			echo json_encode($response);
		} else {
			$filename = $upload_dir['basedir'] . '/Project - ' . $project->name . '.csv';	 
			$filename = fopen($filename, 'w');
			fputcsv($filename, array('ID', 'User ID', 'Name', 'Description', 'Completed', 'Assignees', 'Categories', 'Date Created', 'Date Due', 'Date Start', 'Date Completed', 'Other Data'));

			$completed = $project->completed;
			if ($completed == '1') {
				$completed = 'Yes';
			} else {
				$completed = 'No';
			}

			$filedata = [
				'id' => $project->id,
				'user_id' => $project->user_id,
				'name' => $project->name,
				'description' => $project->description,
				'completed' => $completed,
				'assignees' => Members::memberIdStringToNameString($project->assignees),
				'categories' => implode(',', maybe_unserialize($project->categories)),
				'date_created' => $project->date_created,
				'date_due' => $project->date_due,
				'date_start' => $project->date_start,
				'date_completed' => $project->date_completed,
				'other_data' => '',
			];


			fputcsv($filename, (array) $filedata);
			$filename = $upload_dir['baseurl'] . '/Project - ' . $project->name . '.csv';

			// Download project tasks to CSV as well
			$tasks = Tasks::get_project_tasks($_POST['project_id'], true);
			$tasks = Projects::getOrderedTasks($projectId, $tasks);
			$tasks_file = $upload_dir['basedir'] . '/' . $project->name . ' - Tasks.csv';
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="project_tasks.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');
			 
			$tasks_file = fopen($tasks_file, 'w');

			fputcsv($tasks_file, array('ID', 'Parent ID', 'Created By', 'Project', 'Assignee', 'Task Name', 'Task Description', 'Categories', 'Completed', 'Created At', 'Start Date', 'Due Date', 'Completed At'));
	
			// save each row of the data
			foreach ($tasks as $row) {
				$completed = $row->completed;
				if ($completed == '1') {
					$completed = 'Yes';
				} else {
					$completed = 'No';
				}
				$data = (object) [
					'id' => $row->id,
					'parent_id' => $row->parent_id,
					'created_by' => $row->user_id,
					'project' => $row->project,
					'assignee' => Members::memberIdStringToNameString($row->assignee),
					'name' => $row->name,
					'description' => $row->description,
					'categories' => implode(',', maybe_unserialize($row->categories)),
					'completed' => $completed,
					'created' => $row->date_created,
					'start' => $row->date_start,
					'due' => $row->date_due,
					'completed_at' => $row->date_completed
				];

				fputcsv($tasks_file, get_object_vars($data));
			}

			$tasks_file = $upload_dir['baseurl'] . '/' . $project->name . ' - Tasks.csv';
			
			$files = array(
				'project_csv' => $filename,
				'project_tasks_csv' => $tasks_file,
			);

			echo json_encode($files);
		}

		die();
	}

	/**
	* Print a project
	* @return json
	*/
	public function print_project() {
		$project_id = $_POST['project_id'];
		$project = Projects::get_project($project_id);
		$project_tasks = Tasks::get_project_tasks($project_id);
		$data = array();
		$data['project'] = $project;

		foreach ($project_tasks as $project_task) {
			$user = BaseController::get_user_info($project_task->assignee);
			$project_task->username = $user;
			$data['tasks'][] = $project_task;
		}

		echo json_encode($data);
		die();
	}

	/**
	* Get the data for a project chart
	* @return json
	*/
	public function project_progress() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
		$task_count = Tasks::get_project_task_count($project_id);
		$completed_tasks = Tasks::get_project_completed_tasks($project_id);
		$args = array( 'project_id' => $project_id );
		$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
		$pending_tasks = $task_count - $completed_tasks;
		$percent_complete = ($task_count !== 0) ? floor($completed_tasks / $task_count * 100): '100';
		$chart_data = get_option('zpm_chart_data', array());

		//$data = isset( $chart_data[$project_id] ) ? $chart_data[$project_id] : array();
		$data = Utillities::get_project_chart_data( $project_id );
		$response = array(
			'chart_data' => $data
		);
		echo json_encode($response);
		die();
	}

	public function project_task_progress() {
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
		$task_count = Tasks::get_project_task_count( $project_id );
		$completed_tasks = Tasks::get_project_completed_tasks( $project_id );
		$args = array( 
			'project_id' => $project_id 
		);
		$overdue_tasks = sizeof( Tasks::get_overdue_tasks( $args ) );
		$pending_tasks = $task_count - $completed_tasks;
		$percent_complete = ($task_count !== 0) ? floor($completed_tasks / $task_count * 100): '100';
		
		$response = array(
			'total' => $task_count,
			'completed' => $completed_tasks,
			'overdue' => $overdue_tasks,
			'pending' => $pending_tasks,
			'percent' => $percent_complete
		);

		echo json_encode($response);
		die();
	}

	/**
	* Get a single project
	* @return json
	*/
	public function get_project() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$Tasks = new Tasks();
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '-1';
		$general_settings = Utillities::general_settings();
		$project = Projects::get_project($project_id);
		$comments = Projects::get_comments($project_id);
		$categories = maybe_unserialize($project->categories);
		$comments_html = '';

		foreach ($comments as $comment) {
			$html = Projects::new_comment($comment);
			$comments_html .= $html;
		}

		$start_date = new DateTime($project->date_start);
		$due_date = new DateTime($project->date_due);
		$start_date = $start_date->format('Y') !== "-0001" ? date_i18n($general_settings['date_format'], strtotime($project->date_start)) : __( 'None', 'zephyr-project-manager' );
		$due_date = $due_date->format('Y') !== "-0001" ? date_i18n($general_settings['date_format'], strtotime($project->date_due)) : __( 'None', 'zephyr-project-manager' );

		$total_tasks = Tasks::get_project_task_count( $project->id );
		$completed_tasks = Tasks::get_project_completed_tasks( $project->id );
		$active_tasks = (int) $total_tasks - (int) $completed_tasks;
		$message_count = sizeof( $comments );

		$priority = property_exists( $project, 'priority' ) ? $project->priority : 'priority_none';
		$priority_label = Utillities::get_priority_label( $priority );
		$status = Utillities::get_status( $priority );

		ob_start(); ?>

			<?php if ( $priority !== "priority_none" && $priority_label !== "" ) : ?>
				<span class="zpm-task-priority-bubble <?php echo $priority; ?>" style="background: <?php echo $status['color']; ?>; color: <?php echo $status['color'] !== '' ? '#fff' : ''; ?>"><?php echo $status['name']; ?></span>

			<?php endif; ?>

		<?php $priority_html = ob_get_clean();

		ob_start();
		?>
			<span id="zpm_project_modal_dates" class="zpm_project_overview_section">
				<span id="zpm_project_modal_start_date">
					<label class="zpm_label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?>:</label>
					<span class="zpm_project_date"><?php echo $start_date; ?></span>
				</span>

				<span id="zpm_project_modal_due_date">
					<label class="zpm_label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?>:</label>
					<span class="zpm_project_date"><?php echo $due_date; ?></span>
				</span>
			</span>

			<div id="zpm_project_progress">
				<span class="zpm_project_stat">
					<p class="zpm_stat_number"><?php echo $completed_tasks; ?></p>
					<p><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></p>
				</span>
				<span class="zpm_project_stat">
					<p class="zpm_stat_number"><?php echo $active_tasks; ?></p>
					<p><?php _e( 'Active Tasks', 'zephyr-project-manager' ); ?></p>
				</span>
				<span class="zpm_project_stat">
					<p class="zpm_stat_number"><?php echo $message_count; ?></p>
					<p><?php _e( 'Message', 'zephyr-project-manager' ); ?></p>
				</span>
			</div>

			<span id="zpm_project_modal_description" class="zpm_project_overview_section">
				<label class="zpm_label"><?php _e( 'Description', 'zephyr-project-manager' ); ?>:</label>
				<p class="zpm_description"><?php echo $project->description; ?></p>
				<?php if ($project->description == "") : ?>
					<p class="zpm-soft-error"><?php _e('None', 'zephyr-project-manager'); ?></p>
				<?php endif; ?>
			</span>

			<?php do_action( 'zpm_project_preview_fields', $project ); ?>

			<span id="zpm_project_modal_categories" class="zpm_project_overview_section">
				<label class="zpm_label"><?php _e( 'Categories', 'zephyr-project-manager' ); ?>:</label>
				<?php if ( is_array( $categories ) && sizeof( $categories ) ) : ?>
					<?php foreach ($categories as $category) : ?>
						<?php $category = Categories::get_category($category); ?>
						<span class="zpm_project_category"><?php echo $category->name; ?></span>
					<?php endforeach; ?>

				<?php else: ?>
					<p class="zpm-soft-error"><?php _e('No categories assigned', 'zephyr-project-manager'); ?></p>
				<?php endif; ?>
			</span>
		<?php 
		$overview_html = ob_get_clean();

		$project->overview_html = $overview_html;
		$project->comments_html = $comments_html;
		$project->priority_html = $priority_html;
		$project->attachments = Projects::get_attachments( $project->id );

		echo json_encode($project);
		die();
	}

	/**
	* Returns a list of all projects
	* @return json
	*/
	public function get_projects() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$projects = Projects::get_available_projects();
		echo json_encode($projects);
		die();
	}

	/**
	* Like/hearts a project
	* @return json
	*/
	public function like_project() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project_id = $_POST['project_id'];
		$user_id = $this->get_user_id();
		$liked_projects = unserialize(get_option( 'zpm_liked_projects_' . $user_id, false ));

		if (!$liked_projects) {
			$liked_projects = array();
		}

		if (!in_array($project_id, $liked_projects)) {
			$liked_projects[] = $project_id;
		} else {
			$liked_projects = array_diff($liked_projects, [$project_id]);
		}

		$liked_projects = serialize($liked_projects);
		update_option( 'zpm_liked_projects_' . $user_id, $liked_projects );
		echo json_encode($liked_projects);
		die();
	}

	/**
	* Copies a project and adds the copy in the database
	* @return json
	*/
	public function copy_project() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project_id = (isset($_POST['project_id'])) ? $_POST['project_id'] : '';
		$copy_options = (isset($_POST['copy_options'])) ? $_POST['copy_options'] : '';
		$name = isset($_POST['project_name']) ? sanitize_text_field( $_POST['project_name']) : '';
		$args = [
			'project_id' => $project_id,
			'project_name' => $name,
			'copy_options' => $copy_options,
		];
		$last_project = Projects::copy_project($args);

		$response = array(
			'html' => Projects::new_project_cell($last_project)
		);
		echo json_encode($response);
		die();
	}

	/**
	* Ajax function to add a project to the dashboard
	* @uses $_POST['project_id'] Project ID
	* @return string
	*/
	public function add_project_to_dashboard() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : false;
		if ($project_id) {
			Projects::add_to_dashboard($project_id);
		}
		return 'Success';
	}

	public function removeProjectFromDashboard() {
		$projectId = isset($_POST['id']) ? $_POST['id'] : false;
		Projects::removeFromDashboard($projectId);
		echo json_encode([]);
		wp_die();
	}

	/**
	* Ajax function to remove a project from the dashboard
	* @uses $_POST['project_id'] Project ID
	* @return string
	*/
	public function remove_project_from_dashboard() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : false;
		if ($project_id) {
			Projects::remove_from_dashboard($project_id);
		}
		return 'Success';
	}

	/**
	* Create a new task and save it in the database
	* @return json
	*/
	public function new_task() {
		global $wpdb;

		$manager = ZephyrProjectManager();

		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce', false );
		$table_name = ZPM_TASKS_TABLE;
		$assignee = isset($_POST['task_assignee']) && $_POST['task_assignee'] !== '-1' ? $_POST['task_assignee'] : '-1';
		$project = isset($_POST['task_project']) ? $_POST['task_project'] : '';
		$name = isset($_POST['task_name']) ? stripslashes(sanitize_text_field( $_POST['task_name'])) : '';
		$description = isset($_POST['task_description']) ? stripslashes(sanitize_textarea_field( $_POST['task_description'])) : '';
		$date = date('Y-m-d H:i:s');
		$date_due = isset($_POST['task_due_date']) ? sanitize_text_field( $_POST['task_due_date']) : '';
		$date_start = isset($_POST['task_start_date']) ? sanitize_text_field( $_POST['task_start_date']) : $date;
		$team = isset($_POST['team']) ? $_POST['team'] : '';
		$priority = isset($_POST['priority']) ? $_POST['priority'] : 'priority_none';
		$status = isset($_POST['status']) ? $_POST['status'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : 'default';
		$parentId = isset($_POST['parent-id']) ? $_POST['parent-id'] : '-1';

		// Format start and end dates
		if (!empty($date_due)) {
			$date_due = date("Y-m-d H:i:s", strtotime($date_due));
		}
		if (!empty($date_start)) {
			$date_start = date("Y-m-d H:i:s", strtotime($date_start));
		}

		if ( is_array( $assignee ) ) {
			$assignee_string = '';
			foreach ($assignee as $id) {
				$assignee_string .= $id . ',';
			}
			$assignee = $assignee_string;
		}

		$settings = array(
			'user_id' 	  	 => $this->get_user_id(),
			'parent_id'		 => $parentId,
			'assignee' 	  	 => $assignee,
			'project' 	  	 => $project,
			'name' 		  	 => $name,
			'description' 	 => $description,
			'completed'   	 => false,
			'date_start'  	 => $date_start,
			'date_due' 	  	 => $date_due,
			'date_created' 	 => $date,
			'date_completed' => '',
			'priority'		 => $priority,
			'status'		 => $status
		);

		if ( Utillities::table_column_exists( ZPM_TASKS_TABLE, 'team' ) ) {
			$settings['team'] = $team;
		}

		if ( Zephyr::isPro() ) {
			$settings['custom_fields'] = isset($_POST['task_custom_fields']) ? serialize( $_POST['task_custom_fields']) : '';
			$settings['kanban_col'] = isset($_POST['kanban_col']) ? sanitize_text_field( $_POST['kanban_col']) : '';
			$settings['kanban_col'] = apply_filters( 'zpm_new_task_kanban_col', $settings['kanban_col'], $settings );
		}

		$settings = apply_filters( 'zpm_new_task_data', $settings );

		$frontend_settings = get_option('zpm_frontend_settings');

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		$task = Tasks::get_task( $last_id );
		Utillities::sendMentionEmails($description, 'task', $task);
		$manager::add_task( $task );

		$recurrence = isset($_POST['recurrence']) ? $_POST['recurrence'] : '';
		$recurrence_type = isset($recurrence['type']) ? $recurrence['type'] : 'default';
		$recurrence_expiration = isset($recurrence['expires']) ? $recurrence['expires'] : '';
		$recurrence_days = isset($recurrence['days']) ? $recurrence['days'] : '';

		Tasks::update_task_data( $last_id, array(
			'type' => $recurrence_type,
			'expires' => $recurrence_expiration,
			'days' => $recurrence_days
		));
		
		$due_date = new DateTime($task->date_due);
		$task->date_due = $due_date->format('Y') !== '-0001' ? $due_date->format('d M') : '';
		$task->original_due_date = $settings['date_due'];
		$task_project = Projects::get_project($task->project);
		$task->project_name = is_object( $task_project ) ? $task_project->name : '';

		if ($task->project !== "-1") {
			$completed_project_tasks = Tasks::get_project_completed_tasks( $task->project );
			$project_tasks = Tasks::get_project_tasks( $task->project );

			if ( $completed_project_tasks == sizeof( $project_tasks ) ) {
				$completed = '1';
			} else {
				$completed = '0';
			}

			Projects::mark_complete( $task->project, $completed );
		}

		do_action( 'zpm_new_task', $task );
		do_action( 'zpm_task_created', $task );

		Activity::log_activity( $settings['user_id'], $last_id, '', $name, 'task', 'task_added' );
		Activity::log_activity( $settings['user_id'], $last_id, '', $name, 'task', 'task_assigned' );
		
		$task->sending_email = "true";
		$emails = Emails::assignedTaskEmail( $task );
		
		Projects::update_progress( $project );

		$date = new DateTime($task->date_due);
    	$frontend = isset($_POST['frontend']) ? true : false;

		$kanban_html = "";

		if (class_exists('Inc\\ZephyrProjectManager\\Kanban')) {
			$kanban = new Kanban();
			$kanban_html = method_exists($kanban, 'taskHtml') ? Kanban::taskHtml( $task, $frontend ) : '';
			$kanban_col = isset($settings['kanban_col']) ? $settings['kanban_col'] : '1';
			$task->kanban_html = $kanban_html;
			$task->kanban_col = $kanban_col;
		}

		if (!Tasks::hasParent($task)) {
			$task->new_task_html = Tasks::new_task_row( $task, $frontend );
		} else {
			$task->new_task_html = Tasks::subtaskItemHtml($task);
		}
		
		$task->id = $last_id;
		$task->name = $name;
		$task->username = Members::get_member_name( $this->get_user_id() );
		$task->settings = $settings;

		if (isset($_POST['shortcode']) && $_POST['shortcode']) {
			$type = isset($_POST['shortcode_type']) ? $_POST['shortcode_type'] : 'cards';
			$task->shortcode_html = do_shortcode( '[zephyr_task id="' . $task_id . '" type="' . $type . '"]' );
		}
		$task->settings = $settings;
		$task->emails = $emails;
		echo json_encode($task);
		die();
	}

	/**
	* Loads the content for the view task modal
	* @return json
	*/
	public function view_task() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '-1';
		ob_start();
		Tasks::view_task_modal( $task_id );
		$html = ob_get_clean();
		echo $html;
		die();
	}

	/**
	* Copies a task and adds the duplicate in the database
	* @return json
	*/
	public function copy_task() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task_id = (isset($_POST['task_id'])) ? $_POST['task_id'] : '';
		$task = Tasks::get_task($task_id);
		$copy_options =  (isset($_POST['copy_options'])) ? $_POST['copy_options'] : '';
		$date = date('Y-m-d H:i:s');
		$user_id = $this->get_user_id();
		$assignee = in_array('assignee', $copy_options) ? $task->assignee : $date;
		$name = isset($_POST['task_name']) ? sanitize_text_field($_POST['task_name']) : '';
		$description = in_array('description', $copy_options) ? $task->description : '';
		$date_start = in_array('start_date', $copy_options) ? $task->date_start : $date;
		$date_due = in_array('due_date', $copy_options) ? $task->date_due : '';
		$project_id = isset($_POST['project']) ? $_POST['project'] : $task->project;

		$settings = array(
			'user_id' 		 => $user_id,
			'assignee' 		 => $assignee,
			'project' 		 => $project_id,
			'name' 			 => $name,
			'description' 	 => $description,
			'completed' 	 => $task->completed,
			'date_start' 	 => $date_start,
			'date_due' 		 => $date_due,
			'date_created' 	 => $date,
			'date_completed' => ''
		);

		if (class_exists('Inc\\ZephyrProjectManager\\CustomFields')) {
			if (property_exists($task, 'custom_fields')) {
				$settings['custom_fields'] = $task->custom_fields;
			}
		}

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		$subtasks = Tasks::get_subtasks($task_id);

		foreach ($subtasks as $subtask) {
			$settings = array(
				'parent_id'		 => $last_id,
				'user_id' 		 => $user_id,
				'assignee' 		 => $subtask->assignee,
				'project' 		 => $project_id,
				'name' 			 => $subtask->name,
				'completed' 	 => $subtask->completed,
				'date_start' 	 => $subtask->date_start,
				'date_due' 		 => $subtask->date_due,
				'date_created' 	 => $subtask->date_created,
				'date_completed' => ''
			);

			$wpdb->insert( $table_name, $settings );
		}

		$new_task = Tasks::get_task($last_id);

		$user_id = get_current_user_id();
		$member = Members::get_member( $user_id );
		$event_message = sprintf( __( '%s copied the task %s', 'zephyr-project-manager' ), $member['name'], $task->name );

		$response = array(
			'html' => Tasks::new_task_row($new_task),
			'task' => $new_task,
			'event_message' => $event_message
		);

		if (class_exists('Inc\\ZephyrProjectManager\\Kanban')) {
			$kanban = new Kanban();
			$kanban_html = method_exists($kanban, 'taskHtml') ? Kanban::taskHtml( $new_task, $frontend ) : '';
			$response['kanban_html'] = $kanban_html;
		}
		

		Projects::update_progress( $task->project );

		echo json_encode($response);
		die();
	}

	/**
	* Exports a task to a JSON or CSV file
	*
	* @return json
	*/
	public function export_task() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$task = Tasks::get_task($_POST['task_id']);
		$upload_dir = wp_upload_dir();

		if (isset($_POST['export_to']) && $_POST['export_to'] == 'json') {
			// Save JSON file
			$data = array($task);
			$formattedData = json_encode($data);
			$filename = $upload_dir['basedir'] . '/Task - ' . $task->name . '.json';
			$handle = fopen($filename,'w+');
			fwrite($handle, $formattedData);
			fclose($handle);
			$filename = $upload_dir['baseurl'] . '/Task - ' . $task->name . '.json';
			$response = [
				'file_url'  => $filename,
				'file_name' => 'Task - ' . $task->name . '.json'
			];
			echo json_encode($response);
		} else {
			$filename = $upload_dir['basedir'] . '/Task - ' . $task->name . '.csv';	 
			$filename = fopen($filename, 'w');
			fputcsv($filename, array('ID', 'Parent ID', 'Created By', 'Project', 'Assignee', 'Task Name', 'Task Description', 'Categories', 'Completed', 'Created At', 'Start Date', 'Due Date', 'Completed At'));

			$data = (object) [
				'id' => $task->id,
				'parent_id' => $task->parent_id,
				'created_by' => $task->user_id,
				'project' => $task->project,
				'assignee' => $task->assignee,
				'name' => $task->name,
				'description' => $task->description,
				'categories' => implode(',', maybe_unserialize($task->categories)),
				'completed' => $task->completed,
				'created' => $task->date_created,
				'start' => $task->date_start,
				'due' => $task->date_due,
				'completed_at' => $task->date_completed
			];

			fputcsv($filename, get_object_vars($data));

			$filename = $upload_dir['baseurl'] . '/Task - ' . $task->name . '.csv';
			$response = [
				'file_url'  => $filename,
				'file_name' => 'Task - ' . $task->name . '.csv'
			];
			echo json_encode($response);
		}
		die();
	}

	// Exports all tasks
	public function export_tasks() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$tasks = Tasks::get_tasks();
		$upload_dir = wp_upload_dir();

		if (isset($_POST['export_to']) && $_POST['export_to'] == 'json') {
			
			$formattedData = json_encode($tasks);
			$filename = $upload_dir['basedir'] . '/All Tasks.json';
			$handle = fopen($filename,'w+');
			fwrite($handle, $formattedData);
			fclose($handle);
			$filename = $upload_dir['baseurl'] . '/All Tasks.json';
			echo json_encode($filename);
		} else {
			$filename = $upload_dir['basedir'] . '/All Tasks.csv';
			// save the column headers

			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="all_tasks.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');
			 
			$filename = fopen($filename, 'w');

			fputcsv($filename, array('ID', 'Parent ID', 'Created By', 'Project', 'Assignee', 'Task Name', 'Task Description', 'Categories', 'Completed', 'Created At', 'Start Date', 'Due Date', 'Completed At', 'Team', 'Custom Fields', 'Status', 'Kanban Col', 'Priority', 'Other Data'));
	
			// save each row of the data
			foreach ($tasks as $row) {
				fputcsv( $filename, get_object_vars( $row ) );
			}
			$filename = $upload_dir['baseurl'] . '/All Tasks.csv';
			echo json_encode($filename);
		}
		
		die();
	}

	// Import Tasks
	public function upload_tasks() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$html = '';
		$filename = $_POST['zpm_file'];
		$file_type = $_POST['zpm_import_via'];
		$table_name = ZPM_TASKS_TABLE;
	
		if ($file_type == 'csv') {
			$row = 1;
			$taskArray = array();
			if (($handle = fopen($filename, "r")) !== FALSE) {
			    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			        $num = count($data);
			        $task = array(
			        	'id' 			 => $data[0],
			        	'parent_id' 	 => $data[1], 
			        	'user_id' 		 => $data[2],
			        	'project' 		 => $data[3],
			        	'assignee' 		 => $data[4],
			        	'name' 			 => $data[5],
			        	'description' 	 => $data[6],
			        	'categories' 	 => $data[7],
			        	'completed' 	 => $data[8],
			        	'date_created' 	 => $data[9],
			        	'date_start' 	 => $data[10],
			        	'date_due' 		 => $data[11],
			        	'date_completed' => $data[12],
			        	'team' 			 => $data[13],
			        	'custom_fields'  => $data[14],
			        	'status' 		 => $data[15],
			        	'kanban_col' 	 => $data[16],
			        	'priority' 	 	 => $data[17],
			        	'other_data' 	 => $data[18],
			        );
			        $task['date_start'] = date('Y-m-d', $task['date_start']);
			        $task['date_due'] = date('Y-m-d', $task['date_due']);
			        $task['date_completed'] = date('Y-m-d', $task['date_completed']);

			        if (!Tasks::task_exists($data[0])) {
			        	$wpdb->insert( $table_name, $task );
			        	$task = Tasks::get_task($wpdb->insert_id);
			        	if ($row > 1) {
			        		$html .= Tasks::new_task_row($task);
			        	}	
			        } else {
			        	$task['already_uploaded'] = true;
			        }

			        $row++;

			        $taskArray[] = $task;
			    }
			    fclose($handle);
			}
			$response = [
				'tasks' => $taskArray,
				'html' => $html
			];
			echo json_encode($response);
		} elseif ($file_type == 'json') {
			$json = file_get_contents($filename);
			$json_array = json_decode($json, true);
			$taskArray = array();

			foreach ($json_array as $task) {
				$task = array(
		        	'id' 			 => $task['id'],
		        	'parent_id' 	 => $task['parent_id'], 
		        	'user_id' 		 => $task['user_id'], 
		        	'project' 		 => $task['project'],
		        	'assignee' 		 => $task['assignee'],
		        	'name' 			 => $task['name'],
		        	'description' 	 => $task['description'],
		        	'categories' 	 => $task['categories'],
		        	'completed' 	 => $task['completed'],
		        	'date_created' 	 => $task['date_created'],
		        	'date_start' 	 => $task['date_start'],
		        	'date_due' 		 => $task['date_due'],
		        	'date_completed' => $task['date_completed'],
		        	'team' 			 => $data['team'],
		        	'custom_fields'  => $data['custom_fields'],
		        	'status' 		 => $data['status'],
		        	'kanban_col' 	 => $data['kanban_col'],
		        	'priority' 	 	 => $data['priority'],
		        	'other_data' 	 => $data['other_data']
		        );

		        if (!Tasks::task_exists($task['id'])) {
		        	$wpdb->insert( $table_name, $task );
		        	$task = Tasks::get_task($wpdb->insert_id);
		        	$html .= Tasks::new_task_row($task);
		        } else {
		        	$task['already_uploaded'] = true;
		        }
		        $taskArray[] = $task;
			}
			$response = [
				'tasks' => $taskArray,
				'html' => $html
			];
			echo json_encode($response);
		}
		die();
	}

	// Converts a given task to a project
	public function convert_to_project() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$task_id = (isset($_POST['task_id'])) ? $_POST['task_id'] : '';
		$task = Tasks::get_task($task_id);
		$convert_options =  (isset($_POST['convert_options'])) ? $_POST['convert_options'] : '';

		$subtasks = (in_array('subtasks', $convert_options)) ? Tasks::get_subtasks($task_id) : '';

		$table_name = ZPM_PROJECTS_TABLE;
		$date = date('Y-m-d H:i:s');
		$settings = array();
		$settings['user_id'] = $this->get_user_id();
		$settings['name'] = (isset($_POST['project_name'])) ? sanitize_text_field( $_POST['project_name']) : '';
		$settings['description'] = (in_array('description', $convert_options)) ? $task->description : '';
		$settings['completed'] = false;
		$settings['date_due'] = $task->date_start;
		$settings['date_created'] = $date;
		$settings['date_completed'] = '';
		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		if (is_array($subtasks)) {
			$tasks_table = ZPM_TASKS_TABLE;
			foreach ($subtasks as $subtask) {
				$task_settings = array();
				$task_settings['parent_id'] = '-1';
				$task_settings['user_id'] = $this->get_user_id();
				$task_settings['assignee'] = $this->get_user_id();
				$task_settings['project'] = $last_id;
				$task_settings['name'] = $subtask->name;
				$task_settings['description'] = '';
				$task_settings['completed'] = false;
				$task_settings['date_start'] = $date;
				$task_settings['date_due'] = '';
				$task_settings['date_created'] = $date;
				$task_settings['date_completed'] = '';
				$wpdb->insert( $tasks_table, $task_settings );
			}
		}
		
		$project = new Projects();
		$new_project = $project->get_project($last_id);
		echo json_encode($new_project);
		die();
	}

	public function update_task_completion() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$date = date('Y-m-d H:i:s');
		$completed = isset($_POST['completed']) ? $_POST['completed'] : '0';
		$task_id = isset($_POST['id']) ? $_POST['id'] : '-1';
		
		$settings = array(
			'completed' 		=> $_POST['completed'],
			'date_completed' 	=> $date
		);

		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );
		$task = Tasks::get_task($task_id);

		if ( $completed == '1' ) {
			Emails::task_completed_email( $task );
			do_action( 'zpm_task_completed', $task );
		}

		Tasks::updateTaskProgress($task);

		$completed_project_tasks = Tasks::get_project_completed_tasks( $task->project );
		$project_tasks = Tasks::get_project_tasks( $task->project );

		if ( $completed_project_tasks == sizeof($project_tasks) ) {
			$completed = '1';
		} else {
			$completed = '0';
		}

		do_action( 'zpm_task_status_changed', $task->id, 'completed' );

		Projects::mark_complete( $task->project, $completed );
		Projects::update_progress( $task->project );

		$response = array();
		if (!empty($task->project) && $task->project !== '-1') {
			$percent = Projects::percent_complete($task->project);
			$response['percent'] = $percent;
			Utillities::updateProjectProgress($task->project);
		}
		
		$response['custom_data'] = apply_filters( 'zpm_task_completed_response', $task->id );
		echo json_encode( $response );
		die();
	}

	public function remove_task() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$taskId =  $_POST['task_id'];
		$task = Tasks::get_task($taskId);
		$date = date('Y-m-d H:i:s');

		$settings = array(
			'id' => $taskId
		);

		Emails::delete_task_email( $_POST['task_id'] );
		do_action('zpm_task_deleted', $taskId);
		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		
		Activity::log_activity($this->get_user_id(), $_POST['task_id'], '', $task->name, 'task', 'task_deleted' );

		echo 'Success';
		die();
	}

	// Function that saves changes made to the task
	public function save_task() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$old_task = Tasks::get_task( $task_id );
		$settings = array();
		$settings['name'] 		 = (isset($_POST['task_name'])) ? stripslashes(sanitize_text_field( $_POST['task_name'])) : '';
		$settings['description'] = (isset($_POST['task_description'])) ? stripslashes(sanitize_textarea_field( $_POST['task_description'])) : '';
		$settings['assignee'] = (isset($_POST['task_assignee'])) ? $_POST['task_assignee'] : '-1';
		$settings['date_due'] = (isset($_POST['task_due_date'])) ? sanitize_text_field( $_POST['task_due_date']) : '';
		$settings['date_start'] = (isset($_POST['task_start_date'])) ? sanitize_text_field( $_POST['task_start_date']) : '';
		$settings['project'] = (isset($_POST['task_project'])) ? $_POST['task_project'] : '-1';
		$settings['team'] = (isset($_POST['team'])) ? $_POST['team'] : '';
		$settings['priority'] = (isset($_POST['priority'])) ? $_POST['priority'] : 'priority_none';
		$settings['status'] = (isset($_POST['status'])) ? $_POST['status'] : '';
		$type = ( isset( $_POST['type'] ) ) ? $_POST['type'] : 'default';

		if ( is_array( $settings['assignee'] ) ) {
			$assignee_string = '';

			foreach ($settings['assignee'] as $id) {
				$assignee_string .= $id . ',';
			}
			$settings['assignee'] = $assignee_string;
		}

		if ( Zephyr::isPro() ) {
			$settings['custom_fields'] = isset($_POST['task_custom_fields']) ? serialize( $_POST['task_custom_fields']) : '';
		}

		// Format start and end dates
		if (!empty($settings['date_start'])) {
			$settings['date_start'] = date("Y-m-d H:i:s", strtotime($settings['date_start']));
		}
		if (!empty($settings['date_due'])) {
			$settings['date_due'] = date("Y-m-d H:i:s", strtotime($settings['date_due']));
		}

		$settings = apply_filters( 'zpm_update_task_data', $settings );

		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );

		$recurrence = isset($_POST['recurrence']) ? $_POST['recurrence'] : '';
		$recurrence_type = isset($recurrence['type']) ? $recurrence['type'] : 'default';
		$recurrence_expiration = isset($recurrence['expires']) ? $recurrence['expires'] : '';
		$recurrence_days = isset($recurrence['days']) ? $recurrence['days'] : '';
		$frequency = isset($recurrence['frequency']) ? $recurrence['frequency'] : '';
		$recurrence_start = isset($recurrence['start']) ? $recurrence['start'] : '';

		Tasks::update_task_data( $task_id, array(
			'type' => $recurrence_type,
			'expires' => $recurrence_expiration,
			'days' => $recurrence_days,
			'frequency' => $frequency,
			'start'		=> $recurrence_start
		));

		$date = date('Y-m-d H:i:s');

		if ($old_task->name !== $settings['name']) {
			Activity::log_activity($this->get_user_id(), $task_id, $old_task->name, $settings['name'], 'task', 'task_changed_name' );
		}

		// Mark as complete if the status is changed to 'Completed'
		if ($old_task->status !== $settings['status'] && $settings['status'] == 'completed') {
			Tasks::complete($task_id, 1);
		}

		if ($old_task->date_due !== $settings['date_due']) {
			Activity::log_activity($this->get_user_id(), $task_id, $settings['name'], $settings['date_due'], 'task', 'task_changed_date' );
			$date_due = new DateTime( $settings['date_due'] );
			Emails::task_date_change_email( $task_id, $settings['name'], $date_due );
		}

		if ($old_task->status !== $settings['status']) {
			do_action('zpm_task_status_changed', $task_id, $settings['status']);
		}
		
		$general_settings = Utillities::general_settings();

		$start_datetime = new DateTime( $settings['date_start'] );
		$due_datetime = new DateTime( $settings['date_due'] );

		$start_date = ($start_datetime->format('Y-m-d') !== '-0001-11-30') ? date_i18n($general_settings['date_format'], strtotime($settings['date_start'])) : __( 'Not set', 'zephyr-project-manager' );
		$due_date = ($due_datetime->format('Y-m-d') !== '-0001-11-30') ? date_i18n($general_settings['date_format'], strtotime($settings['date_due'])) : __( 'Not set', 'zephyr-project-manager' );

		$task = Tasks::get_task( $task_id );
		ob_start();
		echo apply_filters( 'zpm_task_update_response', $task_id );
		do_action( 'zpm_task_updated', $task );

		if ($old_task->assignee !== $settings['assignee'] && !empty($settings['assignee']) && $settings['assignee'] !== '-1') {
			Emails::assignedTaskEmail($task);
		}

		$team = Members::get_team( $settings['team'] );
		$team_name = !is_null( $team ) ? $team['name'] : __( 'None', 'zephyr-project-manager' );
		$other = ob_get_clean();
		$status = Utillities::get_status($task->status);

		$response = array(
			'task_id' => $task_id,
			'formatted_start_date' => $start_date,
			'formatted_due_date'   => $due_date,
			'other'				   => $other,
			'team_name'			   => $team_name,
			'status'			   => $status,
			'task' 				   => $task
		);

		if (class_exists('Inc\\ZephyrProjectManager\\Kanban')) {
			$kanban = new Kanban();
			$kanban_html = method_exists($kanban, 'taskHtml') ? Kanban::taskHtml( $task ) : '';
			$response['kanban_html'] = $kanban_html;
		}

		if (isset($_POST['shortcode']) && $_POST['shortcode']) {
			$type = isset($_POST['shortcode_type']) ? $_POST['shortcode_type'] : 'cards';
			$response['shortcode_html'] = do_shortcode( '[zephyr_task id="' . $task_id . '" type="' . $type . '"]' );
		}

		echo json_encode($response);

		die();
	}

	// Marks a task as liked
	public function like_task() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$task_id = $_POST['task_id'];
		$user_id = $this->get_user_id();
		$liked_tasks = get_option( 'zpm_liked_tasks_' . $user_id, false );
		$liked_tasks = unserialize($liked_tasks);

		if (!$liked_tasks) {
			$liked_tasks = array();
		}

		if (!in_array($task_id, $liked_tasks)) {
			$liked_tasks[] = $task_id;
		} else {
			$liked_tasks = array_diff($liked_tasks, [$task_id]);
		}

		$liked_tasks = serialize($liked_tasks);
		update_option( 'zpm_liked_tasks_' . $user_id, $liked_tasks );

		echo json_encode($liked_tasks);
		die();
	}

	/**
	* Ajax function to follow a task
	* @return json
	*/
	public function follow_task() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$task_id = $_POST['task_id'];
		$user_id = $this->get_user_id();
		$followed_tasks = get_option( 'zpm_followed_tasks_' . $user_id, false );
		$followed_tasks = unserialize($followed_tasks);

		if (!$followed_tasks) {
			$followed_tasks = array();
		}

		if (!in_array($task_id, $followed_tasks)) {
			$followed_tasks[] = $task_id;
		} else {
			$followed_tasks = array_diff($followed_tasks, [$task_id]);
		}

		$followed_tasks = serialize($followed_tasks);
		update_option( 'zpm_followed_tasks_' . $user_id, $followed_tasks );
		$user = BaseController::get_project_manager_user($user_id);
		$html = '<span class="zpm_task_follower" data-user-id="' . $user['id'] . '" title="' . $user['name'] . '" style="background-image: url(' . $user['avatar'] . ');"></span>';
		$following = in_array($task_id, unserialize($followed_tasks)) ? true : false;
		$response = array(
			'html' 		=> $html,
			'following' => $following,
			'user_id'   => $user_id
		);
		echo json_encode($response);
		die();
	}

	/**
	* Updates the subtasks (add new subtask | delete subtask | update subtask name)
	* @param int $_POST['task_id']
	* @param string $_POST['subtask_action']
	* @param string $_POST['subtask_name']
	* @return json
	*/
	public function update_subtasks() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task_id = $_POST['task_id'];
		$action = $_POST['subtask_action'];

		switch ($action) {
			case 'new_subtask':
				$parent_task = Tasks::get_task( $task_id );
				$subtask_name = isset($_POST['subtask_name']) ? sanitize_text_field($_POST['subtask_name']) : '';
				$description = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '';
				$start = isset($_POST['start']) ? sanitize_text_field($_POST['start']) : $parent_task->date_start;
				$due = isset($_POST['due']) ? sanitize_text_field($_POST['due']) : $parent_task->date_due;
				
				$date = date('Y-m-d H:i:s');
		
				$settings = array(
					'parent_id' 	 => $parent_task->id,
					'user_id' 		 => $parent_task->user_id,
					'assignee' 		 => $parent_task->assignee,
					'project' 		 => $parent_task->project,
					'name' 			 => $subtask_name,
					'description' 	 => $description,
					'completed' 	 => false,
					'date_start' 	 => $start,
					'date_due' 		 => $due,
					'date_created' 	 => $date,
					'date_completed' => ''
				);

				$wpdb->insert( $table_name, $settings );
				$subtask = Tasks::get_task( $wpdb->insert_id );

				$response = array(
					'name' => $subtask_name,
					'id' => $wpdb->insert_id,
					'html' => Tasks::subtaskItemHtml($subtask)
				);

				Emails::new_subtask_email( $subtask, get_current_user_id() );
				echo json_encode($response);
				break;
			
			case 'delete_subtask':
				$subtask_id = $_POST['subtask_id'];
				$settings = array(
					'id' => $subtask_id
				);
				$wpdb->delete( $table_name, $settings, [ '%d' ] );
				$return = array(
					'success' => true
				);
				echo json_encode($return);
				break;

			case 'update_subtask':
				$new_subtask_name = isset($_POST['new_subtask_name']) ? sanitize_text_field($_POST['new_subtask_name']) : '';
				$description = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '';
				$start = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
				$due = isset($_POST['due_date']) ? sanitize_text_field($_POST['due_date']) : '';
				
				$settings = array(
					'name' => $new_subtask_name,
					'description' => $description,
					'date_due' => $due,
					'date_start' => $start
				);

				$where = array(
					'id' => $_POST['subtask_id']
				);

				$wpdb->update( $table_name, $settings, $where );
				$subtask = Tasks::get_task($_POST['subtask_id']);
				$response = array(
					'html' => Tasks::subtaskItemHtml($subtask)
				);
				echo json_encode($response);
				break;
			default:
				break;
		}

		die();
	}

	public function get_tasks() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$project_id = $_POST['project_id'];
		Tasks::view_task_list();
		die();
	}

	public function get_task() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$user_id = $this->get_user_id();
		$task_id = $_POST['task_id'];
		$task_data = Tasks::get_task( $task_id );

		$followed_tasks = get_option( 'zpm_followed_tasks_' . $user_id, false );
		$followed_tasks = unserialize($followed_tasks);
		$following = in_array($task_id, (array) $followed_tasks);

		$task_data->following = $following;
		$task_data->subtasks = Tasks::get_subtasks($task_id);
		$task_data->recurrence = Tasks::recurrence_string($task_data);
		$task_data->attachments = Tasks::get_task_attachments($task_id);
		$task_data = apply_filters( 'zpm_get_task_data', $task_data );
		
		echo json_encode($task_data);
		die();
	}

	public function filter_by() {
		$filter = isset($_POST['filter']) ? $_POST['filter'] : '-1';
		$current_filter = isset($_POST['current_filter']) ? $_POST['current_filter'] : '-1';
		$assignee = isset($_POST['assignee']) ? $_POST['assignee'] : '-1';
		$user_id = get_current_user_id();
		$tasks = array();

		switch ($current_filter) {
			case '-1':
				$tasks = Tasks::get_tasks();
				break;
			case '0':
				$tasks = Tasks::get_user_tasks( $user_id );
				break;
			case '1':
				$tasks = Tasks::get_completed_tasks( 0 );
				break;
			case '2':
				$tasks = Tasks::get_completed_tasks( 1 );
				break;
			default:
				break;
		}

		if ($assignee !== -1) {
			$filteredTasks = [];

			foreach ($tasks as $task) {
				if (Tasks::is_assignee($task, $user_id)) {
					$filteredTasks[] = $task;
				}
			}

			$tasks = $filteredTasks;
		}

		if (isset($_POST['project'])) {
			$project = $_POST['project'];
			$tempTasks = $tasks;
			$tasks = [];
			foreach ($tempTasks as $task) {
				if ($task->project == $project || $project == '-1') {
					$tasks[] = $task;
				}
			}
		}

		update_user_meta( $user_id, 'zpm_tasks_last_sorting', $filter );

		$tasks = Tasks::sortTasks( $tasks, $filter );

		$html = '';

		$frontend = isset($_POST['frontend']) ? $_POST['frontend'] : false;
		foreach ($tasks as $task) {
			$new_row = Tasks::new_task_row($task);
			if (!$frontend) {
				$html .= $new_row;
			} else {
				$html .= '<a href="?action=task&id=' . $task->id . '">' . $new_row . '</a>';
			}
		}

		if (empty($tasks)) {
			$html = '<p class="zpm_error_message">' . __( 'No results found...', 'zephyr-project-manager' ) . '</p>';
		}

		$response = array(
			'html' => $html
		);


		echo json_encode( $response );
		die();
	}

	/**
	* Filters tasks based on users selection
	*/
	public function filter_tasks() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$manager = ZephyrProjectManager();
		$filter = $_POST['zpm_filter'];
		$user_id = $_POST['zpm_user_id'];
		$tasks = array();

		if ($filter == '-1') {
			// All Tasks
			$tasks = $manager::get_tasks();
		} elseif ($filter == '0') {
			// My Tasks
			$tasks = Tasks::get_user_tasks( $user_id );
		} elseif ($filter == '1') {
			// Completed Tasks 
			$tasks = Tasks::get_completed_tasks( 0 );
		} elseif ($filter == '2') {
			// Incompleted Tasks
			$tasks = Tasks::get_completed_tasks( 1 );
		} elseif ($filter == '3') {

		} elseif ($filter == 'today') {
			$tasks = Tasks::getDueTasks( 'today' );
		} elseif ($filter == '7_days') {
			$tasks = Tasks::getDueTasks( '7 days' );
		} elseif ($filter == 'overdue') {
			$tasks = Tasks::get_overdue_tasks();
		} 
 

		$html = '';

		$frontend = isset( $_POST['frontend'] ) ? $_POST['frontend'] : false;
		
		foreach ($tasks as $task) {
			if ($user_id !== '-1' && !Tasks::is_assignee($task, $user_id)) {
				continue;
			}
			$new_row = Tasks::new_task_row( $task, $frontend );
			if (!$frontend) {
				$html .= $new_row;
			} else {
				$html .= '<a href="?action=task&id=' . $task->id . '">' . $new_row . '</a>';
			}
		}

		if ( empty( $tasks ) || empty( $html ) ) {
			$html = '<p class="zpm_error_message">' . __( 'No results found...', 'zephyr-project-manager' ) . '</p>';
		}

		$response = array(
			'html' => $html
		);

		echo json_encode($response);
		die();
	}

	/**
	* Filters project based on filter
	*/
	public function filter_projects() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$filter = $_POST['zpm_filter'];
		$user_id = $_POST['zpm_user_id'];
		$filter_category = isset($_POST['filter_category']) ? $_POST['filter_category'] : false;
		$projects = array();

		if ( $filter_category == false ) {
			if ($filter == '-1') {
				// All Projects
				$projects = Projects::get_projects();
			} elseif ($filter == '2') {
				// Completed Projects
				$projects = Projects::get_complete_projects();
			} elseif ($filter == '1') {
				// Incompleted projects 
				$projects = Projects::get_incomplete_projects();
			} elseif ($filter == 'archived') {
				// Incompleted projects 
				$args = [
					'archived' => 1
				];
				$projects = Projects::get_projects(null, $args);
			} else {
				$projects = Projects::get_projects();
			}
		} else {
			$projects = Projects::filter_by_category( Projects::get_projects(), $filter_category );
		}
		

		$html = '';

		$frontend = isset($_POST['frontend']) ? $_POST['frontend'] : false;
		
		foreach ($projects as $project) {
			$html .= Projects::new_project_cell( $project );
		}

		if (empty($projects)) {
			$html = '<p class="zpm_error_message">' . __( 'No projects found...', 'zephyr-project-manager' ) . '</p>';
		}

		$response = array(
			'html' => $html
		);

		echo json_encode($response);
		die();
	}

	/* Categories */
	// Creates a new category
	public function create_category() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_CATEGORY_TABLE;
		$settings = array();
		$settings['name'] = (isset($_POST['category_name'])) ? sanitize_text_field( $_POST['category_name']) : '';
		$settings['description'] = (isset($_POST['category_description'])) ? sanitize_text_field( $_POST['category_description']) : '';
		$settings['color'] 	= (isset($_POST['category_color'])) ? sanitize_text_field( $_POST['category_color']) : false;

		if ( ColorPickerApi::checkColor( $settings['color'] ) !== false ) {
			$settings['color'] = ColorPickerApi::sanitizeColor( $settings['color'] );
		} else {
			$settings['color'] = '#eee';
		}

		$wpdb->insert( $table_name, $settings );
		Categories::display_category_list();
		die();
	}

	// Removes selected category
	public function remove_category() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_CATEGORY_TABLE;
		$settings = array(
			'id' => $_POST['id']
		);

		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		Categories::display_category_list();
		die();
	}

	// Saves changes to the category
	public function update_category() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_CATEGORY_TABLE;
		$settings = array();
		$settings['name'] 			= (isset($_POST['category_name'])) ? sanitize_text_field( $_POST['category_name']) : '';
		$settings['description'] 	= (isset($_POST['category_description'])) ? sanitize_text_field( $_POST['category_description']) : '';
		$settings['color'] 	= (isset($_POST['category_color'])) ? sanitize_text_field( $_POST['category_color']) : false;

		if ( ColorPickerApi::checkColor( $settings['color'] ) !== false ) {
			$settings['color'] = ColorPickerApi::sanitizeColor( $settings['color'] );
		} else {
			$settings['color'] = '#eee';
		}

		$where = array(
			'id' => $_POST['category_id']
		);

		$wpdb->update( $table_name, $settings, $where );
		Categories::display_category_list();
		die();
	}

	// Displays all the categories
	public function display_category_list() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		Categories::display_category_list();
		die();
	}

	/**
	* Removes a comment/message from the database
	*/
	public function remove_comment() {
		global $wpdb;
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$table_name = ZPM_MESSAGES_TABLE;
		$comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : '-1';
		$settings = array(
			'id' => $_POST['comment_id']
		);
		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		die();
	}
 	
 	/**
	* Returns the HTML for activities
	*/
	public function display_activities() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$all_activities = Activity::get_activities(array('offset' => $_POST['offset'] * 10, 'limit' => 10));
		echo Activity::display_activities($all_activities);
		die();
	}

	public function dismiss_notice() {
		//check_ajax_referer( 'zpm_nonce', 'zpm_nonce' );
		$notice_id = $_POST['notice'];
		if ($notice_id == 'review_notice') {
			update_option('zpm_review_notice_dismissed', '1');
		} else if ($notice_id == 'welcome_notice') {
			update_option('zpm_welcome_notice_dismissed', '1');
		} else {
			Utillities::dismiss_notice( $notice_id );
		}
	}

	public function update_user_access() {
		$userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
		$access = isset($_POST['access']) ? $_POST['access'] : false;
		if (is_array($userId)) {
			foreach ($userId as $user) {
				Utillities::update_user_access($user['id'], $access);
			}
		} else {
			Utillities::update_user_access($userId, $access);
		}
		
		echo json_encode($_POST);
		die();
	}

	public function add_team() {
		$name = $_POST['name'];
		$description = $_POST['description'];
		$members = (array) $_POST['members'];
		$last_team = Members::add_team( $name, $description, $members );
		$team = Members::get_team( $last_team );

		$response = array(
			'html' => Members::team_single_html( Members::get_team( $last_team ) ),
			'team' => $team
		);
		echo json_encode( $response );
		die();
	}

	public function update_team() {
		$id = $_POST['id'];
		$name = $_POST['name'];
		$description = $_POST['description'];
		$members = (array) $_POST['members'];
		Members::update_team( $id, $name, $description, $members );
		echo json_encode( Members::team_single_html( Members::get_team( $id ) ) );
		die();
	}

	public function get_team() {
		$id = $_POST['id'];
		echo json_encode( Members::get_team( $id ) );
		die();
	}

	public function delete_team() {
		$id = $_POST['id'];
		Members::delete_team( $id );
		die();
	}

	public function get_all_tasks() {
		$tasks = Tasks::getAvailableTasks();

		foreach ($tasks as $task) {
			$project = Projects::get_project( $task->project );
			$task->project_data = $project;
			$assignee = Members::get_member( $task->assignee );
			$assignees = Tasks::get_assignees( $task, true );
			$priority = property_exists( $task, 'priority' ) ? $task->priority : 'priority_none';
			$status = Utillities::get_status( $priority );
			$task->status = $priority;
			$task->assignees = $assignees;

			$categories = is_object($project) ? maybe_unserialize($task->project_data->categories) : [];
			$category = isset($categories[0]) ? Categories::get_category($categories[0]) : '-1';
			$color = is_object($category) ? $category->color : '';
			$task->styles = Utillities::auto_gradient_css( $color, true );
			$task->type = Tasks::get_type($task);
			if ($task->type == 'daily') {
				$startToEnd = Tasks::getStartToEndDays($task);
				$task->otherDays = [];
				if ($startToEnd > 0) {
					for($i = 0; $i < $startToEnd; $i++) {
						$newDate = date('Y-m-d', strtotime($task->date_start . ' + ' . $i . ' days'));
						$task->otherDays[] = $newDate;
					}
				}
			} elseif ($task->type == 'weekly') {
				$task->expires = Tasks::get_expiration_date($task);
				$startToEnd = !empty($task->expires) ? Tasks::getStartToEndDays($task, true) : 200;
				$task->diffDays = $startToEnd;
				$task->otherDays = [];
				if ($startToEnd > 0) {
					for($i = 0; $i < $startToEnd; $i++) {
						$newDate = date('Y-m-d', strtotime($task->date_start . ' + ' . $i . ' days'));
						$day = date('D', strtotime($task->date_start . ' + ' . $i . ' days'));
						//$task->otherDays[] = $day;
						if ($day === 'Mon') {
							$task->otherDays[] = $newDate;
						}
					}
				}
				
			}
		}

		echo json_encode( (array)$tasks );
		die();
	}

	public function getTasksDateRange() {
		
		$options = $_POST['options'];
		$rangeStart = isset($options['start']) ? $options['start'] : '';
		$rangeEnd = isset($options['end']) ? $options['end'] : '';

		$results = Tasks::getTasksDateRange( $rangeStart, $rangeEnd );

		echo json_encode( $results );
		die();
	}

	public function get_project_tasks() {
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '-1';
		$tasks =  Tasks::get_project_tasks( $project_id, true );
		$userId = get_current_user_id();
		foreach ($tasks as $task) {
			$task->hasApplied = Tasks::isApplied($task);
			$task->canComplete = apply_filters( 'zpm_can_complete_task', true, $task );
			$task->isAssignee = Tasks::is_assignee($task, $userId);
			$task->frontendUrl = Utillities::get_frontend_url('action=task&id=' . $task->id);
		}
		echo json_encode( $tasks );
		die();
	}

	public function deactivation_survey() {
		$reason = $_POST['reason'];
		$suggestion_text = $_POST['suggestion'];
		$other_text = $_POST['other'];
		wp_mail( 'dylanjkotze@gmail.com', 'Zephyr Deactivation', 'Reason: ' . $reason . ' \n Suggesstion: ' . $suggestion_text . ' \n Other: ' . $other_text );
		echo json_encode( array( 'result' => 'success' ) );
		die();
	}

	public function create_status() {
		$name = $_POST['name'];
		$color = $_POST['color'];
		$type = $_POST['type'];

		$status = Utillities::create_status( $name, $color, $type );
		$type = empty( $type ) ? 'priority' : $type;
		ob_start();

		?>
		<div class="zpm-<?php echo $type; ?>-list__item" data-status-slug="<?php echo $status['slug']; ?>">
			<span class="zpm-<?php echo $type; ?>-list__item-color" style="background: <?php echo $status['color']; ?>"></span>
			<span class="zpm-<?php echo $type; ?>-list__item-name"><?php echo $status['name']; ?></span>
			<span class="zpm-delete-<?php echo $type; ?> lnr lnr-cross" data-id="<?php echo $status['slug']; ?>"></span>
		</div>
		<?php 
		$html = ob_get_clean();
		echo json_encode( array( 'result' => 'success', 'html' => $html ) );
		die();
	}

	public function update_status() {
		$name = $_POST['name'];
		$color = $_POST['color'];
		$slug = $_POST['slug'];
		$type = $_POST['type'];

		$status = Utillities::update_status( $slug, $name, $color, $type );
		ob_start();

		?>
		<div class="zpm-status-list__item" data-<?php echo $type; ?>-slug="<?php echo $status['slug']; ?>">
			<span class="zpm-<?php echo $type; ?>-list__item-color" style="background: <?php echo $status['color']; ?>"></span>
			<span class="zpm-<?php echo $type; ?>-list__item-name"><?php echo $status['name']; ?></span>
			<span class="zpm-delete-<?php echo $type; ?> lnr lnr-cross" data-id="<?php echo $status['slug']; ?>"></span>
		</div>
		<?php 
		$html = ob_get_clean();
		echo json_encode( array( 'result' => 'success', 'html' => $html ) );
		die();
	}

	public function delete_status() {
		$slug = isset( $_POST['slug'] ) ? $_POST['slug'] : '';
		$type = isset( $_POST['type'] ) ? $_POST['type'] : 'priority';
		$status = Utillities::delete_status( $slug, $type );
		echo json_encode( array( 
			'result' => 'success',
			'status' => $status
		) );
		die();
	}

	public function get_user_by_unique_id() {
		$response = array();
		$user = Members::get_user_by_meta_data( '_zpm_unique_id', $_POST['id'] );
		$response = Utillities::get_user( $user->ID );
		echo json_encode( $response );
		die();
	}

	public function complete_project() {
		$id = isset( $_POST['id'] ) ? $_POST['id'] : '-1';
		$completed = isset( $_POST['completed'] ) ? $_POST['completed'] : 0;
		Projects::mark_complete( $id, $completed );
		echo json_encode( array( ) );
		die();
	}

	public function view_project() {
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '-1';
		ob_start();
		Projects::view_project_modal( $project_id );
		$html = ob_get_clean();
		echo json_encode( array(
			'html' => $html
		) );
		die();
	}

	public static function get_user_projects() {
		$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '-1';
		$projects = Projects::get_member_projects( $user_id );
		echo json_encode( array(
			'projects' => $projects,
			'project_count' => sizeof( $projects )
		) );
		die();
	}

	public function team_members_list_html() {
		$base = new BaseController;
		$users = $base->get_users();

		ob_start();

		foreach ($users as $user) : ?>
			<?php
				$role = '';
				$user_id = $user->data->ID;
				$user_settings_option = get_option('zpm_user_' . $user->data->ID . '_settings');
				$avatar = isset($user_settings_option['profile_picture']) ? esc_url($user_settings_option['profile_picture']) : get_avatar_url($user->data->ID);
				$can_zephyr = isset($user_settings_option['can_zephyr']) ? $user_settings_option['can_zephyr'] : "true";

				$description = isset($user_settings_option['description']) ? esc_html($user_settings_option['description']) : '';

				$user_projects = Projects::get_user_projects($user->data->ID);
				$user_tasks = Tasks::get_user_tasks($user->data->ID);
				$completed_tasks = Tasks::get_user_completed_tasks($user->data->ID);
				$remaining_tasks = Tasks::get_user_completed_tasks($user->data->ID, '0');

				$percent_complete = (sizeof($user_tasks) !== 0) ? (sizeof($completed_tasks) / sizeof($user_tasks)) * 100 : '0';

				if (in_array('zpm_user', $user->roles)) {
					$role = 'ZPM User';
				} elseif (in_array('zpm_client_user', $user->roles)) {
					$role = 'ZPM Client User';
				} elseif (in_array('zpm_manager', $user->roles) || in_array('administrator', $user->roles)) {
					$role = 'ZPM Manager';
				}
			?>

			<?php $edit_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_teams_members')) . '&action=edit_member&user_id=' . $user->data->ID; ?>

			<a class="zpm_team_member <?php echo $can_zephyr == "true" ? 'zpm-user-can-zephyr' : ''; ?>" <?php echo current_user_can( 'administrator' ) ? "href='" . $edit_url . "'" : ''; ?>>
				<div class="zpm_member_details" data-ripple="rgba(0,0,0,0.1)">
					
					<span class="zpm_avatar_image" style="background-image: url(<?php echo $avatar; ?>);"></span>
					<span class="zpm_member_name"><?php echo $user->data->display_name; ?></span>
					<span class="zpm_member_email"><?php echo $user->data->user_email; ?></span>
					<p class="zpm_member_bio"><?php echo $description; ?></p>

					<?php if (current_user_can('administrator')) : ?>
						<!-- Adcurrent_user_can('administrator')min Controls -->
						<div class="zpm-access-controls">
							<label for="zpm-can-zephyr-<?php echo $user_id; ?>" class="zpm_checkbox_label">
								<input type="checkbox" id="zpm-can-zephyr-<?php echo $user_id; ?>" name="zpm_can_zephyr" class="zpm-can-zephyr zpm_toggle invisible" value="1" data-user-id="<?php echo $user->data->ID; ?>" <?php echo $can_zephyr == "true" ? 'checked' : ''; ?>>

								<div class="zpm_main_checkbox">
									<svg width="20px" height="20px" viewBox="0 0 20 20">
										<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
										<polyline points="4 11 8 15 16 6"></polyline>
									</svg>
								</div>
								<?php _e( 'Allow Access', 'zephyr-project-manager' ); ?>
						    </label>
						</div>
					<?php endif; ?>

					<div class="zpm_member_stats">
						<div class="zpm_member_stat">
							<h5 class="zpm_member_stat_number"><?php echo sizeof($user_projects); ?></h5>
							<p class="zpm_member_stat_label"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></p>
						</div>
						<div class="zpm_member_stat">
							<h5 class="zpm_member_stat_number"><?php echo sizeof($completed_tasks); ?></h5>
							<p class="zpm_member_stat_label"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></p>
						</div>
						<div class="zpm_member_stat">
							<h5 class="zpm_member_stat_number"><?php echo sizeof($remaining_tasks); ?></h5>
							<p class="zpm_member_stat_label"><?php _e( 'Remaining Tasks', 'zephyr-project-manager' ); ?></p>
						</div>
						<div class="zpm_member_progress">
							<span class="zpm_member_progress_bar" style="width: <?php echo $percent_complete; ?>%"></span>
						</div>
					</div>
				</div>
			</a>
		<?php endforeach; ?>
		<?php

		$html = ob_get_clean();
		echo json_encode( array(
			'html' => $html
		) );
		die();
	}

	public function get_user_progress() {
		$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '-1';
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '-1';
		
		$user_completed_tasks = [];
		$user_pending_tasks = [];

		$user_tasks = Tasks::get_project_assignee_tasks( $project_id, $user_id );

		foreach ($user_tasks as $task) {
			if ( $task->completed == '1' ) {
				$user_completed_tasks[] = $task;
			} else {
				$user_pending_tasks[] = $task;
			}
		}

		$args = array( 
			'project_id' => $project_id,
			'assignee' => $user_id
		);
		$overdue_tasks = Tasks::get_overdue_tasks( $args );

		$percent_complete = (sizeof($user_tasks) !== 0) ? ( sizeof( $user_completed_tasks ) / sizeof( $user_tasks ) ) * 100 : '0';
		$total = sizeof( $user_tasks );

		ob_start(); ?>

		<div class="zpm-member-progress__stats">
				<div class="zpm-member-progress__stat"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?>: <span class="zpm-stat-val"><?php echo $total; ?></span></div>
				<div class="zpm-member-progress__stat zpm-member-stat__completed"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?>: <span class="zpm-stat-val"><?php echo sizeof( $user_completed_tasks ); ?></span></div>
				<div class="zpm-member-progress__stat zpm-member-stat__pending"><?php _e( 'Pending Tasks', 'zephyr-project-manager' ); ?>: <span class="zpm-stat-val"><?php echo sizeof( $user_pending_tasks ); ?></span></div>
				<div class="zpm-member-progress__stat zpm-member-stat__percentage"><?php _e( 'Percentage Complete', 'zephyr-project-manager' ); ?>: <span class="zpm-stat-val"><?php echo round($percent_complete) . '%'; ?></span></div>
		</div>

		<?php $html = ob_get_clean();

		$results = [
			'tasks' => $user_tasks,
			'tasks_total' => $total,
			'pending_tasks' => $user_pending_tasks,
			'completed_tasks' => $user_completed_tasks,
			'percent_complete' => $percent_complete,
			'overdue_tasks' => $overdue_tasks,
			'completed' => sizeof( $user_completed_tasks ),
			'pending' => sizeof( $user_pending_tasks ),
			'overdue' => sizeof( $overdue_tasks ),
			'html' => $html
		];

		echo json_encode( $results );
		die();
	}

	public function get_project_members() {
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '-1';
		$members = Projects::get_members( $project_id );
		echo json_encode( $members );
		die();
	}

	public function get_members() {
		$page = isset($_POST['page']) ? $_POST['page'] : '';
		$limit = isset($_POST['limit']) ? $_POST['limit'] : '';

		if ( !empty( $page ) && !empty( $limit ) ) {
			$members = Members::get_members( $limit, $page );
		} else {
			$members = Members::get_members();
		}

		foreach ($members as $key => $member) {
			$members[$key]['list_html'] = Members::list_html( $member );
		}
		
		echo json_encode( $members );
		die();
	}

	public function get_paginated_projects() {
		$page = 1;

		if ( !empty( $_POST["page"] ) ) {
			$page = $_POST["page"];
		}

		$limit = isset( $_POST["limit"] ) ? $_POST["limit"] : 10;
		$offset = ($page - 1) * $limit;

		if ( $offset < 0 ) {
			$offset = 0;
		}

		$projects = Projects::get_paginated_projects( $limit, $offset );

		ob_start();

		foreach ($projects as $project) {
			echo Projects::new_project_cell( $project );
		}

		$html = ob_get_clean();

		$response = [
			'projects' => $projects,
			'html' => $html
		];
		echo json_encode( $response );
		die();
	}

	public function get_available_project_count() {
		//$projects = Projects::get_available_projects();
		$count = Projects::get_total_pages();
		$response = [
			'count' => $count
		];
		echo json_encode($response);
		die();
	}

	public function update_user_meta() {
		$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : get_current_user_id();
		$meta_key = isset($_POST['key']) ? $_POST['key'] : '';
		$meta_value = isset($_POST['value']) ? $_POST['value'] : '';
		update_usermeta( $user_id, $meta_key, $meta_value );
		$response = [];
		echo json_encode($response);
		die();
	}

	public function getUserData(){
		$userData = [];
		$manager = ZephyrProjectManager();
		$users = $manager::get_users();

		foreach ($users as $user) {
			$userData[] = [
				'id' => $user['id'],
				'name' => $user['name'],
				'avatar' => $user['avatar'],
				'type' => 'user'
			];
		}

		echo json_encode($userData);
		die();
	}

	public function updateTaskDueDate() {
		$taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$dueDate = isset($_POST['date']) ? $_POST['date'] : '';
		$dateTime = new DateTime($dueDate);
		$data = array(
			'date_due' => $dateTime->format('Y-m-d H:i:s')
		);
		Tasks::update($taskId, $data);
		echo json_encode(array(
			'formatted_date' => $dateTime->format('d M'),
			'data' => $data,
			'task_id' => $taskId
		));
		die();
	}

	public function updateFileProject() {
		global $wpdb;
		$tableName = ZPM_MESSAGES_TABLE;
		$fileId = isset($_POST['file_id']) ? $_POST['file_id'] : '';
		$projectId = isset($_POST['project_id']) ? $_POST['project_id'] : '';

		$settings = array(
			'subject_id' => $projectId
		);

		$where = array(
			'id' => $fileId
		);

		$wpdb->update( $tableName, $settings, $where );
		echo json_encode(['success']);
		die();
	}

	public function newTaskModal() {
		ob_start();
		require_once( ZPM_PLUGIN_PATH . '/templates/parts/new_task.php' );
		$html = ob_get_clean();
		echo json_encode([
			'html' => $html
		]);
		die();
	}

	public function editTaskModal() {
		ob_start();
		$taskId = isset($_POST['id']) ? $_POST['id'] : '-1';
		require_once( ZPM_PLUGIN_PATH . '/templates/parts/task-edit-modal.php' );
		$html = ob_get_clean();
		echo json_encode([
			'html' => $html
		]);
		die();
	}

	public function newProjectModal() {
		ob_start();
		require_once( ZPM_PLUGIN_PATH . '/templates/parts/new-project-modal.php' );
		$html = ob_get_clean();
		echo json_encode([
			'html' => $html
		]);
		die();
	}

	public function editProjectModal() {
		ob_start();
		$projectId = isset($_POST['id']) ? $_POST['id'] : '-1';
		require_once( ZPM_PLUGIN_PATH . '/templates/parts/project-edit-modal.php' );
		$html = ob_get_clean();
		echo json_encode([
			'html' => $html
		]);
		die();
	}

	public function subtaskEditModal(){
		$id = isset($_POST['id']) ? $_POST['id'] : '-1';
		$subtask = new Task($id);
		ob_start();
		?>
		<h5 class="zpm-modal-header"><?php _e( 'Edit Subtask', 'zephyr-project-manager' ); ?></h5>

		<input type="hidden" data-ajax-name="parent-id" value="<?php echo $subtask->parentId; ?>" />
		<div class="zpm-form__group">
			<input type="text" name="zpm-edit-subtask__name" id="zpm-edit-subtask__name" class="zpm-form__field" placeholder="<?php _e( 'Subtask Name', 'zephyr-project-manager' ); ?>" value="<?php echo $subtask->name; ?>" data-ajax-name="name" />
			<label for="zpm-edit-subtask__name" class="zpm-form__label"><?php _e( 'Subtask Name', 'zephyr-project-manager' ); ?></label>
		</div>
		<div class="zpm-form__group">
			<textarea type="text" name="zpm-edit-subtask__description" id="zpm-edit-subtask__description" class="zpm-form__field" placeholder="<?php _e( 'Subtask Description', 'zephyr-project-manager' ); ?>" data-ajax-name="description"><?php echo $subtask->description; ?></textarea>
			<label for="zpm-edit-subtask__description" class="zpm-form__label"><?php _e( 'Subtask Description', 'zephyr-project-manager' ); ?></label>
		</div>

		<div class="zpm-row">
			<div class="zpm-col zpm-col-6">
				<div class="zpm-form__group">
					<input type="text" name="zpm-edit-subtask__start" id="zpm-edit-subtask__start" class="zpm-form__field zpm-datepicker" placeholder="<?php _e( 'Start', 'zephyr-project-manager' ); ?>" value="<?php $subtask->getStartDate('Y-m-d'); ?>" data-ajax-name="start-date" />
					<label for="zpm-edit-subtask__start" class="zpm-form__label"><?php _e( 'Start', 'zephyr-project-manager' ); ?></label>
				</div>
			</div>
			<div class="zpm-col zpm-col-6">
				<div class="zpm-form__group">
					<input type="text" name="zpm-edit-subtask__due" id="zpm-edit-subtask__due" class="zpm-form__field zpm-datepicker" placeholder="<?php _e( 'Due', 'zephyr-project-manager' ); ?>" value="<?php echo $subtask->getDueDate('Y-m-d'); ?>" data-ajax-name="due-date" />
					<label for="zpm-edit-subtask__due" class="zpm-form__label"><?php _e( 'Due', 'zephyr-project-manager' ); ?></label>
				</div>
			</div>
		</div>

		<div class="zpm-modal-buttons__right">
			<div class="zpm-modal-cancel-btn zpm_button" data-zpm-trigger="remove_modal">Cancel</div>
			<div class="zpm-modal-accept-btn zpm_button" data-zpm-trigger="remove_modal">Create</div>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html;
		die();
	}

	public function getCalendarItems() {
		$tasks = Tasks::get_tasks();
		$tasks = apply_filters( 'zpm_filter_global_tasks', $tasks );
		$generalSettings = Utillities::general_settings();

		foreach ($tasks as $key => $task) {
			if (!Utillities::can_view_task($task)) {
				unset($tasks[$key]);
				continue;
			}
			$project = Projects::get_project( $task->project );
			$task->project_data = $project;
			$assignee = Members::get_member( $task->assignee );
			$assignees = Tasks::get_assignees( $task, true );
			$priority = property_exists( $task, 'priority' ) ? $task->priority : 'priority_none';
			$status = Utillities::get_status( $priority );
			$task->status = $priority;
			$task->assignees = $assignees;

			$categories = is_object($project) ? maybe_unserialize($task->project_data->categories) : [];
			$category = isset($categories[0]) ? Categories::get_category($categories[0]) : '-1';
			$color = is_object($category) ? $category->color : '';
			
			if ($color == '#eee') {
				$color = $generalSettings['primary_color'];
			}

			$task->styles = Utillities::auto_gradient_css( $color, true );
			$task->type = Tasks::get_type($task);
			if ($task->type == 'daily') {
				$startToEnd = Tasks::getStartToEndDays($task);
				$task->otherDays = [];
				if ($startToEnd > 0) {
					for($i = 0; $i < $startToEnd; $i++) {
						$newDate = date('Y-m-d', strtotime($task->date_start . ' + ' . $i . ' days'));
						$task->otherDays[] = $newDate;
					}
				}
			} elseif ($task->type == 'weekly') {
				$task->expires = Tasks::get_expiration_date($task);
				$startToEnd = !empty($task->expires) ? Tasks::getStartToEndDays($task, true) : 200;
				$task->diffDays = $startToEnd;
				$task->otherDays = [];
				if ($startToEnd > 0) {
					for($i = 0; $i < $startToEnd; $i++) {
						$newDate = date('Y-m-d', strtotime($task->date_start . ' + ' . $i . ' days'));
						$day = date('D', strtotime($task->date_start . ' + ' . $i . ' days'));
						//$task->otherDays[] = $day;
						if ($day === 'Mon') {
							$task->otherDays[] = $newDate;
						}
					}
				}
				
			}
		}

		$calendarItems = apply_filters( 'zpm_calendar_items', $tasks );

		foreach ($calendarItems as $key => $item) {
			if ($item->date_due == '0000-00-00 00:00:00') {
				$calendarItems[$key]->date_due = $item->date_start;
			}
		}

		echo json_encode( $calendarItems );
		die();
	}

	public function updateMessage() {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;

		$id = isset($_POST['message_id']) ? $_POST['message_id'] : '';
		$message = isset($_POST['message']) ? $_POST['message'] : '';
		$where = [
			'id' => $id
		];
		$args = [
			'message' => serialize($message)
		];
		$wpdb->update($table_name, $args, $where);
		
		echo json_encode([]);
		die();
	}

	public function uploadAjaxFile() {
		$posted_data =  isset( $_POST ) ? $_POST : array();
		$file_data = isset( $_FILES ) ? $_FILES : array();
		$data = array_merge( $posted_data, $file_data );
		$response = array();
		$uploaded_file = wp_handle_upload( $data['file'], array( 'test_form' => false ) );
		$response['uploaded_file'] = $uploaded_file;
		if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
			$response['response'] = "SUCCESS";
			$response['filename'] = basename( $uploaded_file['url'] );
			$response['url'] = $uploaded_file['url'];
			$response['type'] = $uploaded_file['type'];
		} else {
			$response['response'] = "ERROR";
			$response['error'] = $uploaded_file['error'];
		}

		echo json_encode($response);
		die();
	}

	public function getMembers() {
		$args = isset($_POST['args']) ? $_POST['args'] : [];
		$role = isset($args['role']) ? $args['role'] : false;

		$results = [];

		if (current_user_can( 'administrator' )) {
			$members = Members::get_members();
			foreach ($members as $member) {
				if ($role) {
					if (user_can( $member['id'], $role )) {
						$results[] = $member;
					}
					continue;
				}
				$results[] = $member;
			}
		}

		echo json_encode($results);
		wp_die();
	}

	public function sendEmail() {
		$header = isset($_POST['header']) ? $_POST['header'] : '';
		$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
		$body = isset($_POST['body']) ? $_POST['body'] : '';
		$footer = isset($_POST['footer']) ? $_POST['footer'] : '';
		$userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
		$emails = isset($_POST['emails']) ? (array) $_POST['emails'] : [];
		
		$html = Emails::email_template( $header, $body, $footer );
		
		if (!empty($emails)) {
			foreach ($emails as $email) {
				Emails::send_email( $email, $subject, $html );
			}
		} else {
			$member = Members::get_member($userId);
			Emails::send_email( $member['email'], $subject, $html );
		}
		
		echo json_encode([]);
		wp_die();
	}

	public function updateProjectSetting() {
		$projectId = isset($_POST['project_id']) ? $_POST['project_id'] : '';
		$key = isset($_POST['key']) ? $_POST['key'] : '';
		$value = isset($_POST['value']) ? $_POST['value'] : '';
		Projects::updateSetting($projectId, $key, $value);
		echo json_encode([]);
		wp_die();
	}

	public function loadProjectsFromCSV() {
		$file = isset($_POST['file']) ? $_POST['file'] : '';
		$projects = Projects::loadFromCSV($file);
		echo json_encode($projects);
		wp_die();
	}

	public function loadProjectsFromJSON() {
		$file = isset($_POST['file']) ? $_POST['file'] : '';
		$projects = Projects::loadFromJSON($file);
		echo json_encode($projects);
		wp_die();
	}

	// Save multiple projects
	public function saveProjects() {
		$projects = isset($_POST['projects']) ? (array) $_POST['projects'] : [];

		foreach ($projects as $project){
			$project = (array) $project;
			$id = isset($project['id']) ? $project['id'] : '';

			$completed = $project['completed'];
			if (strtolower($completed) == 'yes') {
				$completed = '1';
			} elseif (strtolower($completed) == 'no') {
				$completed = '0';
			}

			$assignees = Members::memberNameStringToIdString($project['assignees']);
			$args = array(
				'user_id'        => $project['user_id'],
				'name'           => $project['name'],
				'description'    => $project['description'],
				'assignees'      => $assignees,
				'categories'     => serialize(explode(',', $project['categories'])),
				'completed'      => $completed,
				'date_start'     => $project['date_start'],
				'date_due'       => $project['date_due'],
				'date_created'   => date('Y-m-d H:i:s'),
				'date_completed' => '',
				'priority'       => 'priority_none'
			);

			if (empty($id)) {
				$projectId = Projects::new_project($args);
			} else {
				$projectId = $id;
				Projects::update($id, $args);
			}

			if (isset($project['tasks'])) {
				foreach ($project['tasks'] as $task) {
					$task = (array) $task;
					$id = isset($task['id']) ? $task['id'] : '';
					$completed = $task['completed'];
					if (strtolower($completed) == 'yes') {
						$completed = '1';
					} elseif (strtolower($completed) == 'no') {
						$completed = '0';
					}

					$assignee = Members::memberNameStringToIdString($task['assignee']);
					$args = array(
						'user_id'        => '',
						'parent_id'      => (int) $task['parent_id'],
						'assignee'       => $assignee,
						'project'      	 => $projectId,
						'name'           => $task['name'],
						'description'    => $task['description'],
						'date_start'     => $task['date_start'],
						'date_due'       => $task['date_due'],
						'date_created'   => date('Y-m-d H:i:s'),
						'date_completed' => '',
						'categories'     => serialize(explode(',', $task['categories'])),
						'completed'      => $completed,
						'priority'       => 'priority_none'
					);
					
					if (empty($id)) {
						Tasks::create($args);
					} else {
						Tasks::update($id, $args);
					}
				}
			}
		}

		echo json_encode($projects);
		wp_die();
	}

	// Save multiple tasks
	public function saveTasks() {
		$tasks = isset($_POST['tasks']) ? (array) $_POST['tasks'] : [];

		foreach ($tasks as $task){
			$task = (array) $task;
			$id = isset($task['id']) ? $task['id'] : '';
			$completed = $task['completed'];
			if (strtolower($completed) == 'yes') {
				$completed = '1';
			} elseif (strtolower($completed) == 'no') {
				$completed = '0';
			}
			$assignee = Members::memberNameStringToIdString($task['assignee']);
			$args = array(
				'user_id'        => (int) $task['user_id'],
				'parent_id'      => (int) $task['parent_id'],
				'assignee'       => $assignee,
				'project'      	 => (int) $task['project'],
				'name'           => $task['name'],
				'description'    => $task['description'],
				'date_start'     => $task['start_date'],
				'date_due'       => $task['due_date'],
				'date_created'   => date('Y-m-d H:i:s'),
				'date_completed' => '',
				'categories'     => serialize(explode(',', $task['categories'])),
				'completed'      => $completed,
				'priority'       => 'priority_none'
			);

			if (empty($args['date_due'])) {
				$args['date_due'] = '0000-00-00 00:00:00';
			}

			if (empty($args['date_start'])) {
				$args['date_start'] = '0000-00-00 00:00:00';
			}

			if (empty($id)) {
				Tasks::create($args);
			} else {
				Tasks::update($id, $args);
			}
		}

		echo json_encode($args);
		wp_die();
	}

	public function exportProjectsToCSV() {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="project_tasks.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$projects = Projects::get_projects();
		$upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'] . '/ZPM Projects.csv';
		$filename = fopen($filename, 'w+');
		fputcsv($filename, array('ID', 'User ID', 'Name', 'Description', 'Completed', 'Assignees', 'Categories', 'Date Created', 'Date Due', 'Date Start', 'Date Completed'));

		foreach ($projects as $project) {
			$completed = $project->completed;
			if ($completed == '1') {
				$completed = 'Yes';
			} else {
				$completed = 'No';
			}

			$filedata = [
				'id' => $project->id,
				'user_id' => $project->user_id,
				'name' => $project->name,
				'description' => $project->description,
				'completed' => $completed,
				'assignees' => Members::memberIdStringToNameString($project->assignees),
				'categories' => implode(',', maybe_unserialize($project->categories)),
				'date_created' => $project->date_created,
				'date_due' => $project->date_due,
				'date_start' => $project->date_start,
				'date_completed' => $project->date_completed
			];

			fputcsv($filename, (array) $filedata);
		}

		$files = array(
			'project_csv' => $filename
		);
		$filename = $upload_dir['baseurl'] . '/ZPM Projects.csv';

		echo json_encode($filename);
		wp_die();
	}

	public function exportTasksToCSV() {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="zpm_tasks.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$tasks = Tasks::get_tasks();
		$upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'] . '/ZPM Tasks.csv';
		$filename = fopen($filename, 'w+');
		fputcsv($filename, array('ID', 'Parent ID', 'Created By', 'Project', 'Assignee', 'Name', 'Description', 'Completed', 'Categories', 'Date Created', 'Due Date', 'Start Date', 'Date Completed'));

		foreach ($tasks as $task) {
			$completed = $task->completed;
			if ($completed == '1') {
				$completed = 'Yes';
			} else {
				$completed = 'No';
			}
			$filedata = [
				'id' => $task->id,
				'parent_id' => $task->parent_id,
				'created_by' => $task->user_id,
				'project' => $task->project,
				'assignee' => Members::memberIdStringToNameString($task->assignee),
				'name' => $task->name,
				'description' => $task->description,
				'completed' => $completed,
				'categories' => implode(',', maybe_unserialize($task->categories)),
				'date_created' => $task->date_created,
				'date_due' => $task->date_due,
				'date_start' => $task->date_start,
				'date_completed' => $task->date_completed
			];

			fputcsv($filename, (array) $filedata);
		}

		$files = array(
			'project_csv' => $filename
		);
		$filename = $upload_dir['baseurl'] . '/ZPM Tasks.csv';

		echo json_encode($filename);
		wp_die();
	}

	public function getTaskPanelHTML() {
		$taskId = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';

		ob_start();
		include( ZPM_PLUGIN_PATH . '/templates/parts/task-panel.php' );
		$html = ob_get_clean();

		echo json_encode([
			'html' => $html
		]);
		wp_die();
	}

	public function getProjectPanelHTML() {
		$projectId = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';

		ob_start();
		include( ZPM_PLUGIN_PATH . '/templates/parts/project-panel.php' );
		$html = ob_get_clean();

		echo json_encode([
			'html' => $html
		]);
		wp_die();
	}
}