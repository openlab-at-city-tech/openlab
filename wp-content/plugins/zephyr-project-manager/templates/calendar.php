<?php 
	/**
	* This is the page for displaying the calender and task dates 
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Projects;
	use Inc\Core\Members;

	$projects = Projects::get_projects();
	$teams = Members::get_teams();
	$members = Members::get_zephyr_members();

	$statusOptions = [
		[
			'name' => __( 'All', 'zephyr-project-manager' ),
			'value' => 'all'
		],
		[
			'name' => __( 'Incompleted', 'zephyr-project-manager' ),
			'value' => '0'
		],
		[
			'name' => __( 'Completed', 'zephyr-project-manager' ),
			'value' => '1'
		]
	];

	$statusOptions = apply_filters( 'zpm_calendar_status_options', $statusOptions );
?>

<main class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<div id="zpm-calendar-actions">
			<?php do_action('zpm_calendar_actions'); ?>
		</div>
		<article class="zpm_body">
			<h3><?php _e( 'Calendar', 'zephyr-project-manager' ); ?></h3>

			<div id="zpm-calendar__filter">

				<div class="zpm-calendar__filter-section">
					<label class="zpm_label"><?php _e( 'Project', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-calendar__filter-project">
						<option value="all"><?php _e( 'All', 'zephyr-project-manager' ); ?></option>
						<?php foreach ( $projects as $project ) : ?>
							<option value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="zpm-calendar__filter-section">
					<label class="zpm_label"><?php _e( 'Team', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-calendar__filter-team">
						<option value="all"><?php _e( 'All', 'zephyr-project-manager' ); ?></option>
						<?php foreach ( $teams as $team ) : ?>
							<option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="zpm-calendar__filter-section">
					<label class="zpm_label"><?php _e( 'Assignee', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-calendar__filter-assignee">
						<option value="all"><?php _e( 'All', 'zephyr-project-manager' ); ?></option>
						<?php foreach ( $members as $member ) : ?>
							<option value="<?php echo $member['id']; ?>"><?php echo $member['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="zpm-calendar__filter-section">
					<label class="zpm_label"><?php _e( 'Status', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-calendar__filter-completed">
						<?php foreach($statusOptions as $option) : ?>
							<option value="<?php echo $option['value']; ?>"><?php echo $option['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div id="zpm-calendar__container" class="zpm_body_panel">
				<div id="zpm_calendar">
					<div class="zpm_task_loader"></div>
				</div>
			</div>
		</article>
	</div>
</main>
<?php $this->get_footer(); ?>