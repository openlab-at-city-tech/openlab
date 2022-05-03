<?php
	// Project Sidebar Panel
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
	use Inc\Core\Categories;
	use Inc\Core\Members;
	use Inc\Base\BaseController;
	use Inc\Core\Activity;

	global $zpmMessages;
	$primaryColor = zpm_get_primary_frontend_color();
	$secondaryColor = zpm_get_secondary_frontend_color();
	$project = Projects::get_project($projectId);
	$Task = new Tasks();

	$userId = get_current_user_id();

	$general_settings = Utillities::general_settings();
	$current_user = wp_get_current_user();
	$liked_projects = unserialize(get_option( 'zpm_liked_projects_' . $current_user->data->ID, false ));
	$date_due = new DateTime($project->date_due);
	$date_start = new DateTime($project->date_start);
	$project->date_due = ($date_due->format('Y-m-d') !== '-0001-11-30') ? date_i18n($general_settings['date_format'], strtotime($project->date_due)) : '';
	$project->date_start = ($date_start->format('Y-m-d') !== '-0001-11-30') ? date_i18n($general_settings['date_format'], strtotime($project->date_start)) : '';

	$edit_due_date = ($date_due->format('Y-m-d') !== '-0001-11-30') ? $date_due->format('Y-m-d') : '';
	$edit_start_date = ($date_start->format('Y-m-d') !== '-0001-11-30') ? $date_start->format('Y-m-d') : '';
	$project_status = maybe_unserialize($project->status);
	$tasks = Tasks::get_project_tasks( $projectId );
	$tasks = Projects::getOrderedTasks( $projectId, $tasks );
	$assigned_categories = (array) unserialize($project->categories);
	$categories = Categories::get_categories();
	$project_members = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
	$members = Utillities::get_users();
	$priority = property_exists( $project, 'priority' ) ? $project->priority : 'priority_none';
	$priority_label = Utillities::get_priority_label( $priority );
	$priorities = Utillities::get_statuses( 'priority' );
	$statuses = Utillities::get_statuses( 'status' );
	$status = Utillities::get_status( $priority );
	$attachments = Projects::get_attachments( $projectId );
	$comments = Projects::get_comments( $projectId );
?>
	
<?php if (Utillities::canEditProject($project)) : ?>
	<div class="zpm-project-preview__section">
		<input type="hidden" data-ajax-name="project_id" value="<?php echo $project->id; ?>" />
		<input type="hidden" data-ajax-name="frontend" value="true" />
		
		<p class="zpm-project-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'About this project', 'zephyr-project-manager' ); ?></p>
		<label class="zpm-project-preview__label" id="zpm-project-preview__section-information" class="zpm-project-preview__section-content"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__name">
			<input data-ajax-name="project_name" value="<?php echo esc_html( $project->name ); ?>" />
		</p>

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__description">
			<textarea data-ajax-name="project_description"><?php echo esc_html( $project->description ); ?></textarea>
		</p>

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__start-date">
			<input type="text" name="start_date" data-ajax-name="project_start_date" id="zpm-task-panel__date-due" class="zpm-form__field zpm-datepicker" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $edit_start_date; ?>">
		</p>

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__due-date">
			<input type="text" name="date_due" data-ajax-name="project_due_date" id="zpm-project-panel__date-due" class="zpm-form__field zpm-datepicker" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $edit_due_date; ?>">
		</p>

		<div id="zpm-project-preview__custom-fields">
			<?php do_action( 'zpm_project_view_fields', $project ); ?>
		</div>

		<div id="zpm-project-preview__status-section">
			<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Status', 'zephyr-project-manager' ); ?></label>
			<p class="zpm-project-preview__label-value zpm-project-preview__status"><?php echo $status['name']; ?>
				<select class="zpm_input zpm-chosen" data-ajax-name="status">
					<option value="-1"><?php _e( 'Select Status', 'zephyr-project-manager' ); ?></option>
					<?php foreach ($statuses as $slug => $value) : ?>
						<option value="<?php echo $slug; ?>" <?php echo isset($project_status['color']) && $slug == $project_status['color'] ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
	<!-- 
		<div class="zpm-project-preview__priority">
			<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Priority', 'zephyr-project-manager' ); ?></label>

			<p class="zpm-project-preview__label-value">
				<span id="zpm-project-preview__priority"></span>
			</p>
		</div> -->

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Categories', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__categories">
			<select class="zpm_input zpm-chosen" data-ajax-name="project_categories" multiple data-placeholder="<?php _e( 'Select Categories', 'zephyr-project-manager' ); ?>">
				<?php foreach ($categories as $category) : ?>
					<option value="<?php echo $category->id; ?>" <?php echo in_array($category->id, $assigned_categories) ? 'selected' : ''; ?>><?php echo $category->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	</div>
