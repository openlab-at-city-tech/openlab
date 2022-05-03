<?php
	/**
	* Template for displaying the Projects Edit/View page
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Zephyr;
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Core\Project;
	use Inc\Core\Projects;
	use Inc\Core\Message;
	use Inc\Core\Utillities;
	use Inc\Core\Categories;
	use Inc\Base\BaseController;
	use Inc\ZephyrProjectManager;

	$manager = ZephyrProjectManager();
	$project = Projects::get_project( $_GET['project'] );
	$base_url =  esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects'));
	$BaseController = new BaseController;
	$user = $BaseController->get_user_info( $project->user_id);
	$current_user = wp_get_current_user();
	$liked_projects = get_option( 'zpm_liked_projects_' . $current_user->data->ID, false );
	$liked_projects = unserialize($liked_projects);
	$date_due = new DateTime($project->date_due);
	$date_start = new DateTime($project->date_start);
	$project->date_due = ($date_due->format('Y-m-d') !== '-0001-11-30') ? $date_due->format('Y-m-d') : '';
	$project->date_start = ($date_start->format('Y-m-d') !== '-0001-11-30') ? $date_start->format('Y-m-d') : '';
	$project_status = maybe_unserialize( $project->status );
	$project_status['color'] = isset($project_status['color']) ? $project_status['color'] : 'not_started';
	$project_members = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
	$members = Utillities::get_users();
	$priority = property_exists( $project, 'priority' ) ? $project->priority : 'priority_none';
	$priority_label = Utillities::get_priority_label( $priority );
	$priorities = Utillities::get_statuses( 'priority' );
	$statuses = Utillities::get_statuses( 'status' );
	$status = Utillities::get_status( $priority );
	$general_settings = Utillities::general_settings();

	if (!Projects::has_project_access( $project )) {
		?>
			<div class="zpm-notice"><?php _e( 'Sorry, you do not have access to this project.', 'zephyr-project-manager' ); ?></div>
		<?php
	}

	$projectInstance = new Project($project);
	$tasks = Tasks::get_project_tasks( $project->id );
	$tasks = Projects::getOrderedTasks( $project->id, $tasks );

?>

<h2 id="zpm_project_name_title" class="zpm_admin_page_title">
	<?php echo $project->name; ?>
	<?php if ( $general_settings['display_project_id'] == '1' ) : ?>
		(#<?php echo Projects::get_unique_id( $project->id ); ?>)
	<?php elseif ( $general_settings['display_database_project_id'] == '1' ) : ?>
		(#<?php echo $project->id; ?>)
	<?php endif; ?>
</h2>

<span id="zpm-project-edit-priority-label" class="zpm-task-priority-bubble <?php echo $priority; ?> <?php echo ( $priority !== "priority_none" && $priority !== "" && !is_null($priority) ) ? '' : 'zpm-label-hidden'; ?>" style="background: <?php echo $status['color']; ?>; color: <?php echo $status['color'] !== '' ? '#fff' : ''; ?>"><?php echo $status['name']; ?></span>

<small class="zpm_title_information"><?php echo Projects::project_created_by($project->id); ?></small>
<input type="hidden" id="zpm-project-id" value="<?php echo $project->id; ?>">

<div class="zpm_nav_holder zpm_body">
	<nav class="zpm_nav">
		<ul class="zpm_nav_list">
			<li class="zpm_nav_item zpm_nav_item_selected" data-zpm-tab="project-overview"><?php _e( 'Overview', 'zephyr-project-manager' ); ?></li>
			<li class="zpm_nav_item" data-zpm-tab="project-tasks"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?></li>
			<li class="zpm_nav_item" data-zpm-tab="project-discussion"><?php _e( 'Discussion', 'zephyr-project-manager' ); ?></li>
			<li class="zpm_nav_item" data-zpm-tab="project-members"><?php _e( 'Members', 'zephyr-project-manager' ); ?></li>
			<li class="zpm_nav_item" id="zpm_update_project_progress" data-zpm-tab="project-progress"><?php _e( 'Progress', 'zephyr-project-manager' ); ?></li>

			<?php echo apply_filters( 'zpm-project-tabs', '' ); ?>

			<li class="zpm_nav_item" data-zpm-tab="project-settings"><?php _e( 'Settings', 'zephyr-project-manager' ); ?></li>
		</ul>
	</nav>
</div>

<div id="zpm_project_editor" class="zpm_body <?php echo 'project-type-' . $project->type; ?>" data-project-id="<?php echo $project->id; ?>">
	<!-- Project Overview / Editing -->
	<div class="zpm_tab_pane zpm_tab_active zpm-form" data-zpm-tab="project-overview">

		<div class="zpm-form__group">
			<input type="text" name="zpm_edit_project_name" id="zpm_edit_project_name" class="zpm-form__field" placeholder="<?php _e( 'Project Name', 'zephyr-project-manager' ); ?>" value="<?php echo $project->name; ?>">
			<label for="zpm_edit_project_name" class="zpm-form__label"><?php _e( 'Project Name', 'zephyr-project-manager' ); ?></label>
		</div>

		<div class="zpm-form__group">
			<textarea name="zpm_edit_project_description" id="zpm_edit_project_description" class="zpm-form__field" placeholder="<?php _e( 'Project Description', 'zephyr-project-manager' ); ?>"><?php echo $project->description; ?></textarea>
			<label for="zpm_edit_project_description" class="zpm-form__label"><?php _e( 'Project Description', 'zephyr-project-manager' ); ?></label>
		</div>

		<div class="zpm-form__group">
			<input type="text" name="zpm_edit_project_start_date" id="zpm_edit_project_start_date" class="zpm-form__field" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $project->date_start; ?>">
			<label for="zpm_edit_project_start_date" class="zpm-form__label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
		</div>

		<div class="zpm-form__group">
			<input type="text" name="zpm_edit_project_due_date" id="zpm_edit_project_due_date" class="zpm-form__field" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $project->date_due; ?>">
			<label for="zpm_edit_project_due_date" class="zpm-form__label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
		</div>

		<!-- Select Status -->
		<label class="zpm_label" for="zpm-edit-task__status"><?php _e( 'Status', 'zephyr-project-manager' ); ?></label>
		<select id="zpm-edit-project__status" class="zpm_input zpm-input-chosen">
			<option value="-1"><?php _e( 'Select Status', 'zephyr-project-manager' ); ?></option>
			<?php foreach ($statuses as $slug => $value) : ?>
				<option value="<?php echo $slug; ?>" <?php echo isset($project_status['color']) && $slug == $project_status['color'] ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
			<?php endforeach; ?>
		</select>

		<div class="zpm-edit-project__assignee-wrap">
			<label class="zpm_label"><?php _e( 'Project Assignees', 'zephyr-project-manager' ); ?></label>

			<select id="zpm-edit-project__assignee" class="zpm_input zpm-input-chosen zpm-multi-select" multiple data-placeholder="<?php _e( 'Select Project Assignees', 'zephyr-project-manager' ); ?>">
				<?php foreach ( $members as $member ) : ?>
					<?php $selected = $projectInstance->hasAssignee($member['id']) ? 'selected' : ''; ?>
					<option <?php echo $selected; ?> value="<?php echo $member['id']; ?>"><?php echo $member['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php do_action( 'zpm_project_view_fields', $project ); ?>

		<div class="zpm_project_editor_categories">
			<label class="zpm_label"><?php _e( 'Categories', 'zephyr-project-manager' ); ?></label>
			<?php $assigned_categories = unserialize($project->categories); ?>
			<?php $categories = $manager::get_categories(); ?>
			<?php $categories_url = menu_page_url( 'zephyr_project_manager_categories', false ); ?>
			<?php $i = 0 ?>

			<?php if ( empty( $categories ) ) : ?>
				<!-- No categories found -->
				<p class="zpm_extra_info zpm_text_italic">
					<?php printf( __('There are no categories yet. You can create categories %s here %s.', 'zephyr-project-manager' ), '<a href="' . $categories_url . '" class="zpm_link">', '</a>' ); ?></p>
			<?php else: ?>
				<select id="zpm-edit-project__categories" class="zpm_input zpm-input-chosen zpm-multi-select" multiple data-placeholder="<?php _e( 'Select Categories', 'zephyr-project-manager' ); ?>">
					<?php foreach ( $categories as $category ) : ?>
						<?php $selected = ( is_array($assigned_categories) && in_array($category->id, $assigned_categories ) ? 'selected' : ''); ?>
							<option <?php echo $selected; ?> value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
						<?php $i++; ?>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>

		<?php if ( Utillities::can_edit_projects() ) : ?>
			<button id="zpm_project_save_settings" class="zpm_button"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
		<?php endif; ?>

		<a class="zpm_button" href="<?php echo $base_url; ?>" id="zpm_back_to_projects"><?php _e( 'Back to Projects', 'zephyr-project-manager' ); ?></a>

		<?php do_action( 'zpm_project_single_buttons' ); ?>

		<input type="hidden" id="zpm-edit-project-priority-value" value="<?php echo $priority; ?>" />
		<span id="zpm-edit-project-priority" class="zpm_button zpm-priority-button zpm-priority-selection" zpm-toggle-dropdown="zpm-edit-project-priority-dropdown" data-priority="<?php echo $priority; ?>" style="background: <?php echo $status['color']; ?> !important; color: <?php echo $status['color'] !== '' ? '#fff !important' : ''; ?>">
			<span class="zpm-priority-name"><?php echo $status['name'] !== "" ? __( 'Priority', 'zephyr-project-manager' ) . ': ' . $status['name'] : __( 'Set Priority', 'zephyr-project-manager' ); ?></span>

			<div id="zpm-edit-project-priority-dropdown" class="zpm-dropdown">

				<div class="zpm-dropdown-item zpm-edit-project-priority" data-value="priority_none" data-color="#f9f9f9"><span class="zpm-priority-indicator zpm-color-none"></span><?php _e( 'None', 'zephyr-project-manager' ); ?></div>

				<?php foreach ( $priorities as $slug => $priority ) : ?>
					<div class="zpm-dropdown-item zpm-edit-project-priority" data-value="<?php echo $slug; ?>" data-color="<?php echo $priority['color']; ?>">

						<span class="zpm-priority-indicator <?php echo $slug; ?>" style="background-color: <?php echo $priority['color']; ?>"></span>
						<span class="zpm-priority-picker__name"><?php echo $priority['name']; ?></span>
					</div>
				<?php endforeach; ?>

 			 </div>
		</span>

		<div class="zpm_project_options">
			<span id="zpm_switch_project_type_button" class="zpm_circle_text_btn zpm-tooltip-parent" data-project-type="<?php echo $project->type == 'list' ? 'board' : 'list'; ?>" data-project-id="<?php echo $project->id; ?>" data-zpm-pro="<?php echo Zephyr::isPro(); ?>">
			<span class="zpm-project-type__label">
				<?php _e( 'Type: ', 'zephyr-project-manager' ); ?>
				<?php switch ($project->type) {
					case 'list':
						_e( 'List', 'zephyr-project-manager' );
						break;
					case 'board':
						_e( 'Kanban (Board)', 'zephyr-project-manager' );
						break;
					// TODO: Add to next version
					// case 'gantt':
					// 	_e( 'Gantt (Timeline)', 'zephyr-project-manager' );
					// 	break;
					default:
						_e( 'List', 'zephyr-project-manager' );
						break;
				} ?>
			</span>
			<div class="zpm-dropdown__extended zpm-dropdown">
				<div class="zpm-dropdown-item zpm-switch-project-type" data-type="list" data-project-id="<?php echo $project->id; ?>" data-zpm-pro="<?php echo Zephyr::isPro(); ?>">
					<p class="zpm-dropdown-item__title"><?php _e( 'List', 'zephyr-project-manager' ); ?></p>
					<p class="zpm-dropdown-item__sub"><?php _e( 'Organize your work in an itemized list.', 'zephyr-project-manager' ); ?></p>
				</div>
				<div class="zpm-dropdown-item zpm-switch-project-type <?php echo !Zephyr::isPro() ? 'zpm-pro-required-item' : ''; ?>" data-type="board" data-project-id="<?php echo $project->id; ?>" data-zpm-pro="<?php echo Zephyr::isPro(); ?>">
					<p class="zpm-dropdown-item__title"><?php _e( 'Kanban (Board)', 'zephyr-project-manager' ); ?>
						<?php echo Zephyr::proRequiredLabel(); ?>
					</p>
					<p class="zpm-dropdown-item__sub"><?php _e( 'Organize your work like sticky notes on a board.', 'zephyr-project-manager' ); ?></p>
				</div>
				<!-- Add to next version -->
				<!-- <div class="zpm-dropdown-item zpm-switch-project-type <?php echo !Zephyr::isPro() ? 'zpm-pro-required-item' : ''; ?>" data-type="gantt" data-project-id="<?php echo $project->id; ?>" data-zpm-pro="<?php echo Zephyr::isPro(); ?>">
					<p class="zpm-dropdown-item__title"><?php _e( 'Gantt (Timeline)', 'zephyr-project-manager' ); ?>
						<?php echo Zephyr::proRequiredLabel(); ?>
					</p>
					<p class="zpm-dropdown-item__sub"><?php _e( 'Organize your work with a visual Gantt timeline chart.', 'zephyr-project-manager' ); ?></p>
				</div> -->
			</div>
		</span>
			<span id="zpm-copy-project-shortcode" title="<?php _e( 'Copy Shortcode', 'zephyr-project-manager' ); ?> [zephyr_project id='<?php echo $project->id; ?>']" class="zpm_circle_option_btn" data-project-id="<?php echo $project->id; ?>" data-shortcode="[zephyr_project id='<?php echo $project->id; ?>']">
					<div class="lnr lnr-code"></div>
				</span>
			<span id="zpm_like_project_btn" class="zpm_circle_option_btn <?php echo (is_array($liked_projects) && in_array($project->id, $liked_projects)) ? 'zpm_liked' : ''; ?>" data-project-id="<?php echo $project->id; ?>">
				<div class="lnr lnr-thumbs-up"></div>
			</span>
		</div>
	</div>

	<!-- Project Tasks -->
	<?php ob_start(); ?>
	<div id="zpm_project_view_tasks" class="zpm_tab_pane zpm_body" data-zpm-tab="project-tasks">
		<button id="zpm_add_new_project_task" class="zpm_button"><?php _e( 'New Task', 'zephyr-project-manager' ); ?></button>
		<div id="zpm-task-list__project" class="zpm_task_list">
			<?php foreach ($tasks as $task) : ?>
				<?php echo Tasks::new_task_row($task); ?>
			<?php endforeach; ?>
		</div>
	</div>
	<?php $html = ob_get_clean();
	if (empty($project->type) || $project->type == 'list' || $project->type == 'board') {
		echo apply_filters('zpm-kanban-tasks', $html);
	} elseif ($project->type == 'gantt') {
		echo apply_filters('zpm_gantt_tasks', $html);
	} else {
		echo apply_filters('zpm-kanban-tasks', $html);
	}
	?>

	<div id="zpm_project_view_discussion" class="zpm_tab_pane" data-zpm-tab="project-discussion">
		<h4 class="zpm_panel_heading"><?php _e( 'Comments', 'zephyr-project-manager' ); ?></h4>
		<div class="zpm_task_comments" data-project-id="<?php echo $project->id; ?>">
			<?php $comments = Projects::get_comments( $project->id ); ?>
			<?php foreach($comments as $comment) : ?>
				<?php $message = new Message($comment); ?>
				<?php echo $message->html(); ?>
			<?php endforeach; ?>
		</div>
		<div class="zpm_chat_box_section">
			<div class="zpm_chat_box">
				<div id="zpm_text_editor_wrap">
					<textarea id="zpm_chat_message" contenteditable="true" placeholder="<?php _e( 'Write comment...', 'zephyr-project-manager' ); ?>"></textarea>
					<div class="zpm_editor_toolbar">
						<a href="#" data-command='addCode'><i class='lnr lnr-code'></i></a>
						<a href="#" data-command='createlink'><i class='lnr lnr-link'></i></a>
						<a href="#" data-command='undo'><i class='lnr lnr-undo'></i></a>
					</div>
				</div>
				<div class="zpm_chat_box_footer">
					<?php if (Utillities::canUploadFiles()) : ?>
						<button data-project-id="<?php echo $project->id; ?>" id="zpm_project_chat_files" class="zpm_task_chat_files zpm_button"><?php _e( 'Upload Files', 'zephyr-project-manager' ); ?></button>
					<?php endif; ?>
					<button data-project-id="<?php echo $project->id; ?>" id="zpm_project_chat_comment" class="zpm_button"><?php _e( 'Comment', 'zephyr-project-manager' ); ?></button>
					<div id="zpm_chat_attachments">
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Memebers -->
	<div id="zpm_project_view_progress" class="zpm_tab_pane" data-zpm-tab="project-members">
		<h4 class="zpm_panel_heading"><?php _e( 'Members', 'zephyr-project-manager' ); ?></h4>
		<div>
			<ul class="zpm-member-list">
				<?php foreach ($members as $member) : ?>
					<li><?php echo $member['name']; ?>
						<span class="zpm-memeber-toggle">
							<input type="checkbox" id="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle zpm-project-member" data-member-id="<?php echo $member['id']; ?>" <?php echo in_array($member['id'], (array)$project_members) ? 'checked' : ''; ?>>
							<label for="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle-label">
							</label>
						</span>
					</li>
				<?php endforeach; ?>
				<button id="zpm-save-project-members" class="zpm_button"><?php _e( 'Save Members', 'zephyr-project-manager' ); ?></button>
				<button id="zpm-select-all-project-members" data-zpm-action="select_all" class="zpm_button"><?php _e( 'Select All', 'zephyr-project-manager' ); ?></button>

			</ul>
		</div>
	</div>

	<!-- Progress -->
	<?php
		$status_name = isset($project_status['status']) ? esc_html($project_status['status']) : '';
	?>
	<div id="zpm_project_view_progress" class="zpm_tab_pane" data-zpm-tab="project-progress">
		<div id="zpm_project_view_status">
			<h4 class="zpm_panel_heading"><?php _e( 'Project Status', 'zephyr-project-manager' ); ?></h4>

			<div id="zpm_project_overview_section">
				<div id="zpm_project_status_colors">
					<?php foreach ($statuses as $slug => $status) : ?>
						<span class="zpm_project_status zpm_status_overdue <?php echo $slug; ?> <?php echo $slug == $project_status['color'] ? 'active' : ''; ?>" data-status="<?php echo $slug; ?>" data-status-name="<?php echo $status['name']; ?>">
							<span class="zpm-project-status__name" style="background-color: <?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
						</span>
					<?php endforeach; ?>
				</div>
				<div id="zpm_project_status" placeholder="<?php _e( 'Project Status', 'zephyr-project-manager' ); ?>" contentEditable="true">
					<?php echo $status_name; ?>
				</div>
				<div id="zpm_project_status_footer">
					<button id="zpm_update_project_status" class="zpm_button" data-project-id="<?php echo $project->id; ?>"><?php _e( 'Update Status', 'zephyr-project-manager' ); ?></button>
				</div>
			</div>
		</div>

		<div id="zpm-project-progress-member__list">
			<?php foreach ( (array) $project_members as $member_id ) : ?>
				<?php $member = Members::get_member( $member_id ); ?>
				<div class="zpm-project-progress-member__item">
					<div class="zpm-project-progress__member" data-user-id="<?php echo $member_id; ?>">
						<span class="zpm-progress-member__avatar" style="background-image: url(<?php echo $member['avatar'] ?>);"></span>
						<span class="zpm-progress-member__name"><?php echo $member['name']; ?></span>
						<span class="zpm-progress-member__percent">-</span>

						<div class="zpm-project-progress__member-details" data-user-id="<?php echo $member_id; ?>">
							<?php _e( 'Loading...', 'zephyr-project-manager' ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div id="zpm-chart-filter__container">
			<label class="zpm-label"><?php _e( 'Filter by member', 'zephyr-project-manager' ) ?>:</label>

			<select id="zpm-project-chart__filter">
				<option value="-1"><?php _e( 'All', 'zephyr-project-manager' ); ?></option>
				<?php foreach ( (array) $project_members as $member_id ) : ?>
					<?php $member = Members::get_member( $member_id ); ?>
					<option value="<?php echo $member['id']; ?>"><?php echo $member['name']; ?></option>
				<?php endforeach; ?>
			</select>


			<canvas id="zpm-project-chart__doughnut" width="400" height="200"></canvas>
		</div>

		<?php
		$project_id = $project->id;
		$report_name = $project->name;
		$project_creator = BaseController::get_project_manager_user($project->user_id);
		$description = ($project->description !== '') ? $project->description : '<span class="zpm_subtle_error">' . __( 'No description', 'zephyr-project-manager' ) . '</span>';
		$date = date('d/m/Y');
		$created_on = new DateTime( $project->date_created );
		$start_on = new DateTime( $project->date_start );
		$due_on =  new DateTime( $project->date_due );
		$start_on = ($start_on->format('Y') !== '-0001') ? $start_on->format('d M Y') : '';
		$due_on = ($due_on->format('Y') !== '-0001') ? $due_on->format('d M Y') : '';
		$task_count = Tasks::get_project_task_count($project_id);
		$args = array(
			'project' => $project_id,
			'completed' => '1'
		);
		$completed_tasks = $manager::get_tasks( $args );
		$completed_tasks_count = sizeof( $completed_tasks );

		$args = array(
			'project' => $project_id,
			'completed' => '0'
		);
		$active_tasks = $manager::get_tasks( $args );
		$args = array( 'project_id' => $project_id );
		$overdue_tasks = sizeof( Tasks::get_overdue_tasks( $args ) );
		$pending_tasks = $task_count - $completed_tasks_count;
		$percent_complete = ($task_count !== 0) ? floor($completed_tasks_count / $task_count * 100): '100';
		ob_start();
		?>

		<div id="zpm-project-progress__task-table" class="zpm-table">
			<div class="zpm-table__header">
				<span class="zpm-table__th"><?php _e( 'Task Name', 'zephyr-project-manager' ); ?></span>
				<span class="zpm-table__th"><?php _e( 'Assignee', 'zephyr-project-manager' ); ?></span>
				<span class="zpm-table__th"><?php _e( 'Status', 'zephyr-project-manager' ); ?></span>
				<span class="zpm-table__th"><?php _e( 'Date Completed', 'zephyr-project-manager' ); ?></span>
			</div>
			<?php foreach ($completed_tasks as $task) : ?>
				<?php
					$completed_date = new DateTime($task->date_completed);
					$completed_date = $completed_date->format('d M Y');
					$members = Tasks::get_assignees( $task, true );
					$member_count = 0;
				?>
				<div class="zpm-project-progress__task zpm-list-item zpm-table__row">
					<span class="zpm-progress__task-name zpm-table__cell"><?php echo $task->name; ?></span>
					<span class="zpm-progress__task-assignee zpm-table__cell">
						<?php foreach( $members as $member ) : ?>
							<span class="zpm-progress__task-assignee-item"><?php echo $member['name']; ?><?php echo $member_count < sizeof( $members ) - 1 ? ', ' : ''; ?></span>
							<?php $member_count++; ?>
						<?php endforeach; ?>

						<?php if ( empty( $members ) ) : ?>
							<?php _e( 'None', 'zephyr-project-manager' ); ?>
						<?php endif; ?>
					</span>
					<span class="zpm-progress__task-completed zpm-table__cell completed"><?php _e( 'Completed', 'zephyr-project-manager' ); ?></span>
					<span class="zpm-progress__task-completed-date zpm-table__cell"><?php echo $completed_date; ?></span>
				</div>
			<?php endforeach; ?>

			<?php foreach ($active_tasks as $task) : ?>
				<?php
					$members = Tasks::get_assignees( $task, true );
					$member_count = 0;
				?>
				<div class="zpm-project-progress__task zpm-list-item zpm-table__row">
					<span class="zpm-progress__task-name zpm-table__cell"><?php echo $task->name; ?></span>
					<span class="zpm-progress__task-assignee zpm-table__cell">
						<?php foreach( $members as $member ) : ?>
							<span class="zpm-progress__task-assignee-item"><?php echo $member['name']; ?><?php echo $member_count < sizeof( $members ) - 1 ? ', ' : ''; ?></span>
							<?php $member_count++; ?>
						<?php endforeach; ?>

						<?php if ( empty( $members ) ) : ?>
							<?php _e( 'None', 'zephyr-project-manager' ); ?>
						<?php endif; ?>
					</span>
					<span class="zpm-progress__task-completed zpm-table__cell"><?php _e( 'Incomplete', 'zephyr-project-manager' ); ?></span>
					<span class="zpm-progress__task-completed-date zpm-table__cell"></span>
				</div>
			<?php endforeach; ?>
		</div>
<!-- 		<h4 class="zpm_panel_heading"><?php _e( 'Progress', 'zephyr-project-manager' ); ?></h4>
		<canvas class="zpm_report_chart" id="zpm_project_progress_chart" width="400" height="200"></canvas>

		<img id="zpm_project_report_chart_img" style="display: none"> -->

		<div class='zpm_report_task_stats'>
			<span class='zpm_report_stat'>
				<label class='zpm_label'><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></label> <?php echo $completed_tasks_count; ?>
			</span>
			<span class='zpm_report_stat'>
				<label class='zpm_label'><?php _e( 'Pending Tasks', 'zephyr-project-manager' ); ?></label> <?php echo $pending_tasks; ?>
			</span>
			<span class='zpm_report_stat'>
				<label class='zpm_label'><?php _e( 'Overdue Tasks', 'zephyr-project-manager' ); ?></label> <?php echo $overdue_tasks; ?>
			</span>
			<span class='zpm_report_stat'>
				<label class='zpm_label'><?php _e( 'Percent Complete', 'zephyr-project-manager' ); ?>:</label> <?php echo round($percent_complete) . '%'; ?>
			</span>
		</div>
	</div>
	<?php echo apply_filters( 'zpm-project-tab-pages', ' ', $project->id ); ?>

	<div id="zpm-project-single__settings" class="zpm_tab_pane" data-zpm-tab="project-settings">
		<div>
			<?php Projects::update_settings( $project->id ); ?>
			<?php $project_settings = Utillities::get_user_project_settings( get_current_user_id(), $project->id ); ?>
			<?php $additionalEmails = Projects::getAdditionalEmails($project->id); ?>

			<label class="zpm_label zpm_divider_label"><?php _e( 'My Notifications', 'zephyr-project-manager' ) ?></label>
			<form method="POST">
				<div>
					<label for="zpm-project-settings__new-task-email" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__new-task-email" name="zpm-project-settings__new-task-email" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['new_task_email'] ) && $project_settings['new_task_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'New Tasks', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label for="zpm-project-settings__task-completed-email" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__task-completed-email" name="zpm-project-settings__task-completed-email" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['task_completed_email'] ) && $project_settings['task_completed_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'Task Completed', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label for="zpm-project-settings__project-comments-email" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__project-comments-email" name="zpm-project-settings__project-comments-email" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['project_comments_email'] ) && $project_settings['project_comments_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'Project Comments', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label for="zpm-project-settings__task-comments-emails" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__task-comments-emails" name="zpm-project-settings__task-comments-emails" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['task_comments_email'] ) && $project_settings['task_comments_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'Task Comments', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label for="zpm-project-settings__task-assignee-comments-emails" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__task-assignee-comments-emails" name="zpm-project-settings__task-assignee-comments-emails" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['task_assignee_comments_email'] ) && $project_settings['task_assignee_comments_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'Assigned Task Comments', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label for="zpm-project-settings__subtasks-email" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__subtasks-email" name="zpm-project-settings__subtasks-email" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['new_subtask_email'] ) && $project_settings['new_subtask_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'New Subtasks', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label for="zpm-project-settings__weekly-update-email" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-project-settings__weekly-update-email" name="zpm-project-settings__weekly-update-email" class="zpm_toggle invisible" value="1" <?php echo isset( $project_settings['weekly_update_email'] ) && $project_settings['weekly_update_email'] !== "0" ? 'checked' : '';  ?>>
						<span class="zpm-material-checkbox-label"><?php _e( 'Weekly Update', 'zephyr-project-manager' ); ?></span>
					</label>
				</div>

				<div>
					<label class="zpm_label"><?php _e( 'Additional Notification Emails (Comma Separated)', 'zephyr-project-manager' ); ?></label>
					<input type="text" name="zpm-project-settings__additional-emails" id="zpm-project-settings__additional-emails" value="<?php echo !empty($additionalEmails) ? zpm_array_to_comma_string($additionalEmails) : '';  ?>" placeholder="<?php _e( 'Additional Emails', 'zephyr-project-manager' ); ?>" class="zpm_input"/>
				</div>

				<?php do_action( 'zpm_project_settings_content', $project ); ?>

				<button id="zpm-project-single__save_settings" name="zpm-update-project-settings" class="zpm_button"><?php _e( 'Save Settings', 'zephyr-project-manager' ); ?></button>
			</form>
		</div>
	</div>

</div>
