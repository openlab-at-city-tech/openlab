<?php
	/**
	* Activity Page
	* This page shows all user activity and project/task activity for each day
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Activity;
	use Inc\Core\Projects;
	use Inc\Base\BaseController;
	use Inc\Api\Emails;

	$BaseController = new BaseController;
	$attachments = Tasks::get_attachments();
	$activities = Activity::get_activities( array( 'limit' => 10, 'offset' => 0 ) );
?>

<main id="zpm_activity_page" class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<div id="zpm_activity_body" class="zpm_body">
			<?php $activities = Activity::display_activities($activities); ?>
			<?php if (!$activities) : ?>
				<div class="zpm_no_results_message"><?php _e('There is no activity yet. Once there is, the activities will be displayed here.', 'zephyr-project-manager'); ?></div>
				<?php exit; ?>
			<?php else: ?>
				<?php echo $activities; ?>
			<?php endif; ?>
		</div>
		<button id="zpm_load_activities" class="zpm_button" data-offset="1"><?php _e('Load More', 'zephyr-project-manager'); ?></button>
	</div>
</main>
<?php $this->get_footer(); ?>