<?php
	/**
	* Dashboard Page
	* Allows users to view project information and upcoming tasks at a glance
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;

	$notice_version = '1.5';
	$user_id = get_current_user_id();
	$settings = Utillities::get_user_settings( $user_id );
	$project_count = Projects::project_count();
	$completed_projects = Projects::completed_project_count();
	$active_projects = $project_count - $completed_projects;
	$task_count = Tasks::get_task_count();
	$completed_tasks = sizeof( Tasks::get_tasks( array( 'completed' => '1' ) ) );
	$active_tasks = $task_count - $completed_tasks;
	$args = array(
		'limit' => 5,
		'assignee' => get_current_user_id()
	);
	$my_tasks = Tasks::get_tasks($args);
	$week_tasks = Tasks::get_week_tasks(get_current_user_id());
	$args = array(
		'assignee' => get_current_user_id()
	);
	$overdue_tasks = Tasks::get_overdue_tasks($args);


?>

<div class="zpm_settings_wrap">
	<?php if (!get_option('zpm_first_time')) : ?>
		<?php include('welcome.php'); ?>
	<?php elseif ($this->is_pro() && !get_option('zpm_welcome_pro')) : ?>
		<?php include( ZEPHYR_PRO_PLUGIN_PATH . 'views/welcome.php'); ?>
	<?php else: ?>
		<?php $this->get_header(); ?>

		<div id="zpm_container">
			<h1 class="zpm_page_title"><?php _e( 'Help', 'zephyr-project-manager' ); ?></h1>
			<div class="zpm_panel_container">

				<div class="zpm-grid-container">
					<div class="zpm-grid-row zpm-grid-row-12">
						<div class="zpm-grid-item zpm-grid-item-3">
							<a href="https://zephyr-one.com/documentation/introduction" target="_blank" class="zpm-material-card zpm-material-card-colored zpm-card-color-darker-blue">
								<h4 class="zpm-card-header"><?php _e( 'Documentation', 'zephyr-project-manager' ); ?></h4>
								<p class="zpm-card__description"><?php _e( 'Click here to view the in-depth documentation on all the features and how to use and make the best out of Zephyr Project Manager.', 'zephyr-project-manager' ); ?></p>
							</a>
							
						</div>
						<div class="zpm-grid-item zpm-grid-item-3">
							<a href="https://zephyr-one.com/" target="_blank" class="zpm-material-card zpm-material-card-colored zpm-card-color-darker-blue">
								<h4 class="zpm-card-header"><?php _e( 'Support / Contact', 'zephyr-project-manager' ); ?></h4>
								<p class="zpm-card__description"><?php _e( 'Click here to go to the website where you can contact me and ask any questions or requests that you might have. You can also contact me at dylanjkotze@gmail.com at any time and I will respond as soon as possible to help as best I can :)', 'zephyr-project-manager' ); ?></p>
							</a>
						</div>
						<div class="zpm-grid-item zpm-grid-item-3">
							<a href="https://zephyr-one.com/" target="_blank" class="zpm-material-card zpm-material-card-colored zpm-card-color-darker-blue">
								<h4 class="zpm-card-header"><?php _e( 'Feature Requests & Bugs', 'zephyr-project-manager' ); ?></h4>
								<p class="zpm-card__description"><?php _e( 'Have any great ideas for new features to add or are there features you would like me to implement for you? I am always happy to help with this and you can contact me dylanjkotze@gmail.com at any time and I will respond as soon as possible to assist you further :)', 'zephyr-project-manager' ); ?></p>
							</a>
						</div>
					</div>

					<div class="zpm-grid-row zpm-grid-row-12">
						<div class="zpm-grid-item zpm-grid-item-3">
							<a href="https://zephyr-one.com/purchase-pro/" target="_blank" class="zpm-material-card zpm-material-card-colored zpm-card-color-darker-blue">
								<h4 class="zpm-card-header"><?php _e( 'Get Zephyr Project Manager Premium', 'zephyr-project-manager' ); ?></h4>
								<p class="zpm-card__description"><?php _e( 'Click here to look at what the Premium add-on has to offer. Including many new features such as a fully customizable Frontend Project Manager, Custom Fields, Kanban Editor, Asana Integration and more!', 'zephyr-project-manager' ); ?></p>
							</a>
							
						</div>
						<div class="zpm-grid-item zpm-grid-item-3">
							<a href="https://www.patreon.com/dylanjkotze" target="_blank" class="zpm-material-card zpm-material-card-colored zpm-card-color-darker-blue">
								<h4 class="zpm-card-header"><?php _e( 'Donate and Support Me', 'zephyr-project-manager' ); ?></h4>
								<p class="zpm-card__description"><?php _e( 'If you like the plugin and would like to support me to continue adding great new features and improvements, please consider supporting me on Patreon. It would truly mean so much to me!', 'zephyr-project-manager' ); ?></p>
							</a>
						</div>
						<div class="zpm-grid-item zpm-grid-item-3">
							<a href="https://wordpress.org/support/plugin/zephyr-project-manager/reviews/#new-post" target="_blank" class="zpm-material-card zpm-material-card-colored zpm-card-color-darker-blue">
								<h4 class="zpm-card-header"><?php _e( 'Leave a Review', 'zephyr-project-manager' ); ?></h4>
								<p class="zpm-card__description"><?php _e( 'If you enjoy using the plugin and have two minutes to spare, please consider leaving a review here - it would truly mean the world to me!', 'zephyr-project-manager' ); ?></p>
							</a>
						</div>
					</div>
				</div>

				<h1 class="zpm_page_title"><?php _e( 'More Zephyr Plugins', 'zephyr-project-manager' ); ?></h1>
				<div class="zpm-grid-container">
					<div class="zpm-grid-row zpm-grid-row-12">
						<a href="https://wordpress.org/plugins/zephyr-modern-admin-theme/" target="_blank" class="zephyr-plugin-card zpm-material-card zpm-material-card-colored">
							<h3 class="zephyr-plugin-card__header"><?php _e( 'Zephyr Modern Admin Theme', 'zephyr-project-manager' ); ?></h3>
							<p class="zephyr-plugin-card__description">
								<?php _e( 'Zephyr Admin Theme allows you to visually transform and reimagine your WordPress dashboard into a completely modernized version. It also includes custom colors selection, beautiful predefined themes, a dark and light mode, login screen customization and much more! Click here to view the WordPress page and give it a try :)', 'zephyr-project-manager' ); ?>
							</p>
						</a>
					</div>
				</div>

				

				<!-- Display Patreon Notice -->
				<?php if ( !Utillities::notice_is_dismissed( 'zpm-patreon-notice' ) ) : ?>
					<div id="zpm-whats-new" class="zpm-panel zpm-panel-12" data-notice="'zpm-patreon-notice'">
						<h4 class="zpm_panel_title"><?php _e( 'Support me on Patreon', 'zephyr-project-manager' ); ?></h4>
						<p><?php _e( 'If you like the plugin and what I do and would like to help me improve the plugin more, please consider supporting me on Patreon. This would help a lot in being able to work on the plugin full-time and focus more on it to make it better and add new features. Thank you so much.', 'zephyr-project-manager' ); ?></p>
						<div class="zpm-notice-buttons">
							
							<button class="zpm-dismiss-notice-button zpm_button" data-notice-version="zpm-patreon-notice"><?php _e( 'Dismiss Notice', 'zephyr-project-manager' ); ?></button>
							<a href="https://www.patreon.com/dylanjkotze" target="_blank" class="zpm-patreon-button zpm_button"><?php _e( 'Support me on Patreon', 'zephyr-project-manager' ); ?></a>
						</div>
					</div>
				<?php endif; ?>

			</div>

		</div>
	<?php endif; ?>
</div>
<?php $this->get_footer(); ?>