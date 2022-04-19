<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Api\Emails;
use Inc\Core\Tasks;
use Inc\Core\Members;
use Inc\Core\Activity;
use Inc\Core\Utillities;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager;
use Inc\Api\Callbacks\AdminCallbacks;

class Projects {

	private $settings;

	function __construct() {
		$this->settings = Utillities::general_settings();
		// Update project progress daily
		add_action( 'zpm_update_progress', array( $this, 'update_progress' ) );
		date_default_timezone_set('UTC');
		$time = strtotime('00:00:00');
		$recurrence = 'daily';
		$hook = 'zpm_update_progress';
		if ( !wp_next_scheduled( $hook ) ) {
			wp_schedule_event( $time, $recurrence, $hook);
		}

		// Send weekly email progress reports
		add_action( 'zpm_weekly_updates', array( $this, 'weekly_updates' ) );
		date_default_timezone_set('UTC');
		$time = strtotime('00:00:00');
		$recurrence = 'weekly';
		$hook = 'zpm_weekly_updates';
		if ( !wp_next_scheduled( $hook ) ) {
			wp_schedule_event( $time, $recurrence, $hook);
		}

		// Send daily updates on due tasks
		add_action( 'zpm_task_notifications', array( $this, 'task_notifications' ) );
		date_default_timezone_set('UTC');
		$time = strtotime('00:00:00');
		$recurrence = 'daily';
		$hook = 'zpm_task_notifications';
		if ( !wp_next_scheduled( $hook ) ) {
			wp_schedule_event( $time, $recurrence, $hook);
		}

		add_filter( 'zpm_filter_project', array( $this, 'filter_project' ) );
		add_filter( 'zpm_filter_projects', array( $this, 'filter_projects' ) );
		add_filter( 'zpm_should_show_project', array( $this, 'should_show_project' ), 1, 2 );
	}

	public static function createProjectsTable() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'zpm_projects';

		$charset_collate = $wpdb->get_charset_collate();

		$columnFields = "id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			parent_id mediumint(9) NOT NULL,
			managers TEXT,
			assignees TEXT,
			name text NOT NULL,
			description text NOT NULL,
			completed boolean NOT NULL,
			archived boolean NOT NULL,
			team TEXT DEFAULT '',
			categories varchar(100) NOT NULL,
			status varchar(255) NOT NULL,
			date_created DATETIME NOT NULL,
			date_due DATETIME NOT NULL,
			date_start DATETIME NOT NULL,
			priority varchar(255),
			date_completed DATETIME NOT NULL,
			other_data TEXT NOT NULL,
			type varchar(255),
			other_settings varchar(999) NOT NULL";

		$columnFields = apply_filters( 'zpm_project_table_sql', $columnFields );

		$sql = "CREATE TABLE $table_name (
			$columnFields,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	* Creates a new project
	* @param array $args Array containing the project details
	* 	$args = [
	*      'user_id'  	    => (int) ID of the project creator
	*      'name' 	  	    => (string) Name of the project
	*      'description'    => (string) Description for the project
	*      'team'    	    => (string) Team assigned to the project
	*      'categories'     => (string) Categories assigned to the project
	*      'completed'      => (bool) Completion status of the project
	*      'date_start'     => (string) Datetime that the project is scheduled to start
	*      'date_due'       => (string) Datetime that the project is due
	*      'date_created'   => (string) Datetime that the project was created
	*      'date_completed' => (string) Datetime that the project was completed
	*   ]
	* @return int Returns the ID of the newly created project project
	*/
	public static function new_project( $args = null ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$defaults = array(
			'user_id'        => get_current_user_id(),
			'name'           => 'Untitled Project',
			'description'    => '',
			'team'           => '',
			'categories'     => '',
			'completed'      => '1',
			'date_start'     => date('Y-m-d H:i:s'),
			'date_due'       => '',
			'date_created'   => date('Y-m-d H:i:s'),
			'date_completed' => '',
			'priority'       => 'priority_none'
		);
		$data = wp_parse_args( $args, $defaults );
		$wpdb->insert( $table_name, $data );
		$new_project_id = $wpdb->insert_id;
		Activity::log_activity($data['user_id'], $wpdb->insert_id, '', $data['name'], 'project', 'project_added' );
		return $new_project_id;
	}

	/**
	* Retrieves all projects
	* @param int $limit The amount of projects to retrieve
	* @return object
	*/
	public static function get_projects( $limit = null, $args = null, $filters = null ) {
		global $wpdb;
		$defaults = array(
			'limit' => '-1'
		);
		$fields = 'id, user_id, name, description, completed, team, categories, status, date_created, date_due, date_start, date_completed, other_data, other_settings, type, priority';
		//$fields = apply_filters( 'zpm_projects_sql_fields', $fields );
		$table_name = ZPM_PROJECTS_TABLE;

		$query = "SELECT * FROM $table_name ";

		if (!isset($args['archived'])) {
			$args['archived'] = '0';
		}

		if ( !is_null( $args ) ) {

			foreach ( $args as $key => $value ) {
				if ( !strpos( $query, 'WHERE' ) ) {
					$query .= " WHERE $key = $value";
				} else {
					$query .= " AND $key = $value";
				}
			}
		}

		$query .= apply_filters( 'zpm_get_projects_query', '' );

		if ( !is_null( $limit ) ) {
			 $query .= " LIMIT $limit ORDER BY id DESC";
		} else {
			$query .= " ORDER BY id DESC";
		}

		$projects = $wpdb->get_results($query);

		foreach ($projects as $project) {
			$project->status = !empty($project->status) ? maybe_unserialize( $project->status ) : array();
			$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		}

		if ( !is_null( $filters ) ) {
			if ( isset( $filters['category'] ) && $filters['category'] !== '-1' && $filters['category'] !== 'all' ) {
				$projects = Projects::filter_by_category( $projects, $filters['category'] );
			}
		}

		return $projects;
	}

	/**
	* Retrieves all projects, paginated
	* @param int $limit The amount of projects to retrieve
	* @return object
	*/
	public static function get_paginated_projects( $limit = null, $offset = null) {
		// global $wpdb;
		// $table_name = ZPM_PROJECTS_TABLE;
		// $query = "SELECT * FROM $table_name ";
		// $query .= apply_filters( 'zpm_get_projects_query', '' );
		// $query .= " ORDER BY id DESC LIMIT $offset, $limit;";
		// $projects = $wpdb->get_results($query);
		$manager = ZephyrProjectManager();
		$projects = $manager::get_projects();
		$results = [];

		$i = 0;
		$j = 0;
		foreach ($projects as $project) {

			if ($i >= $offset) {

				if ($j < $limit) {

					$project->status = $project->status == "" ? maybe_unserialize( $project->status ) : array();
					$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
					if ( apply_filters( 'zpm_should_show_project', true, $project ) ) {
						$results[] = $project;
						$j++;
					}
				}
			}
			if ( apply_filters( 'zpm_should_show_project', true, $project ) ) {
				$i++;
			}
		}

		return $results;
	}

	public static function filter_by_category( $projects, $cat ) {
		$filtered = array();

		foreach ($projects as $project) {
			$cats = (array) maybe_unserialize( $project->categories );
			if ( in_array($cat, $cats) ) {
				$filtered[] = $project;
			}
		}

		return $filtered;
	}

