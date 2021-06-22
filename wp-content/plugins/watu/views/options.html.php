<div class="wrap">

	<h2><?php _e("Watu Settings", 'watu'); ?></h2>

	<div class="postbox-container" style="min-width:60%;max-width:65%;margin-right:2%;">	
	
	<p><?php _e('Go to', 'watu')?> <a href="admin.php?page=watu_exams"><?php printf(__('Manage Your %s', 'watu'), ucfirst(WATU_QUIZ_WORD_PLURAL))?></a>
	&nbsp;|&nbsp; <a href="admin.php?page=watu_social_sharing"><?php _e('Social Sharing Options', 'watu');?></a></p>
	
	<form name="post" action="" method="post" id="post">
	<div>
			<p><?php _e('Send the automated emails from this address:', 'watu')?> <input type="text" name="watu_admin_email" value="<?php echo watu_admin_email()?>" size="30"><br>
<?php printf(__('This defaults to your main admin email. However to set a friendlier sender name you can overwrite it here by entering your name in format: <b>Your Name &lt;email@domain.com&gt;</b><br>If you have problems with getting your emails delivered, please install a plugin like <a href="%s" target="_blank">WP Mail SMTP</a>. Watu will send the emails through the SMTP server that you congigure in it.', 'watu'), 'https://wordpress.org/plugins/wp-mail-smtp/');?></p>	
	
			<p><input type="checkbox" name="debug_mode" value="1" <?php if(get_option('watu_debug_mode')) echo 'checked'?> /> <?php _e('Enable debug mode to see SQL errors. (Useful in case you have any problems)', 'watu')?></p>	
			
		<div class="postarea">
			<div class="postbox">
			
				<h3 class="hndle">&nbsp;<span><?php _e('Defaults', 'watu') ?></span></h3>
				<div class="inside" style="padding:8px">
					<?php 
						$single = $multi = '';
						if( get_option('watu_answer_type') =='radio') $single='checked="checked"';
						else $multi = 'checked="checked"';
					?>
					<label>&nbsp;<input type='radio' name='answer_type' <?php print $single?> id="answer_type_r" value='radio' /> <?php _e('Single Answer', 'watu')?> </label>
					&nbsp;&nbsp;&nbsp;
					<label>&nbsp;<input type='radio' name='answer_type' <?php print $multi?> id="answer_type_c" value='checkbox' /> <?php _e('Multiple Answers', 'watu')?></label>
					
					<p><input type="checkbox" name="no_ajax" value="1" <?php if(get_option('watu_no_ajax') == 1) echo 'checked';?>> <?php printf(__('No ajax (applies when creating a new %s)', 'watu'), WATU_QUIZ_WORD);?></p>
					
					<p><?php _e('Words used for "quiz/quizzes":', 'watu');?> <input type="text" size="10" name="quiz_word" value="<?php echo empty($_POST['quiz_word']) ? WATU_QUIZ_WORD : $_POST['quiz_word'];?>"> / <input type="text" size="10" name="quiz_word_plural" value="<?php echo empty($_POST['quiz_word_plural']) ? WATU_QUIZ_WORD_PLURAL : $_POST['quiz_word_plural'];?>"> <?php printf(__('This is not yet fully implemented! You can change any text within the plugin <a href="%s" target="_blank">this way</a>.', 'watu'), 'http://blog.calendarscripts.info/how-to-change-text-in-a-wordpress-plugin/');?></p>
				</div>
				
				
			</div>
			
	
	<div class="postbox wp-admin" style="padding:5px;">
	<h3><?php _e('Question based captcha', 'watu')?></h3>
	
	<p><?php _e("You can use a simple text-based captcha. We have loaded 3 basic questions but you can edit them and load your own. Make sure to enter only one question per line and use = to separate question from answer.", 'watu')?></p>
	
	<p><textarea name="text_captcha" rows="10" cols="70"><?php echo stripslashes($text_captcha);?></textarea></p>
	<div class="help"><?php printf(__('This question-based captcha can be enabled individually by selecting a checkbox in the %s settings form. If you do not check the checkbox, the captcha question will not be generated.', 'watu'), WATU_QUIZ_WORD);?></div>	
