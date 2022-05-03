<?php

	/**
	* Template for displaying a single project modal
	*
	* @package ZephyrProjectManager
	*
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Core\Categories;
	use Inc\Core\Members;

	$base_url = esc_url( admin_url('/admin.php?page=zephyr_project_manager_projects') );
	$url = $base_url . '&action=edit_project&project=' . $project_id;
	$project = Projects::get_project( $project_id );

	$general_settings = Utillities::general_settings();
	$project = Projects::get_project( $project_id );
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
	$tasks = Tasks::get_project_tasks( $project->id );
	$completed_tasks = Tasks::get_project_completed_tasks( $project->id );
	$active_tasks = (int) $total_tasks - (int) $completed_tasks;
	$overdueTasks = sizeof(Projects::getOverdueProjectTasks( $project->id ));

	$priority = property_exists( $project, 'priority' ) ? $project->priority : 'priority_none';
	$priority_label = Utillities::get_priority_label( $priority );
	$status = Utillities::get_status( $priority );
	$users = Members::get_zephyr_members();
?>

	<div class="zpm_modal_body">
		<h2></h2>
		<h3 id="zpm-project-preview__header">
			<?php echo $project->name; ?>
			<?php if ( $general_settings['display_project_id'] == '1' ) : ?>
				(<?php echo Projects::get_unique_id( $project->id ); ?>)
			<?php elseif ( $general_settings['display_database_project_id'] == '1' ) : ?>
				(<?php echo $project->id; ?>)
			<?php endif; ?>
		</h3>
		<?php if ( $priority !== "priority_none" && $priority_label !== "" ) : ?>
			<span class="zpm-task-priority-bubble <?php echo $priority; ?>" style="background: <?php echo $status['color']; ?>; color: <?php echo $status['color'] !== '' ? '#fff' : ''; ?>"><?php echo $status['name']; ?></span>

		<?php endif; ?>
		<span class="zpm_close_modal lnr lnr-cross"></span>

		<div class="zpm_modal_actions">
			<nav class="zpm_nav">
				<ul class="zpm_nav_list">
					<li class="zpm_nav_item zpm_nav_item_selected" data-zpm-tab="1"><?php _e( 'Overview', 'zephyr-project-manager' ); ?></li>
					<li class="zpm_nav_item" data-zpm-tab="2"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?></li>
					<li class="zpm_nav_item" data-zpm-tab="3"><?php _e( 'Discussion', 'zephyr-project-manager' ); ?></li>
				</ul>
			</nav>
		</div>

		<div class="zpm_tab_pane" data-zpm-tab="2" id="zpm_tasks_tab">
			<button id="zpm_quick_task_add" class="zpm_button_outline"><?php _e( 'Add Task', 'zephyr-project-manager' ); ?></button>
			<div class="zpm_quicktask_container">
				<div class="zpm_quicktask_content">
					<input id="zpm_quicktask_name" class="zpm_input" type="text" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>"/>
					<textarea id="zpm_quicktask_description" class="zpm_input" type="text" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"></textarea>
					<input id="zpm_quicktask_date" class="zpm_input" type="text" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" />

					<div id="zpm_quicktask_assignee">
						<select id="zpm_quicktask_select_assignee" class="zpm_dropdown">
							<option value="-1"><?php _e( 'Select Assignee', 'zephyr-project-manager' ); ?></option>
							<?php foreach ($users as $user) : ?>
								<option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>;
							<?php endforeach; ?>
						</select>
					</div>
					<div class="zpm_quicktask_actions">
						<button id="zpm_create_quicktask" class="zpm_button_outline"><?php _e( 'Save Task', 'zephyr-project-manager' ); ?></button>
					</div>
				</div>
			</div>
				
			
			<div class="zpm_modal_content">
				<div id="zpm-task-list__project" class="zpm_task_list">
					<?php foreach ($tasks as $task) : ?>
						<?php echo Tasks::new_task_row($task); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="zpm_tab_pane zpm_tab_active" data-zpm-tab="1" id="zpm-project-modal-overview">
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
					<p class="zpm_stat_number"><?php echo $overdueTasks; ?></p>
					<p><?php _e( 'Overdue Tasks', 'zephyr-project-manager' ); ?></p>
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

			<?php $status = Projects::get_status( $project ); ?>
			<div class="zpm-project-preview__field">
				<label class="zpm_label"><?php _e( 'Status', 'zephyr-project-manager' ); ?>:</label>
				<p><span class="zpm-project-preview__status-color <?php echo $status['color']; ?>"></span><?php echo $status['status']; ?></p>
			</div>

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
		</div>

		<div class="zpm_tab_pane" data-zpm-tab="3" id="zpm-project-modal-discussion">
			<?php echo $comments_html; ?>
			<?php if ( empty( $comments_html ) ) : ?>
				<p class="zpm-subtle-message zpm-no-comments-error"><?php _e( 'No discussion yet', 'zephyr-project-manager' ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="zpm_modal_buttons">
		<a class="zpm_button" href="<?php echo $url; ?>"><?php _e( 'Go to Project', 'zephyr-project-manager' ); ?></a>
	</div>