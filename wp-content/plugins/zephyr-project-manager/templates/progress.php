<?php
	/**
	* Template for displaying the Progress page
	*
	* @package ZephyrProjectManager
	*
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	use Inc\Core\Projects;
?>

<main class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<h1 class="zpm_page_title"><?php _e( 'Progress', 'zephyr-project-manager' ); ?></h1>
		<div class="zpm_panel_container">
			<div class="zpm_body zpm_category_display">
				<div>
					<p class="zpm_instructions"><?php _e( 'Select a project to display the progress for.', 'zephyr-project-manager' ); ?></p>
					<div class="zpm_progress_project_select">
						<?php Projects::project_select('zpm_project_progress_select'); ?>
					</div>
					<canvas class="zpm_report_chart" id="zpm_project_progress_chart" width="400" height="200"></canvas>
				</div>
			</div>
		</div>
	</div>
</main>
<?php $this->get_footer(); ?>