<?php
	/**
	* Email Notification template
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
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
				border-radius:2px;
				border:1px solid #ddd;
				margin:0 auto;
				box-sizing: border-box;
				position:relative;
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.00), 0 1px 2px rgba(0, 0, 0, 0.07);
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
				width: 24%;
				vertical-align: top;
			}
			.tasks_section {
				margin-top: 10%;
				text-align: center;
			}
			.task_count {
				font-size: 30px;   
				margin-bottom: 10px;
			}
			.task_subject {
				font-size: 15px;
				color: #ccc;
			}
			.email_task {
				padding: 10px 0;
    			border-bottom: 1px solid #eee;
			}
			.email_task_date {
				float: right;
				color: #3ed8a1;
			}
			.email_task_date.overdue {
				color: #ef8181;
			}
	    </style>
	</head>
    <body style="background: #f4f4f4; padding: 50px;">
		<div id="zpm_email_container" style="background: #fff; height: max-content; min-height: none;">
			
			<div id="zpm_email_body" style="padding-bottom: 100px !important; padding: 50px; height: max-content; min-height: none;">
				<div id="zpm_email_title" style="padding: 0;">
					<h2 class="zpm_email_header" style="margin-top: 0;"><?php echo $header; ?></h2>
				</div>
				<?php echo $body ?>
			</div>

			<div id="zpm_email_footer" style="padding: 20px;">
				<?php echo $footer; ?>
			</div>
		</div>

		<div id="zpm_after_email" style="text-align: center;">
			<a class="zpm_after_email_link" href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_settings')); ?>"><?php _e( 'Unfollow', 'zephyr-project-manager' ); ?></a>
		</div>
	</body>
</html>