<?php else: ?>
	<div class="zpm-project-preview__section">
		<input type="hidden" data-ajax-name="project_id" value="<?php echo $project->id; ?>" />
		<input type="hidden" data-ajax-name="frontend" value="true" />
		
		<p class="zpm-project-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'About this project', 'zephyr-project-manager' ); ?></p>
		<label class="zpm-project-preview__label" id="zpm-project-preview__section-information" class="zpm-project-preview__section-content"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__name">
			<?php echo esc_html( $project->name ); ?>
		</p>

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__description">
			<?php echo esc_html( $project->description ); ?>
		</p>

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__start-date">
			<?php echo Tasks::formatDate($project->date_start); ?>
		</p>

		<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__due-date">
			<?php echo Tasks::formatDate($project->date_due); ?>
		</p>

		<!-- <div id="zpm-project-preview__custom-fields">
			<?php do_action( 'zpm_project_view_fields', $project ); ?>
		</div> -->

		<div id="zpm-project-preview__status-section">
			<label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Status', 'zephyr-project-manager' ); ?></label>
			<p class="zpm-project-preview__label-value zpm-project-preview__status">
				<?php echo $status['name']; ?>
			</p>
		</div>

		<!-- <label class="zpm-project-preview__label zpm-project-preview__section-content"><?php _e( 'Categories', 'zephyr-project-manager' ); ?></label>
		<p class="zpm-project-preview__label-value zpm-project-preview__categories">
			<select class="zpm_input zpm-chosen" data-ajax-name="project_categories" multiple data-placeholder="<?php _e( 'Select Categories', 'zephyr-project-manager' ); ?>">
				<?php foreach ($categories as $category) : ?>
					<option value="<?php echo $category->id; ?>" <?php echo in_array($category->id, $assigned_categories) ? 'selected' : ''; ?>><?php echo $category->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p> -->
	</div>
<?php endif; ?>

<div id="zpm-project-preview-section__files" class="zpm-project-preview__section">
	<p class="zpm-project-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'Files', 'zephyr-project-manager' ); ?></p>
	<div id="zpm-project-preview__section-files" class="zpm-project-preview__section-content">
		<div id="zpm-project-preview__files">
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

<div class="zpm-project-preview__section">
	<p class="zpm-project-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?></p>
	<div id="zpm-project-preview__section-tasks" class="zpm-project-preview__section-content">
		<div id="zpm-project-preview__tasks" class="zpm-active">
			<?php foreach ($tasks as $task) : ?>
				<?php
					$atts = $task->completed == '1' ? 'checked' : '';
					$url = Tasks::task_url( $task->id, true );
				?>
				<div class="zpm-project-preview__task">
					<label for="zpm-task-id-<?php echo $task->id; ?>" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm-task-id-<?php echo $task->id; ?>" name="zpm-task-id-<?php echo $task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $atts; ?> data-task-id="<?php echo $task->id; ?>">
						<span class="zpm-material-checkbox-label"></span>
					</label>
					<a href="<?php echo $url; ?>"><?php echo $task->name; ?></a></div>
			<?php endforeach; ?>
			<?php if (empty($tasks)) : ?>
				<p class="zpm-notice__subtle">No tasks</p>
			<?php endif; ?>
		</div>
	</div>
</div>

<div id="zpm-project-preview-section__discussion" class="zpm-project-preview__section">
	<p class="zpm-project-preview__section-title" style="color: <?php echo $secondaryColor; ?>"><?php _e( 'Discussion', 'zephyr-project-manager' ); ?></p>
	<div id="zpm-project-preview__section-comments" class="zpm-project-preview__section-content">
		<div class="zpm_task_comments">
			<?php foreach($comments as $comment) : ?>
				<?php $zpmMessages->addReadMessage($comment->id); ?>
				<?php $message = new Message($comment); ?>
				<?php echo $message->html(); ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="zpm_chat_box_section">
		<div class="zpm_chat_box">
			<div id="zpm_text_editor_wrap">
				<textarea id="zpm_chat_message" placeholder="<?php _e( 'Write comment...', 'zephyr-project-manager' ); ?>" class="zpm-panel__no-update"></textarea>
				<div class="zpm_editor_toolbar">
					<a href="#" data-command='addCode'><i class='lnr lnr-code'></i></a>
					<a href="#" data-command='createlink'><i class='lnr lnr-link'></i></a>
					<a href="#" data-command='undo'><i class='lnr lnr-undo'></i></a>
				</div>
			</div>
			<div class="zpm_chat_box_footer">

				<button data-project-id="<?php echo $projectId; ?>" id="zpm_project_chat_comment" class="zpm_button"><?php _e( 'Comment', 'zephyr-project-manager' ); ?></button>
			</div>
		</div>
	</div>
</div>