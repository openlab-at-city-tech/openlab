<?php
	/**
	* Email template for new task notifications
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Base\BaseController;

	$project_count = Projects::project_count();
	$completed_projects = Projects::completed_project_count();
	$pending_projects = $project_count - $completed_projects;

	$project = Projects::get_project($project_id);
	// $project_id = $project->id;
	// $task_count = Tasks::get_project_task_count($project_id);
	// $completed_tasks = Tasks::get_project_completed_tasks($project_id);
	// $args = array( 'project_id' => $project_id );
	// $overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
	// $pending_tasks = $task_count - $completed_tasks;
	// $percent_complete = ($task_count !== 0) ? floor($completed_tasks / $task_count * 100) : '100';
	// $chart_data = get_option('zpm_chart_data', array());
	// $response = array(
	// 	'chart_data' => $chart_data[$project_id]
	// );
?>

<!DOCTYPE HTML>
<html>
	<head>
	    <style>
		    #zpm_email_footer {
				padding: 10px;
				position: absolute; 
				bottom: 0;
				border-top: 1px solid #eee;
				background: #f7f7f7;
				width: 100%;
				box-sizing: border-box;
				text-align: right;
			}
			.zpm_email_link {
				cursor: pointer;
			}
			#zpm_action_button {
				text-shadow: none;
				border: none;
				box-shadow: none;
				background: #14aaf5;
				margin: 0 4px;
				transition: all .2s ease-in-out;
				transform: translateY(0) !important;
				user-select: none; 
				color: #fff;
				padding: 5px 10px;
				border-radius: 2px;
				cursor: pointer;
			}
			#zpm_after_email {
				width: 500px;
				margin: 10px auto;
				box-sizing: border-box;
			}
			.zpm_after_email_link {
				color: #14aaf5;
				text-decoration: none;
			}
			#zpm_after_email_extra_info {
				display: block;
				margin-top: 7px;
				color: #777;
			}
			#zpm_email_container {
				width:500px;
				min-height:300px;
				border-radius:2px;
				border:1px solid #ddd;
				margin:0 auto;
				box-sizing: border-box;
				position:relative;
			}
			#zpm_email_header {
				padding: 10px; 
				width: 100%; 
				border-bottom: 1px solid #ddd;
				box-sizing: border-box;
				border-bottom: 1px solid #f7f7f7;
			}
			#zpm_email_title {
				padding: 10px;
				font-size: 18px;
				font-weight: 500;
			}
			#zpm_email_byline {
				font-size: 12px;
				color: #999;
				display: block;
			}
			#zpm_email_body {
				padding: 10px;
				height: 255px;
				max-height: 255px;
				overflow-y: auto;
				word-break: break-word;
			}
			.zpm_email_row {
				padding: 10px;
			}
			.zpm_email_row_label {
				width: 100px;display: inline-block;
			}
			.zpm_email_description {
				margin-top: 10px;
			}
			.task_item {
				display: inline-block;
				width: 30%;
			}
			.tasks_section {
				text-align: center;
			}
			.task_count {
				font-size: 20px;   
				margin-bottom: 10px;
			}
	    </style>
	</head>
    <body>
		<div id="zpm_email_container">
			
			<div id="zpm_email_title">
				<h2 class="zpm_email_header"><?php _e( 'Weekly Progress Update', 'zephyr-project-manager' ); ?></h2>
			</div>

			<div id="zpm_email_body">
					<span class="task_item">
						<div class="task_count"><?php echo $project_count; ?></div>
						<div class="task_subject"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></div>
					</span>
					<span class="task_item">
						<div class="task_count"><?php echo $completed_projects; ?></div>
						<div class="task_subject"><?php _e( 'Completed Projects', 'zephyr-project-manager' ); ?></div>
					</span>
					<span class="task_item">
						<div class="task_count"><?php echo $pending_projects; ?></div>
						<div class="task_subject"><?php _e( 'Pending Projects', 'zephyr-project-manager' ); ?></div>
					</span>
				</div>
			</div>

			<div id="zpm_email_footer">
				<a href="" class="zpm_email_link">
					<button id="zpm_action_button"><?php _e( 'View Projects', 'zephyr-project-manager' ); ?></button>
				</a>
			</div>
		</div>

		<div id="zpm_after_email">
			<a class="zpm_after_email_link" href=""><?php _e( 'View Projects in WordPress', 'zephyr-project-manager' ); ?></a> | 
			<a class="zpm_after_email_link" href=""><?php _e( 'Unfollow', 'zephyr-project-manager' ); ?></a>
		</div>
	</body>
</html>