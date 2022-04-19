<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Base\BaseController;

class Activity {

	/**
	* Get all activities
	* @param array $args = [
	* 	'limit'  => (int) The number of activities to retrieve
	* 	'offset' => (offset) The offset to retrieve the activities from
	* ]
	* @return object
    */
	public static function get_activities( $args = null ) {
		global $wpdb;
		$defaults = [
			'limit' => false,
			'offset' => false
		];
		$args = wp_parse_args( $args, $defaults );
		$table_name = ZPM_ACTIVITY_TABLE;
		$query = "SELECT * FROM $table_name ORDER BY date_done DESC ";
		if ($args['limit'] !== false) {
			$query .= "LIMIT " . $args['limit'] . " ";
		}
		if ($args['offset'] !== false) {
			$query .= "OFFSET " . $args['offset'] . " ";
		}
		$activities = $wpdb->get_results($query);
		return $activities;
	}

	/**
     * Logs an activity
     *
     * @param int $user_id
     * @param int $subject_id
     * @param string $old_name
     * @param string $new_name
     * @param string $subject
     * @param string $action
     * @param string $date_done
     * @return array
     */
	public static function log_activity($user_id, $subject_id, $old_name, $subject_name, $subject, $action, $date_done = null) {
		global $wpdb;
		$table_name = ZPM_ACTIVITY_TABLE;
		$date = new DateTime( null, zpm_get_timezone() );
		$date_done = $date->format('Y-m-d H:i:s');
		$settings = array(
			'user_id' 	 	=> $user_id,
			'subject_id' 	=> $subject_id,
			'subject_name' 	=> $subject_name,
			'old_name' 		=> $old_name,
			'subject' 	 	=> $subject,
			'action' 	 	=> $action,
			'date_done'  	=> $date_done,
		);
		$wpdb->insert($table_name, $settings);
		return $settings;
	}

	/**
	* Returns the data to be used to display activities HTML
	* @param array $activities An array of the activities
	* @return array
	*/
	public static function display_activities( $all_activities ) {
		$prev_day = '';
		$activities = array();
		foreach($all_activities as $activity) {
			$user_details = get_user_by('ID', $activity->user_id);
			if (!is_object($user_details)) {
				continue;
			}
			$username = $user_details->display_name;
			$link = '';

			switch ($activity->action) {

				case 'project_added':
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects&action=edit_project&project=' . $activity->subject_id));
					$project_html = '<a class="zpm_link" href="' . $link . '">' . $activity->subject_name . '</a>';
					$username_html = '<b>' . $username . '</b>';
					$message = sprintf( __( '%s created a new project called %s', 'zephyr-project-manager' ), $username_html, $project_html );
					break;
				case 'project_deleted':
					$project_html = '<b>' . $activity->subject_name . '</b>';
					$username_html = '<b>' . $username . '</b>';
					$message = sprintf( __( '%s deleted the project %s', 'zephyr-project-manager' ), $username_html, $project_html );
					break;
				case 'project_changed_name':
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects&action=edit_project&project=' . $activity->subject_id));
					$new_name_html = '<a class="zpm_link" href="' . $link . '">' . $activity->subject_name . '</a>';

					$project_html = '<b>' . $activity->old_name . '</b>';
					$username_html = '<b>' . $username . '</b>';
					$message = sprintf( __( '%s changed the name of the project %s to %s', 'zephyr-project-manager' ), $username_html, $project_html, $new_name_html );
					break;
				case 'task_changed_name':
					// Task name was changed
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=' . $activity->subject_id));
					$new_name_html = '<a class="zpm_link" href="' . $link . '">' . $activity->subject_name . '</a>';

					$project_html = '<b>' . $activity->old_name . '</b>';
					$username_html = '<b>' . $username . '</b>';
					$message = sprintf( __( '%s changed the name of the task %s to %s', 'zephyr-project-manager' ), $username_html, $project_html, $new_name_html );
					break;
				case 'task_changed_date':
					// Task due date was changed
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=' . $activity->subject_id));
					$date = $activity->subject_name;
					if (DateTime::createFromFormat('m/d/Y', $date) !== false) {
						$date = new DateTime($date);
						$date = $date->format('d M');
					}
					$new_date_html = '<a class="zpm_link" href="' . $link . '">' . $date . '</a>';
					$project_html = '<b>' . $activity->old_name . '</b>';
					$username_html = '<b>' . $username . '</b>';
					if ( !empty( $activity->subject_name ) ) {
						$message = sprintf( __( '%s changed the due date of the task %s to %s', 'zephyr-project-manager' ), $username_html, $project_html, $new_date_html );
					}
					break;
				case 'project_changed_description':
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects&action=edit_project&project=' . $activity->subject_id));

					$username_html = '<b>' . $username . '</b>';
					$project_html = '<a class="zpm_link" href="' . $link . '">' . $activity->subject_name . '</a>';

					$message = sprintf( __( '%s changed the description of the project %s', 'zephyr-project-manager' ), $username_html, $project_html );
					break;
				case 'task_added':
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=' . $activity->subject_id));
					$username_html = '<b>' . $username . '</b>';
					$task_html = '<a class="zpm_link" href="' . $link . '">' . $activity->subject_name . '</a>';
					$message = sprintf( __( '%s created a new task called %s', 'zephyr-project-manager' ), $username_html, $task_html );
					break;
				case 'task_assigned':
					$link = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=' . $activity->subject_id));
					$task = Tasks::get_task($activity->subject_id);
					$assignee_name = Tasks::get_assignee_string( $task );
					$username_html = '<b>' . $username . '</b>';
					$task_html = '<a class="zpm_link" href="' . $link . '">' . $activity->subject_name . '</a>';

					if ( is_object($task) && $task->assignee !== "-1" ) {
						$message = sprintf( __( '%s assigned the task %s to %s', 'zephyr-project-manager' ), $username_html, $task_html, $assignee_name );
					}
					break;
				case 'task_deleted':
					$username_html = '<b>' . $username . '</b>';
					$subject_html = '<b>' . $activity->subject_name . '</b>';
					$message = sprintf( __( '%s deleted the task %s', 'zephyr-project-manager' ), $username_html, $subject_html );
					break;
				default:
					$username_html = '<b>' . $username . '</b>';
					$message = $username_html . ' ' . $activity->action;
					break;
			}

			$date = new DateTime( $activity->date_done );
			$day = $date->format('M d');
			$time = $date->format('H:i');

			$new_activity = array(
				'user' 		=> $username,
				'message' 	=> $message,
				'link' 		=> $link,
				'name'		=> $activity->subject_name,
				'time' 		=> $time,
				'day' 		=> $day
			);

			if ($prev_day !== '' && $prev_day == $day) {
				array_push($activities[$day], $new_activity);
			} else {
				$activities[$day] = array($new_activity);
			}
			$prev_day = $day;
		}
		ob_start();
		?>
		<?php foreach ($activities as $date => $activity) : ?>
			<div class="zpm_activity_day">
				<div class="zpm_activity_date"><?php echo $date; ?></div>		
				<?php foreach($activity as $action) : ?>				
					<div class="zpm_activity_entry">
						<span class="zpm_activity_time"><?php echo $action['time']; ?></span>
						<?php echo $action['message']; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach;

		if (empty($activities)) {
			$html = ob_get_clean(); 
			return false; 
		} else {
			$html = ob_get_clean(); 
			return $html;
		}
		
	}
}