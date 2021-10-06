<div class="wrap watupro-wrap">
	<h1><?php echo empty($certificate->ID)?__("Create New", 'watupro'):__("Edit", 'watupro')?> <?php _e('Certificate', 'watupro')?></h1>

	<form method="post" onsubmit="return validate(this);" id="wtpCertificateForm">
	<div class="postbox" id="titlediv">
		<h3 class="hndle">&nbsp; <span><?php _e('Certificate Title', 'watupro') ?></span></h3>
		
		<div class="inside">
			<input type='text' name='title' id="title" value='<?php echo stripslashes(@$certificate->title); ?>' class="i18n-multilingual" />
		</div>
	</div>

	<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea postbox">
	<h3 class="hndle">&nbsp; <span><?php _e('Certificate Text/HTML', 'watupro') ?></span></h3>
	<div class="inside">
	<?php if(get_option('watupro_certificates_no_rtf')):?>
		<textarea name="html" cols="100" rows="20"><?php echo stripslashes(@$certificate->html)?></textarea>
	<?php else: wp_editor(stripslashes(@$certificate->html), 'html', array("editor_class" => 'i18n-multilingual')); endif; ?>

	<p>&nbsp; <strong><?php _e('Usable Variables...', 'watupro') ?></strong></p>
	<table>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watupro') ?></th><th style="text-align:left;"><?php _e('Value', 'watupro') ?></th></tr>
	<tr><td>%%USER-NAME%%</td><td><?php printf(__('Full user name, if provided, otherwise login will be used. Other user data can be included using the <a href="%s" target="_blank">watupro-userinfo shortcode</a>.', 'watupro'), 'http://blog.calendarscripts.info/user-info-shortcodes-from-watupro-version-4-1-1/'); ?></td></tr>
	<tr><td>%%EMAIL%%</td><td><?php _e('The user contact email address', 'watupro') ?></td></tr>
	<tr><td>%%POINTS%%</td><td><?php printf(__('Total points collected. You can use the variable %s to show the points rounded without decimals.', 'watupro'), '%%POINTS-ROUNDED%%'); ?></td></tr>	
	<tr><td>%%GRADE%%</td><td><?php _e('The assigned grade after taking the exam. Or use %%GTITLE%% and %%GDESC%% to separate the grade title from the description.', 'watupro') ?></td></tr>
	<tr><td>%%QUIZ_NAME%%</td><td><?php printf(__('The name of the %s (for single-%s certificates)', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD) ?></td></tr>
	<tr><td>%%QUIZ_CAT%%</td><td><?php printf(__('The category of the %s (for single-%s certificates)', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD) ?></td></tr>
	<tr><td>%%DESCRIPTION%%</td><td><?php _e('The optional description.', 'watupro') ?></td></tr>
	<tr><td>%%DATE%%</td><td><?php printf(__('Date when %s is submitted and the certificate achieved.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	<tr><td>%%EXPIRATION-DATE%%</td><td><?php _e('Expiration date if the certificate expires.', 'watupro') ?></td></tr>
	<tr><td>%%ID%%</td><td><?php _e('Unique ID for this issued certificate.', 'watupro') ?></td></tr>
	<tr><td>%%TIME-SPENT%%</td><td><?php printf(__('The time spent to take the %s.', 'watupro'), WATUPRO_QUIZ_WORD); ?></td></tr>
	<tr><td>%%VERIFICATION%%</td><td><?php printf(__('Will output an unique URL which can be used to verify the validity of the issued certificate. If you have the plugin <a href="%1$s" target="_blank">%2$s</a> installed and activated, the variable will print a QR code instead of an URL.', 'watupro'), 'https://wordpress.org/plugins/doqrcode/', 'QRCode'); ?></td></tr>
	</table>
	<p><?php _e('You can also use the contact field variables from Edit Quiz -> User and Email Related Settings tab -> Ask for user contact details section, if you have selected to request these fields from the respondent.', 'watupro');?></p>
	<p><?php printf(__('Want to use all final screen variables? <a href="%s" target="_blank">See how</a>.', 'watupro'), 'https://blog.calendarscripts.info/using-all-final-page-variables-in-watupro-certificates/');?></p>
	
	<p>&nbsp;</p>
	<h3><?php _e('The %%CERTIFICATE%% Variable', 'watupro')?></h3>
	<p><?php printf(__('If you want to display links to the printable certificate(s) you need to use the %%CERTIFICATE%% variable at the "Final page" box of the %s. It will be replaced by default text and a link to the certificate. You can override this text for each certificate in the box below. Use the variable %s if you want to manually create a HTML link around some of the words in the text. If you skip the variable, the whole text you enter here will be hyperlinked.', 'watupro'), WATUPRO_QUIZ_WORD, '{{url}}');?><br />
	<textarea name="var_text" rows="5" class="watupro-textarea"><?php echo empty($certificate->ID) ? '' : stripslashes($certificate->var_text);?></textarea></p>

	<p>&nbsp;</p>
	<h3><?php _e('Multiple Quiz Certificate', 'watupro')?></h3>
	<p><input type="checkbox" name="is_multi_quiz" value="1" <?php if(!empty($certificate->is_multi_quiz)) echo 'checked'?> onclick="this.checked ? jQuery('#multiQuizOptions').show() : jQuery('#multiQuizOptions').hide();"> <?php _e('This certificate will be issued for completing multiple quizzes.', 'watupro');?></p>
	
	<div id="multiQuizOptions" style='display:<?php echo empty($certificate->is_multi_quiz) ? 'none' : 'block';?>'>
		<p><?php _e('Such certificate will be issued to logged in users who have completed all of the required quizzes and have achieved the required criteria below. If multiple certificates match the criteria only the one with the highest requirements will be issued.', 'watupro');?></p>
		
		<p><label><?php _e('Required quizzes:', 'watupro');?></label> <select name="quiz_ids[]" size="4" multiple="true">
			<?php foreach($exams as $exam):?>
				<option value="<?php echo $exam->ID?>"<?php if(!empty($certificate->quiz_ids) and strstr($certificate->quiz_ids, '|'.$exam->ID.'|')) echo ' selected'?>><?php echo stripslashes($exam->name);?></option>
			<?php endforeach;?>
		</select></p>
		<p><label><?php _e('Points per quiz required:', 'watupro');?></label> <input type="text" size="4" name="avg_points" value="<?php echo @$certificate->avg_points?>"></p>
		<p><label><?php _e('% correct answer required:', 'watupro');?></label> <input type="text" size="4" name="avg_percent" value="<?php echo @$certificate->avg_percent?>"></p>
		
		<h4><?php _e('Average Calculations and requirements', 'watupro');?></h4>
		<p><input type="radio" name="avg_on_each_quiz" value="0" <?php if(empty($certificate->avg_on_each_quiz)) echo "checked"?>> <?php printf(__('The above values will be required as average from all user attempts on the selected %s. The performance on some individual %s may be worse.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD_PLURAL);?> </p>
		<p><input type="radio" name="avg_on_each_quiz" value="1" <?php if(!empty($certificate->avg_on_each_quiz)) echo "checked"?>> <?php printf(__('The required values have to be satisfied on every %s. One successful attempt on each %s will be enough to earn the certificate (we will not average the performance from multiple attempts on every %s).', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?> </p>
	</div>
	
	<?php do_action('watupro-certificate-pdf-settings', @$certificate->ID);?>
	
	<p>&nbsp;</p>
	<h3><?php _e('Admin approval', 'watupro')?></h3>
	<p><?php _e('By default the certificate is issued automatically when a grade assigned to it is achieved by the user. Here you can optionally configure admin approval.', 'watupro')?></p>
	
	<p><input type="checkbox" name="require_approval" value="1" <?php if(!empty($certificate->require_approval)) echo 'checked'?> onclick="this.checked ? jQuery('#certificateApprovalArea').show() : jQuery('#certificateApprovalArea').hide();"> <?php _e('This certificate requires admin approval', 'watupro')?></p>
	
	<div id="certificateApprovalArea" style="display:<?php echo empty($certificate->require_approval) ? 'none' : 'block';?>">
		<p><input type="checkbox" name="require_approval_notify_admin" value="1" <?php if(!empty($certificate->require_approval_notify_admin)) echo 'checked'?>> <?php _e('Notify me when a certificate approval requires my attention.', 'watupro')?></p>
		
		<p><input type="checkbox" name="approval_notify_user" value="1" <?php if(!empty($certificate->approval_notify_user)) echo 'checked'?> onclick="this.checked ? jQuery('#certificateUserNotifyArea').show() : jQuery('#certificateUserNotifyArea').hide();"> <?php _e('Notify the user when I approve their certificate.(You can configure the email contents.)', 'watupro')?></p>
		
		<div id="certificateUserNotifyArea" style="display:<?php echo empty($certificate->approval_notify_user) ? 'none' : 'block';?>">
			<p><label><?php _e('Email subject:', 'watupro')?></label> <input type="text" size="80" name="approval_email_subject" value='<?php echo stripslashes(@$certificate->approval_email_subject)?>' class='i18n-multilingual'></p>
			<p><label><?php _e('Email message:', 'watupro')?></label> <?php echo wp_editor(stripslashes(@$certificate->approval_email_message), 'approval_email_message', array("editor_class" => 'i18n-multilingual'))?></p>
			<p><?php _e('You can use the following variables:', 'watupro');?> <input type="text" value="{{quiz-name}}" readonly onclick="this.select()" size="10"> <?php _e('for quiz name', 'watupro')?>, <input type="text" value="{{certificate}}" readonly onclick="this.select()" size="10"> <?php _e('for certificate name', 'watupro')?>, 
			<input type="text" value="{{date}}" readonly onclick="this.select();" size="6"> <?php _e('for the date when the exam was submitted, and', 'watupro')?> 
			<input type="text" value="{{url}}" readonly onclick="this.select()" size="6"> <?php _e('for the URL where the user can view/print their certificate.', 'watupro')?></p>
		</div>
	</div>
	
	<p>&nbsp;</p>
	<h3><?php _e('Certificate Expiration', 'watupro')?></h3>
	
	<p><input type="checkbox" name="has_expiration" value="1" <?php if(!empty($certificate->has_expiration)) echo 'checked'?> onclick="this.checked ? jQuery('#certificateExpirtation').show() : jQuery('#certificateExpirtation').hide();"> <?php _e('This certificate expires after selected period of time or on a fixed date.', 'watupro');?></p>
	
	<div id="certificateExpirtation" style='display:<?php echo empty($certificate->has_expiration) ? 'none' : 'block';?>'>
	  <p><input type="radio" name="expiration_mode" value="period" <?php if(empty($certificate->expiration_mode) or $certificate->expiration_mode == 'period') echo 'checked'?>> <?php _e('Expiration period:', 'watupro');?> <input type="text" name="expiration_period_num" size="3" value="<?php echo $expiration_num?>">
	  <select name="expiration_period_period">
	     <option value="month"><?php _e('months', 'watupro');?></option>
	     <option value="year" <?php if(!empty($expiration_period) and $expiration_period == 'year') echo 'selected'?>><?php _e('years', 'watupro');?></option>
	  </select>
		&nbsp;
		
		<input type="radio" name="expiration_mode" value="date" <?php if(!empty($certificate->expiration_mode) and $certificate->expiration_mode == 'date') echo 'checked'?>>
		
		<?php _e('Expiration date:', 'watupro');?> <input type="text" name="expiration_date" class="datepicker" value="<?php echo $expiration_date?>">	  
	  </p>
	  <p><?php _e('Message to display when the certificate has expired. if you leave it empty, default message will be used.', 'watupro');?><br>
	  	<?php if(get_option('watupro_certificates_no_rtf')):?>
   		<textarea name="expired_message" cols="80" rows="5"><?php echo stripslashes(@$certificate->expired_message)?></textarea>
   	<?php else: wp_editor(stripslashes(@$certificate->expired_message), 'expired_message', array("editor_class" => 'i18n-multilingual')); endif; ?>
   	</p>
	</div>
	
	<?php if(watupro_intel()):?>
	<p>&nbsp;</p>
	<h3><?php _e('Certificate Fee', 'watupro')?></h3>
	<p><?php _e("Charge", 'watupro')?> <?php echo get_option("watupro_currency")?> <input type="text" size="6" name="fee" value="<?php echo empty($certificate->ID) ? 0 : $certificate->fee?>"> <?php _e('for accessing this certificate. The charge is one-time. Coupon codes and bundles cannot be used for paid certificates. Users with rights to manage WatuPRO will always be able to see paid certificates.', 'watupro');?></p>
	<?php endif;?>
	
	<p>&nbsp;</p>
	<h3><?php _e('Important!', 'watupro')?></h3>
	<ol>
        <li><?php _e("No CSS styles or header from your blog will be applied. Please include all the styling in the certificate.", 'watupro')?></li>
	</ol>

	<p class="submit">	
	<span id="autosave"></span>
	<input type="submit" name="ok" value="<?php _e('Save Certificate', 'watupro') ?>" class="button-primary" tabindex="4" />
	<?php if(!empty($certificate->ID)):?>
		<input type="button" value="<?php _e('Delete Certificate', 'watupro') ?>" onclick="confirmDelete(this.form);" class="button">
	<?php endif;?>
	</p>
	</div>
	<input type="hidden" name="del" value="0">
	<?php wp_nonce_field('watupro_certificate');?>
	</form>
</div>

<script type="text/javascript">
function validate(frm)
{
	if(frm.title.value=='')
	{
		alert("<?php _e('Please enter title of this certificate', 'watupro')?>");
		frm.title.focus();
		return false;
	}

	return true;
}

function confirmDelete(frm)
{
	if(confirm("<?php _e('Are you sure?', 'watupro')?>"))
	{
		frm.del.value=1;
		frm.submit();
	}
}

jQuery(function(){
	jQuery('.datepicker').datepicker({dateFormat: "yy-m-d"});
});
</script>