	public static function get_member_projects( $user_id ) {
		$results = [];
		$projects = Projects::get_projects();
		foreach ($projects as $project) {
			if (Projects::is_project_member($project, $user_id)) {
				$results[] = $project;
			}
		}
		return $results;
	}

	public static function getAssignees( $project ) {
		$assignees = explode(',', $project->assignees);
		$results = [];

		foreach ($assignees as $assignee) {
			$results[] = Members::get_member($assignee);
		}

		return $results;
	}

	public static function get_members( $project_id ) {
		$project = Projects::get_project( $project_id );
		$project_members = is_object( $project ) && maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		return (array) $project_members;
	}

	/**
	* Retrieves all completed projects
	* @return object
	*/
	public static function get_complete_projects() {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT * FROM $table_name WHERE completed = '1' ORDER BY id DESC";
		$projects = $wpdb->get_results($query);

		foreach ($projects as $project) {
			$project->status = $project->status == "" ? maybe_unserialize( $project->status ) : array();
			$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		}

		return $projects;
	}

	/**
	* Retrieves all completed projects
	* @return object
	*/
	public static function get_incomplete_projects() {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT * FROM $table_name WHERE completed = '0' ORDER BY id DESC";
		$projects = $wpdb->get_results($query);

		foreach ($projects as $project) {
			$project->status = $project->status == "" ? maybe_unserialize( $project->status ) : array();
			$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		}

		return $projects;
	}

	/**
	* Retrieves the project data for a single project
	* @param int $project_id The id of the project to retrieve the data for
	* @return object
	*/
	public static function get_project( $project_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT * FROM $table_name WHERE id = $project_id";
		$project = $wpdb->get_row($query);
		if (is_object($project)) {
			$project->status = $project->status !== "" ? maybe_unserialize( $project->status ) : array();
			$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
			$project = apply_filters( 'zpm_filter_project', $project );
		}
		return $project;
	}


	/**
	* Gets the total number of projects
	* @return int $id The project ID
	*/
	public static function delete_project( $id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$tasks_table = ZPM_TASKS_TABLE;
		$project_name = Projects::get_project( $id );
		$project_name = $project_name->name;
		$settings = array( 'id' => $id );
		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		$tasks = Tasks::get_project_tasks( $id );

		foreach ($tasks as $task) {
			$settings = array(
				'id' => $task->id
			);
			$wpdb->delete( $tasks_table, $settings, [ '%d' ] );
		}

		$date_deleted = date('Y-m-d H:i:s');
		$subject_name = $project_name;

		do_action( 'zpm_project_deleted', $id );

		Activity::log_activity( get_current_user_id(), $id, '', $subject_name, 'project', 'project_deleted' );
	}

