<div class="wrap watupro-wrap">
	<h1><?php _e('Add/Edit a Webhook', 'watupro');?></h1>
	
	<p><a href="admin.php?page=watupro_webhooks"><?php _e('Back to webhooks', 'watupro');?></a></p>
	
	<p><?php printf(__('A webhook will be notified only if the user who submits a %s has provided at least an email address. This happens either when the user is logged in or when you have requested an email address when taking the %s.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?> </p>
	
	<div class="postbox">
	<form method="post" class="watupro-form wrap" onsubmit="return validateHookForm(this);">
		<div class="wrap">
			<p><label><?php _e('When someone completes:', 'watupro');?></label> <select name="exam_id" onchange="wcChangeQuiz(this.value);">
				<option value=""><?php _e('- Please select -', 'watupro');?></option>
				<?php foreach($exams as $exam):?>
					<option value="<?php echo $exam->ID?>" <?php if(!empty($hook->exam_id) and $hook->exam_id == $exam->ID) echo 'selected';?>><?php echo stripslashes($exam->name);?></option>
				<?php endforeach;?>		
			</select></p>
			
			<p><label><?php _e('With this grade / result:', 'watupro');?></label>		
				<select name="grade_id" id="webhookGradeID">
					<option value='0'><?php _e('- Any grade -', 'watupro');?></option>
					<?php if(!empty($hook->ID) and count($hook_grades)):
						foreach($hook_grades as $hook_grade):?>
						<option value="<?php echo $hook_grade->ID?>" <?php if($hook->grade_id == $hook_grade->ID) echo 'selected';?>><?php echo stripslashes($hook_grade->gtitle);?></option>
					<?php endforeach; 
					endif;?>
				</select></p>
				
			<p><label><?php _e('Webhook URL:', 'watupro');?></label> <input type="text" name="hook_url" value="<?php echo empty($hook->ID) ? '' : $hook->hook_url;?>" class="watupro-url-field"></p>
			
			<p><?php printf(__("The following data can be passed as a JSON array from the %s to the webhook if they are available. You can set your names for each variable. If a variable has no name, it will not be included in the JSON array.", 'watupro'), WATUPRO_QUIZ_WORD);?></p>
			
			<p><?php _e('You can include several custom attributes with a predefined value in the request. You can use them for an API key, authorization keys, and so on.', 'watupro');?></p>
			
			<table>
				<thead>
					<tr><th><?php _e('Field / Data', 'watupro');?></th><th><?php _e('Variable name', 'watupro');?></th><th><?php _e('Variable value', 'watupro');?></th></tr>
				</thead>
				<tbody>
					<tr><td><b><?php printf(__('Contact (%s taker) name', 'watupro'), WATUPRO_QUIZ_WORD);?></b></td>
					<td><input type="text" name="name_name" value="<?php echo empty($payload_config['name']) ? '' : $payload_config['name']['name'];?>"></td>
					<td><?php _e('Dynamic / provided by user', 'watupro');?></td></tr>
					<tr><td><b><?php _e('Contact email address', 'watupro');?></b></td>
					<td><input type="text" name="email_name" value="<?php echo empty($payload_config['email']) ? '' : $payload_config['email']['name'];?>"></td>
					<td><?php _e('Dynamic / provided by user', 'watupro');?></td></tr>
					<tr><td><b><?php printf(__('Contact phone', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('From "Ask for user contact details" section', 'watupro');?></i></td>
					<td><input type="text" name="field_phone_name" value="<?php echo empty($payload_config['field_phone']) ? '' : $payload_config['field_phone']['name'];?>"></td>
					<td><?php _e('Dynamic / provided by user', 'watupro');?></td></tr>
					<tr><td><b><?php printf(__('Contact company', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('From "Ask for user contact details" section', 'watupro');?></i></td>
					<td><input type="text" name="field_company_name" value="<?php echo empty($payload_config['field_company']) ? '' : $payload_config['field_company']['name'];?>"></td>
					<td><?php _e('Dynamic / provided by user', 'watupro');?></td></tr>
					<tr><td><b><?php printf(__('Contact custom field 1', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('From "Ask for user contact details" section', 'watupro');?></i></td>
					<td><input type="text" name="custom_field1_name" value="<?php echo empty($payload_config['custom_field1']) ? '' : $payload_config['custom_field1']['name'];?>"></td>
					<td><?php _e('Dynamic / provided by user', 'watupro');?></td></tr>
					<tr><td><b><?php printf(__('Contact custom field 2', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('From "Ask for user contact details" section', 'watupro');?></i></td>
					<td><input type="text" name="custom_field2_name" value="<?php echo empty($payload_config['custom_field2']) ? '' : $payload_config['custom_field2']['name'];?>"></td>
					<td><?php _e('Dynamic / provided by user', 'watupro');?></td></tr>
					<tr><td><b><?php printf(__('Custom parameter 1', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('Predefined variable 1', 'watupro');?></i></td>
					<td><input type="text" name="custom_key1_name" value="<?php echo empty($payload_config['custom_key1']) ? '' : $payload_config['custom_key1']['name'];?>"></td>
					<td><input type="text" name="custom_key1_value" value="<?php echo empty($payload_config['custom_key1']) ? '' : $payload_config['custom_key1']['value'];?>"></td></tr>
					<tr><td><b><?php printf(__('Custom parameter 2', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('Predefined variable 2', 'watupro');?></i></td>
					<td><input type="text" name="custom_key2_name" value="<?php echo empty($payload_config['custom_key2']) ? '' : $payload_config['custom_key2']['name'];?>"></td>
					<td><input type="text" name="custom_key2_value" value="<?php echo empty($payload_config['custom_key2']) ? '' : $payload_config['custom_key2']['value'];?>"></td></tr>
					<tr><td><b><?php printf(__('Custom parameter 3', 'watupro'), WATUPRO_QUIZ_WORD);?></b><br />
					<i><?php _e('Predefined variable 3', 'watupro');?></i></td>
					<td><input type="text" name="custom_key3_name" value="<?php echo empty($payload_config['custom_key3']) ? '' : $payload_config['custom_key3']['name'];?>"></td>
					<td><input type="text" name="custom_key3_value" value="<?php echo empty($payload_config['custom_key3']) ? '' : $payload_config['custom_key3']['value'];?>"></td></tr>
				</tbody>
			</table>
			
			<p><input type="submit" value="<?php _e('Save Webhook', 'watupro');?>" class="button button-primary">
			<input type="submit" name="test" value="<?php _e('Test Webhook', 'watupro');?>" class="button button-primary"></p>
		</div>
		<?php wp_nonce_field('watupro_webhooks');?>
		<input type="hidden" name="ok" value="1">
	</form>
	</div>
	
	<?php if(!empty($_POST['test'])):?>
	<div>
		<h2><?php _e('Data sent', 'watupro');?></h2>
			<p><?php echo '<pre>' . var_export($data, true) . '</pre>';;?></p>
		<h2><?php _e('Response from the hook', 'watupro');?></h2>
		<p>
			<?php echo '<pre>' . var_export($result, true) . '</pre>';?>
		</p>
	</div>
	<?php endif;?>
</div>

<script type="text/javascript" >
function validateHookForm(frm) {
	if(frm.exam_id.value == '') {
		alert("<?php printf(__('Please select a %s', 'watupro'), WATUPRO_QUIZ_WORD);?>");
		frm.exam_id.focus();
		return false;
	}
	
	var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
    '(\\#[-a-z\\d_]*)?$','i'); // fragment locator	
	
	if(frm.hook_url.value == '' || !pattern.test(frm.hook_url.value)) {
		alert("<?php _e('Provide a valid Webhook URL');?>");
		frm.hook_url.focus();
		return false;
	}
	
	return true;
}

function wcChangeQuiz(quizID) {
	// array containing all grades by exams
	var grades = {<?php foreach($exams as $exam): echo $exam->ID.' : {';
			foreach($exam->grades as $grade):
				echo $grade->ID .' : "'.$grade->gtitle.'",';
			endforeach;
		echo '},';
	endforeach;?>};
	
	// construct the new HTML
	var newHTML = '';
	newHTML += "<option value='0'><?php _e('- Any grade -', 'watupro');?></option>";
	jQuery.each(grades, function(i, obj){
		if(i == quizID) {
			jQuery.each(obj, function(j, grade) {
				newHTML += "<option value=" + j + ">" + grade + "</option>\n";
			}); // end each grade
		}
	});
	
	jQuery('#webhookGradeID').html(newHTML);
}
</script>