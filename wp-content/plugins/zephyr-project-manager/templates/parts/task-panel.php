<?php
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use \DateTime;
	use Inc\Core\File;
	use Inc\Core\Task;
	use Inc\Core\Tasks;
	use Inc\Core\Message;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Core\Members;
	use Inc\Base\BaseController;
	use Inc\Core\Activity;

	$primaryColor = zpm_get_primary_frontend_color();
	$secondaryColor = zpm_get_secondary_frontend_color();
	$task = Tasks::get_task($taskId);
	$generalSettings = Utillities::general_settings();
	//$user = BaseController::get_user_info($task->user_id); 
	$due_datetime = new DateTime( $task->date_due );
	$start_datetime = new DateTime( $task->date_start );

	$dueDateFormatted = '';
	$startDateFormatted = '';

	if (Tasks::hasDueDate($task)) {
		$dueDateFormatted = date_i18n($generalSettings['date_format'], strtotime($task->date_due));
	}

	if (Tasks::hasStartDate($task)) {
		$startDateFormatted = date_i18n($generalSettings['date_format'], strtotime($task->date_start));
	}
	
	$start_date = ($start_datetime->format('Y-m-d') !== '-0001-11-30') ? $start_datetime->format('Y-m-d H:i') : '';
	$due_date = ($due_datetime->format('Y-m-d') !== '-0001-11-30') ? $due_datetime->format('Y-m-d H:i') : '';
	$priority = property_exists( $task, 'priority' ) ? $task->priority : 'priority_none';
	$priority_label = Utillities::get_priority_label( $priority );
	$priorities = Utillities::get_statuses( 'priority' );
	$statuses = Utillities::get_statuses( 'status' );
	$status = Utillities::get_status($priority);
	$type = Tasks::get_type( $task );
	$days = Tasks::get_days( $task );
	$expires = Tasks::get_expiration_date( $task );
	$taskData = Tasks::get_task_data($task);
	$recurrence_start = isset($taskData['start']) ? $taskData['start'] : '';
	$recurrence_frequency = isset($taskData['frequency']) ? $taskData['frequency'] : '';
	//$task = new Task($task);
	$subtasks = Tasks::get_subtasks($task->id);
	$pages = [];
	$pages = apply_filters( 'zpm_task_pages', $pages, $task );

	$parents = Tasks::getTaskParents($task);
	$users = Members::get_zephyr_members();

	$attachments = Tasks::get_task_attachments($taskId);
	$comments = Tasks::get_comments( $taskId );
	$project = Projects::get_project($task->project);
	$assignees = Tasks::get_assignees( $task, true );
?>

<?php if (Utillities::canEditTask($task)) : ?>
	<div class="zpm-task-preview__section">	
		<input type="hidden" data-ajax-name="task_id" value="<?php echo $task->id; ?>" />
		<input type="hidden" data-ajax-name="task_project" value="<?php echo $task->project; ?>" />
		<input type="hidden" data-ajax-name="frontend" value="true" />

		<p class="zpm-task-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'About this task', 'zephyr-project-manager' ); ?></p>
		<label class="zpm-task-preview__label" id="zpm-task-preview__section-information" class="zpm-task-preview__section-content"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__name">
			<input data-ajax-name="task_name" value="<?php echo esc_html( $task->name ); ?>" class="zpm_input" />
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__description">
			<textarea data-ajax-name="task_description" class="zpm_input"><?php echo esc_html( $task->description ); ?></textarea>
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Assigned To', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__assignee">
			<select id="zpm_edit_task_assignee" data-ajax-name="task_assignee" class="zpm_input zpm-input-chosen zpm-chosen" multiple data-placeholder="<?php _e( 'Select Assignees', 'zephyr-project-manager' ); ?>">
				<?php $assignees = Tasks::get_assignees( $task ); ?>
				<?php foreach ( $users as $user ) : ?>
					<option <?php echo in_array( $user['id'], $assignees ) ? 'selected' : ''; ?> value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>;
				<?php endforeach; ?>
			</select>
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__start-date">
			<input type="text" name="date_due" data-ajax-name="task_start_date" id="zpm-task-panel__date-due" class="zpm-form__field zpm-datepicker zpm_input" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $startDateFormatted; ?>">
			
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__due-date">
			<input type="text" data-ajax-name="task_due_date" name="task_due_date" id="zpm-task-panel__date-start" class="zpm-form__field zpm-datepicker zpm_input" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $dueDateFormatted; ?>">
		</p>

		<div class="zpm-task-preview__priority">
			<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Priority', 'zephyr-project-manager' ); ?></label>

			<p class="zpm-task-preview__label-value">
				<select class="zpm_input zpm-chosen" data-ajax-name="priority">
					<option value="-1"><?php _e( 'None', 'zephyr-project-manager' ); ?></option>
					<?php foreach ( $priorities as $slug => $value ) : ?>
						<option value="<?php echo $slug; ?>" <?php echo $slug == $priority ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>


		<div id="zpm-task-preview__custom-fields">
			<?php do_action('zpm_after_task_settings', $taskId); ?>
		</div>

		<div id="zpm-task-preview__extra" style="display: none;">
			
		</div>

		<!-- <div id="zpm-task-preview__recurrence">
			<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Recurrence', 'zephyr-project-manager' ); ?></label>

			<p class="zpm-task-preview__label-value">
				<span id="zpm-task-preview__recurrence-logo" class="fas fa-sync" title="<?php echo $recurrence_label; ?>"></span><span id="zpm-task-preview__recurrence-value"></span>
			</p>
		</div> -->
	</div>
