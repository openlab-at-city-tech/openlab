<?php
	/**
	* Email template for new task notifications
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	use \DateTime;
	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Base\BaseController;

	$tasks = $subject_id;
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
			.email_task {
			    border: 1px solid #eee;
			    padding: 10px;
			    margin-bottom: 5px;
			}
			#zpm_after_email {
				text-align: center;
			}
			.email_task_date {
			    float: right;
			}
			#zpm_email_container {
				min-height: 200px;   
			}
			.zpm_email_header {
				margin: 10px;
				margin-bottom: 0px;
				font-size: 18px;
				font-weight: normal;
			}
	    </style>
	</head>
    <body>
		<div id="zpm_email_container">
			
			<div id="zpm_email_title">
				<h2 class="zpm_email_header"><?php _e( 'Due Tasks', 'zephyr-project-manager' ); ?></h2>
			</div>

			<div id="zpm_email_body">
				<?php foreach ($tasks as $task) : 
					$due_date = new DateTime($task->date_due);
					?>
					<div class="email_task"><?php echo stripslashes($task->name); ?><span class="email_task_date"><?php echo $due_date->format('d M'); ?></span></div>
				<?php endforeach; ?>
			</div>

		<div id="zpm_after_email">
			<a class="zpm_after_email_link" href=""><?php _e( 'View Tasks in WordPress', 'zephyr-project-manager' ); ?></a> | 
			<a class="zpm_after_email_link" href=""><?php _e( 'Unfollow', 'zephyr-project-manager' ); ?></a>
		</div>
	</body>
</html>