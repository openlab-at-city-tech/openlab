<div id="watuproContactDetails-<?php echo $exam->ID?>-<?php echo $position?>" style='display:<?php echo ($position == 'start') ? 'block' : 'none';?>;' class="watupro-ask-for-contact watupro-ask-for-contact-quiz-<?php echo $exam->ID?>">
	<?php if(!empty($advanced_settings['contact_fields']['intro_text'])) echo wpautop(stripslashes(rawurldecode($advanced_settings['contact_fields']['intro_text'])));?>
	<?php if(!empty($advanced_settings['contact_fields']['email'])):?>
		<p><?php echo $advanced_settings['contact_fields']['email_label'];?> <br>	<input type="text" size="30" name="watupro_taker_email" id="watuproTakerEmail<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['email'] != 'required') {echo 'optional';} else {echo 'watupro-contact-required';}?> watupro-contact-field" value="<?php echo empty($_POST['watupro_taker_email'])? @$user_email : $_POST['watupro_taker_email']?>">
			<span class="error watupro-contact-error" id="watuproTakerEmailError<?php echo $exam->ID?>" style="color:red;"></span>	
		</p>
	<?php endif;?>	
	<?php if(!empty($advanced_settings['contact_fields']['name'])):?>
		<p><?php echo $advanced_settings['contact_fields']['name_label'];?> <br> <input type="text" size="30" name="watupro_taker_name" id="watuproTakerName<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['name'] != 'required') {echo 'optional';} else {echo 'watupro-contact-required';}?> watupro-contact-field" value="<?php echo empty($_POST['watupro_taker_name'])? @$user_identity : htmlentities(stripslashes($_POST['watupro_taker_name']))?>">	
			<span class="error watupro-contact-error" id="watuproTakerNameError<?php echo $exam->ID?>" style="color:red;"></span> 
		</p>
	<?php endif;?>	
	<?php if(!empty($advanced_settings['contact_fields']['phone'])):?>
		<p><?php echo $advanced_settings['contact_fields']['phone_label'];?> <br> <input type="text" size="30" name="watupro_taker_phone" id="watuproTakerPhone<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['phone'] != 'required') {echo 'optional';} else {echo 'watupro-contact-required';}?> watupro-contact-field" value="<?php echo empty($_POST['watupro_taker_phone']) ? '' : htmlentities(stripslashes($_POST['watupro_taker_phone']))?>"> 		<span class="error watupro-contact-error" id="watuproTakerPhoneError<?php echo $exam->ID?>" style="color:red;"></span> 
		</p>
	<?php endif;?>	
	<?php if(!empty($advanced_settings['contact_fields']['company'])):?>
		<p><?php echo $advanced_settings['contact_fields']['company_label'];?> <br> <input type="text" size="30" name="watupro_taker_company" id="watuproTakerCompany<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['company'] != 'required') {echo ' optional';} else {echo ' watupro-contact-required';}?> watupro-contact-field" value="<?php echo empty($_POST['watupro_taker_company']) ? '' : htmlentities(stripslashes($_POST['watupro_taker_company']))?>"> 
			<span class="error watupro-contact-error" id="watuproTakerCompanyError<?php echo $exam->ID?>" style="color:red;"></span>	
		</p>
	<?php endif;?>
	<?php if(!empty($advanced_settings['contact_fields']['field1'])):?>
		<p><?php echo $advanced_settings['contact_fields']['field1_label'];?> <br>
		<?php if(empty($advanced_settings['contact_fields']['field1_is_dropdown'])):?>
			<input type="text" size="30" name="watupro_taker_field1" id="watuproTakerField1<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['field1'] != 'required') {echo ' optional';} else {echo ' watupro-contact-required';}?> watupro-contact-field" value="<?php echo empty($_POST['watupro_taker_field1']) ? '' : htmlentities(stripslashes($_POST['watupro_taker_field1']));?>">
		<?php else:
			$field1_ddvalues = explode(PHP_EOL, $advanced_settings['contact_fields']['field1_dropdown_values']);
			?>
			<select name="watupro_taker_field1" id="watuproTakerField1<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['field1'] != 'required') {echo ' optional';} else {echo ' watupro-contact-required';}?> watupro-contact-field">
				<?php foreach($field1_ddvalues as $value):
					$value = trim($value);
					$selected = (!empty($_POST['watupro_taker_field1']) and stripslashes($_POST['watupro_taker_field1']) == $value) ? ' selected' : '';?>
					<option value="<?php echo $value?>"<?php echo $selected?>><?php echo $value?></option>
				<?php endforeach;?>
			</select>
		<?php endif; // end if field is dropdown?> 
		<span class="error watupro-contact-error" id="watuproTakerField1<?php echo $exam->ID?>Error" style="color:red;"></span>
		</p>
	<?php endif;?>
	<?php if(!empty($advanced_settings['contact_fields']['field2'])):?>
		<p><?php echo $advanced_settings['contact_fields']['field2_label'];?> <br>
		<?php if(empty($advanced_settings['contact_fields']['field2_is_dropdown'])):?>		
		<input type="text" size="30" name="watupro_taker_field2" id="watuproTakerField2<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['field2'] != 'required') {echo ' optional';} else {echo ' watupro-contact-required';}?> watupro-contact-field" value="<?php echo empty($_POST['watupro_taker_field2']) ? '' : htmlentities(stripslashes($_POST['watupro_taker_field2']))?>">
		<?php else:
			$field2_ddvalues = explode(PHP_EOL, $advanced_settings['contact_fields']['field2_dropdown_values']);
			?>
			<select name="watupro_taker_field2" id="watuproTakerField2<?php echo $exam->ID?>" class="<?php if($advanced_settings['contact_fields']['field2'] != 'required') {echo ' optional';} else {echo ' watupro-contact-required';}?> watupro-contact-field">
				<?php foreach($field2_ddvalues as $value):
					$value = trim($value);
					$selected = (!empty($_POST['watupro_taker_field2']) and stripslashes($_POST['watupro_taker_field2']) == $value) ? ' selected' : '';?>
					<option value="<?php echo $value?>"<?php echo $selected?>><?php echo $value?></option>
				<?php endforeach;?>
			</select>
		<?php endif; // end if field is dropdown?>	
		<span class="error watupro-contact-error" id="watuproTakerField2<?php echo $exam->ID?>Error" style="color:red;"></span>
		</p>
	<?php endif;?>
	<?php if(!empty($advanced_settings['contact_fields']['checkbox'])):?>
		<p><input type="checkbox" name="watupro_taker_checkbox" id="watuproTakerCheckbox<?php echo $exam->ID?>" class="watupro-contact-required watupro-contact-field" value="1" <?php if(!empty($_POST['watupro_taker_checkbox'])) echo 'checked'?>> <?php echo $advanced_settings['contact_fields']['checkbox'];?> <span class="error watupro-contact-error" id="watuproTakerCheckbox<?php echo $exam->ID?>Error" style="color:red;"></span></p>		
	<?php endif;?>	
</div>