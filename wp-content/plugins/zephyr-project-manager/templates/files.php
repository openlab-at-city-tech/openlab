<?php
	/*
	* Files Page
	* Page is used to display and view/download files that have been included in Tasks and Projects
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Base\BaseController;

	$attachments = BaseController::get_attachments();
	$projects = Projects::get_projects();
	$filetypes = array();

	// Get an array of all filetypes that are used
	foreach ($attachments as $attachment) {
		$attachment_url = wp_get_attachment_url($attachment['message']);
		$attachment_type = wp_check_filetype($attachment_url)['ext'];
		if (!in_array($attachment_type, $filetypes)) {
			array_push($filetypes, $attachment_type);
		}
	}
?>

<main id="zpm_file_page" class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<h1 class="zpm_page_title"><?php _e( 'Files', 'zephyr-project-manager' ); ?></h1>
		<?php if (Utillities::canUploadFiles()) : ?>
			<button data-task-id="no-task" id="zpm_upload_file_btn" class="zpm_task_chat_files zpm_button"><?php _e( 'Upload Files', 'zephyr-project-manager' ); ?></button>
		<?php endif; ?>
		<div class="zpm_body">
			<div class="zpm_side_navigation">
				<ul>
					<li data-project-id="-1" class="zpm_filter_file zpm_selected_link"><?php _e( 'All Files', 'zephyr-project-manager' ); ?></li>
					<?php foreach($projects as $project) : ?>
						<li data-project-id="<?php echo $project->id; ?>" class="zpm_filter_file"><?php echo $project->name; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<input type="hidden" id="zpm-new-file-project-value" value="-1">

			<div class="zpm_files_container">
				<?php foreach($attachments as $attachment) : ?>
					<?php

						if (!Utillities::can_view_file( $attachment )) {
							continue;
						}

						$subject_name = __( 'None', 'zephyr-project-manager' );

						if ($attachment['subject'] == 'task') {
							$task = (is_object(Tasks::get_task($attachment['subject_id']))) ? Tasks::get_task($attachment['subject_id']) : false;
							$subject_name = ($task) ? stripslashes($task->name) : __( 'No Task', 'zephyr-project-manager' );
						}
						if ($attachment['subject'] == 'project') {
							$project = (is_object(Projects::get_project($attachment['subject_id']))) ? Projects::get_project($attachment['subject_id']) : false;
							$subject_name = ($project) ? stripslashes($project->name) : __( 'No Project', 'zephyr-project-manager' );
						}

						$project_id = $attachment['subject_id'];
						$attachmentId = $attachment['message'];
						$attachmentDatetime = new DateTime( $attachment['date_created'] );
						$attachmentDate = $attachmentDatetime->format('d M Y H:i');
						$attachmentUrl = wp_get_attachment_url($attachmentId);
						$attachmentType = wp_check_filetype($attachment_url)['ext']; 
						$attachmentName = basename(get_attached_file($attachmentId));

					?>
					<div class="zpm_file_item_container" data-project-id="<?php echo $project_id; ?>">
						<div class="zpm_file_item" data-attachment-id="<?php echo $attachment['id']; ?>" data-attachment-url="<?php echo $attachmentUrl; ?>" data-attachment-name="<?php echo $attachmentName; ?>" data-task-name="<?php  echo $subject_name; ?>" data-attachment-date="<?php echo $attachment_date; ?>">
							<?php if (wp_attachment_is_image($attachmentId)) : ?>
								<!-- If attachment is an image -->
								<div class="zpm_file_preview" data-zpm-action="show_info">
									<span class="zpm_file_image" style="background-image: url(<?php echo $attachmentUrl; ?>);"></span>
								</div>
							<?php else: ?>
								<div class="zpm_file_preview" data-zpm-action="show_info">
									<div class="zpm_file_type"><?php echo '.' . $attachmentType; ?></div>
								</div>
							<?php endif; ?>

							<h4 class="zpm_file_name">
								<?php echo $attachmentName; ?>
								<span class="zpm_file_actions">
									<span class="zpm_file_action lnr lnr-download" data-zpm-action="download_file"></span>
									<span class="zpm_file_action lnr lnr-question-circle" data-zpm-action="show_info"></span>
									<span class="zpm_file_action lnr lnr-trash" data-zpm-action="remove_file"></span>
								</span>
							</h4>
						</div>
					</div>
				<?php endforeach; ?>
				<p id="zpm_no_files" class="zpm_error_message" style="display: none;"><?php _e( 'No Files', 'zephyr-project-manager' ); ?></p>
			</div>
		</div>
	</div>
</main>
<?php $this->get_footer(); ?>