<?php else: ?>
	<div class="zpm-task-preview__section">	
		<input type="hidden" data-ajax-name="task_id" value="<?php echo $task->id; ?>" />
		<input type="hidden" data-ajax-name="task_project" value="<?php echo $task->project; ?>" />
		<input type="hidden" data-ajax-name="frontend" value="true" />

		<p class="zpm-task-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'About this task', 'zephyr-project-manager' ); ?></p>
		<label class="zpm-task-preview__label" id="zpm-task-preview__section-information" class="zpm-task-preview__section-content"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__name">
			<?php echo esc_html( $task->name ); ?>
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__description">
			<?php echo esc_html( $task->description ); ?>
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Assigned To', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__assignee">
			<?php foreach ($assignees as $assignee) : ?>
				<span class="zpm-task-modal__assignee">
					<span class="zpm_task_user_avatar" style="background-image: url(<?php echo $assignee['avatar']; ?>);">
					</span>
					<span class="zpm_task_username"><?php echo $assignee['name']; ?></span>
				</span>
			<?php endforeach; ?>
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__start-date">
			<?php echo Tasks::formatDate($task->date_start); ?>			
		</p>

		<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-task-preview__label-value zpm-task-preview__due-date">
			<?php echo Tasks::formatDate($task->date_due); ?>	
		</p>

		<div class="zpm-task-preview__priority">
			<label class="zpm-task-preview__label zpm-task-preview__section-content"><?php _e( 'Priority', 'zephyr-project-manager' ); ?></label>

			<p class="zpm-task-preview__label-value">
				<?php echo $priority_label; ?>
			</p>
		</div>


		<div id="zpm-task-preview__extra" style="display: none;">
			
		</div>

	</div>
<?php endif; ?>

<div id="zpm-task-preview-section__files" class="zpm-task-preview__section">
	<p class="zpm-task-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'Files', 'zephyr-project-manager' ); ?></p>
	<div id="zpm-task-preview__section-files" class="zpm-task-preview__section-content">
		<div id="zpm-task-preview__files">
			<?php foreach ($attachments as $attachment) : ?>
				<?php $file = new File($attachment); ?>
				<?php echo $file->html(); ?>
			<?php endforeach; ?>
			<?php if (empty($attachments)) : ?>
				<p class="zpm-notice__subtle"><?php _e( 'No files', 'zephyr-project-manager' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="zpm-task-preview__section">
	<p class="zpm-task-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'Subtasks', 'zephyr-project-manager' ); ?></p>
	<div id="zpm-task-preview__section-subtasks" class="zpm-task-preview__section-content">
		<div id="zpm-task-preview__subtasks" class="active">
			<?php foreach ($subtasks as $subtask) : ?>
				<?php
					$atts = $subtask->completed == '1' ? 'checked' : '';
				?>
					<div class="zpm-task-preview__subtask">
						<label for="zpm-subtask-id-<?php echo $subtask->id; ?>" class="zpm-material-checkbox">
							<input type="checkbox" id="zpm-subtask-id-<?php echo $subtask->id; ?>" name="zpm-subtask-id-<?php echo $subtask->id; ?>" class="zpm_subtask_is_done zpm_toggle invisible" value="1" <?php echo $atts; ?> data-task-id="<?php echo $subtask->id; ?>">
							<span class="zpm-material-checkbox-label"></span>
						</label>
						<?php echo esc_html( $subtask->name ); ?>
					</div>
					</label>
			<?php endforeach; ?>
			<?php if (empty($subtasks)) : ?>
				<p class="zpm-notice__subtle"><?php _e( 'No subtasks', 'zephyr-project-manager' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>

<div id="zpm-task-preview-section__discussion" class="zpm-task-preview__section">
	<p class="zpm-task-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'Discussion', 'zephyr-project-manager' ); ?></p>
	<div id="zpm-project-preview__section-comments" class="zpm-project-preview__section-content">
		<div class="zpm_task_comments">
			<?php foreach($comments as $comment) : ?>
				<?php $message = new Message($comment); ?>
				<?php echo $message->html(); ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="zpm_chat_box_section">
		<div class="zpm_chat_box">
			<div id="zpm_text_editor_wrap">
				<textarea id="zpm_chat_message" placeholder="<?php _e( 'Write comment...', 'zephyr-project-manager' ); ?>"></textarea>
				
			</div>
			<div class="zpm_chat_box_footer">
				<button data-task-id="<?php echo $task->id; ?>" id="zpm-submit-comment" class="zpm_button"><?php _e( 'Comment', 'zephyr-project-manager' ); ?></button>
			</div>
		</div>
	</div>
</div>

<?php if ( is_object( $project ) ) : ?>
	<div id="zpm-task-preview-section__project" class="zpm-task-preview__section">
		<span class="zpm-task__project-name"><?php echo $project->name; ?></span>
	</div>
<?php endif; ?>

