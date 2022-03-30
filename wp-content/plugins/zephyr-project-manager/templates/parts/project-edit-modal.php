<?php 
	/**
	* Template for displaying the Edit Project modal
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
	$project = isset($projectId) ? Projects::get_project($projectId) : false;
?>

<div id="zpm_edit_task" class="zpm-modal <?php echo $extra_classes; ?>">
	<h5 class="zpm_modal_header"><?php _e( 'Edit Project', 'zephyr-project-manager' ); ?></h5>
	
	<div class="zpm_modal_body">
		<div class="zpm_modal_content">
			<input type="hidden" data-ajax-name="project-id" value="<?php echo $projectId; ?>" />
			<div class="zpm-form__group">
				<input type="text" name="zpm_edit_project_name" id="zpm_edit_project_name" class="zpm-form__field" placeholder="<?php _e( 'Project Name', 'zephyr-project-manager' ); ?>" value="<?php echo $project->name; ?>" data-ajax-name="name">
				<label for="zpm_edit_project_name" class="zpm-form__label"><?php _e( 'Project Name', 'zephyr-project-manager' ); ?></label>
			</div>

			<div class="zpm-form__group">
				<textarea name="zpm_edit_project_description" id="zpm_edit_project_description" class="zpm-form__field" placeholder="<?php _e( 'Project Description', 'zephyr-project-manager' ); ?>" data-ajax-name="description"><?php echo $project->description; ?></textarea>
				<label for="zpm_edit_project_description" class="zpm-form__label"><?php _e( 'Project Description', 'zephyr-project-manager' ); ?></label>
			</div>

			<div class="zpm-form__group">
				<input type="text" name="zpm_edit_project_start_date" id="zpm_edit_project_start_date" class="zpm-form__field" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $project->date_start; ?>" data-ajax-name="start-date">
				<label for="zpm_edit_project_start_date" class="zpm-form__label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
			</div>

			<div class="zpm-form__group">
				<input type="text" name="zpm_edit_project_due_date" id="zpm_edit_project_due_date" class="zpm-form__field" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $project->date_due; ?>" data-ajax-name="due-date">
				<label for="zpm_edit_project_due_date" class="zpm-form__label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
			</div>
		</div>

		<div class="zpm_modal_buttons">

			<button id="zpm-update-project__btn" class="zpm_button"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
		</div>
	</div>
</div>