	public static function update( $id, $args ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $args, $where );
	}

	/**
	* Gets the total number of projects
	* @return int
	*/
	public static function project_count() {
		$manager = ZephyrProjectManager();
		$projects = $manager::get_projects();
		return sizeof( $projects );
	}

	public static function get_total() {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "
        SELECT
            COUNT(*)
        FROM
            $table_name";

        $total = $wpdb->get_var($query);

	}

	/**
	* Retrieves the completed project count
	* @return int
	*/
	public static function completed_project_count() {
		$results = [];
		$manager = ZephyrProjectManager();
		$projects = $manager::get_projects();
		foreach ( $projects as $project ) {
			$status = $project->status;
			if (isset($status['color'])) {
				$status = $status['color'];
			}
			if ( $project->completed == '1' || $status == 'completed' ) {
				$results[] = $project;
			}
		}
		return sizeof( $results );
	}

	/**
	* Gets the percentage of completion of a project
	* @param int $id The ID of the Project to return the percentage for
	* @return int The percent complete without the % symbol
	*/
	public static function percent_complete( $project_id ) {
		$total_tasks = Tasks::get_project_task_count( $project_id );
		$completed_tasks = Tasks::get_project_completed_tasks( $project_id );
		$percent_complete = ($total_tasks !== 0) ? floor($completed_tasks / $total_tasks * 100) : 100;
		return $percent_complete;
	}

	/**
	* Retrieves all projects created by a specific user
	* @param int $user_id The ID of the user to retrieve the data for
	* @return object
	*/
	public static function get_user_projects( $user_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT * FROM $table_name WHERE user_id = '" . $user_id . "'";
		$projects = $wpdb->get_results($query);
		return $projects;
	}

	/**
	* Returns the byline with the project creators name and the date it was craeted
	* Example: Project Name created by Dylan on 24-07-2018 at 22:00
	* @param int $project_id The project ID to create the byline for
	* @return string
	*/
	public static function project_created_by( $project_id ) {
		global $wpdb;

		$table_name = ZPM_PROJECTS_TABLE;

		$query = "SELECT user_id, date_created FROM $table_name WHERE id = $project_id";
		$data = $wpdb->get_row($query);
		$user = get_user_by('ID', $data->user_id);
		$today = new DateTime(date('Y-m-d H:i:s'));
		$created_on = new DateTime($data->date_created);

		if ( is_object( $user ) ) {
			$return = ($today->format('Y-m-d') == $created_on->format('Y-m-d'))
					? sprintf( __( 'Created by %s at %s today', 'zephyr-project-manager' ), $user->display_name, $created_on->format('H:i') )
					: sprintf( __( 'Created by %s on %s at %s', 'zephyr-project-manager' ), $user->display_name, $created_on->format('d M'), $created_on->format('H:i') );
		} else {
			$return = ($today->format('Y-m-d') == $created_on->format('Y-m-d'))
			? sprintf( __( 'Created at %s today', 'zephyr-project-manager' ), $created_on->format('H:i') )
			: sprintf( __( 'Created on %s at %s', 'zephyr-project-manager' ), $created_on->format('d M'), $created_on->format('H:i') );
		}

		return $return;
	}

	/**
	* Generates the HTML for a new project cell
	* @param object $project The Project data to create the new project cell for
	* @return string
	*/
	public static function new_project_cell( $project, $args = [] ) {
		$defaultArgs = [
			'is_dashboard_project' => false
		];
		$args = wp_parse_args( $args, $defaultArgs );
		$base_url = esc_url( admin_url('/admin.php?page=zephyr_project_manager_projects') );
		$color = maybe_unserialize( $project->other_data);
		$color = isset($color['color']) ? $color['color'] : '#f4f4f4';
		$complete = ( ($project->completed == '1') ? 'completed disabled' : '' );
		$categories = maybe_unserialize( $project->categories );
		$team = maybe_unserialize( $project->team );
		$total_tasks = Projects::get_task_count( $project->id );
		$completed_tasks = Projects::get_completed_task_count( $project->id );
		$active_tasks = (int) $total_tasks - (int) $completed_tasks;
		$overdueTasks = sizeof(Projects::getOverdueProjectTasks($project->id));
		$general_settings = Utillities::general_settings();
		$due_date = new DateTime($project->date_due);
		$url = $base_url . '&action=edit_project&project=' . $project->id;
		$url = apply_filters( 'zpm_project_item_url', $url, $project->id );

		if ($due_date->format('Y') == "-0001") {
			$due_date = '';
		} else {
			//$due_date = $due_date->format($general_settings['date_format']);
			$due_date = $due_date->format('d M Y');
		}


		if ( !Projects::has_project_access( $project ) ) {
			echo '';
			return;
		}

		$userId = get_current_user_id();
		$unread = Projects::unreadCommentsCount( $project->id );
		ob_start();
		$stats = apply_filters( 'zpm_project_stats', [], $project );

		?>
		<div class="zpm_project_grid_cell">
			<div class="zpm_project_grid_row zpm_project_item <?php echo $project->type; ?>" data-project-id="<?php echo $project->id; ?>">
				<a href="<?php echo $url; ?>" data-project_id="<?php echo $project->id; ?>" class="zpm_project_title project_name" data-ripple="rgba(0,0,0,0.2)">
					<span class="zpm_project_grid_name">
						<?php if ($project->archived) : ?>
							<i class="zpm-project-title__icon fa fa-archive"></i>
						<?php endif; ?>
						<?php echo $project->name; ?>
						<?php if ( $general_settings['display_project_id'] == '1' ) : ?>
							(#<?php echo Projects::get_unique_id( $project->id ); ?>)
						<?php elseif ( $general_settings['display_database_project_id'] == '1' ) : ?>
							(#<?php echo $project->id; ?>)
						<?php endif; ?>
					</span>
					<!-- Project options button and dropwdown -->
					<span class="zpm_project_grid_options">
						<i class="zpm_project_grid_options_icon dashicons dashicons-menu"></i>
						<div class="zpm_dropdown_menu">
							<ul class="zpm_dropdown_list">
								<?php if (Utillities::canDeleteProject($userId, $project)) : ?>
									<li id="zpm_delete_project"><?php _e( 'Delete Project', 'zephyr-project-manager' ); ?></li>
									<?php if ($project->archived) : ?>
										<li id="zpm-project-action__archive" data-archived="0"><?php _e( 'Unarchive Project', 'zephyr-project-manager' ); ?></li>
									<?php else: ?>
										<li id="zpm-project-action__archive" data-archived="1"><?php _e( 'Archive Project', 'zephyr-project-manager' ); ?></li>
									<?php endif; ?>
								<?php endif; ?>

								<?php if (Utillities::can_create_projects()) : ?>
									<li id="zpm_copy_project"><?php _e( 'Copy Project', 'zephyr-project-manager' ); ?></li>
								<?php endif; ?>
								<li id="zpm_export_project" class="zpm_dropdown_subdropdown"><?php _e( 'Export Project', 'zephyr-project-manager' ); ?>
									<div class="zpm_export_dropdown zpm_submenu_item">
										<ul>
											<li id="zpm_export_project_to_csv" class="zpm_project_option_sub"><?php _e( 'Export to CSV', 'zephyr-project-manager' ); ?></li>
											<li id="zpm_export_project_to_json" class="zpm_project_option_sub"><?php _e( 'Export to JSON', 'zephyr-project-manager' ); ?></li>
										</ul>
									</div>
								</li>
								<?php if (!$args['is_dashboard_project']) : ?>
									<li id="zpm_add_project_to_dashboard"><?php _e( 'Add to Dashboard', 'zephyr-project-manager' ); ?></li>
								<?php else : ?>
									<li id="zpm-remove-from-dashboard"><?php _e( 'Remove from Dashboard', 'zephyr-project-manager' ); ?></li>
								<?php endif; ?>
							</ul>
						</div>
					</span>
				</a>

				<div class="zpm_project_body">
					<span class="zpm_project_description project_description"><?php echo $project->description; ?></span>
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
							<p class="zpm_stat_number"><?php echo $overdueTasks; ?></p>
							<p><?php _e( 'Overdue Tasks', 'zephyr-project-manager' ); ?></p>
						</span>
						<?php foreach ($stats as $stat) : ?>
							<span class="zpm_project_stat">
								<p class="zpm_stat_number"><?php echo $stat['value']; ?></p>
								<p class="zpm-stat-label"><?php echo $stat['label']; ?></p>
							</span>
						<?php endforeach; ?>
						<!-- <span class="zpm_project_stat zpm-project-single__unread <?php echo $unread > 0 ? 'zpm-unread-new' : ''; ?>">
							<p class="zpm_stat_number"><?php echo $unread; ?></p>
							<p><?php _e( 'Unread', 'zephyr-project-manager' ); ?></p>
						</span> -->
					</div>
					<div class="zpm_project_progress_bar_background">
						<div class="zpm_project_progress_bar" data-total_tasks="<?php echo $total_tasks; ?>" data-completed_tasks="<?php echo $completed_tasks; ?>"></div>
					</div>

					<span class="zpm-project-grid__date"><?php echo $due_date; ?></span>

					<?php
					$i = 0;
					if (sizeof((array)$team) !== 0) : ?>
						<div class="zpm_project_grid_member">
							<div class="zpm_project_avatar">
								<?php
								foreach ( (array) $team as $member ) :
									$member = BaseController::get_project_manager_user($member);
									if (!isset($member['name'])) : ?>
										<p class="zpm_friendly_notice"><?php _e( 'There are no members assigned to this project.', 'zephyr-project-manager' ); ?></p>
										<?php continue; ?>
									<?php endif; ?>

									<span class="zpm_avatar_container">
										<span class="zpm_avatar_background"></span>
										<span class="zpm_avatar_image" title="<?php echo $member['name']; ?>" style="background-image: url(<?php echo $member['avatar']; ?>);">
										</span>
									</span>

								<?php
								$i++;
								endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		$html = apply_filters( 'zpm_project_cell_html', $content, $project );
		return $html;
	}

	public static function has_project_access( $project ) {

		if ( !is_object($project) ) {
			return false;
		}

		$manager = ZephyrProjectManager::get_instance();
		$user_roles = Utillities::get_user_roles();
		$userId = get_current_user_id();

		if (current_user_can( 'zpm_all_zephyr_capabilities' )) {
			return true;
		}

		if ( Utillities::is_zephyr_role($userId) ) {
			if ( current_user_can( 'zpm_view_assigned_projects' ) ) {
				if (Projects::is_project_member($project, $userId)) {
					return true;
				}
			}
			if ( !current_user_can( 'zpm_view_projects' ) ) {
				return false;
			}
		}

		$project_members = property_exists($project, 'team') && maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		$members = $manager::get_users();
		$general_settings = Utillities::general_settings();
		return true;
	}

	public static function is_project_member( $project, $user_id ) {
		$project = is_object( $project ) ? $project : Projects::get_project( (int) $project );
		if (!is_object($project)) {
			return;
		}

		$project_members = property_exists($project, 'team') && maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();

		if ( in_array( (int) $user_id, (array) $project_members ) || (int) $user_id == (int) $project->user_id ) {
			return true;
		}

		return false;
	}

	public static function getUserProjects( $userId ) {
		$results = [];
		$projects = Projects::get_projects();

		foreach($projects as $project) {
			if (Projects::is_project_member($project, $userId)) {
				$results[] = $project;
			}
		}

		return $results;
	}

	/**
	* Returns the HTML for a project item
	*/
	public static function frontend_project_item( $project, $theme = 'default' ) {
		ob_start();

		$general_settings = Utillities::general_settings();
		$manager = ZephyrProjectManager();

		$start_date = new DateTime($project->date_start);
		$due_date = new DateTime($project->date_due);
		$date_created = new DateTime($project->date_created);
		$start_date = $start_date->format('Y') !== "-0001" ? date_i18n($general_settings['date_format'], strtotime($project->date_start)) : __( 'None', 'zephyr-project-manager' );
		$due_date = $due_date->format('Y') !== "-0001" ? date_i18n($general_settings['date_format'], strtotime($project->date_due)) : __( 'None', 'zephyr-project-manager' );
		$date_created = $date_created->format('Y') !== "-0001" ? date_i18n($general_settings['date_format'], strtotime($project->date_created)) : __( 'None', 'zephyr-project-manager' );
		$categories = maybe_unserialize($project->categories);
		$category = isset($categories[0]) ? $manager::get_category($categories[0]) : '-1';

		$priority = property_exists( $project, 'priority' ) ? $project->priority : 'priority_none';
		$priority_label = Utillities::get_priority_label( $priority );

		?>
		<li class="zpm-project-item col-md-12" data-project-id="<?php echo $project->id; ?>">
			<a class="zpm-project-item__link" href="?action=project&id=<?php echo $project->id; ?>"></a>
			<a class="zpm-project-item-title" href="?action=project&id=<?php echo $project->id; ?>"><?php echo stripslashes($project->name); ?>
				<?php if ( $general_settings['display_project_id'] == '1' ) : ?>
					(#<?php echo Projects::get_unique_id( $project->id ); ?>)
				<?php elseif ( $general_settings['display_database_project_id'] == '1' ) : ?>
					(#<?php echo $project->id; ?>)
				<?php endif; ?>
			</a>
			<span class="zpm-project-list-details"><?php echo $date_created; ?>
				<span class="zpm-project-item__due_date">
					<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>: <?php echo $due_date; ?>
				</span>
			</span>

			<?php if ( $priority !== "priority_none" && $priority_label !== "" ) : ?>
				<span class="zpm-task-priority-bubble <?php echo $priority; ?>"><?php echo $priority_label; ?></span>
			<?php endif; ?>
			<?php if ($theme == 'ultimate') : ?>
				<?php if (is_null($category)) : ?>
					<span class="project-edge"></span>
				<?php else: ?>
					<span class="project-edge" style="background: <?php echo $category->color; ?>"></span>
				<?php endif; ?>
			<?php endif; ?>

			<p class="zpm-project-list-description"><?php echo stripslashes($project->description); ?>
				<?php if ($project->description == "") : ?>
					<p class="zpm-error-subtle"><?php _e( 'No description', 'zephyr-project-manager' ); ?></p>
				<?php endif; ?>
			</p>

			<div class="zpm-project-item__footer">
				<?php if (is_array($categories)) : ?>
					<?php foreach ($categories as $category) : ?>
						<?php $category = $manager::get_category($category); ?>
						<?php if (!is_null($category)) : ?>
							<span class="zpm-project-footer__category" style="background-color: <?php echo $category->color; ?>"><?php echo $category->name; ?></span>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</li>
		<?php
		return ob_get_clean();
	}

	/**
	* Renders a <select> input field with all the projects
	* @param string $id The ID for the input field
	* @param int $default The ID of the project that should be selected by default
	* @return string
	*/
	public static function project_select( $id = null, $default = null ) {
		//$manager = ZephyrProjectManager::get_instance();
		//$projects = $manager::get_projects();
		$projects = Projects::get_available_projects();
		$html = !is_null($id) ? '<select id="' . $id . '" class="zpm_input">' : '<select class="zpm_input">';
		$html .= '<option value="-1">' . __( 'None', 'zephyr-project-manager' ) . '</option>';

		foreach ($projects as $project) {
			if ( !is_object( $project ) ) {
				continue;
			}
			if ( !is_null($default) && $default == $project->id ) {
				$html .= '<option value="' . $project->id . '" selected>' . $project->name . '</option>';
			} else {
				$html .= '<option value="' . $project->id . '">' . $project->name . '</option>';
			}
		}
		$html .= '</select>';

		if (empty($projects)) {
			$html = '<p class="zpm_error">' . __( 'There are no projects yet.', 'zephyr-project-manager' ) . '</p>';
		}

		echo $html;
	}

	public static function update_project_status( $id, $status, $color ) {
		global $wpdb;

		$table_name = ZPM_PROJECTS_TABLE;

		$data = array(
			'status' => $status,
			'color' => $color
		);

		$settings = array(
			'status' => serialize( $data )
		);

		$where = array(
			'id' => $id
		);

		return $wpdb->update( $table_name, $settings, $where );
	}

	public function update_members($id, $members) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$settings = array(
			'team' => serialize($members)
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	/**
	* Marks a task as complete
	*/
	public static function mark_complete( $id, $complete ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$settings = array(
			'completed' => $complete
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	public static function getOverdueProjectTasks( $projectId ) {
		$args = array(
			'project_id' => $projectId
		);
		$overdueTasks = Tasks::get_overdue_tasks($args);
		return $overdueTasks;
	}

	/**
	* Updates the progress of a project
	*/
	public static function update_progress( $id = null ) {
		$chart_data = array();
		$current_chart_data = get_option('zpm_chart_data');

		if ($id) {
			if ($id == '' || $id == '-1') {
				return;
			}
			$project = Projects::get_project( $id );
			$data = isset($current_chart_data[$project->id]) ? $current_chart_data[$project->id] : array();

			$task_count = Tasks::get_project_task_count($project->id);
			$completed_tasks = Tasks::get_project_completed_tasks($project->id);
			$pending_tasks = $task_count - $completed_tasks;
			$args = array( 'project_id' => $project->id );
			$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));

			$project_data = array(
				'project'			=> $project->id,
				'tasks' 			=> $task_count,
				'completed_tasks' 	=> $completed_tasks,
				'pending_tasks' 	=> $pending_tasks,
				'overdue_tasks' 	=> $overdue_tasks,
				'date'				=> date('d M')
			);

			$added = false;

			foreach ($data as $key => $value) {
				if (!$added) {
					if (isset($data[$key])) {
						if ($data[$key]['date'] == $project_data['date']) {
							$data[$key] = $project_data;
							$added = true;
						}
					}
				}
			}

			if (!$added) {
				array_push($data, $project_data);
			}

			$chart_data[$project->id] = $data;

		} else {
			$all_projects = Projects::get_projects();
			foreach ($all_projects as $project) {
				$data = isset($current_chart_data[$project->id]) ? $current_chart_data[$project->id] : array();

				$task_count = Tasks::get_project_task_count($project->id);
				$completed_tasks = Tasks::get_project_completed_tasks($project->id);
				$pending_tasks = $task_count - $completed_tasks;
				$args = array( 'project_id' => $project->id );
				$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
				$project_data = array(
					'project'			=> $project->id,
					'tasks' 			=> $task_count,
					'completed_tasks' 	=> $completed_tasks,
					'pending_tasks' 	=> $pending_tasks,
					'overdue_tasks' 	=> $overdue_tasks,
					'date'				=> date('d M')
				);

				$added = false;

				foreach ($data as $key => $value) {
					if (!$added) {
						if (isset($data[$key]) && $data[$key]['date'] == $project_data['date']) {
							$data[$key] = $project_data;
							$added = true;
						}
					}
				}

				if (!$added) {
					array_push($data, $project_data);
				}


				$chart_data[$project->id] = $data;
			}
		}

		update_option('zpm_chart_data', $chart_data);
	}

	/**
	* Retrieves all the comments for a project
	* @param int $task_id The ID of the task to retrieve the comments for
	* @return object
	*/
	public static function get_comments( $project_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'project' AND subject_id = '$project_id' ORDER BY date_created DESC";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Returns the data for a specific project comment
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'project' AND id = '$comment_id'";
		$comment = $wpdb->get_row($query);
		return $comment;
	}

	/**
	* Retrieves all the attachments for a single project comment
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment_attachments( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'project' AND parent_id = '$comment_id' ORDER BY date_created DESC";
		$attachments = $wpdb->get_results($query);
		return $attachments;
	}

	/**
	* Gets all the attachments for all projects
	* @return array
	*/
	public static function get_attachments( $project_id = null ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'project'";

		if (!is_null($project_id)) {
			$query .= " AND subject_id = '$project_id'";
		}
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
					'html' => Utillities::attachment_html( $attachment )
				);
			}
		}

		return $attachments_array;
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
		$comment_attachments = Projects::get_comment_attachments($comment->id);

		$new_comment = '';
		$is_mine = $comment->user_id == get_current_user_id() ? true : false;
		$custom_classes = $is_mine ? 'zpm-my-message' : '';

		if (unserialize($comment->type) !== 'attachment') {

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
		}
		return $new_comment;
	}

	public static function unreadCommentsCount( $projectId ) {
		global $zpmMessages;
		$unread = 0;
		$comments = Projects::get_comments( $projectId );
		foreach($comments as $comment) {
			if (!$zpmMessages->isRead($comment->id)) {
				$unread++;
			}
		}
		return $unread;
	}

	public static function file_html( $attachment_id, $comment_id ) {
		$attachment = BaseController::get_attachment( $comment_id );
		$project_id = $attachment->subject_id;
		$attachment_datetime = new DateTime();
		$attachment_date = $attachment_datetime->format('d M Y H:i');
		$attachment_url = wp_get_attachment_url($attachment_id);
		$attachment_type = wp_check_filetype($attachment_url)['ext'];
		$attachment_name = basename(get_attached_file($attachment_id));
		ob_start();
	?>
	<div class="zpm_file_item_container" data-project-id="<?php echo $project_id; ?>">
		<div class="zpm_file_item" data-attachment-id="<?php echo $attachment_id; ?>" data-attachment-url="<?php echo $attachment_url; ?>" data-attachment-name="<?php echo $attachment_name; ?>" data-task-name="None" data-attachment-date="<?php echo $attachment_date; ?>">
			<?php if (wp_attachment_is_image($attachment_id)) : ?>
				<!-- If attachment is an image -->
				<div class="zpm_file_preview" data-zpm-action="show_info">
					<span class="zpm_file_image" style="background-image: url(<?php echo $attachment_url; ?>);"></span>
				</div>
			<?php else: ?>
				<div class="zpm_file_preview" data-zpm-action="show_info">
					<div class="zpm_file_type"><?php echo '.' . $attachment_type; ?></div>
				</div>
			<?php endif; ?>

			<h4 class="zpm_file_name">
				<?php echo $attachment_name; ?>
				<span class="zpm_file_actions zpm-colors__background-primary">
					<span class="zpm_file_action lnr lnr-download" data-zpm-action="download_file"></span>
					<span class="zpm_file_action lnr lnr-question-circle" data-zpm-action="show_info"></span>
					<span class="zpm_file_action lnr lnr-trash" data-zpm-action="remove_file"></span>
				</span>
			</h4>
		</div>
	</div>
	<?php
	return ob_get_clean();
	}

	/**
	* Displays HTML for the New Project Modal
	*/
	public static function project_modal() {
		$manager = ZephyrProjectManager();
		$statuses = Utillities::get_statuses();
		$categories = $manager::get_categories();
		?>
		<div id="zpm_project_modal" class="zpm-modal">
			<div class="zpm_modal_body">
				<h3><?php _e( 'Create a new project', 'zephyr-project-manager' ); ?></h3><span class="zpm_close_modal lnr lnr-cross"></span>
				<input class="zpm_project_name_input zpm_input" name="zpm_project_name" placeholder="<?php _e( 'Add a project name', 'zephyr-project-manager' ); ?>" />

				<textarea id="zpm-new-project-description" class="zpm_input" placeholder="<?php _e( 'Project Description', 'zephyr-project-manager' ); ?>"></textarea>

				<div class="zpm-new-project__field">
					<label class="zpm_label" for="zpm-new-task__status"><?php _e( 'Categories', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-new-project__categories" class="zpm_input zpm-input-chosen" multiple data-placeholder="<?php _e( 'Select Categories', 'zephyr-project-manager' ); ?>">
						<?php foreach ( $categories as $category ) : ?>
					    	<option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
					    <?php endforeach; ?>
					</select>
				</div>

				<div id="zpm-new-project__fields">
					<?php echo apply_filters( 'zpm_new_project_fields', '' ); ?>
				</div>

				<div class="zpm_modal_content">
					<div class="zpm_col_container">

						<?php ob_start(); ?>

						<div class="zpm_modal_item">
							<div class="image zpm_project_selected" data-project-type="list">
							<img class="zpm_selected_image" src="<?php echo ZPM_PLUGIN_URL . "/assets/img/project_list_selected.png"; ?>" />
							<img src="<?php echo ZPM_PLUGIN_URL . "/assets/img/project_list.png"; ?>" />
							</div>
							<h4 class="title"><?php _e( 'List', 'zephyr-project-manager' ); ?></h4>
							<p class="description"><?php _e( 'Organize your work in an itemized list.', 'zephyr-project-manager' ); ?></p>
						</div>

						<?php
							$project_types = ob_get_clean();
							echo apply_filters( 'zpm_project_types', $project_types );
						?>
					</div>
				</div>

				<input id="zpm-project-type" type="hidden" value="list">

				<div class="zpm_modal_buttons">
					<input type="hidden" id="zpm-new-project-priority-value" class="zpm-priority-value" value="priority_none" />
					<button id="zpm-new-project-priority" class="zpm_button zpm-priority-selection" zpm-toggle-dropdown="zpm-new-project-priority-dropdown" data-priority="priority_none"><span class="zpm-priority-name"><?php _e( 'Priority', 'zephyr-project-manager' ); ?>: <?php _e( 'None', 'zephyr-project-manager' ); ?></span>
						<div id="zpm-new-project-priority-dropdown" class="zpm-dropdown zpm-priority-dropdown">


							<div class="zpm-dropdown-item zpm-new-project-priority" data-value="priority_none" data-color="#f9f9f9"><span class="zpm-priority-indicator zpm-color-none"></span><?php _e( 'None', 'zephyr-project-manager' ); ?></div>

							<?php foreach ( $statuses as $slug => $status ) : ?>
								<div class="zpm-dropdown-item zpm-new-project-priority" data-value="<?php echo $slug; ?>" data-color="<?php echo $status['color']; ?>">

									<span class="zpm-priority-indicator <?php echo $slug; ?>" style="background-color: <?php echo $status['color']; ?>"></span>
									<span class="zpm-priority-picker__name"><?php echo $status['name']; ?></span>
								</div>
							<?php endforeach; ?>

		     			 </div>
					</button>
					<button id="zpm_modal_add_project" class="zpm_button"><?php _e( 'Create Project', 'zephyr-project-manager' ); ?></button>

					<?php if (!BaseController::is_pro()) : ?>
						<p class="zpm-pro-upselling"><?php _e( 'Create Kanban-style board projects with the ', 'zephyr-project-manager' ); ?> <a class="zpm-pro-link" href="https://zephyr-one.com/purchase-pro" target="_blank"><?php _e( 'Pro Version', 'zephyr-project-manager' ); ?></a>.</p>
					<?php endif; ?>
				</div>
			</div>
		</div
		<?php
	}

	/**
	* Copy a project
	* @param array $args [
	*	'project_id'   => (int) ID of the project to copy
	*	'project_name' => (string) The new name of the copied project
	*	'copy_options' => (array) Options to copy
	* ]
	*/
	public static function copy_project( $args = null ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$defaults = [
			'project_id' => -1,
			'project_name' => false,
			'copy_options' => array()
		];
		$args = wp_parse_args( $args, $defaults );
		$project = Projects::get_project($args['project_id']);
		$description = in_array('description', $args['copy_options']) ? $project->description : '';
		$date = date('Y-m-d H:i:s');
		$date_start = in_array('start_date', $args['copy_options']) ? $project->date_start : $date;
		$date_due = in_array('due_date', $args['copy_options']) ? $project->date_due : '';

		$settings = array(
			'user_id' 	  	 => wp_get_current_user()->ID,
			'name' 		  	 => $args['project_name'],
			'description' 	 => $description,
			'completed'   	 => $project->completed,
			'categories'	 => $project->categories,
			'date_start'  	 => $date_start,
			'date_due' 	  	 => $date_due,
			'date_created' 	 => $date,
			'other_data'	 => $project->other_data,
			'date_completed' => '',
			'priority'		 => $project->priority,
			'type'			 => $project->type
		);

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;
		$last_project = Projects::get_project($last_id);
		$tasks = Tasks::get_project_tasks($args['project_id']);
		$task_table = ZPM_TASKS_TABLE;

		$i = $j = 0;
		if ((in_array('tasks', $args['copy_options']))) {
			foreach ($tasks as $task) {
				$settings = (array) $task;
				unset($settings['id']);
				$settings['project'] = $last_id;

				if (property_exists($task, 'kanban_col')) {
					$settings['kanban_col'] = $task->kanban_col;
				}

				$wpdb->insert( $task_table, $settings );
				$last_task_id = $wpdb->insert_id;
				$i++;
				if ($settings['completed']) {
					$j++;
				}

				$subtasks = Tasks::get_subtasks($task->id);

				foreach ($subtasks as $subtask) {
					$settings = array(
						'parent_id'		 => $last_task_id,
						'user_id' 		 => $subtask->user_id,
						'assignee' 		 => $subtask->assignee,
						'project' 		 => $last_id,
						'name' 			 => $subtask->name,
						'completed' 	 => $subtask->completed,
						'date_start' 	 => $subtask->date_start,
						'date_due' 		 => $subtask->date_due,
						'date_created' 	 => $subtask->date_created,
						'date_completed' => ''
					);

					$wpdb->insert( $task_table, $settings );
				}
			}
		}

		do_action( 'zpm_copy_project', $project->id, $last_id );

		$last_project->task_count = $i;
		$last_project->completed_tasks = $j;
		return $last_project;
	}

	/**
	* Adds a projoct to the Dashboard projects
	* @param int $project_id The ID of the project to add to the Dashboard
	*/
	public static function add_to_dashboard( $project_id ) {
		$option = maybe_unserialize(get_option('zpm_dashboard_projects', array()));
		if (!in_array($project_id, $option)) {
			$option[] = $project_id;
		}
		update_option('zpm_dashboard_projects', serialize($option));
	}

	public static function removeFromDashboard( $project_id ) {
		$option = maybe_unserialize(get_option('zpm_dashboard_projects', array()));
		if (($key = array_search($project_id, $option)) !== false) {
		    unset($option[$key]);
		}
		update_option('zpm_dashboard_projects', serialize($option));
	}

	/**
	* Gets all the Dashboard projects
	* @return array
	*/
	public static function get_dashboard_projects($object = true) {
		$results = [];
		$ids = maybe_unserialize( get_option('zpm_dashboard_projects', array()) );
		$projects = apply_filters( 'zpm_filter_global_projects', Projects::get_projects() );

		if (!$object) {
			$results = $ids;
		} else {
			foreach ($ids as $id) {
				foreach ($projects as $project) {

					if ($project->id == $id) {
						$results[] = $project;
					}
				}
			}

		}

		return $results;
	}

	/**
	* Removes a project from the Dashboard
	* @param int $project_id The ID of the project to remove from the Dashboard
	*/
	public static function remove_from_dashboard( $project_id ) {
		$dashboard_projects = Projects::get_dashboard_projects(false);
		if (($project_id = array_search($project_id, $dashboard_projects)) !== false) {
		    unset($dashboard_projects[$project_id]);
		}
		update_option('zpm_dashboard_projects', serialize($dashboard_projects));
	}

	/**
	* Sends weekly email updates on project progress
	*/
	public function weekly_updates() {
		$projects = Projects::get_projects();
		//$progress_data = get_option('zpm_chart_data');
		Emails::weekly_updates($projects);
	}

	/**
	* Sends daily notifications via email for due tasks
	*/
	public function task_notifications() {
		$tasks = Tasks::get_week_tasks();
		Emails::task_notifications( $tasks );
	}

	/**
	* Search for projects
	*/
	public static function search( $query ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$result_projects = [];
		$added = [];
		$results = $wpdb->get_results($wpdb->prepare(
		    "SELECT
		        id, name
		    FROM
		        `{$table_name}`
		    WHERE
		        name LIKE %s OR id LIKE %s",
		    '%' . $wpdb->esc_like($query) . '%',
		    '%' . $wpdb->esc_like($query) . '%'
		));

		foreach ($results as $result) {
			$result_projects[] = $result;
			$added[] = $result->id;
		}

		$projects = Projects::get_projects();

		foreach ($projects as $project) {

			if ( !empty( $project->categories ) ) {
				$cats = Projects::extract_categories( $project );
				foreach ($cats as $cat) {
					if ( strpos( strtolower($cat->name), strtolower($query) ) > -1 ) {
						$project->cat_name = $cat->name;
					}
				}
			}
		}

		foreach ($result_projects as $result) {
			if ($result->id == $query) {
				$result->name .= ' (#' . $result->id . ')';
			}
		}

		$result_projects = apply_filters( 'zpm_project_search_results', $result_projects, $projects, $query );

		return $result_projects;
	}

	public static function send_comment( $project_id, $data, $files = null ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');

		$user_id = isset($data['user_id']) ? sanitize_text_field( $data['user_id']) : get_current_user_id();
		$message = isset($data['message']) ? serialize( sanitize_textarea_field($data['message']) ) : '';
		$type = isset($data['type']) ? serialize( $data['type']) : '';
		$parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
		$subject = 'project';

		$settings = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'subject_id' => $project_id,
			'message' => $message,
			'date_created' => $date,
			'type' => $type,
			'parent_id' => $parent_id,
		);

		$wpdb->insert($table_name, $settings);

		// if ($attachments) {
		// 	foreach ($attachments as $attachment) {
		// 		$parent_id = (!$last_comment) ? '' : $last_comment;
		// 		$attachment_type = ($subject == '' && $attachment['attachment_type'] !== '') ? $attachment['attachment_type'] : $subject;
		// 		$subject_id = ($subject_id == '' && $attachment['subject_id'] !== '') ? $attachment['subject_id'] : $subject;
		// 		$settings['user_id'] = $attachment_type;
		// 		$settings['subject'] = $attachment_type;
		// 		$settings['subject_id'] = $subject_id;
		// 		$settings['parent_id'] = $parent_id;
		// 		$settings['type'] = serialize('attachment');
		// 		$settings['message'] = serialize($attachment['attachment_id']);
		// 		$wpdb->insert($table_name, $settings);
		// 	}
		// }
		return $wpdb->insert_id;
	}

	public static function view_project_modal( $project_id ) {
		include (ZPM_PLUGIN_PATH . '/templates/parts/project-view.php');
	}

	public static function view_project_container( $project_id = null ) {
		?>
		<div id="zpm_quickview_modal" class="zpm-modal" data-project-id="<?php echo !is_null($project_id) ? $project_id : ''; ?>">
		</div>
		<?php
	}

	public static function get_other_data( $project_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT other_data FROM $table_name WHERE id = '" . $project_id . "' ORDER BY id DESC";
		$row = $wpdb->get_row($query);
		return (array) maybe_unserialize( $row->other_data );
	}

	public static function get_settings( $project_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT other_settings FROM $table_name WHERE id = '" . $project_id . "'";
		$row = $wpdb->get_row($query);
		$settings = maybe_unserialize( $row->other_settings );
		$defaults = array(
			'weekly_update_email' 		   => '0',
			'task_completed_email' 		   => '1',
			'new_subtask_email' 		   => '0',
			'task_assignee_comments_email' => '0',
			'task_comments_email' 		   => '0',
			'project_comments_email' 	   => '0',
			'new_task_email' 			   => '1',
			'task_order'				   => [],
			'additional_emails'			   => []
		);
		return wp_parse_args( $settings, $defaults );
	}

	public static function check_setting( $project_id, $setting ) {
		$settings = Projects::get_settings( $project_id );

		if ( !isset( $settings[$setting] ) || $project_id == '-1' ) {
			return false;
		}

		if ( $settings[$setting] !== '0' ) {
			return true;
		} else {
			return false;
		}
	}

	public static function update_settings( $project_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		if (!isset($_POST['zpm-update-project-settings'])) {
			return;
		}

		$settings = Projects::get_settings( $project_id );

		if ( isset( $_POST['zpm-project-settings__new-task-email'] ) ) {
			$settings['new_task_email'] = '1';
		} else {
			$settings['new_task_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__task-completed-email'] ) ) {
			$settings['task_completed_email'] = '1';
		} else {
			$settings['task_completed_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__subtasks-email'] ) ) {
			$settings['new_subtask_email'] = '1';
		} else {
			$settings['new_subtask_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__weekly-update-email'] ) ) {
			$settings['weekly_update_email'] = '1';
		} else {
			$settings['weekly_update_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__task-assignee-comments-emails'] ) ) {
			$settings['task_assignee_comments_email'] = '1';
		} else {
			$settings['task_assignee_comments_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__task-comments-emails'] ) ) {
			$settings['task_comments_email'] = '1';
		} else {
			$settings['task_comments_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__project-comments-email'] ) ) {
			$settings['project_comments_email'] = '1';
		} else {
			$settings['project_comments_email'] = '0';
		}

		if ( isset( $_POST['zpm-project-settings__additional-emails'] ) ) {
			$_POST['zpm-project-settings__additional-emails'] = str_replace(' ', '', $_POST['zpm-project-settings__additional-emails'] );
			$settings['additional_emails'] = explode(',', $_POST['zpm-project-settings__additional-emails']);
		}

		$settings = apply_filters( 'zpm_updated_project_user_settings', $settings, $project_id );
		Utillities::update_user_project_settings( get_current_user_id(), $project_id, $settings );



		$args = array(
			'other_settings' => serialize( $settings )
		);

		$where = array(
			'id' => $project_id
		);

		$wpdb->update( $table_name, $args, $where );
	}

	public static function updateSetting($projectId, $key, $value) {
		$settings = Projects::get_settings( $projectId );
		$settings[$key] = $value;
		$data = [
			'other_settings' => serialize($settings)
		];
		Projects::update($projectId, $data);
	}

	public static function getSetting($projectId, $key) {
		$settings = Projects::get_settings( $projectId );
		if (isset($settings[$key])) {
			return $settings[$key];
		} else {
			return null;
		}
	}

	public static function get_unique_id( $project_id ) {

		$other_data = Projects::get_other_data( $project_id );

		if ( isset( $other_data['unique_id'] ) ) {
			return $other_data['unique_id'];
		} else {
			return Projects::update_unique_id( $project_id );
		}
	}

	public static function update_unique_id( $project_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$other_data = Projects::get_other_data( $project_id );
		$other_data['unique_id'] = Utillities::generate_random_string(8);

		$args = array(
			'other_data' => serialize( $other_data )
		);

		$where = array(
			'id' => $project_id
		);

		$wpdb->update( $table_name, $args, $where );
		return $other_data['unique_id'];
	}

	public static function extract_categories( $project ) {
		$results = array();
		$manager = ZephyrProjectManager();
		if ( !is_object( $project ) ) {
			return $results;
		}

		$categories = maybe_unserialize( $project->categories ) ? (array) maybe_unserialize( $project->categories ) : array();

		foreach ( $categories as $category ) {
			if ( !empty( $category ) ) {
				$results[] = $manager::get_category( $category );
			}
		}
		return $results;
	}

	public static function get_status( $project ) {
		$defaults = array(
			'status' => __( 'None', 'zephyr-project-manager' ),
			'color' => __( 'zpm-default-color', 'zephyr-project-manager' )
		);
		$status = maybe_unserialize( $project->status );
		return (array) wp_parse_args( $status, $defaults );
	}

	public static function category_projects( $category_id, $all_fields = false ) {
		$results = [];
		$manager = ZephyrProjectManager::get_instance();
		$projects = $manager::get_projects();

		foreach ($projects as $project) {
			if ( Projects::has_category( $project, $category_id ) || $category_id == '' || $category_id == '-1' ) {
				$results[] = $project;
			}
		}

		return $results;
	}

	public static function has_category( $project, $category_id ) {
		$categories = (array) maybe_unserialize( $project->categories );
		if ( in_array( $category_id, $categories ) ) {
			return true;
		}
		return false;
	}

	public static function get_available_projects() {
		$results = [];
		$manager = ZephyrProjectManager();
		$projects = $manager::get_projects();

		foreach ( $projects as $project ) {
			if ( Projects::has_project_access( $project ) && apply_filters( 'zpm_should_show_project', true, $project ) ) {
				$results[] = $project;
			}
		}
		//$results = apply_filters( 'zpm_project_grid_projects', $results );
		return $results;
	}

	public static function get_total_pages() {
		$settings = Utillities::general_settings();
		$projects_per_page = $settings['projects_per_page'];
		$projects = Projects::get_available_projects();
		$count = sizeof( $projects );
		return ceil( $count / $projects_per_page );
	}

	/**
	* Gets the project task count
	*/
	public static function get_task_count( $id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT COUNT(*) FROM $table_name WHERE project = '$id' AND parent_id = '-1'";
		$count = $wpdb->get_var($query);
		return $count;
	}

	/**
	* Gets the project completed task count
	*/
	public static function get_completed_task_count( $id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT COUNT(*) FROM $table_name WHERE project = '$id' AND completed = '1' AND parent_id = '-1'";
		$count = $wpdb->get_var($query);
		return $count;
	}

	/**
	* Gets the project actove task count
	*/
	public static function get_active_task_count( $id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT COUNT(*) FROM $table_name WHERE project = '$id' AND completed = '0'";
		$count = $wpdb->get_var($query);
		return $count;
	}

	// This hook is run to filter projects by categories if they are currently viewing by category
	public function filter_projects( $projects ) {
		$results = [];
		$category = isset( $_GET['category_id'] ) ? $_GET['category_id'] : '';
		foreach ($projects as $project) {
			// if ( empty( $category ) || $category == '-1' ) {
			// 	$results[] = $project;
			// 	continue;
			// }
			if ( Projects::has_category( $project, $category ) ) {
				$results[] = $project;
			}
		}
		return $results;
	}

	public function should_show_project( $show, $project ) {
		$category_id = isset( $_REQUEST['category_id'] ) ? $_REQUEST['category_id'] : '';

		$showComplete = isset($_GET['completed']) ? $_GET['completed'] : '';

		if (!empty($showComplete)) {
			if ($showComplete == 'true') {
				$status = $project->status;
				$statusCompleted = isset($status['color']) && $status['color'] == 'completed' ? true : false;
				if ($project->completed == '1' || $statusCompleted) {
					return true;
				} else {
					return false;
				}
			}
		}

		if (isset($_GET['user'])) {
			if (Projects::is_project_member($project, $_GET['user'])) {
				return true;
			} else {
				return false;
			}
		}

		if ( Projects::has_category( $project, $category_id ) || ($category_id == '-1' || empty($category_id)) ) {
			return true;
		}
		return false;
	}

	// Filter single project data
	public function filter_project( $project ) {
		$start_datetime = new DateTime($project->date_start);
		$due_datetime = new DateTime($project->date_start);
		$start_date = ($start_datetime->format('Y-m-d') !== '-0001-11-30' && $project->date_start !== '0000-00-00 00:00:00') ? date_i18n($this->settings['date_format'], strtotime($project->date_start)) : __( 'Not set', 'zephyr-project-manager' );
		$due_date = ($due_datetime->format('Y-m-d') !== '-0001-11-30' && $project->date_due !== '0000-00-00 00:00:00') ? date_i18n($this->settings['date_format'], strtotime($project->date_due)) : __( 'Not set', 'zephyr-project-manager' );
		$priority = Utillities::get_status($project->priority);
		$project->formatted_start_date = $start_date;
		$project->formatted_due_date = $due_date;
		$project->formatted_priority = $priority;
		return $project;
	}

	public static function getTaskOrder($projectId) {
		$order = Projects::getSetting($projectId, 'task_order');
		if (is_null($order)) {
			$order = [];
		}
		return $order;
	}

	public static function getOrderedTasks($projectId, $tasks) {
		$orderIds = Projects::getTaskOrder($projectId);
		usort($tasks, function ($a, $b) use ($orderIds) {
		    $pos_a = array_search($a->id, $orderIds);
		    $pos_b = array_search($b->id, $orderIds);
		    return $pos_a - $pos_b;
		});
		return $tasks;
	}

	public static function getAdditionalEmails($projectId) {
		$emails = Projects::getSetting($projectId, 'additional_emails');
		if (is_null($emails)) {
			$emails = [];
		}
		return $emails;
	}

	public static function loadFromCSV( $file ) {
		$projectArray = array();

		if (($handle = fopen($file, "r")) !== FALSE) {
			$row = 0;

		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	if ($row > 0) {
		    		$num = count($data);
			        $project = array(
			        	'id' 			 => $data[0],
			        	'user_id' 		 => $data[1],
			        	'name' 			 => $data[2],
			        	'description' 	 => $data[3],
			        	'completed' 	 => $data[4],
			        	'team' 			 => $data[5],
			        	'categories' 	 => $data[6],
			        	'date_created' 	 => $data[7],
			        	'date_due' 		 => $data[8],
			        	'date_start' 	 => $data[9],
			        	'date_completed' => $data[10],
			        	'other_data' 	 => $data[11]
			        );
			        // $task['date_start'] = date('Y-m-d', $task['date_start']);
			        // $task['date_due'] = date('Y-m-d', $task['date_due']);
			        // $task['date_completed'] = date('Y-m-d', $task['date_completed']);

			        // if (!Tasks::task_exists($data[0])) {
			        // 	$wpdb->insert( $table_name, $task );
			        // 	$task = Tasks::get_task($wpdb->insert_id);
			        // 	if ($row > 1) {
			        // 		$html .= Tasks::new_task_row($task);
			        // 	}
			        // } else {
			        // 	$task['already_uploaded'] = true;
			        // }

			        // $row++;

			        $projectArray[] = $project;
		    	}

		        $row++;
		    }
		    fclose($handle);
		}
		return $projectArray;
	}

	public static function loadFromJSON( $file ) {
		$json = file_get_contents($file);
		$jsonResult = json_decode($json);

		$projects = array();

		if (!is_array($jsonResult)) {
			$jsonArray[] = $jsonResult;
		} else {
			$jsonArray = $jsonResult;
		}

		foreach ($jsonArray as $project) {
			$project = array(
	        	'id' 			 => $project->id,
	        	'parent_id' 	 => $project->parent_id,
	        	'user_id' 		 => $project->user_id,
	        	'name' 			 => $project->name,
	        	'description' 	 => $project->description,
	        	'categories' 	 => $project->categories,
	        	'completed' 	 => $project->completed,
	        	'date_created' 	 => $project->date_created,
	        	'date_start' 	 => $project->date_start,
	        	'date_due' 		 => $project->date_due,
	        	'date_completed' => $project->date_completed,
	        	'team' 			 => $project->team,
	        	'custom_fields'  => $project->custom_fields
	        );

	        $projects[] = $project;
		}
		return $projects;
	}

	public static function getStatus( $project ) {
		$status = $project->status;

		if (isset($status['color'])) {
			return $status['color'];
		}

		return '';
	}
}
