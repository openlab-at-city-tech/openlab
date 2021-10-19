<style type="text/css">
textarea {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;

    width: 100%;
}
</style>
<div class="wrap watupro-wrap">
<h1><?php _e("Watu PRO Settings", 'watupro'); ?></h1>

<p><?php _e('Go to', 'watupro')?> <a href="admin.php?page=watupro_exams"><?php printf(__("Manage Your %s", 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))?></a></p>

<form name="post" action="" method="post" id="post">
<div id="poststuff">

	<h2 class="nav-tab-wrapper">
		<a class='nav-tab nav-tab-active' href='#' onclick="watuproChangeTab(this, 'generalsettings');return false;"><?php _e('General Settings', 'watupro')?></a>		
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'themesettings');return false;" id="themelnk"><?php _e('Theme &amp; Design', 'watupro')?></a>		
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'captchasettings');return false;"><?php _e('Captcha &amp; reCaptcha', 'watupro')?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'usersettings');return false;"><?php _e('User Settings and Pages', 'watupro')?></a>			
		<?php if(watupro_intel()):?>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'paymentsettings');return false;"><?php _e('Payment Settings', 'watupro')?></a>
		<?php endif;?>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'ajaxsettings');return false;"><?php printf(__('Ajax in %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'defaultsettings');return false;"><?php _e('Defaults', 'watupro');?></a>
		<?php if(!empty($watu_exams) and count($watu_exams)):?>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'watusettings');return false;"><?php printf(__('%s from Watu Basic', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))?></a>
		<?php endif;?>
	</h2>

