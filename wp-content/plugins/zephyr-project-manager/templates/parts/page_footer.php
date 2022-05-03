<?php 
/**
* Template for displaying the footer of the Zephyr Project Manager pages
*/
if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Core\Utillities;
use Inc\Core\Categories;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager\CustomFields;

?>

<div id="zpm_new_file_upload" class="zpm-modal">
	<h3 class="zpm_modal_header"><?php _e( 'New Attachment', 'zephyr-project-manager' ); ?></h3>
	<input type="hidden" id="zpm_uploaded_file_name">
	<label class="zpm_label"><?php _e( 'Project', 'zephyr-project-manager' ); ?></label>
	<?php Projects::project_select('zpm_file_upload_project'); ?>
	<div class="zpm_modal_footer">
		<button id="zpm_upload_file" class="zpm_button"><?php _e( 'Select File', 'zephyr-project-manager' ); ?></button>
		<button id="zpm_submit_file" class="zpm_button"><?php _e( 'Upload Attachment', 'zephyr-project-manager' ); ?></button>
	</div>
</div>
<?php Tasks::new_task_modal(); ?>
<?php Tasks::view_container(); ?>
<?php Projects::project_modal(); ?>
<?php Categories::new_category_modal(); ?>
<?php Categories::new_status_modal(); ?>
<?php Categories::edit_status_modal(); ?>
<?php if (BaseController::is_pro()) : ?>
	<?php CustomFields::task_custom_fields(); ?>
<?php endif; ?>

<?php Projects::view_project_container(); ?>

<div id="zpm-task-to-project-modal" class="zpm-modal">
	<h3 class="zpm-modal-title"><?php _e( 'Copy to Project', 'zephyr-project-manager' ); ?></h3>

	<div class="zpm-modal-content">
		<input type="hidden" id="zpm-kanban-to-project-task-id" />
		<input type="hidden" id="zpm-kanban-to-project-task-name" />
		<?php Projects::project_select( 'zpm-kanban-to-project-id' ); ?>
	</div>

	<div class="zpm-modal-buttons">
		<button id="zpm-kanban-copy-task-to-project" class="zpm_button"><?php _e( 'Copy Task', 'zephyr-project-manager' ); ?></button>
	</div>
</div>

<div id="zpm-column-to-project-modal" class="zpm-modal">
	<h3 class="zpm-modal-title"><?php _e( 'Copy Column to Project', 'zephyr-project-manager' ); ?></h3>

	<input type="hidden" id="zpm-kanban-to-project__project-id" />

	<div class="zpm-modal-content">
		<input type="hidden" id="zpm-kanban-column-to-project-task-id" />
		<input type="hidden" id="zpm-kanban-column-to-project-task-name" />
		<?php Projects::project_select( 'zpm-kanban-column-to-project-id' ); ?>
	</div>

	<div class="zpm-modal-buttons">
		<button id="zpm-kanban-column-to-project-btn" class="zpm_button"><?php _e( 'Copy Column', 'zephyr-project-manager' ); ?></button>
	</div>
</div>

<?php
	Utillities::zephyr_modal( 
		'zpm-task-attachments-modal', 
		__( 'Task Attachments', 'zephyr-project-manager' ),
		'', 
		array(
			array(
				'id' => 'zpm-task-attachments__close-btn',
				'text' => __( 'Close', 'zephyr-project-manager' )
			)
		)
	);
?>


<?php do_action( 'zpm_modals' ); ?>