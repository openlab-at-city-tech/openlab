<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Zephyr;
use Inc\Core\File;
use Inc\Core\Task;
use Inc\Core\Members;
use Inc\Core\Utillities;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager;

class Tasks {

	private $settings;

	public function __construct() {
		$this->settings = Utillities::general_settings();
		$this->userId = get_current_user_id();
		add_filter( 'zpm_filter_task', array( $this, 'filter_task_data' ) );

		add_filter( 'zpm_can_complete_task', array( $this, 'canCompleteTask' ), 1, 2 );
		add_filter( 'zpm_can_view_task', array( $this, 'canViewTask' ), 10, 2 );
	}

	public function canViewTask($canView, $task) {

		if (apply_filters( 'zpm_override_hide_task', false, $task )) {
			return false;
		}

		if (current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		if ( Utillities::is_zephyr_role($this->userId) ) {
			if ( current_user_can( 'zpm_view_assigned_tasks' ) && !current_user_can( 'zpm_view_tasks' ) ) {
				if (Tasks::is_assignee( $task, $this->userId )) {
					return true;
				} else {
					return false;
				}
			}
		}

		return true;
	}


	public function canCompleteTask($canComplete, $task) {
		$canCompleteTasks = $this->settings['can_complete_tasks'];

		switch ($canCompleteTasks) {
			case '0':
				return true;
				break;
			case '1':
				if (Tasks::is_assignee($task, $this->userId)) {
					return true;
				} else {
					return false;
				}
				break;
			case '2':
				if (current_user_can( 'administrator' )) {
					return true;
				} else {
					return false;
				}
				break;
			case '3':
				return false;
				break;
			default:
				return true;
				break;
		}
		return false;
	}

	public function filter_task_data( $task ) {
		if (!is_object($task)){
			return;
		}
		if ($this->settings['show_time']) {
			$this->settings['date_format'] = $this->settings['date_format'] . ' H:i';
		}
		
		$start_datetime = new DateTime($task->date_start);
		$due_datetime = new DateTime($task->date_start);
		$start_date = ($start_datetime->format('Y-m-d') !== '-0001-11-30') ? date_i18n($this->settings['date_format'], strtotime($task->date_start)) : __( 'Not set', 'zephyr-project-manager' );
		$due_date = ($due_datetime->format('Y-m-d') !== '-0001-11-30') ? date_i18n($this->settings['date_format'], strtotime($task->date_due)) : __( 'Not set', 'zephyr-project-manager' );
		$priority = Utillities::get_status($task->priority);
		$task->formatted_start_date = $start_date;
		$task->formatted_due_date = $due_date;
		$task->formatted_priority = $priority;
		$task->description = Utillities::getMentions($task->description);
		return $task;
	}

	/**
	* Retrieves a list of tasks
	* 	$args = [
	*      'limit'     => (string) The amount of tasks to retrieve
	*      'user_id'   => (string) The user ID to get the tasks for
	*      'project'   => (string) The project ID to get the tasks for
	*      'assignee'  => (string) The assignee to get the tasks for
	*      'completed' => (string) The completion status of the task
	*   ]
	* @return object
	*/
	public static function get_tasks( $args = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$defaults = array(
			'limit' 	=> false,
			'user_id'	=> false,
			'project'	=> false,
			'assignee' 	=> false,
			'completed' => 'all'
		);

		$fields = 'id, parent_id, user_id, project, assignee, name, description, completed, team, status, date_created, date_due, categories, date_start, date_completed, other_data, priority, archived, type';
		$fields = apply_filters( 'zpm_tasks_sql_fields', $fields );

		$args = wp_parse_args( $args, $defaults );
		$query = "SELECT $fields FROM $table_name WHERE archived = 0 AND ";
		if ($args['user_id']) {
			$query .= "user_id = '" . $args['user_id'] . "' AND ";
		}
		if ($args['project']) {
			$query .= "project = '" . $args['project'] . "' AND ";
		}
		if ($args['assignee']) {
			$query .= "assignee = '" . $args['assignee'] . "' AND ";
		}
		if ($args['completed'] !== 'all') {
			$query .= "completed = '" . $args['completed'] . "' AND ";
		}
		$query .= " parent_id = '-1' ORDER BY id DESC";
		if ($args['limit']) {
			$query .= " LIMIT " . $args['limit'] . " ";
		}
		$tasks = $wpdb->get_results($query);
		$tasks = Tasks::sortByStartDate($tasks);
		return $tasks;
	}

	/**
	* Gets the task data of a given task ID
	* @param int $task_id
	* @return object
	*/
	public static function get_task( $task_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE id = '$task_id'";
		$task = $wpdb->get_row($query);
		$task = apply_filters( 'zpm_filter_task', $task );
		return $task;
	}

	/**
	* Creates a new task
	*/
	public static function create( $data ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$defaults = [
			'user_id' => '-1',
			'parent_id' => '-1',
			'assignee' => '-1',
			'project' => '-1',
			'name' => '',
			'description' => '',
			'date_start' => '',
			'date_due' => '',
			'date_created' => date('Y-m-d H:i:s'),
			'date_completed' => '',
			'completed' => 0,
			'team' => '',
			'priority' => 'priority_none'
		];

		if (Zephyr::isPro()) {
			$defaults['custom_fields'] = '';
			if (isset($data['custom_fields'])) {
				$data['custom_fields'] = serialize( (array) $data['custom_fields'] );
			}
		}

		$args = wp_parse_args( $data, $defaults );
		$task = $wpdb->insert($table_name, $args);
		return $wpdb->insert_id;
	}

	/**
	* Creates a new task
	*/
	public static function copy( $id, $extra_args = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		if ( !is_object( $id ) ) {
			$task = Tasks::get_task( $id );
		} else {
			$task = $id;
		}
		
		$args = [
			'user_id' => $task->user_id,
			'parent_id' => $task->parent_id,
			'assignee' => $task->assignee,
			'project' => $task->project,
			'name' => $task->name,
			'description' => $task->description,
			'date_start' => $task->date_start,
			'date_due' => $task->date_due,
			'date_created' => date('Y-m-d H:i:s'),
			'date_completed' => '',
			'completed' => 0,
			'priority' => $task->priority,
			'other_data' => $task->other_data
		];

		if ( !is_null( $extra_args ) ) {
			foreach ($extra_args as $key => $value) {
				$args[$key] = $value;
			}
		}

		$task = $wpdb->insert($table_name, $args);
		return $wpdb->insert_id;
	}

	/**
	* Converts a taks to a project
	*/
	public static function convert( $id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$task = Tasks::get_task( $id );
		$subtasks = Tasks::get_subtasks( $id );
		$date = date('Y-m-d H:i:s');
		$user_id = get_current_user_id();
		$settings = [
			'user_id' 		 => $user_id,
			'name' 			 => $task->name,
			'description' 	 => $task->description,
			'completed' 	 => false,
			'date_start' 	 => $task->date_start,
			'date_due' 		 => $task->date_due,
			'date_completed' => ''
		];

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		if ( is_array( $subtasks ) ) {
			$tasks_table = ZPM_TASKS_TABLE;
			foreach ($subtasks as $subtask) {
				$task_settings = [
					'parent_id' 	 => '-1',
					'user_id' 		 => $user_id,
					'assignee' 		 => '-1',
					'project' 		 => $last_id,
					'name' 			 => $subtask->name,
					'description' 	 => '',
					'completed' 	 => false,
					'date_start' 	 => $date,
					'date_due' 		 => '',
					'date_created' 	 => $date,
					'date_completed' => ''
				];
				$wpdb->insert( $tasks_table, $task_settings );
			}
		}

		$new_project = Projects::get_project( $last_id );
		return $new_project;
	}

	// Updates a task
	public static function update( $id, $args ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $args, $where );
	}