<div id="postdiv" class="postarea">
	<div class="postbox watupro-tab-div" id="generalsettings">
		<div class="inside" style="padding:8px">
			<?php showWatuProOption('single_page', sprintf(__('Show all questions in a <strong>single page</strong> (This global setting can be overwritten for every %s)', 'watupro'), WATUPRO_QUIZ_WORD)); ?>
			
			<p><input type="checkbox" name="debug_mode" value="1" <?php if(get_option('watupro_debug_mode')) echo 'checked'?> /> <?php _e('Enable debug mode to see SQL errors. (Useful in case you have any problems)', 'watupro')?></p>
			
			<p><input type="checkbox" name="low_memory_mode" value="1" <?php if($low_memory_mode == 1) echo 'checked'?> onclick="if(this.checked) {this.form.stats_widget_off.checked = true;}" /> <?php printf(__('Enable low memory mode to reduce the server resources usage. <a href="%s" target="_blank">Learn more.</a>', 'watupro'), 'http://blog.calendarscripts.info/watupro-low-memory-mode/')?></p>
			
			<p><input type="checkbox" name="stats_widget_off" value="1" <?php if($low_memory_mode == 1 or get_option('watupro_stats_widget_off') == 1) echo 'checked'?> /> <?php _e('Switch off the dashboard stats widgets. (In case it slows down your administration dahsboard.)', 'watupro');?></p>
			
			<p><input type="checkbox" name="always_load_scripts" value="1" <?php if(get_option('watupro_always_load_scripts')) echo 'checked'?> /> <?php printf(__('Load WatuPRO javascripts on all pages (select this only if %s do not work otherwise).', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
			
			<p><input type="checkbox" name="disable_copy" value="1" <?php if(get_option('watupro_disable_copy')) echo 'checked'?> /> <?php printf(__('Disable copy and context menu on %s.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
			
			<p><?php _e('Send the automated emails from this address:', 'watupro')?> <input type="text" name="watupro_admin_email" value="<?php echo watupro_admin_email()?>" size="30"><br>
			<?php _e('This defaults to your main admin email. However to set a friendlier sender name you can overwrite it here by entering your name in format: <b>Your Name &lt;email@domain.com&gt;</b>', 'watupro')?></p>
		</div>
		

			<h2 class="hndle"><?php _e('Automated Dashboard Updates', 'watupro') ?></h2>	
			<div class="inside" style="padding:8px">
				<p><?php _e('If you want to update the plugin automatically through the dashboard you need to enter the email used to purchase it, and the license key. These fields are not required to use the plugin: they are needed only for automated dashboard updates. You can still use the plugin and update it manually even without a license key.', 'watupro');?></p>
				<p><label><?php _e('Order email address:','watupro')?></label> <input type="text" name="license_email" value="<?php echo get_option('watupro_license_email');?>"></p>
				<p><label><?php _e('License key:','watupro')?></label> <input type="text" name="license_key" value="<?php echo get_option('watupro_license_key');?>"></p>	
				<p><input type="checkbox" name="no_auto_updates" value="1" <?php if(get_option('watupro_no_auto_updates') == 1) echo 'checked'?>> <?php _e('Do not check for plugin updates.', 'watupro');?></p>
			</div>
		
		
				<h2 class="hndle"><?php _e('CSV Exports', 'watupro') ?></h2>
				<div class="inside" style="padding:8px">
					<p><label><?php _e('Field separator:','watupro')?></label> <select name="csv_delim">
						<option value="," <?php if($delim == ',') echo 'selected'?>><?php _e('Comma', 'watupro');?></option>
						<option value="tab" <?php if($delim == 'tab') echo 'selected'?>><?php _e('TAB', 'watupro');?></option>
					</select></p>
					<input type="checkbox" name="csv_quotes" value="1" <?php if(get_option('watupro_csv_quotes')) echo 'checked'?>> <?php _e('Add quotes around text fields (recommended)', 'watupro')?>	
				</div>
			
			
				<h2 class="hndle"><?php _e('Database', 'watupro') ?></h2>
				<div class="inside" style="padding:8px">
					<p><input type="checkbox" name="auto_db_cleanup" value="1" <?php if(get_option('watupro_auto_db_cleanup')=='1') echo 'checked'?>> 					
						<?php printf(__('Automatically cleanup user submitted data older than %s days', 'watupro'), '<input type="text" size="5" name="auto_db_cleanup_days" value="'.get_option('watupro_auto_db_cleanup_days').'">');?>
						&nbsp;
						<input type="checkbox" name="watupro_auto_db_cleanup_config_points_config" value="1" <?php if(!empty($db_cleanup_config['points_config'])) echo 'checked'?>> <?php _e('only if points are', 'watupro');?> <select name="watupro_auto_db_cleanup_config_points_condition">
							<option value="more" <?php if(!empty($db_cleanup_config['points_condition']) and $db_cleanup_config['points_condition'] == 'more') echo 'selected'?>><?php _e('above', 'watupro');?></option>
							<option value="less" <?php if(!empty($db_cleanup_config['points_condition']) and $db_cleanup_config['points_condition'] == 'less') echo 'selected'?>><?php _e('below', 'watupro');?></option>	
							</select>
						<input type="text" name="watupro_auto_db_cleanup_config_points" value="<?php echo empty($db_cleanup_config['points']) ? 0 : $db_cleanup_config['points']?>" size="4">		
						
						&nbsp;
						<input type="checkbox" name="watupro_auto_db_cleanup_config_percent_config" value="1" <?php if(!empty($db_cleanup_config['percent_config'])) echo 'checked'?>> <?php _e('only if percent correct answers are', 'watupro');?> <select name="watupro_auto_db_cleanup_config_percent_condition">
							<option value="more" <?php if(!empty($db_cleanup_config['percent_condition']) and $db_cleanup_config['percent_condition'] == 'more') echo 'selected'?>><?php _e('above', 'watupro');?></option>
							<option value="less" <?php if(!empty($db_cleanup_config['percent_condition']) and $db_cleanup_config['percent_condition'] == 'less') echo 'selected'?>><?php _e('below', 'watupro');?></option>	
							</select>
						<input type="text" name="watupro_auto_db_cleanup_config_percent" value="<?php echo empty($db_cleanup_config['percent']) ? 0 : $db_cleanup_config['percent']?>" size="4">%					
						</p>
						
						<p><input type="checkbox" name="auto_db_blankout" value="1" <?php if(get_option('watupro_auto_db_blankout')=='1') echo 'checked'?>> 					
						<?php printf(__('Automatically blankout user submitted data older than %s days', 'watupro'), '<input type="text" size="5" name="auto_db_blankout_days" value="'.get_option('watupro_auto_db_blankout_days').'">');?>
						&nbsp;
						<input type="checkbox" name="watupro_auto_db_blankout_config_points_config" value="1" <?php if(!empty($db_blankout_config['points_config'])) echo 'checked'?>> <?php _e('only if points are', 'watupro');?> <select name="watupro_auto_db_blankout_config_points_condition">
							<option value="more" <?php if(!empty($db_blankout_config['points_condition']) and $db_blankout_config['points_condition'] == 'more') echo 'selected'?>><?php _e('above', 'watupro');?></option>
							<option value="less" <?php if(!empty($db_blankout_config['points_condition']) and $db_blankout_config['points_condition'] == 'less') echo 'selected'?>><?php _e('below', 'watupro');?></option>	
							</select>
						<input type="text" name="watupro_auto_db_blankout_config_points" value="<?php echo empty($db_blankout_config['points']) ? 0 : $db_blankout_config['points']?>" size="4">		
						
						&nbsp;
						<input type="checkbox" name="watupro_auto_db_blankout_config_percent_config" value="1" <?php if(!empty($db_blankout_config['percent_config'])) echo 'checked'?>> <?php _e('only if percent correct answers are', 'watupro');?> <select name="watupro_auto_db_blankout_config_percent_condition">
							<option value="more" <?php if(!empty($db_blankout_config['percent_condition']) and $db_blankout_config['percent_condition'] == 'more') echo 'selected'?>><?php _e('above', 'watupro');?></option>
							<option value="less" <?php if(!empty($db_blankout_config['percent_condition']) and $db_blankout_config['percent_condition'] == 'less') echo 'selected'?>><?php _e('below', 'watupro');?></option>	
							</select>
						<input type="text" name="watupro_auto_db_blankout_config_percent" value="<?php echo empty($db_blankout_config['percent']) ? 0 : $db_blankout_config['percent']?>" size="4">%					
						</p>
						
						
					<p><?php _e('These settings let you keep your DB space usage low.', 'watupro');?> <?php _e('Cleaning up all data may affect user levels and points, and the reports. Alternatively you can just blank out the data which will keep all user points and reports and will only remove the textual data from some fields. This will reduce less DB space but will keep most of the things intact.', 'watupro');?><br />
					<?php printf(__('This function will be executed when you visit your <a href="%s">%s</a> page.', 'watupro'), 'admin.php?page=watupro_exams', WATUPRO_QUIZ_WORD_PLURAL);?></p>	
				</div>
				
				<h2 class="hndle"><?php printf(__('Timed %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></h2>
				<div class="inside" style="padding:8px">
					<p><?php printf(__('Time allowance for timed %s (seconds):','watupro'), WATUPRO_QUIZ_WORD_PLURAL);?> <input type="text" size="6" name="timer_allowance" value="<?php echo $timer_allowance;?>"><br />
					<?php printf(__('When a timed %s is auto-submitted there is some time required for the server to process the results. In order to avoid a wrong "time expired" message we allow 10 seconds by default. Slow servers, very large %s or overloaded sites may need more. Change this allowance in case you are getting empty data from automatically submitted %s.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD_PLURAL);?> </p>
				</div>
				
				<h2 class="hndle"><?php printf(__('Contact details in %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL); ?></h3>
				
				<div class="inside" style="padding:8px">
					<p><input type="checkbox" name="save_contacts" value="1" <?php if(get_option('watupro_save_contacts') == 1) echo 'checked';?>> <?php printf(__('When a %1$s requires contact details and the user is logged in save these details and pre-fill them in other %2$s.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD_PLURAL);?></p>
				</div>
				
				<br>
				<h2 class="hndle"><?php _e('MoolaMojo Integration', 'watupro') ?></h3>
					<div class="inside" style="padding:8px">
					<input type="checkbox" name="integrate_moolamojo" value="1" <?php if(get_option('watupro_integrate_moolamojo') == 1) echo 'checked'?>> <?php printf(__('Integrate with <a href="%s" target="_blank">MoolaMojo</a> so %s points can be transferred as virtual credits.', 'watupro'), 'https://moolamojo.com', WATUPRO_QUIZ_WORD);?> <br>
					<?php printf(__('The MoolaMojo plugin must be installed and active. The setting can be reconfigured per %s so instead of transferring the earned points, a fixed amount of credits depending on the achieved grade/result is transferred.', 'watupro'), WATUPRO_QUIZ_WORD);?>
				</div>

			
			<a name="roles">&nbsp;</a>
			<h2 class="hndle"><?php printf(__('Roles that can manage %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></h2>
			<div class="inside" style="padding:8px">		
				<p><?php printf(__('By default only Administrator and Super admin can manage WatuPRO %s. You can enable other roles here.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
				<p><?php foreach($roles as $key=>$r):
								if($key=='administrator') continue;
								$role = get_role($key);?>
								<input type="checkbox" name="manage_roles[]" value="<?php echo $key?>" <?php if(!empty($role->capabilities['watupro_manage_exams'])) echo 'checked';?>> <?php echo $role->name?> &nbsp;
							<?php endforeach;?></p>
				<?php if(watupro_intel() and current_user_can('manage_options')):?>
					<p><a href="admin.php?page=watupro_multiuser" target="_blank"><?php _e('Fine-tune these settings.', 'watupro')?></a></p>
				<?php endif;?>
				<?php if(current_user_can('manage_options')):?>
					<p><input type="checkbox" name="unfiltered_html" value="1" <?php if(get_option('watupro_unfiltered_html') == 1) echo "checked"?>> 
						<?php printf(__('Allow these users to include raw HTML in %s. Check this only if you trust them.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></p>
				<?php endif;?>
				<p><?php _e('Only administrator or superadmin can change this!', 'watupro')?></p>
			</div>
			
			<div class="inside" style="padding:8px"><p>
				<input type="submit" value="<?php _e('Save Options', 'watupro') ?>" class="button-primary" />
			</p></div>	
	</div>
	<!-- end general settings -->

	<!-- theme and design -->
	<div class="postbox watupro-tab-div" id="themesettings" style="display:none;">
		<div class="inside" style="padding:8px">
			<label><?php _e('Select design theme:', 'watupro')?></label>
			<select name="design_theme">			
				<?php foreach($design_themes as $theme):?>
					<option value="<?php echo $theme?>" <?php if($theme == $watupro_design_theme) echo 'selected'?>><?php echo $theme?></option>
				<?php endforeach;?>
			</select>
			<a href="http://blog.calendarscripts.info/design-themes-from-watupro-4-6-5/" target="_blank"><?php _e('See previews', 'watupro')?></a> | <a href="admin.php?page=watupro_design_themes" target="_blank"><?php _e('Create and manage your custom themes', 'watupro');?></a> 
			
				<p><?php _e('There are some properties you can specify here without editing any CSS file or using design themes. If you specify them, they will override any CSS specified by your theme. If you leave them blank, the theme defaults will be used.', 'watupro');?></p>
			
			<h4><?php _e('Spacing Adjustments:', 'watupro');?></h4>	
			
			<p><?php _e('Spacing between questions:', 'watupro');?> <input type="text" name="question_spacing" size="4" value="<?php echo empty($ui['question_spacing']) ? '' : $ui['question_spacing']?>"> <?php _e('px', 'watupro');?> &nbsp;
			<?php _e('Spacing between question and answers:', 'watupro');?> <input type="text" name="answer_spacing" size="4" value="<?php echo empty($ui['answer_spacing']) ? '' : $ui['answer_spacing']?>"> <?php _e('px', 'watupro');?>
			<?php _e('Space before "Question X of Y" line:', 'watupro');?> <input type="text" name="qxofy_spacing" size="4" value="<?php echo empty($ui['qxofy_spacing']) ? '' : $ui['qxofy_spacing']?>"> <?php _e('px', 'watupro');?></p>
			
			<p><?php _e('Vertical adjustment for radio buttons and checkboxes on quesiton choices:', 'watupro');?> <input type="text" name="choices_valign" value="<?php echo empty($ui['choices_valign']) ? '' : $ui['choices_valign']?>" size="6">em<br>
			<em><?php _e('This is used if the answer text does not perfectly align to the radio buttons or checkboxes. The value is in em and can be negative and contain decimals.', 'watupro');?></em></p>
			
			<h4><?php _e('Spacing Adjustments for Mobile Screens:', 'watupro');?></h4>
			
			<p><?php _e('Apply these when the screen max width is:', 'watupro');?> <input type="text" size="4" name="media_query_mobile" value="<?php echo empty($ui['media_query_mobile']) ? 600 : $ui['media_query_mobile']?>"> <?php _e('px.', 'watupro');?></p>
			
			<p><?php _e('By default we have 0.5em top and bottom margin on question choices (radio buttons, checkboxes, etc) to avoid "clickable elements too close together" problems. You can increase or decrease this here.', 'watupro');?></p>
			
			<p><?php printf(__('Top and bottom margin on question choices:', 'watupro'));?> <input type="text" size="4" name="mobile_choice_margin" value="<?php echo empty($ui['mobile_choice_margin']) ? '' : $ui['mobile_choice_margin']?>"> <select name="mobile_choice_margin_unit">
				<option value="em" <?php if( (empty($ui['mobile_choice_margin_unit']) and empty($ui['mobile_choice_margin'])) or (!empty($ui['mobile_choice_margin_unit']) and $ui['mobile_choice_margin_unit'] == 'em')) echo 'selected'?>><?php _e('em', 'watupro');?></option>
				<option value="px" <?php if((empty($ui['mobile_choice_margin_unit']) and !empty($ui['mobile_choice_margin'])) or (!empty($ui['mobile_choice_margin_unit']) and $ui['mobile_choice_margin_unit'] == 'px')) echo 'selected'?>><?php _e('px.', 'watupro');?></option>
			</select></p>	
			
			<h4><?php _e('Buttons design','watupro');?></h4>
			<p><?php _e('Width in px.:', 'watupro');?> <input type="text" name="buttons_width" size="4" value="<?php echo empty($ui['buttons_width']) ? '' : $ui['buttons_width']?>"> 
			<?php _e('Height in px.:', 'watupro');?> <input type="text" name="buttons_height" size="4" value="<?php echo empty($ui['buttons_height']) ? '' : $ui['buttons_height']?>">
			<?php _e('Font size:', 'watupro');?> <input type="text" size="8" name="buttons_font_size" value="<?php echo empty($ui['buttons_font_size']) ? '' : $ui['buttons_font_size']?>"><?php printf(__('(Example: "14px" or "small" or "120%%" etc. Enter without quotes. More examples <a href="%s" target="_blank">here</a>)', 'watupro'), 'https://developer.mozilla.org/en-US/docs/Web/CSS/font-size');?></p>
			<p><input type="checkbox" name="use_legacy_buttons_table" value="1" <?php if(!empty($ui['use_legacy_buttons_table'])) echo 'checked';?>>
			<?php _e('Use the legacy buttons table (useful mostly if you have written custom CSS for it)', 'watupro');?></p>
			<h4><?php _e('Font size','watupro');?></h4>
			<p><?php _e('Global within the quiz:', 'watupro');?> <input type="text" name="quiz_font_size" size="4" value="<?php echo empty($ui['quiz_font_size']) ? '' : $ui['quiz_font_size']?>"> 
			<?php _e('Font size of questions:', 'watupro');?> <input type="text" name="question_font_size" size="4" value="<?php echo empty($ui['question_font_size']) ? '' : $ui['question_font_size']?>">
			<?php _e('Font size of choices (answers):', 'watupro');?> <input type="text" name="choice_font_size" size="4" value="<?php echo empty($ui['choice_font_size']) ? '' : $ui['choice_font_size']?>">
			<?php if(watupro_intel()):?>
				<?php _e('Font size inside boxes of "Fill the gaps" questions:', 'watupro');?> <input type="text" name="gap_font_size" size="4" value="<?php echo empty($ui['gap_font_size']) ? '' : $ui['gap_font_size']?>">
			<?php endif;?></p>		
			
			<h4><?php _e('Additional CSS', 'watupro');?></h4>
			
			<p><textarea name="additional_css"><?php echo empty($ui['additional_css']) ? '' : $ui['additional_css'];?></textarea></p>
			
			<h4><?php _e('Flag for review image','watupro');?></h4>
			
			<p><input type="radio" name="flag_review" value="" <?php if(empty($ui['flag_review'])) echo 'checked'?>> <img src="<?php echo WATUPRO_URL?>/img/mark-review.png" alt="Flag image">
			&nbsp; <input type="radio" name="flag_review" value="-excl" <?php if(!empty($ui['flag_review']) and $ui['flag_review'] == '-excl') echo 'checked'?>> <img src="<?php echo WATUPRO_URL?>/img/mark-review-excl.png" alt="Flag image"></p>
			
			<h4><?php _e('Timer', 'watupro');?></h4>
			<p><?php _e('Position:', 'watupro')?> <select name="timer_position" onchange="this.value == 'fixed' ? jQuery('#wtpTimerPosition').show() :jQuery('#wtpTimerPosition').hide();">
				<option value=""><?php printf(__('Normal (static above the %s)', 'watupro'), WATUPRO_QUIZ_WORD);?></option>
				<option value="fixed" <?php if(!empty($ui['timer_position']) and $ui['timer_position'] == 'fixed') echo 'selected'?>><?php _e('Scrolling with the screen', 'watupro');?></option>
			</select>
				<span style='display:<?php echo empty($ui['timer_position']) ? 'none' : 'inline';?>' id="wtpTimerPosition"> &nbsp;
					<?php _e('Distance from', 'watupro');?> <select name="timer_distance_vertical">
						<option value="top"><?php _e('top', 'watupro');?></option>
						<option value="bottom" <?php if(@$ui['timer_distance_vertical'] == 'bottom') echo 'selected'?>><?php _e('bottom', 'watupro');?></option>
					</select>
					<input type="text" size="4" name="timer_position_top" value="<?php echo @$ui['timer_position_top']?>"><?php _e('px', 'watupro');?> &nbsp;
					<?php _e('Distance from:', 'watupro');?> <select name="timer_distance_horizontal">
						<option value="left"><?php _e('left', 'watupro');?></option>
						<option value="right" <?php if(@$ui['timer_distance_horizontal'] == 'right') echo 'selected'?>><?php _e('right', 'watupro');?></option>
					</select>
					<input type="text" size="4" name="timer_position_left" value="<?php echo @$ui['timer_position_left']?>"><?php _e('px', 'watupro');?>
				</span>		
			</p>
			<p>	
				<?php _e('Font color:', 'watupro');?> <input type="text" name="timer_color" value="<?php echo @$ui['timer_color'];?>" size="4">
				<?php _e('Font size:', 'watupro');?> <input type="text" name="timer_font_size" value="<?php echo @$ui['timer_font_size'];?>" size="4">
			</p>
			<p><input type="radio" name="timer_format" value="textual" <?php if(empty($ui['timer_format']) or $ui['timer_format'] == 'textual') echo 'checked'?>> <?php _e('Display in textual format. Example: 5 minutes and 25 seconds', 'watupro');?><br>
			<input type="radio" name="timer_format" value="short" <?php if(!empty($ui['timer_format']) and $ui['timer_format'] == 'short') echo 'checked'?>> <?php _e('Display in short format. Example: 5:25', 'watupro');?></p>
			
			
			<?php if(watupro_intel()):?>
				<h4><?php _e('Sortable questions:', 'watupro');?></h4>
				<p><?php _e('Border:', 'watupro');?> <input type="text" name="sortable_border" value="<?php echo @$ui['sortable_border'];?>" size="20"> <?php printf(__('Short CSS syntax for border. The default is "%s"', 'watupro'), '1pt dashed #DDD');?></p>
				<p>
					<?php _e('Font color:', 'watupro');?> <input type="text" name="sortable_color" value="<?php echo @$ui['sortable_color'];?>" size="4">
					<?php _e('Background color:', 'watupro');?> <input type="text" name="sortable_bgcolor" value="<?php echo @$ui['sortable_bgcolor'];?>" size="4">
					<?php _e('Font size:', 'watupro');?> <input type="text" name="sortable_font_size" value="<?php echo @$ui['sortable_font_size'];?>" size="4">
				</p>
			<?php endif;?>
			
			<h4><?php _e('Emailing test results', 'watupro');?></h4>
			<input type="checkbox" name="email_text_checkmarks" value="1" <?php if(get_option('watupro_email_text_checkmarks') == 1) echo 'checked'?>> <?php _e('When emailing test results use textual checkmarks instead of graphical checkmarks to show which answer is correct or wrong. You may prefer this because many email programs do not show graphics by default.', 'watupro');?>
			
			<h4><?php _e('Custom login/registration URL', 'watupro');?></h4>
			<p><?php printf(__('If you are using custom login or registration page you can change the default "You need to be logged in" message that appears on member-only %1$s here. The box below accepts HTML tags. If your custom login and registration URLs accept some "redirect to" parameter you can use the %2$s variable to pass the URL of the %3$s where to redirect back after login or registration.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, '{{{quiz-url}}}', WATUPRO_QUIZ_WORD);?><br>
			<textarea rows="3" class="watupro-textarea" name="login_register_text"><?php echo stripslashes(get_option('watupro_login_register_text'));?></textarea></p>
			
			<h4><?php _e('Define Wording', 'watupro');?></h4>
			<p><?php _e('Words used for "quiz/quizzes":', 'watupro');?> <input type="text" size="10" name="quiz_word" value="<?php echo get_option('watupro_quiz_word');?>"> / <input type="text" size="10" name="quiz_word_plural" value="<?php echo get_option('watupro_quiz_word_plural');?>"> <?php printf(__('You can change some other phrases <a href="%s" target="_blank">here</a>. You can change any text within the plugin <a href="%s" target="_blank">this way</a>.', 'watupro'), 'admin.php?page=watupro_text_options', 'http://blog.calendarscripts.info/how-to-change-text-in-a-wordpress-plugin/');?></p>
			
			<h4><?php _e('Other adjustments', 'watupro');?></h4>
			
			<p><input type="checkbox" name="autocomplete_off" value="1" <?php if(!empty($ui['autocomplete_off'])) echo 'checked';?>> <?php printf(__('Switch off the browser autocomplete feature on the %s form to avoid showing data from previous attempts.', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
			
			<p><input type="checkbox" name="mathjax_problem" value="1" <?php if(!empty($ui['mathjax_problem'])) echo 'checked'?>> <?php _e("I'm using MathJax or similar LaTeX plugin and have problems with some question choices not being displayed.", 'watupro');?></p>
			
			<p><?php _e('When question title is available, on the admin pages show:', 'watupro');?> <select name="show_title_desc">
				<option value="0"><?php _e('title instead of question contents', 'watupro');?></option>
				<option value="1" <?php if(get_option('watupro_show_title_desc') == 1) echo 'selected';?>><?php _e('both title and question contents', 'watupro');?></option>
			</select></p>
		</div>
		<div class="inside" style="padding:8px">
			<p class="submit">
				<input type="submit" value="<?php _e('Save Options', 'watupro') ?>" class="button-primary" />
			</p>
		</div>
	</div>
	<!-- end theme and design -->

	<!-- captcha and recaptcha -->
	<div class="postbox watupro-tab-div" id="captchasettings" style="display:none;">
		<div class="inside" style="padding:8px">		
			<p><label><?php _e('ReCaptcha Public Key:', 'watupro')?></label> <input type="text" name='recaptcha_public' value="<?php echo get_option('watupro_recaptcha_public')?>" size="50"></p>
			<p><label><?php _e('ReCaptcha Private Key:', 'watupro')?></label> <input type="text" name='recaptcha_private' value="<?php echo get_option('watupro_recaptcha_private')?>" size="50"></p>
			
		<p><label><?php _e('reCaptcha Version:', 'watupro');?></label> <select name="recaptcha_version" onchange="watuproChangeReCaptchaVersion(this.value);">
			<option value="1" <?php if($recaptcha_version == 1) echo 'selected'?>><?php _e('1 (Old reCaptcha)', 'watupro');?></option>
			<option value="2" <?php if(empty($recaptcha_version) or $recaptcha_version == 2) echo 'selected'?>><?php _e('2 (no Captcha reCaptcha)', 'watupro');?></option>
		</select></p>	
		
		<div id="reCaptchaInfo" style='display:<?php echo ($recaptcha_version == 1) ? 'none' : 'block';?>'>
			<p><?php _e('Language code:', 'watupro');?> <input type="text" name="recaptcha_lang" value="<?php echo get_option('watupro_recaptcha_lang') ? get_option('watupro_recaptcha_lang') : 'en'?>" size="4"> <a href="https://developers.google.com/recaptcha/docs/language" target="_blank"><?php _e('See language codes', 'watupro');?></a></p>
			<p><?php _e('Note that global keys are not supported in No Captcha reCaptcha. You need to create explicit keys for your domains. If you want to test the captcha on localhost you have to create a key for "localhost".', 'watupro');?></p>
		</div>		
			
			<p><?php _e('Setting up <a href="http://www.google.com/recaptcha/intro/index.html" target="_blank">ReCaptcha</a> is optional. If you choose to do so you will be able to require image validation on chosen exams to avoid spam box submissions.', 'watupro');?></p>
		</div>
	
		<h2 class="hndle"><?php _e('Question based captcha', 'watupro')?></h2>
		<div class="inside" style="padding:8px">		
			<p><?php _e("In addition to ReCaptcha or instead of it, you can use a simple text-based captcha. We have loaded 3 basic questions but you can edit them and load your own. Make sure to enter only one question per line and use = to separate question from answer.", 'watupro')?></p>
			
			<p><textarea name="text_captcha" rows="10" cols="70"><?php echo stripslashes($text_captcha);?></textarea></p>
			<div class="help"><?php printf(__('This question-based captcha can be enabled individually by selecting a checkbox in the %s settings form. If you do not check the checkbox, the captcha question will not be generated.', 'watupro'), WATUPRO_QUIZ_WORD);?></div>
		</div>
		
		<div class="inside" style="padding:8px">
			<p class="submit">
				<input type="submit" value="<?php _e('Save Options', 'watupro') ?>" class="button-primary" />
			</p>
		</div>
	</div>
	<!-- end captcha and recaptcha -->
	
	<!-- user settings and pages -->
	<div class="postbox watupro-tab-div" id="usersettings" style="display:none;">
		<div class="inside">
			<p><input type="checkbox" name="auto_del_user_data" value="yes" <?php if(get_option('watupro_auto_del_user_data') == 'yes') echo 'checked'?>> <?php _e('Automatically delete user quiz-related data when the user account is deleted. Note: this will disregard the "Attribute all content to" option given by WordPress when you manually delete the user.', 'watupro')?></p>
			<?php if(class_exists('WatuPROPlay')):?>
				<p><input type="checkbox" name="del_play_data" value="yes" <?php if(get_option('watupro_del_play_data') == 'yes') echo 'checked'?>> <?php printf(__('When manually deleting all %s data of the user cleanup also all their achievements from the Play plugin.', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
			<?php endif;?>
				<p><input type="checkbox" name="gdpr" value="1" <?php if(get_option('watupro_gdpr') == 1) echo 'checked'?>> <?php printf(__('Enable <a href="%s" target="_blank">GDPR compliance features</a>. This means logged in users will be able to delete or export their quiz results. You should still ensure they can delete their whole WP user account.', 'watupro'), 'http://blog.calendarscripts.info/watupro-and-gdpr/');?></p>
				
				<p><input type="checkbox" name="calculate_total_user_points" value="1" <?php if(get_option('watupro_calculate_total_user_points') == 1) echo 'checked'?>> <?php printf(__('Calculate & display total points earned on <a href="%s" target="_blank">Users</a> page.', 'watupro'), 'users.php');?></p>
			<hr>	
		
			<p><input type="checkbox" name="nodisplay_myquizzes" value="1" <?php if(get_option('watupro_nodisplay_myquizzes')) echo 'checked'?> onclick="this.checked ? jQuery('#detailsDefaultView').hide() : jQuery('#detailsDefaultView').show();"> <?php printf(__('Do not display "My %s" page in user dashboard.', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))?></p>
			<p id="detailsDefaultView" style='display:<?php echo (get_option('watupro_nodisplay_myquizzes') == 1) ? 'none' : 'block';?>'><?php _e('Default view for "View details" pop-up:', 'watupro');?> <select name="taking_details_default_view">
				<option value="table"><?php _e('Table format', 'watupro');?></option>
				<option value="snapshot" <?php if(get_option('watupro_taking_details_default_view') == 'snapshot') echo 'selected';?>><?php _e('Snapshot of final page', 'watupro');?></option>
			</select>
			<?php _e('Default download view:', 'watupro');?> <select name="taking_details_default_download">
				<option value="snapshot"><?php _e('Snapshot of final page', 'watupro');?></option>
				<option value="table" <?php if(get_option('watupro_taking_details_default_download') == 'table') echo 'selected';?>><?php _e('Table format', 'watupro');?></option>
			</select>
				<span style='display:<?php echo (function_exists('pdf_bridge_init') and get_option('watupro_generate_pdf_certificates') == "1") ? 'none' : 'inline';?>'>
					<?php _e('File format:', 'watupro');?>
					<select name="taking_details_default_download_file">
						<option value="html"><?php _e('HTML (recommended)', 'watupro');?></option>
						<option value="doc" <?php if(get_option('watupro_taking_details_default_download_file') == 'doc') echo 'selected';?>><?php _e('Doc (MS Word)', 'watupro');?></option>
					</select>
				</span>
			</p>
			<p><?php _e('Hide these columns in "Table format" of "View details" page:', 'watupro');?> &nbsp;<input type="checkbox" name="view_details_hidden_columns[]" value="id" <?php if(!empty($view_details_hidden_columns) and in_array('id', $view_details_hidden_columns)) echo 'checked';?>><?php _e('ID', 'watupro');?>
			&nbsp;<input type="checkbox" name="view_details_hidden_columns[]" value="num" <?php if(!empty($view_details_hidden_columns) and in_array('num', $view_details_hidden_columns)) echo 'checked';?>><?php _e('No.', 'watupro');?>
			&nbsp;<input type="checkbox" name="view_details_hidden_columns[]" value="cat" <?php if(!empty($view_details_hidden_columns) and in_array('cat', $view_details_hidden_columns)) echo 'checked';?>><?php _e('Category', 'watupro');?>
			&nbsp;<input type="checkbox" name="view_details_hidden_columns[]" value="points" <?php if(!empty($view_details_hidden_columns) and in_array('points', $view_details_hidden_columns)) echo 'checked';?>><?php _e('Points', 'watupro');?>
			&nbsp;<input type="checkbox" name="view_details_hidden_columns[]" value="result" <?php if(!empty($view_details_hidden_columns) and in_array('result', $view_details_hidden_columns)) echo 'checked';?>><?php _e('Result', 'watupro');?> </p>
			<p><input type="checkbox" name="nodisplay_mycertificates" value="1" <?php if(get_option('watupro_nodisplay_mycertificates')) echo 'checked'?>> <?php _e('Do not display "My certificates" page in user dashboard.', 'watupro')?></p>
			<p><input type="checkbox" name="nodisplay_mysettings" value="1" <?php if(get_option('watupro_nodisplay_mysettings')) echo 'checked'?>> <?php printf(__('Do not display "%s Settings" page in user dashboard.', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></p>
			
			<p><?php printf(__('When user registers on the site from a %s that requires user login, assign them %s.', 'watupro'), WATUPRO_QUIZ_WORD, '<select name="register_role">
				<option value="student" '.((empty($register_role) or $register_role == 'student') ? 'selected' : '').'>'.__('student role').'</option>
				<option value="default" '.((!empty($register_role) and $register_role == 'default') ? 'selected' : '').'>'.__('default role for the site').'</option>
			</select>');?></p>
			
			<p><input type="checkbox" name="hide_stats_widget" value="1" <?php if(get_option('watupro_hide_stats_widget') == 1) echo 'checked';?>> <?php printf(__('Display the dashboard stats widget only to users with rights to manage %s.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></p>
			
			<?php if(watupro_module('reports')):?>
				<h2><?php _e('Reporting module tabs:', 'watupro');?></h2>
				<p><?php printf(__('In the "%s Reports" page do not display these tabs:', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></p>
				<p><input type="checkbox" name="nodisplay_reports_tests" value="1" <?php if(get_option('watupro_nodisplay_reports_tests')) echo 'checked'?>> <?php _e('Tests', 'watupro')?> &nbsp; <input type="checkbox" name="nodisplay_reports_skills" value="1" <?php if(get_option('watupro_nodisplay_reports_skills')) echo 'checked'?>> <?php _e('Skills', 'watupro')?> &nbsp;
				<input type="checkbox" name="nodisplay_reports_history" value="1" <?php if(get_option('watupro_nodisplay_reports_history')) echo 'checked'?>> <?php _e('History', 'watupro')?> &nbsp;</p>
			<?php endif;?>
			
			<p><input type="submit" value="<?php _e('Save Options', 'watupro')?>" class="button-primary"></p>
		</div>
	</div>
	<!-- end user settings and pages -->

	<!-- payment settings -->
	<?php if(watupro_intel()): 
		if(@file_exists(get_stylesheet_directory().'/watupro/i/payment-options.html.php')) require get_stylesheet_directory().'/watupro/i/payment-options.html.php';
		else require WATUPRO_PATH."/i/views/payment-options.html.php";
	endif;?>
	<!-- end payment settings -->
	
	<!-- defaults -->	
	<div class="postbox watupro-tab-div" id="defaultsettings" style="display:none;">
	
				<h2 class="hndle"><?php _e('Default Answer Type (When creating a new question)', 'watupro') ?></h2>
				<div class="inside" style="padding:8px">
					<?php $answer_type = get_option('watupro_answer_type'); ?>
					<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'radio') echo 'checked'?> id="answer_type_r" value='radio' /><?php _e("Single Answer", 'watupro')?> </label>
					&nbsp;&nbsp;&nbsp;
					<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'checkbox') echo 'checked'?> id="answer_type_c" value='checkbox' /><?php _e("Multiple Answers", 'watupro')?></label>
					&nbsp;&nbsp;&nbsp;
					<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'textarea') echo 'checked'?> id="answer_type_c" value='textarea' /><?php _e("Open End", 'watupro')?></label>
					
					<?php if(watupro_intel()):?>
						&nbsp;&nbsp;&nbsp;
						<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'gaps') echo 'checked'?> id="answer_type_c" value='gaps' /><?php _e("Fill the gaps", 'watupro')?></label>
						
						&nbsp;&nbsp;&nbsp;
						<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'sort') echo 'checked'?> id="answer_type_c" value='sort' /><?php _e("Sort the values", 'watupro')?></label>
						
						&nbsp;&nbsp;&nbsp;
						<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'nmatrix') echo 'checked'?> id="answer_type_c" value='nmatrix' /><?php _e("Matrix / Match values", 'watupro')?></label>
						
						&nbsp;&nbsp;&nbsp;
						<label>&nbsp;<input type='radio' name='answer_type' <?php if($answer_type == 'slider') echo 'checked'?> id="answer_type_c" value='slider' /><?php _e("Slider", 'watupro')?></label>
					<?php endif;?>
				</div>
			
			
				<h2 class="hndle"><?php _e('Default Answer Points', 'watupro') ?></h2>
				<div class="inside" style="padding:8px">
					<input type="checkbox" name="set_default_points" value="1" <?php if(!empty($set_default_points)) echo 'checked'?> onclick="this.checked ? jQuery('#watuproDefaultPoints').show() : jQuery('#watuproDefaultPoints').hide();"> <?php printf(__('Set default points for correct / wrong answers. This will auto-set the points when you click the "correct" checkbox next to each answer. The option can be overriden at %s level. You can use decimals.', 'watupro'), WATUPRO_QUIZ_WORD);?>
					<div id="watuproDefaultPoints" style='display:<?php echo empty($set_default_points) ? 'none' : 'block';?>;'>
						<p><?php _e('Points for correct answer:', 'watupro');?> <input type="text" size="6" name="correct_answer_points" value="<?php echo $correct_answer_points?>"> <?php _e('Ex.: 1.00');?></p> 
						<p><?php _e('Points for incorrect answer:', 'watupro');?> <input type="text" size="6" name="incorrect_answer_points" value="<?php echo $incorrect_answer_points?>"> </p>
					</div>	
				</div>
				
				<h2 class="hndle"><?php _e('Default Final Screen', 'watupro') ?></h2>
				<div class="inside" style="padding:8px">
					<p><?php printf(__('Default "Final screen" when creating a new %s', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
					<?php echo wp_editor(stripslashes($default_final_screen), 'default_final_screen', array("editor_class" => 'i18n-multilingual'));?>
				</div>
				
				<h2 class="hndle"><?php _e('Default Email Output', 'watupro') ?></h2>				
				<div class="inside" style="padding:8px">
					<p><?php printf(__('Default email output when creating a new %s', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
					<?php echo wp_editor(stripslashes($default_email_output), 'default_email_output', array("editor_class" => 'i18n-multilingual'));?>
					
					<p><input type="checkbox" name="exclude_details_of_taker" value="1" <?php if(!empty($ui['exclude_details_of_taker'])) echo 'checked';?>> <?php _e('Exclude the default "Details of ..." line from the contents of the email sent to admin. You can use the available variables instead.', 'watupro');?></p>
				</div>
				
		<div class="inside" style="padding:8px">
			<p class="submit">
				<input type="submit" value="<?php _e('Save Options', 'watupro') ?>" class="button-primary" />
			</p>
		</div>
	</div> <!-- end defaults -->

      <input type="hidden" name="save_options" value="1">
   </div>
</div>
<?php wp_nonce_field('watupro_options');?>
</form>

	<!-- ajax in quizzes -->	
	<div class="postbox watupro-tab-div" id="ajaxsettings" style="display:none;">
		<div class="inside">
			<p><?php printf(__('Here you can select %s which will be submitted by regular post submit rather than using Ajax. You may want to do this mostly for two reasons:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
			
			<ol>
				<li><?php _e('To allow file uploads in "open end" questions.', 'watupro')?></li>
				<li><?php _e('To embed in the "Final screen" content from plugins that do not normally work well in Ajax mode.', 'watupro')?></li>
			</ol>
			
			<p><b><?php printf(__('The selected %s will NOT use Ajax when users submit them.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></b></p>
			
			<form name="post" action="" method="post" id="post">
			<div>
				<div class="postarea">
					<?php foreach($quizzes as $quiz):?>
						<input type="checkbox" name="no_ajax[]" value="<?php echo $quiz->ID?>" <?php if(!empty($quiz->no_ajax)) echo 'checked'?>> <?php echo stripslashes($quiz->name);?><br>
					<?php endforeach;?>
				</div>
				
				<div class="postarea">
					<h2><?php _e('Limitations for uploading files:', 'watupro')?></h2>
					
					<p><?php printf(__('In the %s that do NOT use ajax for submitting you will be able to allow file uploads for open end questions. Set your size and file type limitations here. Note that your server may also imply limitations on the uploaded file size.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?><br>
					<b><?php printf(__('File uploads cannot be stored in an unfinished %s.', 'watupro'), WATUPRO_QUIZ_WORD);?></b></p>
					
					<p><?php _e('Max uploaded file size:', 'watupro')?> <input type="text" name="max_upload" value="<?php echo get_option('watupro_max_upload')?>" size="4"> <?php _e('In KB. Keep it reasonably low.', 'watupro')?></p>
					
					<p><?php _e('Allowed file extensions:', 'watupro')?> <input type="text" name="upload_file_types" value="<?php echo get_option('watupro_upload_file_types')?>" size="50"> <?php _e('Separate with comma like this: "jpg, gif, png, doc". (Input them without quotes, all small letters)', 'watupro')?></p>
				</div>
				
				<p><input type="submit" name="save_ajax_settings" value="<?php _e('Save Ajax Related Settings', 'watupro')?>" class="button-primary"></p>
			</div>
			</form>
		</div>	
	</div>
	<!-- end ajax in quizzes -->
	
	<!-- quizzes from Watu basic -->
	<?php if(!empty($watu_exams) and count($watu_exams)):?>
	<div class="postbox watupro-tab-div" id="watusettings" style="display:none;">
		<form method="post">
			<div class="inside">
				<?php if(!empty($copy_message)):?>
					<p class="watupro-alert"><?php echo $copy_message?></p>
				<?php endif;?>		
			
				<p><?php printf(__("You have %d %s created in the basic free Watu plugin. Do you want to copy these exams in Watu PRO? You can do this any time, and multiple times.", 'watupro'), sizeof($watu_exams), WATUPRO_QUIZ_WORD_PLURAL)?></p>
				
				<p><input type="checkbox" name="replace_watu_shortcodes" value="1"> <?php _e('Automatically replace all Watu shortcodes embedded in posts so they will be managed by WatuPRO', 'watupro')?></p>
				
				<p><input type="checkbox" name="copy_takings" value="1"> <?php _e('Copy also the results of users who submitted', 'watupro')?></p>
				
				<p class="submit"><input type="submit" name="copy_exams" value="<?php printf(__('Copy These %s to WatuPRO', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))?>" class="button-primary"></p>
			</div>
		</form>
	</div>			
	<?php endif;?>
	<!-- end  quizzes from Watu basic -->
	
</div>

<script type="text/javascript" >
function watuproChangeReCaptchaVersion(val) {
	 if(val == 1) jQuery('#reCaptchaInfo').hide();
	 else jQuery('#reCaptchaInfo').show();
}

function watuproChangeTab(lnk, tab) {
	jQuery('.watupro-tab-div').hide();
	jQuery('#' + tab).show();
	
	jQuery('.nav-tab-active').addClass('nav-tab').removeClass('nav-tab-active');
	jQuery(lnk).addClass('nav-tab-active');
}
</script>

<?php function showWatuProOption($option, $title) {?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('watupro_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php _e($title, 'watupro') ?></label><br />
<?php }