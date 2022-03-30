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

	$task = Tasks::get_task($subject_id);
	$task_project = Projects::get_project($task->project);
	$task->project_name = is_object($task_project) ? $task_project->name : '';
	$url = admin_url('admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=' . $task->id);
	$assignee_data = BaseController::get_project_manager_user($task->assignee);
	$creator = BaseController::get_project_manager_user($task->user_id);
	$task->assignee_name = $assignee_data['name'];
	$task->assignee_email = $assignee_data['email'];
	$subject = stripslashes($task->name);
	$date = date('d M Y H:i');

    $message_data = array(
    	'heading' 		=> __( 'New Task', 'zephyr-project-manager' ),
    	'description'	=> $task->description,
    	'assignee'		=> $task->assignee_name,
    	'project'		=> $task->project_name,
    	'due_date'		=> $task->date_due,
    	'created_by'	=> $creator['name'],
    	'date'			=> $date,
    	'url'			=> $url
    );
?>

<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
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
	    </style>
	</head>
    <body>
		<div id="zpm_email_container">
			
			<div id="zpm_email_title">
				<?php echo $subject; ?>
				<?php


				?>
				<span id="zpm_email_byline"> - <?php printf( __e( 'Created by %s on $s', 'zephyr-project-manager' ), $message_data['created_by'], $message_data['date'] ); ?></span>
			</div>

			<div id="zpm_email_body">
				<div class="zpm_email_row">
					<span class="zpm_email_row_label"><?php _e( 'Assignee', 'zephyr-project-manager' ); ?>: </span>
					<?php echo $message_data['assignee']; ?>
				</div>

				<div class="zpm_email_row">
					<span class="zpm_email_row_label"><?php _e( 'Project', 'zephyr-project-manager' ); ?>: </span>
					<?php echo $message_data['project']; ?>
				</div>

				<div class="zpm_email_row">
					<span class="zpm_email_row_label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?>: </span>
					<?php echo $message_data['due_date']; ?>
				</div>

				<div class="zpm_email_row">
					<span class="zpm_email_row_label"><?php _e( 'Description', 'zephyr-project-manager' ); ?>: </span>
					<div class="zpm_email_description">
						<?php echo $message_data['description'] ;?>
					</div>
				</div>
			</div>

			<div id="zpm_email_footer">
				<a href="<?php echo $message_data['url']; ?>" class="zpm_email_link">
					<button id="zpm_action_button"><?php _e( 'Go to Task', 'zephyr-project-manager' ); ?></button>
				</a>
			</div>
		</div>

		<div id="zpm_after_email">
			<a class="zpm_after_email_link" href="<?php echo $message_data['url']; ?>"><?php _e( 'View Task in WordPress', 'zephyr-project-manager' ); ?></a> | 
			<a class="zpm_after_email_link" href="<?php echo $message_data['url']; ?>"><?php _e( 'Unfollow', 'zephyr-project-manager' ); ?></a>

			
			<span id="zpm_after_email_extra_info"><?php printf( __( 'Reply with %s to mark as complete | %s to comment', 'zephyr-project-manager'), '<b>complete</b>', '<b>comment</b>' ); ?></span>
		</div>
	</body>
</html>