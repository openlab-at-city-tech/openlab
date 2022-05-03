<?php 
	/**
	* Template for displaying the Edit Task modal
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Zephyr;
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Base\BaseController;
	use Inc\ZephyrProjectManager;
	
	$manager = ZephyrProjectManager::get_instance();
	$projects = $manager::get_projects();
	$args = array( 'can_zephyr' => true );
	$users = $manager::get_users( true, $args );
	$date = date('Y-m-d');
	$statuses = Utillities::get_statuses( 'status' );
	$priorities = Utillities::get_statuses( 'priority' );

	$general_settings = Utillities::general_settings();
	$extra_classes = $general_settings['hide_default_task_fields'] == '1' ? 'zpm-hide-default-fields' : '';
	$task = isset($taskId) ? Tasks::get_task($taskId) : false;
	$type = Tasks::get_type($task);
	$days = Tasks::get_days( $task );
	$expires = Tasks::get_expiration_date( $task );
?>

<div id="zpm_edit_task" class="zpm-modal <?php echo $extra_classes; ?>">
	<h5 class="zpm_modal_header"><?php _e( 'Edit Task', 'zephyr-project-manager' ); ?></h5>
	
	<div class="zpm_modal_body">
		<div class="zpm_modal_content">
			<input type="hidden" id="zpm-edit-task__id" value="<?php echo $task->id; ?>" />
			<div class="zpm-form__group zpm-edit-task-field__name">
				<input type="text" name="zpm_edit_task_name" id="zpm_edit_task_name" class="zpm-form__field" placeholder="<?php _e( 'Task Name', 'zephyr-project-manager' ); ?>" autocomplete="off" value="<?php echo $task->name; ?>">
				<label for="zpm_edit_task_name" class="zpm-form__label"><?php _e( 'Task Name', 'zephyr-project-manager' ); ?></label>
			</div>

			<div class="zpm-form__group zpm-edit-task-description-field">
				<textarea type="text" name="zpm_edit_task_description" id="zpm_edit_task_description" class="zpm-form__field" placeholder="<?php _e( 'Task Description', 'zephyr-project-manager' ); ?>" autocomplete="off"><?php echo $task->description; ?></textarea>
				<label for="zpm_edit_task_description" class="zpm-form__label"><?php _e( 'Task Description', 'zephyr-project-manager' ); ?></label>
			</div>

			<?php if (!isset($_GET['project'])) : ?>
				<div class="zpm-edit-task-field__project">
					<label class="zpm_label" for="zpm_edit_task_project"><?php _e( 'Project', 'zephyr-project-manager' ); ?></label>
					<select id="zpm_edit_task_project">
						<option value="-1"><?php _e( 'Select Project', 'zephyr-project-manager' ); ?></option>
						<?php foreach ($projects as $project) : ?>
							<?php $selected = $task->project == $project->id ? 'selected' : ''; ?>
							<option <?php echo $selected; ?> value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php else : ?>
				<input type="hidden" id="zpm_edit_task_project" value="<?php echo $_GET['project'] ?>"/>
			<?php endif; ?>
			
			<div class="zpm-edit-task-field__assignee">
				<label class="zpm_label" for="zpm_edit_task_assignee"><?php _e( 'Assignee', 'zephyr-project-manager' ); ?></label>
				<select id="zpm_edit_task_assignee" multiple data-placeholder="<?php _e( 'Select Assignees', 'zephyr-project-manager' ); ?>">
					<?php foreach ($users as $user) : ?>
						<?php $selected = Tasks::is_assignee($task, $user['id']) ? 'selected' : ''; ?>
						<option <?php echo $selected; ?> value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>;
					<?php endforeach; ?>
				</select>
			</div>

			<div class="zpm-edit-task-field__team">
				<label class="zpm_label" for="zpm-edit-task-team-selection"><?php _e( 'Team', 'zephyr-project-manager' ); ?></label>
				<?php echo Members::team_dropdown_html( 'zpm-edit-task-team-selection' ); ?>
			</div>

			<div class="zpm_options_container zpm-edit-task-field__dates">
				<span class="zpm_options_col">
					<div class="zpm-form__group">
						<input type="text" autocomplete="off" name="zpm_edit_task_start_date" id="zpm_edit_task_start_date" class="zpm-form__field" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $task->date_start !== '0000-00-00 00:00:00' ? $task->date_start : ''; ?>" />
						<label for="zpm_edit_task_start_date" class="zpm-form__label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
					</div>
				</span>
				<span class="zpm_options_col">
					<div class="zpm-form__group">
						<input type="text" autocomplete="off" name="zpm_edit_task_due_date" id="zpm_edit_task_due_date" class="zpm-form__field" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $task->date_due !== '0000-00-00 00:00:00' ? $task->date_due : ''; ?>"/>
						<label for="zpm_edit_task_due_date" class="zpm-form__label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
					</div>
				</span>
			</div>

			<!-- Select Status -->
			<div class="zpm-edit-task-field__status">
				<label class="zpm_label" for="zpm-edit-task__status"><?php _e( 'Status', 'zephyr-project-manager' ); ?></label>
				<select id="zpm-edit-task__status" class="zpm_input zpm-input-chosen">
					<option value="-1"><?php _e( 'Select Status', 'zephyr-project-manager' ); ?></option>
					<?php foreach ($statuses as $slug => $status) : ?>
						<?php $selected = $task->status == $slug ? 'selected' : ''; ?>
						<option value="<?php echo $slug; ?>"><?php echo $status['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Recurrence -->
			<div id="zpm-edit-task__recurrence" class="zpm-task-recurrence__wrap">
				<div id="zpm-edit-task__recurrence-selection" class="zpm-recurrence-selection">
					<label class="zpm_label"><?php _e( 'Set Recurrence', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-edit-task__recurrence-select" class="zpm_input zpm-input-chosen">
						<option value="default" <?php echo $type == 'default' ? 'selected' : ''; ?>><?php _e( 'None', 'zephyr-project-manager' ); ?></option>
						<option value="daily" <?php echo $type == 'daily' ? 'selected' : ''; ?>><?php _e( 'Daily', 'zephyr-project-manager' ); ?></option>
						<option value="weekly" <?php echo $type == 'weekly' ? 'selected' : ''; ?>><?php _e( 'Weekly', 'zephyr-project-manager' ); ?></option>
						<option value="monthly" <?php echo $type == 'monthly' ? 'selected' : ''; ?>><?php _e( 'Monthly', 'zephyr-project-manager' ); ?></option>
						<option value="annually" <?php echo $type == 'annually' ? 'selected' : ''; ?>><?php _e( 'Annually', 'zephyr-project-manager' ); ?></option>
					</select>

					<!-- Daily Reccurence Settings -->
					<div class="zpm-edit-task__recurrence-section" data-section="daily" style="<?php echo $type !== 'daily' ? 'display: none;' : ''; ?>">
						<label class="zpm_label"><?php _e( 'Repeat Every', 'zephyr-project-manager' ); ?></label>
						<div class="zpm-edit-task__recurrence-settings">
							<select id="zpm-edit-task__recurrence-daily" class="zpm-multi-select" multiple data-placeholder="<?php _e( 'Days to Repeat', 'zephyr-project-manager' ); ?>">
								<option value="0" <?php echo in_array('0', $days) ? 'selected' : ''; ?>><?php _e( 'Monday', 'zephyr-project-manager' ); ?></option>
								<option value="1" <?php echo in_array('1', $days) ? 'selected' : ''; ?>><?php _e( 'Tuesday', 'zephyr-project-manager' ); ?></option>
								<option value="2" <?php echo in_array('2', $days) ? 'selected' : ''; ?>><?php _e( 'Wednesday', 'zephyr-project-manager' ); ?></option>
								<option value="3" <?php echo in_array('3', $days) ? 'selected' : ''; ?>><?php _e( 'Thursday', 'zephyr-project-manager' ); ?></option>
								<option value="4" <?php echo in_array('4', $days) ? 'selected' : ''; ?>><?php _e( 'Friday', 'zephyr-project-manager' ); ?></option>
								<option value="5" <?php echo in_array('5', $days) ? 'selected' : ''; ?>><?php _e( 'Saturday', 'zephyr-project-manager' ); ?></option>
								<option value="6" <?php echo in_array('6', $days) ? 'selected' : ''; ?>><?php _e( 'Sunday', 'zephyr-project-manager' ); ?></option>
							</select>
						</div>

						<label class="zpm_label"><?php _e( 'Expires On (leave empty for no expiration):', 'zephyr-project-manager' ); ?></label>
						<input class="zpm-datepicker zpm_input" id="zpm-edit-task__recurrence-expiration-date" value="<?php echo !empty($expires) ? $expires : ''; ?>" />
					</div>

					<!-- Weekly Reccurence Settings -->
					<div class="zpm-edit-task__recurrence-section" data-section="weekly" style="<?php echo $type !== 'weekly' ? 'display: none;' : ''; ?>">
						<label class="zpm_label"><?php _e( 'Expires On (leave empty for no expiration):', 'zephyr-project-manager' ); ?></label>
						<input class="zpm-datepicker zpm_input" id="zpm-edit-task__recurrence-expiration-date-weekly" value="<?php echo !empty($expires) ? $expires : ''; ?>" />
					</div>

					<!-- Monthly Reccurence Settings -->
					<div class="zpm-edit-task__recurrence-section" data-section="monthly" style="<?php echo $type !== 'monthly' ? 'display: none;' : ''; ?>">
						<label class="zpm_label"><?php _e( 'Expires On (leave empty for no expiration):', 'zephyr-project-manager' ); ?></label>
						<input class="zpm-datepicker zpm_input" id="zpm-edit-task__recurrence-expiration-date-monthly" value="<?php echo !empty($expires) ? $expires : ''; ?>" />
					</div>

					<!-- Annual Reccurence Settings -->
					<div class="zpm-edit-task__recurrence-section" data-section="annually" style="<?php echo $type !== 'annually' ? 'display: none;' : ''; ?>">
						<label class="zpm_label"><?php _e( 'Expires On (leave empty for no expiration):', 'zephyr-project-manager' ); ?></label>
						<input class="zpm-datepicker zpm_input" id="zpm-edit-task__recurrence-expiration-date-annual" value="<?php echo !empty($expires) ? $expires : ''; ?>" />
					</div>
				</div>
			</div>

		</div>

		<div class="zpm_modal_buttons">
			<input type="hidden" id="zpm-edit-task-priority-value" value="priority_none" />

			<button id="zpm-update-task__btn" class="zpm_button"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
		</div>
	</div>
</div>