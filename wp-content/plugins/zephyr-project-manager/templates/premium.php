<?php
	/**
	* Premium features page
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
?>

<main id="zpm_welcome_page">
	<div id="zpm_welcome_container" class="zpm_body">
		<h1><?php _e( 'Zephyr Project Manager Pro', 'zephyr-project-manager' ); ?></h1>
		<div id="zpm-welcome-content">
			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-asana.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Asana Integration', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'View your Asana projects and tasks in WordPress and import them into Zephyr to manage all your projects and tasks in one place.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>
			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-custom.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Custom Fields', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'Create more detailed and personalized tasks by creating your own custom fields and assiging them to any tasks.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>
			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-kanban.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Kanban Boards', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'View your tasks in a Kanban board style and drag and drop them into different columns to keep them organized.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-frontend.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Beautiful & Customizable Frontend', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'Includes a beautiful Frontend Project Manager that is customizable and easy to use to allow users or yourself to manage projects from the frontend with the dedicated user interface.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-stats.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Reporting and Advanced Search', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'Create detailed project progress reports for any project and customize which data should be shown in the report. You can then print or save the reports.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-custom.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Task Templates', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'Create reusable and useful templates for your tasks with your custom fields, to add to your tasks in a single click and customize your projects and tasks to your needs.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-ellipsis.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'Mobile App', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'Manage your projects and tasks on the go and stay up to date from anywhere. Increase your productivity now with this beautifully designed Android app and get more work done, wherever you are.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>

			<span id="zpm-mobile-feature" class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/zephyr-tasks-framed.png'; ?>">
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-ellipsis.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title"><?php _e( 'And much more...', 'zephyr-project-manager' ); ?></h3>
					<p class="zpm-feature-description"><?php _e( 'Plus many more features. If you have any feature suggestions I would be more than happy to hear them. You can contact me at dylanjkotze@gmail.com.', 'zephyr-project-manager' ); ?></p>
				</div>
			</span>
		</div>
		<a class="zpm_button" href="https://zephyr-one.com/purchase-pro" target="_blank"><?php _e( 'Get Zephyr Pro Now', 'zephyr-project-manager' ); ?></a>
	</div>
</main>
<?php $this->get_footer(); ?>