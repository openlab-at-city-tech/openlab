<?php

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	
	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Api\Callbacks\AdminCallbacks;
	use Inc\Base\BaseController;

	$zpm_base = new AdminCallbacks();

	$projects = Projects::get_projects();
	$project = $projects[6];
	$tasks = Tasks::get_project_tasks( 6 );
?>

<div class="zpm_settings_wrap">
	<?php $zpm_base->get_header(); ?>
	<div id="zpm_container" class="zpm-gantt">
		<div id="zephyr-gantt-chart"></div>
		<div class="zpm-gantt-chart">
			<div class="zpm-gantt-tasks">
				<?php foreach ($tasks as $task) : ?>

					<?php
						$subtasks = Tasks::get_subtasks( $task->id );
					?>
					<!-- Load All Tasks -->
					<div class="zpm-gantt-task"><?php echo $task->name; ?>
						
						<?php if ( !empty($subtasks) && sizeof( $subtasks ) > 0) : ?>
							<!-- Load all subtasks -->

							<div class="zpm-gantt-subtask-list">
								<?php foreach ($subtasks as $subtask) : ?>
									<div class="zpm-gantt-subtask">
										<?php echo $subtask->name; ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php $zpm_base->get_footer(); ?>
</div>