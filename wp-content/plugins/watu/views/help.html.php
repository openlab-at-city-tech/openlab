<div class="wrap">
	<h1><?php _e('Watu - Help and Support', 'watu');?></h1>
	
	<div class="postbox-container" style="width:65%;margin-right:2%;">
		<h2><?php _e('Creating Quizzes', 'watu');?></h2>
		
		<p><?php printf(__('Go to Watu Quizzes and click on "Create new %s". Each %s has title, description and various settings. The %s also consists of questions and grades / results.', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD, WATU_QUIZ_WORD);?><br>
		<?php printf(__('Questions are mandatory - you cannot have a %s without at least one question. Grades are optional - for example if you want to use %s as surveys you might not need grades. But the regular %s will have grades because this is the whole purpose of a %s - to calculate a result based on the collected points.', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD_PLURAL, WATU_QUIZ_WORD_PLURAL,WATU_QUIZ_WORD);?><br>
		<?php _e('Make sure to calculate how many points the user could collect if they answer all questions correctly or all wrong so your grades cover both edges of min/max collected points.', 'watu');?></p>
		
		<h2><?php _e('Frequently Asked Questions', 'watu');?></h2>
		
		<p><?php printf(__('Please check them <a href="%s" target="_blank">online</a>.', 'watu'), 'https://wordpress.org/plugins/watu/faq/');?></p>
		
		<h2><?php _e('Shortcodes', 'watu');?></h2>
		<p><?php printf(__('Watu uses a shortcode to publish %s inside a post, page or any other area of your site. There are some other useful shortcodes too:', 'watu'), WATU_QUIZ_WORD_PLURAL);?>
		
		<p><b>[watu-basic-chart]</b> <?php printf(__('Displays chart from answers on a given %s. More details on the Edit Quiz page.', 'watu'), WATU_QUIZ_WORD);?></p>
		<p><b>[watu-takings]</b> <?php printf(__('Displays the table of results on a %s. Get the specific shortcode for each %s from its "View results" page.', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD);?>
		<?php _e('You can use the following shortcode attributes: ', 'watu');?>
			<ul style="margin-left: 3em;">	
			   <li><b>ob</b> <?php printf(__('To define order column. Possible values: %s.', 'watu'),  'points, percent_correct, date');?></li>
			   <li><b>dir</b> <?php printf(__('To define direction of ordering. Possible values: %s.', 'watu'),  'ASC or DESC');?></li>
			   <li><b>num</b> <?php _e('To limit the number of listed records. Useful to create a leaderboard. For a lot more advanced leaderboards check WatuPRO.', 'watu');?></li>
			   <Li><b>show_email=0</b> <?php _e('To hide the email address', 'watu');?></li>
			   <Li><b>show_points=0</b> <?php _e('To hide the points column', 'watu');?></li>
			   <Li><b>show_points=0</b> <?php _e('To hide the percent correct column', 'watu');?></li>
			</ul>		
		</p>
		<p><b>[watu-userinfo]</b> <?php printf(__('Lets you display profile fields from logged in users profile. <a href="%s" target="_blank">Click here</a> for more details and examples.', 'watu'), 'http://blog.calendarscripts.info/user-info-shortcodes-from-watupro-version-4-1-1/');?></p>
		<p><b>[watu-takings quiz_id=X]</b> <?php printf(__('Displays the results table on the selected %s. You can get the exact shortcode on the "View results" page of the %s. The shortcode accepts 3 other parameters that define whether to show the corresponding data: <b>%s, %s, and %s</b>. All of these parameters accept values 0 or 1 and default on all of them is 1 which means the data or column will be shown.', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD, 'show_email', 'show_points', 'show_percent');?></p>
		
		<h2><?php _e('Requesting Help', 'watu');?></h2>
		
		<p><b><?php printf(__('When opening a <a href="%s" target="_blank">support thread</a> please provide URL (link) where we can see your problem.', 'watu'), 'https://wordpress.org/support/plugin/watu');?></b></p>
		
		<h2><?php _e('PRO Inquiries', 'watu');?></h2>
		
		<p><?php printf(__('If you have pre-sales or support questions about WatuPRO please send them using the contact details on the <a href="%s" target="_blank">official WatuPRO site</a>. Do not use the wordpress.org forum for this - it allows only free plugin discussions.', 'watu'), 'http://calendarscripts.info/watupro/support.html');?></p>
		
		<h2><?php _e('MailChimp Integration', 'watu');?></h2>
		
		<p><?php printf(__('You can integrate your %s with MailChimp so when someone completes a chosen %s with a given result, they can be subscribed in a mailing list. You will need <a href="%s" target="_blank">this free bridge</a> to do that.', 'watu'), WATU_QUIZ_WORD_PLURAL, WATU_QUIZ_WORD, 'https://wordpress.org/plugins/watu-bridge-to-mailchimp/');?></p>
	</div>	
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
</div>