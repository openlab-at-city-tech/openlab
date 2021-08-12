<script type="text/javascript">
	function validate() {
		var ret= true;
		return ret;
	}
</script>

<div class="wrap">
<h2><?php printf(__('%1$s %2$s', 'watu'), ucfirst($action), ucfirst(WATU_QUIZ_WORD)); ?></h2>

<div class="postbox-container" style="min-width:60%;max-width:70%;margin-right:2%;">	

	<p><a href="admin.php?page=watu_exams"><?php printf(__('Back to %s', 'watu'), WATU_QUIZ_WORD_PLURAL)?></a>
	<?php if(!empty($dquiz->ID)):?>
		| <a href="admin.php?page=watu_questions&quiz=<?php echo $dquiz->ID?>"><?php _e('Manage Questions', 'watu')?></a>
		| <a href="admin.php?page=watu_grades&quiz_id=<?php echo $dquiz->ID?>"><?php _e('Manage Grades / Results', 'watu')?></a>
	<?php endif;?>	
	</p>
	
	<form name="post" action="admin.php?page=watu_exam" method="post" id="post" onsubmit="return validate()">
	<div>	
	<div class="postbox wrap" id="titlediv">
		<h3>&nbsp;<?php printf(__('%s Name and Settings', 'watu'), ucfirst(WATU_QUIZ_WORD))?></h3>
		<div class="inside wrap">
			<input type='text' name='name' id="title" value='<?php echo stripslashes(@$dquiz->name); ?>' />
		</div>
		<div class="inside">
			<?php if(!$is_published):?>
			 	<p><input type="checkbox" name="auto_publish" value="1"> <?php printf(__('Automatically publish this %s in new post once I hit the "Save" button. (The new post will be auto-generated with the %s title used for post title.)', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD);?></p>
			 <?php endif;?>		
			<p><input id="watuRandomize" type="checkbox" name="randomize" value="1" <?php if(!empty($dquiz->randomize)) echo 'checked'?>> <label for="watuRandomize"><?php _e("Randomize questions", 'watu');?></label> <br> <?php _e('and pull random', 'watu')?>
			<input type="text" name="pull_random" value="<?php echo intval(@$dquiz->pull_random)?>" size="4" onkeyup="if(this.value > 0) { jQuery('#watuRandomize').attr('checked', true);}"> <?php printf(__('questions from the %s. Leave 0 to pull all the questions (default behavior).','watu'), WATU_QUIZ_WORD)?></p>
			
			<p><input id="watuRandomize" type="checkbox" name="randomize_answers" value="1" <?php if(!empty($dquiz->randomize_answers)) echo 'checked'?>> <label for="watuRandomize"><?php _e("Randomize answers", 'watu');?></label></p>
			
			<p><?php printf(__('Note: randomization will not work well if you are caching the page where your %s is published!', 'watu'), WATU_QUIZ_WORD)?></p>
			
			<p><input id="watuSingle" type="checkbox" name="single_page" value="1" <?php if(!empty($dquiz->single_page)) echo 'checked'?> onclick="this.checked ? jQuery('#watuPrevButton').hide() : jQuery('#watuPrevButton').show();"> <label for="watuSingle"><?php _e("Show all questions on single page", 'watu');?></label></p>
			
			<p style='display:<?php echo empty($dquiz->single_page) ? 'block' : 'none';?>' id="watuPrevButton"><input id="watuPrev" type="checkbox" name="show_prev_button" value="1" <?php if(!empty($dquiz->show_prev_button)) echo 'checked'?>> <label for="watuPrev"><?php _e("Show 'previous question' button", 'watu');?></label></p>
			
			<p><input type="checkbox" name="require_login" value="1" <?php if(!empty($dquiz->require_login)) echo 'checked'?> id="watuReqLogin" onclick="this.checked ? jQuery('#watuTakeAgain').show() : jQuery('#watuTakeAgain').hide();"> <label for="watuReqLogin"><?php _e('Require user login (displays login and / or register link depending on your blog settings.)', 'watu')?></label></p>
			
			 <p id="watuTakeAgain" style="margin-left:50px;display:<?php echo empty($dquiz->require_login) ? 'none': 'block';?>"><input type="checkbox" name="take_again" value="1" <?php if(!empty($dquiz->take_again)) echo "checked"?> onclick="this.checked?jQuery('#timesToTake').show():jQuery('#timesToTake').hide();"> <?php printf(__('Allow users to submit the %s multiple times:', 'watu'), __('quiz', 'watu'))?> 
	        		<div id='timesToTake' style='margin-left:50px;<?php if(empty($dquiz->take_again)) echo 'display:none;'?>'>
	        			<?php _e('Allow', 'watu')?> <input type="text" size="4" name="times_to_take" value="<?php echo @$dquiz->times_to_take?>"> <?php _e('times (For unlimited times enter 0) There is even more flexibility in WatuPRO.', 'watu')?></div></p>
			
			<p><input type="checkbox" name="notify_admin" value="1" <?php if(!empty($dquiz->notify_admin)) echo 'checked'?> id="watuNotifyAdmin" onclick="this.checked ? jQuery('#notifyEmails').show() : jQuery('#notifyEmails').hide();"> <label for="watuNotifyAdmin"><?php printf(__('Notify me when someone takes this %s (the email goes to the address given in your WordPress Settings page).', 'watu'), WATU_QUIZ_WORD)?></label>
			<div id="notifyEmails" style='display:<?php echo empty($dquiz->notify_admin) ? 'none' : 'block';?>;'>
				<label><?php _e('Email address(es) to notify:', 'watu');?></label> <input type="text" name="notify_email" size="30" value="<?php echo empty($dquiz->notify_email) ? get_option('admin_email') : $dquiz->notify_email;?>"> <?php _e('You can specify multiple email addresses separated with comma.', 'watu');?>
			</div></p>
			<p><input type="checkbox" name="notify_user" value="1" <?php if(!empty($dquiz->notify_user)) echo 'checked'?> id="watuNotifyUser"> <label for="watuNotifyUser"><?php printf(__('Send email to the %s taker (if taker is non-logged in this will generate a required email field on the %s page).', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD)?></label></p>
			
			<p><input type="checkbox" name="dont_store_data" value="1" <?php if(!empty($dquiz->dont_store_data)) echo 'checked'?> id="watuDontStoreData"> <label for="watuDontStoreData"><?php _e('Do not store user answers in the database to save DB space.', 'watu')?></label></p>
			
			<p><input type="checkbox" name="dont_display_question_numbers" value="1" <?php if(!empty($dquiz->dont_display_question_numbers)) echo 'checked'?> id="watuDontDisplayNumbers"> <label for="watuDontDisplayNumbers"><?php _e('Do not display question numbers.', 'watu')?></label></p>
			
			<p><input type="checkbox" name="no_alert_unanswered" value="1" <?php if(!empty($dquiz->no_alert_unanswered)) echo 'checked'?> id="watuNoAlertUnanswered"> <label for="watuNoAlertUnanswered"><?php _e('Do not alert the user when skipping a non-required question.', 'watu')?></label></p>
				
			<p><input type="checkbox" name="save_source_url" <?php if(!empty($dquiz->save_source_url)) echo 'checked'?>> <?php printf(__('Save source URL when submitting the %s (useful if you have published the %s in multiple places on your site).', 'watu'), WATU_QUIZ_WORD, WATU_QUIZ_WORD);?> </p>	
			
			  <p><?php _e('Value of the submit button:', 'watu');?> <input type="text" name="submit_button_value" size="10" value="<?php echo empty($advanced_settings['submit_button_value']) ? __('Submit', 'watu') : $advanced_settings['submit_button_value'];?>"></p>
			  
			  <p><?php _e('Design theme:', 'watu');?> <select name="design_theme">
			  	  <option value="default"><?php _e('Default', 'watu');?></option>
				  <option value="buttons" <?php if(!empty($advanced_settings['design_theme']) and $advanced_settings['design_theme'] == 'buttons') echo 'selected';?>><?php _e('Modern / buttons', 'watu');?></option>
			  </select></p>
			
				<h3><span><?php _e('Anti-Spam Measures', 'watu'); ?></span></h3>
			 <p><input type="checkbox" name="require_text_captcha" value="1" <?php if(!empty($dquiz->require_text_captcha)) echo "checked"?>> <?php printf(__('Require question based captcha to submit the %s', 'watu'), WATU_QUIZ_WORD);?></p>
			 		<p><input type="checkbox" name="use_honeypot" value="1" <?php if(!empty($dquiz->use_honeypot)) echo "checked"?>> <?php _e('Use a "honeypot" field (It is almost as efficient as captchas but is unobtrusive and does not require any action from the user)', 'watu');?></p>	
			 		
			 <?php if(get_option('watu_integrate_moolamojo') == 1):?>
			 	<h3><span><?php _e('MoolaMojo Integration', 'watu');?></span></h3>
			 	<p><input type="checkbox" name="transfer_moola" value="1" <?php if(!empty($advanced_settings['transfer_moola'])) echo 'checked'?> onclick="this.checked ? jQuery('#moolaMojoOptions').show() : jQuery('#moolaMojoOptions').hide();"> <?php printf(__('Transfer points from this %s as virtual <a href="%s" target="_blank">MoolaMojo</a> credits.', 'watu'), WATU_QUIZ_WORD, 'https://moolamojo.com');?>
					<div id="moolaMojoOptions" style='padding-left:25px;display:<?php echo empty($advanced_settings['transfer_moola']) ? 'none' : 'block';?>'>
						<input type="radio" name="transfer_moola_mode" value="equal" <?php if(empty($advanced_settings['transfer_moola_mode']) or $advanced_settings['transfer_moola_mode'] == 'equal') echo 'checked'?>> <?php _e('Transfer the earned points as MoolaMojo credits 1:1 (if negative points are earned, the user will be charged credits)', 'watu');?> <br />
						<input type="radio" name="transfer_moola_mode" value="grades" <?php if(!empty($advanced_settings['transfer_moola_mode']) and $advanced_settings['transfer_moola_mode'] == 'grades') echo 'checked'?>> <?php _e('Transfer fixed amount of points based on the grade/result achieved (A field will appear for each grade/result on your Grades page to enter amount of virtual credits to be transferred)', 'watu');?>
					</div>			
				</p>
			 <?php endif;?>		
		</div>
	</div>
	
	<div class="postbox">
		<h3>&nbsp;<?php _e('Correct Answer Display', 'watu') ?></h3>
		<div class="inside">				
			<input type="checkbox" name="show_answers" <?php if($answer_display == '2') echo 'checked="checked"'; ?> value="2" id="show-between" /> <label for="show-between"><?php _e("Show the answer of a question immediately after the user have selected an answer. (Will not work in single page mode and <b>is not secure</b> - use it only for fun quizzes, not exams. You can handle exams in this mode with WatuPRO.)", 'watu') ?></label><br />
			<label for="show-end"><?php _e('"<b>Show answers at the end of the Quiz" is now obsolete option.</b> Instead of using it, enter the %%ANSWERS%% variable where you want the results to appear.', 'watu') ?></label><br />
		</div>
	</div>
	
	<div class="postbox">
	<h3>&nbsp;<?php _e('Description', 'watu') ?></h3>
	<div class="inside">
	<textarea name='description' rows='5' cols='50' style='width:100%'><?php echo stripslashes(@$dquiz->description); ?></textarea>
	<p><?php printf(__('If provided, description shows on top of the %s. It can optionally be included in the final output as well.', 'watu'), WATU_QUIZ_WORD)?></p>
	</div></div>
	
	
	<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea postbox">
	<h3>&nbsp;<?php _e('Final Screen', 'watu') ?></h3>
	<div class="inside">
	<?php wp_editor($final_screen, 'content'); ?>
	
	<p><input type="checkbox" value="1" name="different_email_output" <?php if(!empty($dquiz->email_output)) echo 'checked'?> onclick="this.checked ? jQuery('#watuEmailOutput').show() : jQuery('#watuEmailOutput').hide();"> <?php _e('Send different content to the notification email sent to user / admin.', 'watu');?></p>
	
	<div id="watuEmailOutput" style='display:<?php echo empty($dquiz->email_output) ? 'none' : 'block';?>'>
		<p><label><?php _e('Email subject:', 'watu');?></label> <textarea name="email_subject" cols="80" rows="1"><?php echo empty($dquiz->email_subject) ? '' : stripslashes($dquiz->email_subject);?></textarea></p>
				<p><?php printf(__('If you leave it empty, default subject will be used. You can use the variable %1$s to include the %2$s name and the variable %3$s to include the user name.', 'watu'), '%%QUIZ_NAME%%', WATU_QUIZ_WORD, '%%USER-NAME%%')?></p>			
	
		<?php wp_editor(stripslashes(@$dquiz->email_output), 'email_output'); ?>
		<p><?php _e('If you leave this empty, the contents from the "Final screen" will be sent by email as well.', 'watu');?></p>
		<p><?php printf(__('By default this content is used for both the email sent to user, and the email sent to admin. You can however use the %s tag to make the email contents different. The content before the %s tag will be sent to the user (if the corresponding checkbox is checked) and the content after the %s tag - to the admin.', 'watu'), '{{{split}}}', '{{{split}}}', '{{{split}}}')?></p>
	</div>
	
	<p><strong><?php _e('Usable Variables:', 'watu') ?></strong></p>
	<table>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watu') ?></th><th style="text-align:left;"><?php _e('Value', 'watu') ?></th></tr>
	<tr><td>%%ANSWERS%%</td><td><?php _e('This will display the questions along with the answers that user gave to them marked as correct or incorrect.', 'watu') ?></td></tr>
	<tr><td>%%POINTS%%</td><td><?php _e('The number of points collected. (The old %%SCORE%% tag also works)', 'watu') ?></td></tr>
	<tr><td>%%MAX-POINTS%%</td><td><?php _e('Maximum number of points', 'watu') ?></td></tr>
	<tr><td>%%GRADE%%</td><td><?php _e('Shows the achieved grade - title and description together. If you want to design this better, you can use %%GRADE-TITLE%% for grade title and %%GRADE-DESCRIPTION%% for grade description', 'watu') ?>.</td></tr>
	<tr><td>%%CORRECT%%</td><td><?php _e('Number of correct answers. In multiple-select questions even one correct answer makes the question correct. In WatuPRO this is configurable', 'watu')?></td></tr>
	<tr><td>%%WRONG%%</td><td><?php _e('Number of answers you got wrong', 'watu') ?></td></tr>
	<tr><td>%%EMPTY%%</td><td><?php _e('Number of unanswered questions', 'watu') ?></td></tr>
	<tr><td>%%PERCENTAGE%%</td><td><?php _e('Percent correct answers given', 'watu') ?></td></tr>
	<tr><td>%%RATING%%</td><td><?php _e("A rating of your performance - it could be 'Failed'(0-39%), 'Just Passed'(40%-50%), 'Satisfactory', 'Competent', 'Good', 'Excellent' and 'Unbeatable'(100%).", 'watu') ?> <?php printf(__('The rating can be changed only by <a href="%s" target="_blank">translating the plugin</a>. So we recommend you to use the grades instead. They are fully configurable right from this page.', 'watu'), 'http://blog.calendarscripts.info/how-to-translate-a-wordpress-plugin/')?></td></tr>
	<tr><td>%%QUIZ_NAME%%</td><td><?php printf(__('The name of the %s', 'watu'), WATU_QUIZ_WORD) ?></td></tr>
	<tr><td>%%DESCRIPTION%%</td><td><?php _e('The text entered in the description field.', 'watu') ?></td></tr>
	<tr><td>%%AVG-POINTS%%</td><td><?php printf(__('The average points earned by the other %s participants.', 'watu'), WATU_QUIZ_WORD) ?></td></tr>
	<tr><td>%%BETTER-THAN%%</td><td><?php printf(__('Shows the percentage of users collected less points on the %s.', 'watu'), WATU_QUIZ_WORD); ?></td></tr>
	<tr><td>%%EMAIL%%</td><td><?php _e('The user email address if we know it - use it only if you require / expect logged in users OR you have selected the option to email user their results (so email address will be requested).', 'watu') ?></td></tr>
	<tr><td>%%USER-NAME%%</td><td><?php _e('The user name of the logged in user - use it only if you require / expect logged in users. If the user is not logged in, the variable will display "Guest".', 'watu') ?></td></tr>
	</table>
	
	<p><?php printf(__('The shortcode %s lets you display a chart of user points vs. average collected points. It accepts arguments <b>%s</b> and <b>%s</b> to specify the colors of the bars.', 'watu'), '<input type="text" value="[watu-basic-chart]" readonly="readonly" onclick="this.select();">', 'your_color', 'avg_color');?></p>
	</div>
	</div>
	
	<p class="submit">
	<?php wp_nonce_field('watu_create_edit_quiz'); ?>
	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="quiz" value="<?php echo empty($_REQUEST['quiz']) ? 0 : intval($_REQUEST['quiz']); ?>" />
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo $user_ID ?>" />
	<span id="autosave"></span>
	<input type="submit" name="submit" value="<?php _e('Save', 'watu') ?>" class="button button-primary" tabindex="4" />
	</p>
	
	</div>
	</form>
	
	</div>
	
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
</div>