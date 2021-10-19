<h3><?php _e('Usable Variables:', 'watupro') ?></strong> [<a href="#" onclick="jQuery('#usableVariables').toggle();return false;"><?php _e('show/hide', 'watupro')?></a>]</h3> 
<p><?php _e('(All the variables can be used in grade descriptions as well.)', 'watupro')?></p>
	<table id='usableVariables'>
	<tr><td colspan="2"><b><?php printf(__('%s Variables', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></b><hr></td></tr>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watupro') ?></th><th style="text-align:left;"><?php _e('Explanation', 'watupro') ?></th></tr>
	<tr><td>%%CORRECT%%</td><td><?php _e('The number of correct answers (the old %%SCORE%% also works)', 'watupro') ?></td></tr>
	<tr><td>%%WRONG%%</td><td><?php _e('The number of wrong answers', 'watupro') ?></td></tr>
	<?php if(empty($edit_mode)):?><tr><td>%%EMPTY%%</td><td><?php _e('The number of unanswered questions (note that some question types like Slider and Sortable are always considered answered).', 'watupro') ?></td></tr>
	<tr><td>%%ATTEMPTED%%</td><td><?php _e('The number of attempted (non-empty) questions (note that some question types like Slider and Sortable are always considered non-empty).', 'watupro') ?></td></tr><?php endif;?>
	<tr><td>%%TOTAL%%</td><td><?php _e('Total number of questions. Questions marked as survey are not counted here!', 'watupro') ?></td></tr>
	<tr><td>%%POINTS%%</td><td><?php printf(__('Total points collected. You can use the variable %s to show the points rounded without decimals.', 'watupro'), '%%POINTS-ROUNDED%%'); ?></td></tr>
	<tr><td>%%MAX-POINTS%%</td><td><?php _e('Maximum number of points that user could collect.', 'watupro') ?></td></tr>
	<tr><td>%%PERCENT%%</td><td><?php _e('Correct answer percentage', 'watupro') ?></td></tr>
	<tr><td>%%PERCENT-WRONG%%</td><td><?php _e('Wrong answer percentage', 'watupro') ?></td></tr>
	<tr><td>%%PERCENTOFMAX%%</td><td><?php _e('Percentage of points achieved vs. maximum possible points', 'watupro') ?></td></tr>
	<tr><td>%%PERCENTOFMAXLEFT%%</td><td><?php _e('100 - %%PERCENTOFMAX%%', 'watupro') ?></td></tr>
	<tr><td>%%GRADE%%</td><td><?php printf(__('The assigned grade after taking the %s - title and description together', 'watupro'), WATUPRO_QUIZ_WORD) ?>.</td></tr>
	<tr><td>%%GTITLE%%</td><td><?php _e('The assigned grade - title only', 'watupro') ?>.</td></tr>
	<tr><td>%%GDESC%%</td><td><?php _e('The assigned grade - description only', 'watupro') ?>.</td></tr>
	<?php if(empty($edit_mode)):?>
		<tr><td>%%RATING%%</td><td><?php _e("A generic rating of your performance - it could be 'Failed'(0-39%), 'Just Passed'(40%-50%), 'Satisfactory', 'Competent', 'Good', 'Excellent' and 'Unbeatable'(100%)", 'watupro') ?></td></tr>
	<?php endif;?>
	<tr><td>%%QUIZ_NAME%%</td><td><?php printf(__('The name of the %s', 'watupro'), WATUPRO_QUIZ_WORD) ?></td></tr>
	<tr><td>%%CERTIFICATE%%</td><td><?php printf(__('Outputs a link to printable certificate. Will be displayed only if certificate is assigned to the achieved grade and you have the user email. For troubleshooting check <a href="%s" target="_blank">this post</a>.', 'watupro'), 'http://blog.calendarscripts.info/when-the-%certificate-variable-in-watupro-shows-nothing/');?></td></tr>
		<tr><td>%%CERTIFICATE_ID%%</td><td><?php _e('The ID of the earned certificate.', 'watupro');?></td></tr>
	<tr><td>%%ANSWERS%%</td><td><?php if(empty($edit_mode)) _e('Displays the question and the user answers along with correct/incorrect mark. It will also include any feedback by user or admin.', 'watupro');
	else _e('Displays table with user answers, points, and teacher comments', 'watupro')?></td></tr>		
	<?php if(empty($edit_mode)):?>
		<tr><td>%%RESOLVED%%</td><td><?php _e('Shows only the correctly answered questions.', 'watupro') ?></td></tr>
		<tr><td>%%UNRESOLVED%%</td><td><?php _e('Shows unresolved questions without showing which is the correct answer. Useful if you want to point user attention where they need to work more without exposing the correct results. Questions that are considered unresolved are unanswered ones or the questions where points collected are less or equal to 0.', 'watupro') ?></td></tr>
	<?php endif;?>
	
	<?php if(empty($edit_mode)):?><tr><td nowrap="true">%%ANSWERS-PAGINATED%%</td><td><?php printf(__('Same as %%ANSWERS%% but one question/answer is shown at a time with a numbered paginator at the bottom. <a href="%s" target="_blank">Learn more about this.</a>', 'watupro'), 'https://blog.calendarscripts.info/answers-paginator-in-watupro/');?></td></tr>
	<tr><td>%%SHORT-ANSWERS%%</td><td><?php _e("This variable will display the question along with user's answer without checkmarks or feedback. The variable is suitable for surveys or similar tests that do not have correct or wrong answers.", 'watupro');?></td></tr>
	<tr><td nowrap="true">%%ANSWERS-TABLE%%</td><td><?php _e('A paginated interactive table with the answers. (Non-paginted and no review links if used in the email contents) ', 'watupro');?></td></tr><?php endif;?>
	<tr><td>%%CATGRADES%%</td><td><?php _e('Grades and stats per category in case you have defined such grades.', 'watupro') ?></td></tr>	
	<tr><td>%%DATE%%</td><td><?php printf(__('The date when the %s is completed (Date format comes from your Wordpress Settings page).', 'watupro'), WATUPRO_QUIZ_WORD); ?>.</td></tr>
	<tr><td>%%START-TIME%%</td><td><?php printf(__('The time when the %s was started.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	<tr><td>%%END-TIME%%</td><td><?php printf(__('The time when the %s was completed', 'watupro'), WATUPRO_QUIZ_WORD); ?>.</td></tr>
	<tr><td>%%TIME-SPENT%%</td><td><?php printf(__('The time spent to take the %s.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>	
	<tr><td>%%AVG-POINTS%%</td><td><?php printf(__('Shows the average points achieved by others who took the same %s.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	<tr><td>%%AVG-PERCENT%%</td><td><?php printf(__('Shows the average percent correct answer given by others who took the same %s.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	<tr><td>%%AVG-PERCENTOFMAX%%</td><td><?php printf(__('Shows the average percent from maximum points achieved by others who took the same %s.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	<?php if(empty($edit_mode)):?><tr><td>%%BETTER-THAN%%</td><td><?php printf(__('Shows the percentage of users performed worse. It will compare by percent correct answers or by points depending on how the %s calculates grades - by points or percent correct answers.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr><?php endif;?>
	<tr><td>%%ADMIN-URL%%</td><td><?php _e('Direct URL to view this submission in the administration. Do not use this variable in the email sent to user.', 'watupro'); ?></td></tr>
	<tr><td colspan="2"><b><?php _e('User Info Variables', 'watupro')?></b><hr></td></tr>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watupro') ?></th><th style="text-align:left;"><?php _e('Explanation', 'watupro') ?></th></tr>
	<tr><td>%%EMAIL%%</td><td><?php _e('User email address.', 'watupro') ?></td></tr>
	<tr><td>%%USER-NAME%%</td><td><?php _e('The logged in (or requested by a {{{name-field}}} tag, or "Ask for user contact details") user name. If empty, it will display "Guest"', 'watupro'); ?>.</td></tr>
	<tr><td>%%FIELD-COMPANY%%</td><td><?php _e('The value of the "Company" field from the optional "Ask user for contact details" section.', 'watupro'); ?>.</td></tr>
	<tr><td>%%FIELD-PHONE%%</td><td><?php _e('The value of the "Phone" field from the optional "Ask user for contact details" section.', 'watupro'); ?>.</td></tr>
	<tr><td>%%UNIQUE-ID%%</td><td><?php printf(__('A unique identifier of the submitted %s attempt.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	</table>