	/**
	* Creates a new task
	*/
	public static function delete( $id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$where = [
			'id' => $id
		];
		$wpdb->delete( $table_name, $where );
		return $id;
	}

	/**
	* Gets the subtasks for a task
	* @param int $task_id
	* @return object
	*/
	public static function get_subtasks( $task_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE parent_id = '$task_id'";
		$subtasks = $wpdb->get_results($query);
		return $subtasks;
	}

	/**
	* Returns the total number of tasks
	* @return int
	*/
	public static function get_task_count() {
		// global $wpdb;
		// $table_name = ZPM_TASKS_TABLE;
		// $query = "SELECT id FROM $table_name WHERE parent_id = '-1'";
		// $tasks = $wpdb->query($query);
		$manager = ZephyrProjectManager();
		$tasks = $manager::get_tasks();
		return sizeof( $tasks );
	}

	/**
	* Checks whether a task already exists
	* @param int $task_id
	* @return boolean
	*/
	public static function task_exists( $task_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE id = '$task_id'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Gets all tasks that are either complete or incomplete
	* @param boolean $completed
	* @return object
	*/
	public static function get_completed_tasks( $completed ) {
		// global $wpdb;
		// $table_name = ZPM_TASKS_TABLE;
		// $query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE completed = '$completed' AND parent_id = '-1'";
		// $tasks = $wpdb->get_results($query);
		$manager = ZephyrProjectManager();
		$args = array(
			'completed' => $completed
		);
		$tasks = $manager::get_tasks( $args );
		return $tasks;
	}

	/**
	* Gets the number of completed tasks
	* @return int
	*/
	public static function get_completed_task_count() {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE completed = '1' AND parent_id = '-1'";
		$task_count = $wpdb->query($query);
		return $task_count;
	}

	/**
	* Gets all tasks assigned to given user
	* @param int $user_id
	* @return object
	*/
	public static function get_user_tasks( $user_id ) {
		$manager = ZephyrProjectManager::get_instance();
		$all_tasks = $manager::get_tasks();

		$tasks = array();

		// Add tasks if user is assigned or in team
		if ( is_array( $tasks ) ) {
			foreach ( $all_tasks as $task ) {
				if ( is_object( $task ) ) {
					$team = property_exists( $task, 'team' ) ? $task->team : '-1';

					if ( Tasks::is_assignee( $task, $user_id ) ) {
						$tasks[] = $task;
					}
				}
			}
		}

		return $tasks;
	}

	public static function is_user_in_team( $user_id, $task_id ) {
		$task = Tasks::get_task( $task_id );
		$team = property_exists( $task, 'team' ) ? $task->team : '-1';
		return Members::is_user_in_team( $user_id, $team );
	}

	public static function sortByStartDate($tasks) {
		$sorted_array = [];
		 foreach($tasks as $task){
            //$key = date('Y-m-d',strtotime($task->date_start));
            $random = Utillities::generate_random_string(6);
            $key = $task->date_start . $random;
            if ($task->date_start == '' || $task->date_start == '0000-00-00 00:00:00') {
            	$key = 9999 . $random;
            }
            $sorted_array[$key] = $task;
        }
        $tasks = $sorted_array;
        krsort($tasks);
        return array_reverse($tasks);
	}

	/**
	* Gets the completed tasks of a certain user
	* @param boolean $completed
	* @return object
	*/
	public static function get_user_completed_tasks( $user_id, $completed = '1' ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;

		$results = [];
		$tasks = Tasks::get_user_tasks( $user_id );

		foreach ($tasks as $task) {
			if ( $completed == $task->completed ) {
				$results[] = $task;
			}
		}

		return $results;
	}

	/**
	* Gets the tasks of a certain project
	* @param int $project_id
	* @return object
	*/
	public static function get_project_tasks( $project_id, $subtasks = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE project = '$project_id'";

		if($subtasks == null) {
			$query .= " AND parent_id = '-1'";
		}
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Gets the tasks of a certain project for a certain user
	* @param int $project_id
	* @return object
	*/
	public static function get_project_assignee_tasks( $project_id, $user_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$results = [];
		$tasks = Tasks::get_user_tasks( $user_id );

		foreach ($tasks as $task) {
			if ( Tasks::is_project( $task, $project_id ) ) {
				$results[] = $task;
			}
		}

		return $results;
	}

	public static function is_project( $task, $project_id ) {
		if ( $task->project == $project_id ) {
			return true;
		}
		return false;
	}

	/**
	* Gets the number of tasks for a project
	* @param int $project_id
	* @return int
	*/
	public static function get_project_task_count( $project_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE project = '$project_id' AND parent_id = '-1'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Gets the number of completed tasks for a project
	* @param int $project_id
	* @return int
	*/
	public static function get_project_completed_tasks( $project_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE project = '$project_id' AND completed = '1' AND parent_id = '-1'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Retrieves all overdue tasks
	* @param int $project_id The ID of the project to filter by
	* @return array
	*/
	public static function get_overdue_tasks( $args = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$defaults = array(
			'project_id' => '-1',
			'assignee'	 => '-1'
		);
		$data = wp_parse_args( $args, $defaults );
		
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE ";

		if ($data['project_id'] !== '-1') {
			$query .= "project = '" . $data['project_id'] . "' AND ";
		}
		if ($data['assignee'] !== '-1') {
			$query .= "assignee = '" . $data['assignee'] . "' AND ";
		}
		$query .= "completed = '0' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		$date = new DateTime();
		$tasks_overdue = array();

		foreach ($tasks as $task) {
			if ($task->date_due == '0000-00-00 00:00:00') { 
				continue; 
			}

			$task_due = new DateTime($task->date_due);
			$dueTime = strtotime($task->date_due);
			$now = strtotime('now');
			
			if ($dueTime < $now) {
				array_push($tasks_overdue, $task);
			}
		}

		return $tasks_overdue;
	}

	/**
	* Returns a list of all tasks due this week
	* @param int $user_id
	* @param int $project_id
	* @return object
	*/
	public static function get_week_tasks( $assignee = null, $project_id = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE ";
		if (!is_null($project_id)) {
			$query .= "project = '$project_id' AND ";
		}
		$query .= "parent_id = '-1' AND completed = '0' ORDER BY id DESC";
		$tasks = $wpdb->get_results($query);
		$datetime = new DateTime();
		$date = strtotime($datetime->format('d M Y'));
		$start_of_week = date("Y-m-d", strtotime('sunday last week'));  
		$end_of_week = date("Y-m-d", strtotime('sunday this week'));
		$this_week_tasks = array();
		foreach ($tasks as $task) {
			if (!Tasks::is_assignee( $task, $assignee )) {
				continue;
			}
			$date_due = date("Y-m-d", strtotime($task->date_due)); 
			if ($date_due > $start_of_week && $date_due < $end_of_week) {
				array_push($this_week_tasks, $task);
			}
		}
		return $this_week_tasks;
	}

	/**
	* Returns the task creators name and creation date
	* @param int $project_id
	* @return string
	*/
	public static function task_created_by( $task_id ) {
		global $wpdb;

		$table_name = ZPM_TASKS_TABLE;

		$query = "SELECT user_id, date_created FROM $table_name WHERE id = $task_id";
		$data = $wpdb->get_row($query);
		$user = get_user_by('ID', $data->user_id);
		$today = new DateTime(date('Y-m-d H:i:s'));
		$created_on = new DateTime($data->date_created);
		$username = (is_object($user)) ? $user->display_name : 'user';
		$return = ($today->format('Y-m-d') == $created_on->format('Y-m-d')) 
					? sprintf( __( 'Created by %s at %s today', 'zephyr-project-manager' ), $username, $created_on->format('H:i') )
					: sprintf( __( 'Created by %s on %s at %s', 'zephyr-project-manager' ), $username, $created_on->format('d M'), $created_on->format('H:i') );
		return $return;
	}

	/**
	* Gets all the tasks 
	* @param int $project_id (optional)
	* @return object
	*/
	public function get_task_list( $project_id = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE project = '$project_id' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Retrieves all the comments for a task 
	* @param int $task_id The ID of the task to retrieve the comments for
	* @return object
	*/
	public static function get_comments( $task_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'task' AND subject_id = '$task_id' ORDER BY date_created DESC";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Returns the data for a specific comment 
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'task' AND id = '$comment_id'";
		$comment = $wpdb->get_row($query);
		return $comment;
	}

	/**
	* Retrieves all the attachments for a single comment 
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment_attachments( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$attachments = [];
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'task' AND parent_id = '$comment_id' ORDER BY date_created DESC";
		$results = $wpdb->get_results($query);

		foreach ($results as $result) {
			$file = new File( $result );
			if ($file->isType('attachment')) {
				$attachments[] = $result;
			}
		}

		return $attachments;
	}

	/**
	* Gets all the attachments for all tasks 
	* @return array
	*/
	public static function get_attachments() {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'task'";
		$attachments = $wpdb->get_results($query);
		$attachments_array = [];
		$attachment_types = zpm_get_attachment_types();

		foreach($attachments as $attachment) {
			$type = unserialize($attachment->type);
			if (in_array($type, $attachment_types)) {
				$attachments_array[] = array(
					'id' 	  => $attachment->id,
					'user_id' => $attachment->user_id,
					'subject' => $attachment->subject,
					'subject_id' => $attachment->subject_id,
					'message' => unserialize($attachment->message),
					'date_created' => $attachment->date_created
				);
			}
		}

		return $attachments_array;
	}

	/**
	* Gets all the attachments for all tasks 
	* @return array
	*/
	public static function get_task_attachments( $task_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'task' AND subject_id = '" . $task_id . "'";
		$attachments = $wpdb->get_results($query);
		$attachments_array = [];

		foreach($attachments as $attachment) {
			if (unserialize($attachment->type) == 'attachment') {
				$attachments_array[] = array(
					'id' 	  => $attachment->id,
					'user_id' => $attachment->user_id,
					'subject' => $attachment->subject,
					'subject_id' => $attachment->subject_id,
					'message' => unserialize($attachment->message),
					'date_created' => $attachment->date_created,
					'html' => Utillities::attachment_html($attachment)
				);
			}
		}

		return $attachments_array;
	}

	/**
	* Displays the new task modal
	*/
	public static function new_task_modal() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/new_task.php' );
	}

	/**
	* Gets the html for the task list page
	* @param array $filters
	*/
	public static function view_task_list( $filters = NULL ) {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/task-list.php' );
	}

	/**
	* Includes the task view modal container
	* @param int $task_id The ID of the modal to display
	*/
	public static function view_container( $task_id = null) {
		?>
		<div id="zpm_task_view_container" class="zpm-modal" data-task-id="<?php echo $task_id; ?>">
		</div>
		<?php
	}

	/**
	* Includes the task view modal
	* @param int $task_id The ID of the modal to display
	*/
	public static function view_task_modal( $task_id ) {
		include (ZPM_PLUGIN_PATH . '/templates/parts/task_view.php');
	}

	/**
	* Generates the HTML for a new task row
	* @param object $task The data for the task
	* @return HTML
	*/
	public static function new_task_row( $task, $frontend = false ) {
		global $zpm_settings;
		$general_settings = $zpm_settings;
		$manager = ZephyrProjectManager();
		$today = new DateTime();
		$due_datetime = new DateTime( $task->date_due );
		$users = $manager::get_users();
		$user_id = wp_get_current_user()->ID;
		$task_project = $manager::get_project( $task->project );

		$type = Tasks::get_type( $task );
		if ($type == 'daily') {
			if ($task->completed == '1') {
				
				if ( Utillities::is_past_date( $task->date_completed ) && !Utillities::is_past_date( $task->date_due ) ) {
					Tasks::complete( $task->id, '0' );
					$task->completed = '0';
				}
			}
		}

		$project_name = is_object($task_project) ? $task_project->name : '';
        $row_classes = (($task->completed == '1') ? 'zpm_task_complete' : '');
        $row_classes = apply_filters( 'zpm_row_classes', $row_classes, $task );
        $assignees = Tasks::get_assignees( $task, true );
		$due_today = ($today->format('Y-m-d') == $due_datetime->format('Y-m-d')) ? true : false;
		$overdue = ($today > $due_datetime && !$due_today) ? true : false;
		$due_date = (!$due_today) ? $due_datetime->format($general_settings['date_format']) : __( 'Today', 'zephyr-project-manager' );
		$due_date = ($task->date_due !== '0000-00-00 00:00:00') ? date_i18n($general_settings['date_format'], strtotime($task->date_due)) : '';

		$due_datetime = new DateTime($task->date_due);
		$hours_minutes = $due_datetime->format('H:i');
		if ($hours_minutes !== '00:00') {
			$due_date .= ' ' . $hours_minutes;
		}

		$complete = (($task->completed == '1') ? 'completed disabled' : '');
		$task = $manager::get_task( $task->id );
		$checked = (($task->completed == '1') ? 'checked' : '');
		$priority = property_exists( $task, 'priority' ) ? $task->priority : 'priority_none';
		$task_url = Tasks::task_url( $task->id );
		$status = Utillities::get_status( $task->priority );
		
		if (!is_admin() || $frontend) {
			$query = 'action=task&id=' . $task->id;
			$task_url = Utillities::get_frontend_url($query);
		}

		if (!Utillities::can_view_task( $task )) {
			return '';
		}
		
        ob_start(); ?>


        <a href="<?php echo $task_url; ?>" class="zpm_task_list_row <?php echo $row_classes; ?>" data-task-id="<?php echo $task->id; ?>" ripple="ripple" data-ripple="rgba(0,0,0,0.1)" data-task-name="<?php echo $task->name; ?>" id="zpm-task-list__item-<?php echo $task->id; ?>">

        	<?php if (apply_filters( 'zpm_can_complete_task', true, $task )) : ?>
        		<label for="zpm_task_id_<?php echo $task->id; ?>" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm_task_id_<?php echo $task->id; ?>" name="zpm_task_id_<?php echo $task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $checked; ?> data-task-id="<?php echo $task->id; ?>">
					<span class="zpm-material-checkbox-label"></span>
				</label>
			<?php endif; ?>

			<span class="zpm_task_list_data task_name">
				<?php echo $task->name; ?>
				<?php if ($general_settings['display_task_id']) : ?>
					<?php echo '(#' . $task->id . ')'; ?>
				<?php endif; ?>
				<?php if ($task->description !== '' && $task->description !== null) : ?>
					<span class="zpm_task_description"> - <?php echo stripslashes($task->description); ?></span>
				<?php endif; ?>

				<span class="zpm-task-item-extra-details">
					<?php echo apply_filters( 'zpm_task_item_extra_details', '', $task ); ?>
				</span>

			</span>

			<span class="zpm_task_details">

				<?php if ($project_name !== '' && !zpm_is_single_project()) : ?>
					<span title="Project" class="zpm_task_project"><?php echo $project_name; ?></span>
				<?php endif; ?>

				<?php if (property_exists($task, 'team') && $task->team !== "") : ?>
					<?php $team = Members::get_team( $task->team ); ?>
					<?php if (isset($team['name']) && $team['name'] !== "" && !empty($team['name'])) : ?>
						<span title="Team" class="zpm_task_project zpm-task-team"><?php echo $team['name']; ?></span>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( !empty( $assignees ) ) : ?>
					<span class="zpm-task_assignees">
						<?php foreach ($assignees as $assignee) : ?>
							<span title="<?php echo $assignee['name']; ?>" class='zpm_task_assignee' style='background-image: url("<?php echo $assignee['avatar'] ?>"); <?php echo $assignee['avatar'] == '' ? 'display: none;' : ''; ?>' title="<?php echo $assignee['name'] ?>"></span>
						<?php endforeach; ?>
					</span>
				<?php endif; ?>

				<span class="zpm_task_due_date <?php echo $overdue ? 'zpm_overdue' : ''; ?>"><?php echo $due_date; ?>
					<?php if ($type == 'daily') : ?>
						<span class="zpm-task__type-label"><?php _e( 'Daily', 'zephyr-project-manager' ); ?></span>
					<?php endif; ?>
				</span>
				<?php do_action('zpm_task_row_details', $task); ?>
			</span>

			<div class="zpm-task-actions__hidden">
				<span class="zpm-task__delete-checkbox">
					<label for="zpm-delete-task__checkbox<?php echo $task->id; ?>" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm-delete-task__checkbox<?php echo $task->id; ?>" name="zpm-delete-task__checkbox<?php echo $task->id; ?>" class="zpm-delete-task__checkbox zpm_toggle invisible" value="1" data-task-id="<?php echo $task->id; ?>">
					<span class="zpm-material-checkbox-label"></span>
				</label>
				</span>
			</div>

			<?php do_action( 'zpm_task_row_html', $task ); ?>
			<span class="zpm-task-priority-display <?php echo $priority; ?>" style="background: <?php echo $status['color'] !== '' ? $status['color'] : ''; ?>"></span>
		</a>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function getAvailableTasks() {
		$results = [];
		$tasks = Tasks::get_tasks();

		foreach ($tasks as $task) {
			if (Utillities::can_view_task( $task )) {
				$results[] = $task;
			}
		}
		
		return $results;
	}

	/**
	* Generates the HTML for a new single comment
	*
	* @param object $comment The comment data for the comment to add
	* @return HTML
	*/
	public static function new_comment( $comment ) {
		$current_user = wp_get_current_user();
		$this_user = BaseController::get_project_manager_user($comment->user_id);
		$datetime1 = new DateTime(date('Y-m-d H:i:s'));
		$datetime2 = new DateTime($comment->date_created);

		if ($datetime1->format('m-d') == $datetime2->format('m-d')) {
			// Was sent today
			$time_sent = $datetime2->format('H:i');
		} else {
			// Was sent earlier than today
			$time_sent = $datetime2->format('H:i m/d');
		}
				
		$timediff = human_time_diff(date_timestamp_get($datetime1), date_timestamp_get($datetime2));
		$comment_attachments = Tasks::get_comment_attachments($comment->id);

		$new_comment = '';
		$is_mine = $comment->user_id == get_current_user_id() ? true : false;
		$custom_classes = $is_mine ? 'zpm-my-message' : '';

		$type = unserialize($comment->type);
		$attachment_types = zpm_get_attachment_types();

		// If not file
		if (!in_array($type, $attachment_types)) {
			$new_comment .= '<div data-zpm-comment-id="' . $comment->id . '" class="zpm_comment ' . $custom_classes . '">
				<div class="zpm-comment-bubble">
				<span class="zpm_comment_user_image">
					<span class="zpm_comment_user_avatar" style="background-image: url(' . $this_user['avatar'] . ')"></span>
				</span>';

			if ($comment->user_id == $current_user->ID) {
				$new_comment .= '<span class="zpm_delete_comment lnr lnr-trash"></span>';
			}
				
			$new_comment .= '<span class="zpm_comment_user_text">
				<span class="zpm_comment_from">' . $this_user['name'] . '</span>
				<span class="zpm_comment_time_diff">' . $time_sent . '</span>
				<p class="zpm_comment_content">'. stripslashes_deep(unserialize($comment->message)) . '</p>';

			if (!empty($comment_attachments)) {
				$new_comment .= '<ul class="zpm_comment_attachments"><p>Attachments:</p>';

				foreach($comment_attachments as $attachment) {
					$attachment_id = unserialize( $attachment->message );
					$attachment = wp_get_attachment_url( $attachment_id );
					if (wp_attachment_is_image( $attachment_id )) {
						// Image preview
						$new_comment .= '<li class="zpm_comment_attachment"><a class="zpm_link" href="' . $attachment . '" download><img class="zpm-image-attachment-preview" src="' . $attachment . '"></a></li>';
					} else {
						// Attachment link
						$new_comment .= '<li class="zpm_comment_attachment"><a class="zpm_link" href="' . $attachment . '" download>' . $attachment . '</a></li>';
					}
				}
				$new_comment .= '</ul>';
			}
			$new_comment .= '</span></div></div>';
		} else {
			//$file = new File($comment);
			//echo $file->html();
		}
		return $new_comment;
	}

	public static function search( $query ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$added = [];
		$result_tasks = [];

		$results = $wpdb->get_results($wpdb->prepare(
		    "SELECT
		        id, name
		    FROM
		        `{$table_name}`
		    WHERE
		        name LIKE %s OR description LIKE %s;",
		    '%' . $wpdb->esc_like($query) . '%',
		    '%' . $wpdb->esc_like($query) . '%'
		));

		foreach ($results as $result) {
			$result_tasks[] = $result;
			$added[] = $result->id; 
		}

		$message_table = ZPM_MESSAGES_TABLE;
		$comment_tasks = $wpdb->get_results($wpdb->prepare(
			    "SELECT * FROM {$table_name} WHERE id IN 
	   				(SELECT subject_id FROM {$message_table} WHERE message LIKE %s)",
			    '%' . $wpdb->esc_like($query) . '%'
		));

		foreach ($comment_tasks as $result) {
			if ( !in_array($result->id, $added) ) {
				$result->name = $result->name . ' - ' . __( 'results found in comments' );
				$result_tasks[] = $result;
				$added[] = $result->id; 
			}
		}

		return $result_tasks;
	}

	public static function complete( $id, $complete ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$date = date('Y-m-d H:i:s');

		$settings = array(
			'completed' 	 => $complete,
			'date_completed' => $date,
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	public static function get_templates() {
		$templates = get_option('zpm_task_templates', array());
		return maybe_unserialize( $templates );
	}

	public static function get_template( $id ) {
		$templates = Tasks::get_templates();

		foreach ( $templates as $template ) {
			$template['default_assignee'] = isset($template['default_assignee']) ? maybe_unserialize( $template['default_assignee'] ) : [];
			if ($template['id'] == $id) {
				return $template;
			}
		}
		return null;
	}

	public static function create_template( $name, $customFields, $defaultAssignee = '', $defaultProject = '', $defaultTeam = '' ) {
		$templates = Tasks::get_templates();

		$last_template = end( $templates );
		$id = !empty( $last_template ) ? (int) $last_template['id'] + 1 : '0';
		$assignee = is_array($defaultAssignee) ? serialize($defaultAssignee) : '';

		$new_template = array(
			'id' => $id,
			'name' => $name,
			'custom_fields' => (array) $customFields,
			'default_project' => $defaultProject,
			'default_assignee' => $assignee,
			'default_team' => $defaultTeam
		);

		reset( $templates );

		$templates[] = $new_template;
		update_option( 'zpm_task_templates', serialize( $templates ) );

		return $new_template['id'];
	}

	public static function remove_template( $id ) {
		$templates = Tasks::get_templates();

		foreach ($templates as $key => $value) {
			if ($value['id'] == $id) {
				unset( $templates[$key] );
			}
		}

		update_option( 'zpm_task_templates', serialize( $templates ) );

		return true;
	}

	public static function update_template( $id, $name, $fields, $defaultAssignee = '', $defaultProject = '', $defaultTeam = ''  ) {
		$templates = Tasks::get_templates();

		foreach ($templates as $key => $value) {
			if ($value['id'] == $id) {
				$assignee = is_array($defaultAssignee) ? serialize($defaultAssignee) : '';
				$templates[$key]['name'] = $name;
				$templates[$key]['custom_fields'] = (array) $fields;
				$templates[$key]['default_assignee'] = $assignee;
				$templates[$key]['default_project'] = $defaultProject;
				$templates[$key]['default_team'] = $defaultTeam;

			}
		}

		update_option( 'zpm_task_templates', serialize( $templates ) );

		return true;
	}

	public static function template_row_html( $id ) {
		$template = Tasks::get_template( $id );
		$is_default = ( $id == Tasks::get_default_template() ) ? true : false;

		ob_start();

		?>
		<div class="zpm-custom-task-template" data-template-id="<?php echo $template['id']; ?>">

			<label for="zpm-task-template-checkbox-<?php echo $template['id']; ?>" class="zpm-material-checkbox">
			  <input type="checkbox" id="zpm-task-template-checkbox-<?php echo $template['id']; ?>" name="zpm_can_zephyr" class="zpm-default-task-template zpm_toggle invisible" value="1" data-template-id="<?php echo $template['id']; ?>" <?php echo $is_default ? 'checked' : ''; ?>>
			  <span></span>
			</label>

			<span class="zpm-task-template-name"><?php echo $template['name']; ?> <?php echo Tasks::get_default_template() == $template['id'] ? '<span class="zpm-task-template-default-notice">' . __( 'Default', 'zephyr-project-manager' ) . '</span>' : ''; ?></span>

		    <span class="zpm-remove-task-template lnr lnr-cross" data-template-id="<?php echo $template['id']; ?>"></span>
		</div>
		<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function get_default_template() {
		$default = get_option( 'zpm_default_template', 0 );
		return $default;
	}

	public static function set_default_template( $id ) {
		update_option( 'zpm_default_template', $id );
	}

	public static function send_comment( $task_id, $data, $files = null ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');

		$user_id = isset($data['user_id']) ? sanitize_text_field( $data['user_id']) : get_current_user_id();
		$subject_id = isset($data['subject_id']) ? sanitize_text_field( $data['subject_id']) : '';
		$message = isset($data['message']) ? serialize( sanitize_textarea_field($data['message']) ) : '';
		$type = isset($data['type']) ? serialize( $data['type']) : '';
		$parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
		$subject = isset($data['subject']) ? $data['subject'] : '';

		$settings = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'subject_id' => $task_id,
			'message' => $message,
			'date_created' => $date,
			'type' => $type,
			'parent_id' => $parent_id,
		);

		$wpdb->insert($table_name, $settings);
		return $wpdb->insert_id;
	}

	public static function get_task_data( $task ) {
		
		$args = is_object( $task ) && property_exists($task, 'other_data') ? maybe_unserialize( $task->other_data ) : array();
		$defaults = array(
			'width' => 300,
			'type' => 'default'
		);
		return wp_parse_args( $args, $defaults );
	}

	public static function update_task_data( $task_id, $args ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;

		$task = $task_id;

		if ( !is_object( $task_id ) ) {
			$task = Tasks::get_task( $task_id );
		}

		$defaults = Tasks::get_task_data( $task );

		foreach ($args as $key => $arg) {
			$defaults[$key] = $arg;
		}

		$data = wp_parse_args( $args, $defaults );
		$settings = array(
			'other_data' => serialize( $data )
		);

		$where = array(
			'id' => $task->id
		);

		$wpdb->update( $table_name, $settings, $where );
		return $data;
	}

	public static function get_type( $task ) {
		$data = Tasks::get_task_data( $task );
		if (isset($data['type'])) {
			return $data['type'];
		}
		return 'default';
	}

	public static function get_days( $task ) {
		$data = Tasks::get_task_data( $task );
		if (isset($data['days'])) {
			return (array) $data['days'];
		}
		return [];
	}

	public static function get_expiration_date( $task ) {
		$data = Tasks::get_task_data( $task );
		if (isset($data['expires'])) {
			return $data['expires'];
		}
		return '';
	}

	public static function get_daily_tasks( $user_id = null ) {
		$manager = ZephyrProjectManager();
		$results = [];
		$tasks = $manager::get_tasks();
		$date = new DateTime();

		foreach ($tasks as $task) {
			$type = Tasks::get_type( $task );
			$due = new DateTime( $task->date_due );
			$pending = $due->format('d-m-y') > $date->format('d-m-y') || !strtotime( $task->date_due ) ? true : false;

			if ( $type == 'daily' && $pending && Tasks::is_assignee( $task, $user_id ) ) {
				if ($task->completed == '1') {
					if ( Utillities::is_past_date( $task->date_completed ) && !Utillities::is_past_date( $task->date_due ) ) {
						Tasks::complete( $task->id, '0' );
						$task->completed = '0';
					}
				}
				$results[] = $task;
			} 
		}

		return $results;
	}

	public static function task_url( $task_id, $frontend = false ) {
		$url = esc_url( admin_url( "/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=" . $task_id ) );

		if ( $frontend ) {
			$query = 'action=task&id=' . $task_id;
			$url = Utillities::get_frontend_url($query);
		}

		return $url;
	}

	public static function get_assignees( $task, $get_object = false ) {
		$results = [];

		if ( !is_object( $task ) ) {
			$task = Tasks::get_task( (int) $task );
			if (!is_object($task)) {
				return $results;
			}
		}

		$added = [];
		$assignee_ids = explode( ',', $task->assignee );
		$team = property_exists( $task, 'team' ) ? Members::get_team( $task->team ) : array();
		
		foreach ( $assignee_ids as $id ) {
			
			if ( !in_array( $id, $added ) ) {
				if ( $get_object ) {
					$member = Members::get_member( $id );
					if ( isset($member['id']) && !empty( $member['id'] ) ) {
						$results[] = $member;
						$added[] = $id;
					}
				} else {
					$results[] = $id;
					$added[] = $id;
				}	
			}
		}
		
		if ( !is_null( $team ) ) {
			if (isset($team['members'])) {
				foreach ( (array) $team['members'] as $member ) {
					if ( isset($member['id']) && !in_array( $member['id'], $added ) ) {
						if ( $get_object ) {
							$member = Members::get_member( $member['id'] );
							if ( !empty( $member['id'] )) {
								$results[] = $member;
								$added[] = $member['id'];
							}
						} else {
							$results[] = $member['id'];
							$added[] = $member['id'];
						}
					}
				}
			}
		}

		return $results;
	}

	public static function is_assignee( $task, $user_id = null ) {
		$assignees = Tasks::get_assignees( $task );
		$user_id = is_null( $user_id ) ? get_current_user_id() : $user_id;

		// $overrideAssignee = apply_filters( 'zpm_override_assignee', false, $task, $user_id );
		// if ($overrideAssignee) {
		// 	return true;
		// }

		if ( in_array( $user_id, $assignees ) ) {
			return true;
		}

		return false;
	}

	public static function get_assignee_string( $task ) {
		$string = '';
		$assignees = Tasks::get_assignees( $task, true );
		$count = 0;

		foreach ($assignees as $assignee) {
			if ( $count <= 0 ) {
				$string .= $assignee['name'];
			} else {
				if ($count == (sizeof($assignees) - 1)) {
					$string .= ' & ' . $assignee['name'];
				} else {
					$string .= ', ' . $assignee['name'];
				}
				
			}
			$count++;
		}

		return $string;
	}

	public static function set_last_recurrence( $task, $date ) {
		Tasks::update_task_data( $task, array(
			'last_recurrence' => $date
		));
	}

	public static function get_last_recurrence( $task ) {
		$data = Tasks::get_task_data( $task );
		if (isset($data['last_recurrence'])) {
			return $data['last_recurrence'];
		}
		return '';
	}

	public static function archive( $id ) {
		Tasks::update( $id, array(
			'archived' => 1
		));
	}

	public static function unarchive( $id ) {
		Tasks::update( $id, array(
			'archived' => 0
		));
	}

	public static function recur_task( $task ) {

		if ($task->archived) {
			return;
		}

		$taskData = Tasks::get_task_data($task);
		$type = Tasks::get_type( $task );
		$start = $task->date_start;
		$end = $task->date_due;

		if ($start == '0000-00-00 00:00:00'){
		    $start =  $task->date_created;
		}

		if (isset($taskData['start'])) {
			$start = $taskData['start'];
		}

		if ($end == '0000-00-00 00:00:00'){
		    return;
		}

		$frequency = isset($taskData['frequency']) ? $taskData['frequency'] : 1;

		$today = date('Y-m-d H:i:s');
		$last_recurrence = Tasks::get_last_recurrence($task);

		switch ($type) {
			case 'daily':

				$day_of_week = date('N') - 1;
				$days = Tasks::get_days( $task );
				$dayCount = 1 * $frequency;
				if (!in_array($day_of_week, (array)$days)) {
					return;
				}
				if ( Utillities::is_past_date( $end ) && ( strtotime($last_recurrence) <= strtotime('-' . $dayCount . ' days')  )) {

					$days = strtotime("+" . $dayCount . " day");
					$new_start_date = $today;
					$new_due_date = date( 'Y-m-d H:i:s', $days );
					$task->date_start = $new_start_date;
					$task->date_due = $new_due_date;
					$new_task = Tasks::copy( $task );
					Tasks::archive( $task->id );
					Tasks::set_last_recurrence( $task, $new_start_date );
				}
			
				break;
			case 'weekly':
				$weekCount = 30 * $frequency;
				if ( strtotime($end) < strtotime('-' . $weekCount . ' days') && ( strtotime($last_recurrence) <= strtotime('-' . $weekCount . ' days') || empty($last_recurrence) ) ) {

					$days = strtotime("+" . $weekCount . " day");
					$new_start_date = $today;
					$new_due_date = date( 'Y-m-d H:i:s', $days );
					$task->date_start = $new_start_date;
					$task->date_due = $new_due_date;
					$new_task = Tasks::copy( $task );
					Tasks::archive( $task->id );
					Tasks::set_last_recurrence( $task, $new_start_date );
				 }

				break;
			case 'monthly':
				$dayCount = 30 * $frequency;
				if ( strtotime($end) <= strtotime('-' . $dayCount . ' days') && ( strtotime($last_recurrence) <= strtotime('-' . $dayCount . ' days') || empty($last_recurrence) ) ) {
					$months = strtotime("+" . $dayCount . " day");
					$new_start_date = $today;
					$new_due_date = date( 'Y-m-d H:i:s', $months );
					$task->date_start = $new_start_date;
					$task->date_due = $new_due_date;
					$new_task = Tasks::copy( $task );
					Tasks::archive( $task->id );
					Tasks::set_last_recurrence( $task, $new_start_date );
				 }

				break;
			case 'anually':

				$yearCount = 1 * $frequency;
				if ( strtotime($end) < strtotime('-' . $yearCount . ' year') && ( strtotime($last_recurrence) <= strtotime('-' . $yearCount . ' year') || empty($last_recurrence) ) ) {
					$years = strtotime("+" . $yearCount . " year");
					$new_start_date = $today;
					$new_due_date = date( 'Y-m-d H:i:s', $years );
					$task->date_start = $new_start_date;
					$task->date_due = $new_due_date;
					$new_task = Tasks::copy( $task );
					Tasks::archive( $task->id );
					Tasks::set_last_recurrence( $task, $new_start_date );
				 }

				break;
			default:
				break;
		}
	}

	public static function recurrence_string( $task ) {
		$type = Tasks::get_type( $task );
		$days = Tasks::get_days( $task );

		$days_of_week = [
			'0' => __( 'Monday', 'zephyr-project-manager' ),
			'1' => __( 'Tuesday', 'zephyr-project-manager' ),
			'2' => __( 'Wednesday', 'zephyr-project-manager' ),
			'3' => __( 'Thursday', 'zephyr-project-manager' ),
			'4' => __( 'Friday', 'zephyr-project-manager' ),
			'5' => __( 'Saturday', 'zephyr-project-manager' ),
			'6' => __( 'Sunday', 'zephyr-project-manager' )
		];

		switch ($type) {
			case 'daily':
				$recurrence_label = __( 'Repeats Daily', 'zephyr-project-manager' );

				if (sizeof($days) > 0) {
					$recurrence_label .= ' on ';
				}

				for ($i = 0; $i < sizeof($days); $i++) { 
					if ($i < (sizeof($days) - 1)) {
						if ($i > 0) {
							$recurrence_label .= ', ' . $days_of_week[$days[$i]];;
						} else {
							$recurrence_label .= $days_of_week[$days[$i]];
						}  
					} else {
						$recurrence_label .= ' & ' . $days_of_week[$days[$i]];
					}
				}
				break;
			case 'weekly':
				$recurrence_label = __( 'Repeats Weekly', 'zephyr-project-manager' );
				break;
			case 'monthly':
				$recurrence_label = __( 'Repeats Monthly', 'zephyr-project-manager' );
				break;
			case 'annually':
				$recurrence_label = __( 'Repeats Annually', 'zephyr-project-manager' );
				break;
			default:
				$recurrence_label = '';
				break;
		}

		return $recurrence_label;
	}

	// Returns whether a task has been applied for
	public static function isApplied($task) {

		return apply_filters( 'zpm_is_task_applied', true, $task );
	}

	public static function getStartToEndDays($task, $expires = false) {
		if (!is_object($task)) {
			return 0;
		}
		$start = strtotime($task->date_start);
		$end = strtotime($task->date_due);
		

		if ($task->date_start == '0000-00-00 00:00:00') {
			$start = time();
		}
		if ($task->date_due == '0000-00-00 00:00:00') {
			$end = time();
		}

		if ($expires) {
			$expireDate = Tasks::get_expiration_date($task);
			$end = strtotime($expireDate);
		}

		$datediff = $end - $start;

		return round($datediff / (60 * 60 * 24));
	}

	public static function subtaskItemHtml( $task, $classes = '' ) {
		$task = new Task($task);
		$frontend = isset($_POST['frontend']) || !is_admin() ? true : false;

		ob_start();
		?>
			<li class="zpm_subtask_item <?php echo $task->isCompleted() ? 'zpm_task_complete' : ''; ?> <?php echo $classes; ?>" data-zpm-subtask="<?php echo $task->id; ?>">
				<label for="zpm_subtask_<?php echo $task->id; ?>" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm_subtask_<?php echo $task->id; ?>" class="zpm_subtask_is_done" data-task-id="<?php echo $task->id; ?>" <?php echo $task->isCompleted() ? 'checked' : ''; ?>>
					<span class="zpm-material-checkbox-label"></span>
				</label>
				<span class="zpm_subtask_name"><a class="zpm_link" href="<?php echo Tasks::task_url($task->id, $frontend); ?>" target="_BLANK"><?php echo stripslashes($task->name); ?></a></span>
				<span class="zpm-subtask__description"><?php echo !empty($task->description) ? ' - ' . stripslashes($task->description) : ''; ?></span>
				<span data-zpm-subtask-id="<?php echo $task->id; ?>" class="zpm_update_subtask"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></span>
				<span data-zpm-subtask-id="<?php echo $task->id; ?>" class="zpm_delete_subtask"><?php _e( 'Delete', 'zephyr-project-manager' ); ?></span>
				<span class="zpm-subtask__due"><?php echo $task->getDueDate(); ?></span>
			</li>
		<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function newSubtaskModal() {
		?>
		<div id="zpm_new_subtask_modal" class="zpm-modal zpm_compact_modal">
			<div class="zpm-form__group">
				<input type="text" name="zpm_new_subtask_name" id="zpm_new_subtask_name" class="zpm-form__field" placeholder="<?php _e( 'Subtask Name', 'zephyr-project-manager' ); ?>" data-ajax-name="name" />
				<label for="zpm_new_subtask_name" class="zpm-form__label"><?php _e( 'Subtask Name', 'zephyr-project-manager' ); ?></label>
			</div>

			<div class="zpm-form__group">
				<textarea type="text" name="zpm-new-subtask__description" id="zpm-new-subtask__description" class="zpm-form__field" placeholder="<?php _e( 'Subtask Description', 'zephyr-project-manager' ); ?>" data-ajax-name="description"></textarea>
				<label for="zpm-new-subtask__description" class="zpm-form__label"><?php _e( 'Subtask Description', 'zephyr-project-manager' ); ?></label>
			</div>

			<div class="zpm-row">
				<div class="zpm-col zpm-col-6">
					<div class="zpm-form__group">
						<input type="text" name="zpm-new-subtask__start" id="zpm-new-subtask__start" class="zpm-form__field zpm-datepicker" placeholder="<?php _e( 'Start', 'zephyr-project-manager' ); ?>" data-ajax-name="start-date" />
						<label for="zpm-new-subtask__start" class="zpm-form__label"><?php _e( 'Start', 'zephyr-project-manager' ); ?></label>
					</div>
				</div>
				<div class="zpm-col zpm-col-6">
					<div class="zpm-form__group">
						<input type="text" name="zpm-new-subtask__due" id="zpm-new-subtask__due" class="zpm-form__field zpm-datepicker" placeholder="<?php _e( 'Due', 'zephyr-project-manager' ); ?>" data-ajax-name="due-date" />
						<label for="zpm-new-subtask__due" class="zpm-form__label"><?php _e( 'Due', 'zephyr-project-manager' ); ?></label>
					</div>
				</div>
			</div>

			<button id="zpm_save_new_subtask" class="zpm_button"><?php _e( 'Create Subtask', 'zephyr-project-manager' ); ?></button>
		</div>
		<?php
	}

	public static function getTaskProgress($taskId, $format = 'Y-m-d') {
		$data = maybe_unserialize( get_option( 'zpm_tasks_progress', array() ) );

		if (!is_null($taskId)) {
			if (isset($data[$taskId])) {
				$results = [];
				foreach ($data[$taskId] as $date => $entry) {
					if ($format !== 'Y-m-d') {
						$time = strtotime($date);
						$formattedDate = date($format, $time);
						$results[$formattedDate] = $entry;
					} else {
						$results[$date] = $entry;
					}
				}
				
				$date = date($format);

				//$results[$date] = Projects::percent_complete($projectId);
				return $results;
			} else {
				return [];
			}
		}

		return (array) $data;
	}

	public static function updateTaskProgress($taskId) {
		if (is_object($taskId)) {
			$completed = $taskId->completed ? true : false;
			$taskId = $taskId->parent_id == '-1' || empty($taskId->parent_id) ? $taskId->id : $taskId->parent_id;
		}
		$progressData = Tasks::getTaskProgress($taskId);
		$date = date('Y-m-d');
		$percentageCompleted = !$completed ? Tasks::getTaskPercentage($taskId) : 100;

		if (isset($progressData[$taskId])) {
			$progressData[$taskId][$date] = $percentageCompleted;
		} else {
			$progressData[$taskId] = [];
			$progressData[$taskId][$date] = $percentageCompleted;
		}
		
		update_option( 'zpm_tasks_progress', serialize($progressData) );
		return (array) $progressData;
	}

	public static function getTaskPercentage($taskId) {
		$subtasks = Tasks::get_subtasks( $taskId );
		$completed = 0;
		foreach ($subtasks as $subtask) {
			if ($subtask->completed) {
				$completed++;
			}
		}

		$total = sizeof($subtasks);
		$pending = $total - $completed;
		$percentage = ($total !== 0) ? floor($completed / $total * 100) : 0;
		return $percentage;
	}

	public static function getTaskParents( $task ) {
		$parents = [];
		$parentsDone = 0;
		$prevParent = $task;
		while($parentsDone !== 1){
			if ($prevParent->parent_id !== '-1') {
				$newParent = Tasks::get_task($prevParent->parent_id);
				$parents[] = $newParent;
				$prevParent = $newParent;
			} else {
				$parentsDone = 1;
			}
		}
		$parents = array_reverse($parents);
		return $parents;
	}

	public static function hasParent( $task ) {
		if ($task->parent_id == '-1' || empty($task->parent_id)) {
			return false;
		} else {
			return true;
		}
	}

	public static function hasProject( $task ) {
		if ($task->project == '-1' || empty($task->project)) {
			return false;
		} else {
			return true;
		}
	}

	public static function hasDueDate( $task ) {
		if ($task->date_due == '' || $task->date_due == '0000-00-00 00:00:00') {
			return false;
		} else {
			return true;
		}
	}

	public static function hasStartDate( $task ) {
		if ($task->date_start == '' || $task->date_start == '0000-00-00 00:00:00') {
			return false;
		} else {
			return true;
		}
	}

	public static function getDueTasks( $dateFilter ) {
		$results = [];
		$tasks = Tasks::get_tasks();

		$current = strtotime(date("Y-m-d"));

		foreach ($tasks as $task) {
			if (Tasks::hasDueDate($task)) {
				$date = strtotime($task->date_due);
				$datediff = $date - $current;
				$difference = floor($datediff/(60*60*24));

				if ($dateFilter == 'today') {
					if ($difference == 0) {
						$results[] = $task;
					}
				}

				if ($dateFilter == '7 days') {
					if ($difference > 0 && $difference < 8) {
						$results[] = $task;
					}
				}

			}
		}

		return $results;
	}

	public static function getTasksDateRange( $rangeStart, $rangeEnd, $args = [] ) {
		$defaults = [
			'project' => '-1'
		];
		$results = [];
		$tasks = Tasks::get_tasks();
		$args = wp_parse_args( $args, $defaults );
		$dates = [];

		$start = strtotime($rangeStart);
		$end = strtotime($rangeEnd);

		if (!$end) {
			$end = strtotime('tomorrow');
		}

		if (!$start) {
			$start = strtotime('-30 days');
		}

		foreach ($tasks as $task) {
			if ($args['project'] == '-1' || $task->id == $args['project']) {
				$time = strtotime($task->date_completed);
				if ($time <= $end && $time >= $start) {
					$results[] = $task;
				}
			}
		}

		return $results;
	}

	public static function getProjectName( $task ) {
		$name = '';
		if (Tasks::hasProject($task)) {
			$project = Projects::get_project($task->project);
			if (is_object($project)) {
				$name = $project->name;
			}
		}

		return $name;
	}

	public static function formatDate($dateString) {
		$dateTime = new DateTime($dateString);
		$formattedDate = $dateString;
		$settings = Utillities::general_settings();

		if ($dateTime->format('Y') == "-0001") {
			$formattedDate = __( 'None', 'zephyr-project-manager' );
		} else {
			$formattedDate = date_i18n($settings['date_format'], strtotime($dateString));
		}
		return $formattedDate;
	}

	public static function getLastSorting() {
		$userId = get_current_user_id();
		$lastSorting = get_user_meta( $userId, 'zpm_tasks_last_sorting', true );

		if (!$lastSorting) {
			$lastSorting = 'created';
		}

		return $lastSorting;
	}

	public static function sortTasks( $tasks = [], $filter = 'created' ) {
		$sorted_array = array();
		$tasks = (array) $tasks;

		switch ($filter) {
			case 'created':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->date_created));
				    $random = Utillities::generate_random_string(6);
				    $sorted_array[$task->date_created . $random] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;
			case 'start':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->date_start));
				    $sorted_array[$task->date_start . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;
			case 'due':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->date_due));
				    $sorted_array[$task->date_due . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;
			case 'due-asc':
				foreach($tasks as $task){

				    $key = date('Y-m-d',strtotime($task->date_due));
				    $random = Utillities::generate_random_string(6);
				    $key = $task->date_due . $random;
				    if (!Tasks::hasDueDate($task)) {
				    	$key = '999' . $random;
				    }
				    $sorted_array[$key] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				$tasks = array_reverse($tasks);
				break;
			case 'assignee':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->assignee));
				    $sorted_array[$task->assignee . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;	
			case 'name':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->name));
				    $sorted_array[$task->name . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				ksort($tasks);
				break;	
			default:
				break;
		}

		return $tasks;
	}
}