</div>		

	<?php if($is_admin):?>
			<div class="postbox">				
				<div class="inside">
					<h3 class="hndle"><?php _e('Role Management','watu')?></h3>
					<h3><?php _e('WordPress roles that can administrate the plugin', 'watu')?></h3>
			
					<p><?php _e('By default this is only the blog administrator. Here you can enable any of the other roles as well', 'watu')?></p>
					
					<p><?php foreach($roles as $key=>$r):
						if($key=='administrator') continue;
						$role = get_role($key);?>
						<input type="checkbox" name="manage_roles[]" value="<?php echo $key?>" <?php if($role->has_cap('watu_manage')) echo 'checked';?>> <?php _e($role->name, 'watu')?> &nbsp;
					<?php endforeach;?></p>
				</div>				
			</div>
	
	<?php endif;?>	
	
	<div class="postbox">
			<div class="inside">
			<h3 class="hndle"><?php _e('CSV Exports','watu')?></h3>
				<p><label><?php _e('Field separator:','watu')?></label> <select name="csv_delim">
					<option value="," <?php if($delim == ',') echo 'selected'?>><?php _e('Comma', 'watu');?></option>
					<option value="tab" <?php if($delim == 'tab') echo 'selected'?>><?php _e('TAB', 'watu');?></option>
				</select></p>
				<input type="checkbox" name="csv_quotes" value="1" <?php if(get_option('watu_csv_quotes')!==0) echo 'checked'?>> <?php _e('Add quotes around text fields (recommended)', 'watu')?>	
			</div>
		</div>
	
	<div class="postbox">
		<h3 class="hndle">&nbsp;<span><?php _e('Other settings', 'watu') ?></span></h3>
		<div class="inside" style="padding:8px">
			<p><label>&nbsp;<input type='checkbox' value="1" name='use_the_content' <?php if(get_option('watu_use_the_content') == '1') echo 'checked'?>  />&nbsp;<?php _e('Use "the_content" instead of our custom content filter (do not select this unless adviced so)', 'watu')?> </label></p>
			<p><label>&nbsp;<input type='checkbox' value="1" name='integrate_moolamojo' <?php if(get_option('watu_integrate_moolamojo') == '1') echo 'checked'?>  />&nbsp;<?php printf(__('Integrate <a href="%s" target="_blank">MoolaMojo</a> plugin to transfer %s points to the user virtual credits balance. The plugin must be installed and activated.', 'watu'), 'https://moolamojo.com', WATU_QUIZ_WORD)?> </label></p>
			<p><label>&nbsp;<input type='checkbox' value="1" name='dont_autoscroll' <?php if(get_option('watu_dont_autoscroll') == '1') echo 'checked'?>  />&nbsp;<?php _e('Do not auto-scroll top the page when user goes from page to page in a paginated quiz (selecting this is useful only on some page designs).', 'watu')?> </label></p>
		</div>
	</div>
	
	<div class="postbox">
		<h3 class="hndle">&nbsp;<span><?php _e('Database Option', 'watu') ?></span></h3>
		<div class="inside" style="padding:8px">
		<?php 
			$check = get_option('watu_delete_db');
		?>
		<label>&nbsp;<input type='checkbox' value="1" name='delete_db' <?php if($delete_db) echo 'checked'?> onclick="this.checked ? jQuery('#deleteDBConfirm').show() : jQuery('#deleteDBConfirm').hide();" />&nbsp;<?php _e('Delete stored Watu data when deinstalling the plugin.', 'watu')?> </label>
		
			<span id="deleteDBConfirm" style='display: <?php echo empty($delete_db) ? 'none' : 'inline';?>'>
				<?php _e('Please confirm by typing "yes" in the box:', 'watu')?> <input type="text" name="delete_db_confirm" value="<?php echo get_option('watu_delete_db_confirm')?>">		
			</span>
		</div>
	</div>
		
	<p class="submit">	
	<span id="autosave"></span>
	<input type="submit" name="submit" value="<?php _e('Save Options', 'watu') ?>"  class="button-primary" />
	</p>
	
	</div>
</div>
<?php wp_nonce_field('watu_options'); ?>
	</form>
	
	
	<div class="wrap">
		<div class="postbox">
			<div class="inside">
				<h2><?php printf(__('Ajax in %s', 'watu'), WATU_QUIZ_WORD_PLURAL); ?></h2>
				
				<p><?php printf(__('Here you can select %s which will be submitted by regular post submit rather than using Ajax. You may want to do this mostly for the following reason:', 'watu'), WATU_QUIZ_WORD_PLURAL)?></p>
				
				<ul>				
					<li><?php _e('To embed in the "Final screen" content from plugins that do not normally work well in Ajax mode.', 'watu')?></li>
				</ul>
				
				<p><b><?php printf(__('The selected %s will NOT use Ajax when users submit them.', 'watu'), WATU_QUIZ_WORD_PLURAL)?></b></p>
				
				<form name="post" action="" method="post" id="post">
				<div>
					<div class="postarea">
						<?php foreach($quizzes as $quiz):?>
							<input type="checkbox" name="no_ajax[]" value="<?php echo $quiz->ID?>" <?php if(!empty($quiz->no_ajax)) echo 'checked'?>> <?php echo stripslashes($quiz->name);?><br>
						<?php endforeach;?>
					</div>
										
					<p><input type="submit" name="save_ajax_settings" value="<?php _e('Save Ajax Related Settings', 'watu')?>" class="button-primary"></p>
				</div>
				<?php wp_nonce_field('watu_ajax_options'); ?>
				</form>
			</div>	
		</div>
	</div>

	</div>
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
</div>	
