<?php
	/**
	* The modal that is used to display a tasks details
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Base\BaseController;

	$task = Tasks::get_task($task_id);
	$general_settings = Utillities::general_settings();
	$users = get_users();
	$user_id = wp_get_current_user()->ID;
	$tasks_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks'));
	$created_by = Tasks::task_created_by($task_id);
	$subtasks = Tasks::get_subtasks($task_id);
	$my_followed_tasks = unserialize(get_option( 'zpm_followed_tasks_' . $user_id, false ));
	$is_following = in_array($task_id, (array) $my_followed_tasks) ? true : false;
	$followers = array();
	$due_datetime = new DateTime($task->date_due);
	$start_datetime = new DateTime($task->date_start);
	$priority = property_exists( $task, 'priority' ) ? $task->priority : 'priority_none';
	$priority_label = Utillities::get_priority_label( $priority );
	$status = Utillities::get_status($priority);

	$taskStatus = property_exists( $task, 'status' ) ? $task->status : '';
	$taskStatusData = Utillities::get_status($taskStatus);

	if ($start_datetime->format('Y') == "-0001") {
		$start_date = __( 'None', 'zephyr-project-manager' );
	} else {
		//$start_date = $start_date->format($general_settings['date_format']);
		$start_date = date_i18n($general_settings['date_format'], strtotime($task->date_start));
	}

	if ($due_datetime->format('Y') == "-0001") {
		$due_date = __( 'None', 'zephyr-project-manager' );
	} else {
		//$due_date = $due_date->format($general_settings['date_format']);
		$due_date = date_i18n($general_settings['date_format'], strtotime($task->date_due));
	}

	$hours_minutes = $due_datetime->format('H:i');
	if ($hours_minutes !== '00:00') {
		$due_date .= ' ' . $hours_minutes;
	}

	$hours_minutes = $start_datetime->format('H:i');
	if ($hours_minutes !== '00:00') {
		$start_date .= ' ' . $hours_minutes;
	}

	$team = $task->team !== "" ? Members::get_team($task->team) : "";
	$project = Projects::get_project( $task->project );

	foreach ($users as $user) {
		$user_followed_tasks = unserialize(get_option( 'zpm_followed_tasks_' . $user->data->ID, false ));
		if (in_array($task_id, (array) $user_followed_tasks)) {
			$follower = BaseController::get_project_manager_user($user->data->ID);
			$followers[] = $follower;
		}
	}
	$type = Tasks::get_type( $task );
	$assignees = Tasks::get_assignees( $task, true );
	$days = Tasks::get_days( $task );

	$recurrence_label = Tasks::recurrence_string( $task );
?>

<input type="hidden" id="zpm_task_view_id" value="<?php echo $task->id; ?>"/>
<div class="zpm_modal_body">
	<div class="zpm_modal_top">
		
		<?php if (apply_filters( 'zpm_can_complete_task', true, $task )) : ?>
			<label for="zpm_task_id_<?php echo $task->id; ?>" class="zpm-material-checkbox">
				<input type="checkbox" id="zpm_task_id_<?php echo $task->id; ?>" name="zpm_task_id_<?php echo $task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $task->completed == '1' ? 'checked' : ''; ?> data-task-id="<?php echo $task->id; ?>">
				<span class="zpm-material-checkbox-label"></span>
			</label>
		<?php endif; ?>

		<h3 class="zpm_modal_task_name">
			<?php echo stripslashes($task->name); ?>
			<?php if ($general_settings['display_task_id']) : ?>
				<?php echo '(#' . $task->id . ')'; ?>
			<?php endif; ?>
		</h3>

		<?php if ( $priority !== "priority_none" && $priority_label !== "" ) : ?>
			<span class="zpm-task-priority-bubble <?php echo $priority; ?>" style="background: <?php echo $status['color']; ?>; color: <?php echo $status['color'] !== '' ? '#fff' : ''; ?>"><?php echo $status['name']; ?></span>
		<?php endif; ?>

		<?php if ($type == 'daily') : ?>
			<span class="zpm-task__type-label"><?php _e( 'Daily', 'zephyr-project-manager' ); ?></span>
		<?php endif; ?>

	</div>
	<small class="zpm_title_information"><?php echo $created_by; ?></small>
	<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown">
		<i class="dashicons dashicons-menu"></i>
		<div class="zpm_modal_dropdown" id="zpm_view_task_dropdown">
			<ul class="zpm_modal_list">
				<li id="zpm_delete_task"><?php _e( 'Delete Task', 'zephyr-project-manager' ); ?></li>
				<li id="zpm_copy_task"><?php _e( 'Copy Task', 'zephyr-project-manager' ); ?></li>
				<li id="zpm_export_task"><?php _e( 'Export Task', 'zephyr-project-manager' ); ?> 
					<div class="zpm_export_dropdown">
						<ul>
							<li id="zpm_export_task_to_csv"><?php _e( 'Export to CSV', 'zephyr-project-manager' ); ?></li>
							<li id="zpm_export_task_to_json"><?php _e( 'Export to JSON', 'zephyr-project-manager' ); ?></li>
						</ul>
					</div>
				</li>
				<li id="zpm_convert_task"><?php _e( 'Convert to Project', 'zephyr-project-manager' ); ?></li>
				<li id="zpm_print_task"><?php _e( 'Print', 'zephyr-project-manager' ); ?></li>
			</ul>
		</div>
	</span>

	<nav class="zpm_nav">
		<ul class="zpm_nav_list">
			<li class="zpm_nav_item zpm_nav_item_selected" data-zpm-tab="task-overview"><?php _e( 'Overview', 'zephyr-project-manager' ); ?></li>
			<li class="zpm_nav_item" data-zpm-tab="task-discussion"><?php _e( 'Discussion', 'zephyr-project-manager' ); ?></li>
		</ul>
	</nav>

	<div class="zpm_modal_content">
		<div class="zpm_tab_pane zpm_tab_active" data-zpm-tab="task-overview">

			<span id="zpm_task_dates">
				<span id="zpm_task_start_date">
					<label class="zpm_label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?>:</label>
					<span class="zpm_task_due_date"><?php echo $start_date; ?></span>
				</span>
				<span id="zpm_task_due_date">
					<label class="zpm_label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?>:</label>
					<span class="zpm_task_due_date"><?php echo $due_date; ?></span>
				</span>
			</span>

			<span id="zpm_task_modal_assignee">
				<label class="zpm_label"><?php _e( 'Assigned To', 'zephyr-project-manager' ); ?>:</label>
				<?php if ( !empty( $assignees ) ) : ?>
					<?php foreach ($assignees as $assignee) : ?>
						<span class="zpm-task-modal__assignee">
							<span class="zpm_task_user_avatar" style="background-image: url(<?php echo $assignee['avatar']; ?>);">
							</span>
							<span class="zpm_task_username"><?php echo $assignee['name']; ?></span>
						</span>
					<?php endforeach; ?>
					
				<?php else: ?>
					<p class="zpm-no-result-error"><?php _e( 'Not assigned to any members', 'zephyr-project-manager' ); ?></p>
				<?php endif; ?>
			</span>

			<span id="zpm_task_modal_task_assignee">
				<label class="zpm_label"><?php _e( 'Assigned Team', 'zephyr-project-manager' ); ?>:</label>

				<?php if ($team !== "" && !is_null($team)) : ?>
					<span class="zpm-task-view-team-name"><?php echo $team['name']; ?></span>
				<?php else: ?>
					<p class="zpm-no-result-error"><?php _e( 'Not assigned to any teams', 'zephyr-project-manager' ); ?></p>
				<?php endif; ?>

			</span>

			<label class="zpm_label"><?php _e( 'Description', 'zephyr-project-manager' ); ?>:</label>
			<p id="zpm_view_task_description">
				<?php echo $task->description !== "" ? $task->description : "<p class='zpm-no-result-error'>" . _e( 'No description added', 'zephyr-project-manager' ) . "</p>"; ?>
			</p>
			<div id="zpm_view_task_subtasks">
				<label class="zpm_label"><?php _e( 'Subtasks', 'zephyr-project-manager' ); ?></label>
				<ul class="zpm_view_subtasks">
					<?php foreach ($subtasks as $subtask) : ?>
						<?php echo Tasks::subtaskItemHtml($subtask, ''); ?>
					<?php endforeach; ?>

					<?php if (sizeof($subtasks) <= 0) : ?>
						<p class='zpm-no-result-error'><?php _e( 'No subtasks created.', 'zephyr-project-manager' ); ?></p>
					<?php endif; ?>
				</ul>

				<span id="zpm_task_following">
					<span id="zpm_follow_task" class="lnr lnr-plus-circle <?php echo $is_following ? 'zpm_following' : ''; ?>"></span>
					<?php foreach($followers as $follower) : ?>
						<span class="zpm_task_follower" data-user-id="<?php echo $follower['id']; ?>" title="<?php echo $follower['name']; ?>" style="background-image: url('<?php echo $follower['avatar']; ?>');"></span>
					<?php endforeach; ?>
				</span>

			</div>

			<div id="zpm-task-view__status" class="zpm-task-view__field">
				<label class="zpm_label"><?php _e( 'Status', 'zephyr-project-manager' ); ?></label>
				<p><?php echo $taskStatusData['name']; ?></p>
			</div>

			<?php do_action('zpm_task_overview', $task_id); ?>

			<div class="zpm-task-view-info">
				<?php if ($task->team !== "") : ?>
					<?php $team = Members::get_team( $task->team ); ?>
					<?php if ($team['name'] !== "" && !empty($team['name'])) : ?>
						<span title="Team" class="zpm_task_project zpm-task-team"><?php echo $team['name']; ?></span>
					<?php endif; ?>
				<?php endif; ?>
						
				<?php if ( is_object( $project ) ) : ?>
					<?php $project_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects&action=edit_project&project=' . $project->id)); ?>
					<a href="<?php echo $project_url ?>" target="_blank" class="zpm-task-view-project">
						<?php echo $project->name; ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<div class="zpm_tab_pane" data-zpm-tab="task-discussion">
			<!-- Discussion -->
			<div class="zpm_task_comments" data-task-id="<?php echo $task_id; ?>">
				<?php $comments = Tasks::get_comments($task_id); ?>
				<?php foreach ($comments as $comment) : ?>
					<?php echo Tasks::new_comment($comment); ?>
				<?php endforeach; ?>
			</div>

			<!-- Chat box -->
			<div class="zpm_chat_box_section">
				<div class="zpm_chat_box">
					<div id="zpm_text_editor_wrap">
						<textarea id="zpm_chat_message" contenteditable="true" placeholder="<?php _e( 'Write comment...', 'zephyr-project-manager' ); ?>"></textarea>
						<div class="zpm_message_action_buttons">
							<button data-task-id="<?php echo $task_id; ?>" id="zpm_task_chat_files" class="zpm_task_chat_files zpm_button"><span class="zpm_message_action_icon lnr lnr-file-empty"></span></button>
							<button data-task-id="<?php echo $task_id; ?>" id="zpm_task_chat_comment" class="zpm_button"><span class="zpm_message_action_icon lnr lnr-arrow-right"></span></button>
						</div>
					</div>
					<div class="zpm_chat_box_footer">
						<div id="zpm_chat_attachments">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="zpm_modal_buttons">
		
		<?php if ( $type !== 'default' ) : ?>
			<div id="zpm-task-view__footer-details">
				<span class="fas fa-sync" title="<?php echo $recurrence_label; ?>"></span>
			</div>
		<?php endif; ?>

		<a class="zpm_button" href="<?php echo $tasks_url . '&action=view_task&task_id=' . $task_id; ?>" id="zpm_edit_task_link"><?php _e( 'Go to Task', 'zephyr-project-manager' ); ?></a>
	</div> 
</div>