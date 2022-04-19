<?php
	/**
	* Dashboard Page
	* Allows users to view project information and upcoming tasks at a glance
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\ZephyrProjectManager;

	$manager = ZephyrProjectManager::get_instance();
	$dashboard_projects = Projects::get_dashboard_projects();
	$notice_version = '1.5';
	$user_id = get_current_user_id();
	$settings = Utillities::get_user_settings( $user_id );
	$general_settings = Utillities::general_settings();
	$project_count = Projects::project_count();
	$completed_projects = Projects::completed_project_count();
	$active_projects = $project_count - $completed_projects;

	$task_count = Tasks::get_task_count();
	$completed_tasks = sizeof(Tasks::get_completed_tasks('1'));
	$active_tasks = $task_count - $completed_tasks;
	$args = array(
		'limit' => 5,
		'assignee' => get_current_user_id()
	);
	$my_tasks = Tasks::get_tasks($args);
	$week_tasks = Tasks::get_week_tasks(get_current_user_id());
	$args = array(
		'assignee' => get_current_user_id()
	);
	$overdue_tasks = Tasks::get_overdue_tasks($args);

	$user_tasks = Tasks::get_user_tasks( $user_id );
	$user_completed_tasks = Tasks::get_user_completed_tasks( $user_id );
	$user_pending_tasks = Tasks::get_user_completed_tasks( $user_id , '0');
	$percent_complete = (sizeof($user_tasks) !== 0) ? ( sizeof( $user_completed_tasks ) / sizeof( $user_tasks ) ) * 100 : '0';
	$daily_tasks = Tasks::get_daily_tasks( $user_id );
	$tasksUrl = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks'));
	$projectsUrl = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects'));
?>

<div class="zpm_settings_wrap">

	<?php if ( !apply_filters( 'zpm_hide_zephyr', false ) ) : ?>
		<?php if (!get_option('zpm_first_time')) : ?>
			<?php include('welcome.php'); ?>
		<?php elseif ($this->is_pro() && !get_option('zpm_welcome_pro')) : ?>
			<?php include( ZEPHYR_PRO_PLUGIN_PATH . 'views/welcome.php'); ?>
		<?php else: ?>
			<?php $this->get_header(); ?>
			<div id="zpm_container">
				<h1 class="zpm_page_title"><?php _e( 'Dashboard', 'zephyr-project-manager' ); ?></h1>
				<div class="zpm_panel_container">

					<div class="zpm-grid-container">
						<div class="zpm-grid-row zpm-grid-row-12">
							<div class="zpm-grid-item zpm-grid-item-3">
								<div class="zpm-material-card zpm-material-card-colored zpm-card-color-blue">
									<h4 class="zpm-card-header"><?php _e( 'Projects Overview', 'zephyr-project-manager' ); ?></h4>
										<div class="zpm-stat-list-item">
											<span id="zpm_projects_created_count" data-zpm-stat="projects_count" class="zpm-stat-value"><?php echo $project_count; ?></span>
											<?php _e( 'Projects', 'zephyr-project-manager' ); ?>
										</div>
										<div class="zpm-stat-list-item">
											<span data-zpm-stat="projects_completed" class="zpm-stat-value"><?php echo $completed_projects; ?></span>
											<?php _e( 'Completed Projects', 'zephyr-project-manager' ); ?>
										</div>
										<div class="zpm-stat-list-item">
											<span id="zpm_projects_active_count" data-zpm-stat="projects_active" class="zpm-stat-value"><?php echo $active_projects; ?></span>
											<?php _e( 'Active Projects', 'zephyr-project-manager' ); ?>
										</div>
								</div>
							</div>
							<div class="zpm-grid-item zpm-grid-item-3">
								<div class="zpm-material-card zpm-material-card-colored zpm-card-color-purple">
									<h4 class="zpm-card-header"><?php _e( 'Tasks Overview', 'zephyr-project-manager' ); ?></h4>
									<div class="zpm-stat-list-item">
										<span id="zpm_stat_tasks_total" class="zpm-stat-value"><?php echo $task_count; ?></span>
										<?php _e( 'Tasks Total', 'zephyr-project-manager' ); ?>
									</div>
									<div class="zpm-stat-list-item">
										<span class="zpm-stat-value"><?php echo $completed_tasks; ?></span>
										<?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?>
									</div>
									<div class="zpm-stat-list-item">
										<span id="zpm_stat_tasks_active" class="zpm-stat-value"><?php echo $active_tasks; ?></span>
										<?php _e( 'Active Tasks', 'zephyr-project-manager' ); ?>
									</div>
								</div>
							</div>
							<div class="zpm-grid-item zpm-grid-item-3">
								<div class="zpm-material-card zpm-material-card-colored zpm-card-color-red">
									<h4 class="zpm-card-header"><?php _e( 'Tasks Due This Week', 'zephyr-project-manager' ); ?></h4>
									<ul class="zpm-tasks-due-list">
									<?php foreach($week_tasks as $task) : ?>
										<?php $due_date = date('D', strtotime($task->date_due)); ?>
										<li class="zpm-tasks-due-item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>" class="zpm_link"><?php echo stripslashes($task->name); ?></a><span class="zpm_widget_date zpm_date_pending"><?php echo $due_date; ?></span></li>
									<?php endforeach; ?>
									</ul>
									
									<?php if (empty($week_tasks)) : ?>
										<p><?php _e( 'You have no tasks due this week', 'zephyr-project-manager' ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

					<div class="zpm-panel zpm-panel-8">

						<h4 class="zpm_panel_title"><?php _e( 'Overview', 'zephyr-project-manager' ); ?></h4>

						<div id="zpm-project-stat-overview">
							<span class="zpm-project-stat">
								<span id="zpm_project_stats_total" class="zpm-project-stat-value"><?php echo $project_count; ?></span>
								<span class="zpm-project-stat-label"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $projectsUrl; ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
							<span class="zpm-project-stat">
								<span class="zpm-project-stat-value good"><?php echo $completed_projects; ?></span>
								<span class="zpm-project-stat-label"><?php _e( 'Completed Projects', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $projectsUrl; ?>&completed=true"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
							<span class="zpm-project-stat">
								<span class="zpm-project-stat-value good"><?php echo $completed_tasks; ?></span>
								<span class="zpm-project-stat-label"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $tasksUrl; ?>&completed=true"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
						</div>
					</div>

					<div class="zpm-panel zpm-panel-8">

						<h4 class="zpm_panel_title"><?php _e( 'User Overview', 'zephyr-project-manager' ); ?></h4>

						<div id="zpm-project-stat-overview" class="zpm-user-overview-stats">
							<span class="zpm-project-stat">
								<span id="zpm_project_stats_total" class="zpm-project-stat-value zpm-user-project-count-value">-</span>
								<span class="zpm-project-stat-label"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $projectsUrl; ?>&user=<?php echo $user_id; ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
							<span class="zpm-project-stat">
								<span id="zpm_project_stats_total" class="zpm-project-stat-value"><?php echo sizeof( $user_tasks ); ?></span>
								<span class="zpm-project-stat-label"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $tasksUrl; ?>&user=<?php echo $user_id; ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
							<span class="zpm-project-stat">
								<span class="zpm-project-stat-value medium"><?php echo sizeof( $user_pending_tasks ); ?></span>
								<span class="zpm-project-stat-label"><?php _e( 'Pending Tasks', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $tasksUrl; ?>&user=<?php echo $user_id; ?>&status=pending"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
							<span class="zpm-project-stat">
								<span class="zpm-project-stat-value good"><?php echo sizeof( $user_completed_tasks ); ?></span>
								<span class="zpm-project-stat-label"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></span>
								<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo $tasksUrl; ?>&user=<?php echo $user_id; ?>&completed=true"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
							</span>
						</div>
					</div>

					<?php if ( sizeof( $daily_tasks ) > 0 ) : ?>
						<div class="zpm-panel zpm-panel-8">
							<h4 class="zpm_panel_title"><?php _e( 'Daily Tasks', 'zephyr-project-manager' ); ?></h4>
							<div id="zpm-dashboard__daily-tasks">
								<?php foreach ($daily_tasks as $task) : ?>
									<?php
										$url = Tasks::task_url($task->id);
										$today = new DateTime();
										$due_datetime = new DateTime($task->date_due);
										$due_today = ($today->format('Y-m-d') == $due_datetime->format('Y-m-d')) ? true : false;
										$due_date = (!$due_today) ? $due_datetime->format($general_settings['date_format']) : _e( 'Today', 'zephyr-project-manager' );
												$due_date = ($task->date_due !== '0000-00-00 00:00:00') ? date_i18n($general_settings['date_format'], strtotime($task->date_due)) : '';
										$priority = property_exists( $task, 'priority' ) ? $task->priority : 'priority_none';
										$status = Utillities::get_status( $task->priority );
										$assignees = Tasks::get_assignees( $task, true );
									?>
									<a class="zpm-daily-tasks__list-item zpm-block-url" href="<?php echo $url; ?>">
										<label for="zpm_task_id_<?php echo $task->id; ?>" class="zpm-material-checkbox">
											<input type="checkbox" id="zpm_task_id_<?php echo $task->id; ?>" name="zpm_task_id_<?php echo $task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $task->completed ? 'checked' : ''; ?> data-task-id="<?php echo $task->id; ?>">
											<span class="zpm-material-checkbox-label"></span>
										</label>
										<span class="zpm-daily-task__description">
											<?php echo $task->name; ?>
											<?php echo !empty($task->description) ? ' - ' . $task->description : ''; ?>
										</span>

										<div class="zpm-daily-task__details">
											<?php if ( !empty( $assignees ) ) : ?>
												<?php foreach ($assignees as $assignee) : ?>
													<span title="<?php echo $assignee['name']; ?>" class='zpm_task_assignee' style='background-image: url("<?php echo $assignee['avatar'] ?>"); <?php echo $assignee['avatar'] == '' ? 'display: none;' : ''; ?>' title="<?php echo $assignee['name'] ?>"></span>
												<?php endforeach; ?>
											<?php endif; ?>
											<span class="zpm_task_due_date"><?php echo $due_date; ?></span>

											<?php if (!empty($priority) && $priority !== 'priority_none') : ?>
												<span class="zpm-daily-task__priority" style="background-color: <?php echo $status['color']; ?>;"><?php echo $status['name']; ?></span>
											<?php endif; ?>
										</div>

									</a>

								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php do_action('zpm_dashboard_panels'); ?>

					<?php
						$i = 0;
						foreach ($dashboard_projects as $project) :
							
							if (!is_object($project)) {
								continue;
							}

							?>
							<div class="<?php echo sizeof($dashboard_projects) > 1 ? 'zpm_panel_6' : 'zpm_panel_12'; ?> zpm_dashboard_project_container">
								<div class="zpm_panel zpm_chart_panel zpm_dashboard_project" data-project-id="<?php echo $project->id; ?>">
									<?php $chart_data = get_option('zpm_chart_data', array()); ?>
									<h4 class="zpm_panel_heading"><?php echo $project->name; ?></h4>
									<span class="zpm_remove_project_from_dashboard lnr lnr-cross-circle"></span>
									<canvas id="zpm_line_chart" class="zpm-dashboard-project-chart" width="600" height="400" data-project-id="<?php echo $project->id; ?>" data-chart-data='<?php echo json_encode($chart_data[$project->id]); ?>'></canvas>

								</div>
							</div>
							<?php
							$i++;
						endforeach;

						if ( empty( $dashboard_projects ) || $i == 0 ) {
							$project_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')); ?>
								<div class="zpm_no_results_message">
								<?php printf( __( 'Welcome to the Dashboard. To add projects to the dashboard and keep track of important projects, navigate to the %s Projects %s page and click on the options button for the project, then select the option %s Add to Dashboard %s.', 'zephyr-project-manager' ), '<a href="' . $project_url . '" class="zpm_link">', '</a>', '<i>', '</i>' ); ?>
								</div>
							<?php
						}
					?>

					<!-- Display Patreon Notice -->
					<!-- <?php if ( !Utillities::notice_is_dismissed( 'zpm-patreon-notice' ) ) : ?>
						<div id="zpm-whats-new" class="zpm-panel zpm-panel-12" data-notice="'zpm-patreon-notice'">
							<h4 class="zpm_panel_title"><?php _e( 'Support me on Patreon', 'zephyr-project-manager' ); ?></h4>
							<p><?php _e( 'If you like the plugin and what I do and would like to help me improve the plugin more, please consider supporting me on Patreon. This would help a lot in being able to work on the plugin full-time and focus more on it to make it better and add new features. Thank you so much.', 'zephyr-project-manager' ); ?></p>
							<div class="zpm-notice-buttons">
								
								<button class="zpm-dismiss-notice-button zpm_button" data-notice-version="zpm-patreon-notice"><?php _e( 'Dismiss Notice', 'zephyr-project-manager' ); ?></button>
								<a href="https://www.patreon.com/dylanjkotze" target="_blank" class="zpm-patreon-button zpm_button"><?php _e( 'Support me on Patreon', 'zephyr-project-manager' ); ?></a>
							</div>
						</div>
					<?php endif; ?> -->

				</div>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<?php do_action( 'zpm_dashboard_content', '' ); ?>
	<?php endif; ?>
</div>
<?php $this->get_footer(); ?>