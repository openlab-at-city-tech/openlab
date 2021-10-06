<style type="text/css">
textarea {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;

    width: 100%;
}
</style>
<div class="wrap watupro-wrap">
<h1><?php echo ($action == 'new') ? sprintf(__('Add %s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)) :  sprintf(__('Edit %1$s: %2$s', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD), stripslashes($dquiz->name))?></h1>

<?php watupro_display_alerts(); ?>

<p><a href="admin.php?page=watupro_exams"><?php printf(__("Back to %s List", 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL))?></a> 
	<?php if(!empty($dquiz->ID)):?>| <a href="admin.php?page=watupro_copy_exam&id=<?php echo $dquiz->ID?>&comefrom=edit"><?php printf(__("Copy into another %s", 'watupro'), WATUPRO_QUIZ_WORD)?></a>
	| <a href="admin.php?page=watupro_questions&quiz=<?php echo $dquiz->ID?>"><?php _e('Manage Questions', 'watupro')?></a>
	| <a href="admin.php?page=watupro_grades&quiz=<?php echo $dquiz->ID?>"><?php if(empty($dquiz->is_personality_quiz)) _e('Manage Grades', 'watupro');
	else _e('Manage Personality Types', 'watupro');?></a><?php endif;?>
</p>

<form name="post" action="admin.php?page=watupro_exam" method="post" id="examForm" onsubmit="return WatuPROValidateExam(this);">
<div id="poststuff">
	<h2 class="nav-tab-wrapper">
		<a class='nav-tab nav-tab-active' href='#' onclick="watuproChangeTab(this, 'namedesc');return false;"><?php _e('Name and Description', 'watupro')?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'settings');return false;"><?php _e('General Settings', 'watupro')?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'useremailsettings');return false;" id="useremailsettingslnk"><?php _e('User and Email Related Settings', 'watupro')?></a>
		<?php if(watupro_intel()):?>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'intel');return false;"><?php _e('Intelligence Module Settings', 'watupro')?></a>			
		<?php endif;?>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'advanced');return false;"><?php _e('Other Advanced Settings', 'watupro')?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'finalscreen');return false;"><?php printf(__('Final Page / %s Result', 'watupro'), __('Quiz', 'watupro'))?></a>	
	</h2>
	
	<div class="postbox watupro-tab-div" id="namedesc">
		<div class="postbox" id="titlediv">
		    <h3 class="hndle"><span><?php printf(__('%s Name', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)) ?></span></h3>
		    
		    <div class="inside">
		    <textarea name='name' rows="1" cols="100" id="title" class="i18n-multilingual"><?php echo stripslashes($dquiz->name); ?></textarea>
		    </div>
		</div>
		<div class="inside">
			 <p><input type="checkbox" name="is_inactive" value="1" <?php if(!empty($dquiz->ID) and empty($dquiz->is_active)) echo 'checked'?>> <?php printf(__('Deactivate this %s.', 'watupro'), WATUPRO_QUIZ_WORD)?></p>   
			 
			 <?php if(!$is_published):?>
			 	<p><input type="checkbox" name="auto_publish" value="1" onclick="this.checked ? jQuery('#autoPublishSettings').show() : jQuery('#autoPublishSettings').hide();"> <b><?php printf(__('Automatically publish this %s in new post once I hit the "Save" button. (The new post will be auto-generated with the %s title used for post title.)', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></b></p>
			 	<div style="display:none;" id="autoPublishSettings">
			 		<h4><?php _e('Optional configurations (leave blank to use defaults)', 'watupro');?></h4>
			 		<p><?php _e('Post title:', 'watupro');?> <input type="text" name="auto_publish_name" size="30">
			 		<?php _e('Post type:', 'watupro');?> <select name="auto_publish_post_type">
			 		<?php foreach($post_types as $post_type):?>	
			 			<option value="<?php echo $post_type?>"><?php echo $post_type;?></option>
			 		<?php endforeach;?>
			 		</select></p>
			 	</div>
			 <?php endif;?>
		</div>
	
		 <h3 class="hndle"><?php _e('Optional description', 'watupro')?></h3>    
		 <div class="inside">
		 	
		 	<?php echo wp_editor(stripslashes($dquiz->description), 'description', array("editor_class" => 'i18n-multilingual'));?>
		 	<p><?php printf(__('If provided, the description will be shown when starting the %s. It can also be used in certificates. You can use the {{{button}}} tag <a href="%s" target="_blank">(more info)</a> to make the %s start with a start button.', 'watupro'), WATUPRO_QUIZ_WORD, 'http://blog.calendarscripts.info/create-start-button-in-watupro-using-the-button-tag/',  WATUPRO_QUIZ_WORD);?></p>
		 	
		 	<p><input type="checkbox" name="published_odd" value="1" <?php if(!empty($dquiz->published_odd)) echo 'checked'?> onclick="this.checked ? jQuery('#publishedURL').show() : jQuery('#publishedURL').hide();"> 
		 		<?php printf(__('This %s is published in custom field or other non-standard way (<a href="%s" target="_blank">what is this?</a>)', 'watupro'), WATUPRO_QUIZ_WORD, 'http://blog.calendarscripts.info/watupro-quizzes-published-in-custom-fields')?>
				<span id="publishedURL" style='display:<?php echo empty($dquiz->published_odd) ? 'none' : 'inline';?>'><?php _e('URL:', 'watupro')?> <input type="text" name="published_odd_url" size="40" value="<?php echo empty($dquiz->published_odd) ? '' : $dquiz->published_odd_url?>"></span>		 	
		 	</p>
		 </div>
		 
		  <h3 class="hndle"><?php _e('Internal comments', 'watupro')?></h3>    
		 <div class="inside">
		 	<textarea rows="5" cols="60" name="admin_comments"><?php echo empty($advanced_settings['admin_comments']) ? '' : stripslashes(rawurldecode($advanced_settings['admin_comments'])); ?></textarea>
		 	
		 	<p><?php _e('For management purposes. These comments will be shown only in the administration.', 'watupro');?></p>
		 
		 </div> 		
		 
		   <h3 class="hndle"><?php _e('Tags', 'watupro')?></h3>    
		 <div class="inside">
		 	<input type="text" size="100" name="tags" value="<?php echo empty($dquiz->tags) ? '' : str_replace('|', ', ', stripslashes(trim($dquiz->tags, '|')))?>">
		 	
		 	<p><?php _e('Enter multiple tags separated by comma. Tags are used for filtering in administration. They are not shown to the user.', 'watupro');?></p>
		 
		 </div> 		
		 
		 <h3 class="hndle"><?php _e('Thumbnail', 'watupro')?></h3>    
		 <div class="inside">
		 	<input type="text" size="100" name="thumb" value="<?php echo empty($dquiz->thumb) ? '' : $dquiz->thumb?>">
		 	
		 	<p><?php printf(__('Optional thumbnail can be used in the shortcodes which list %1$s. You can upload a thumbnail in your <a href="%2$s" target="_blank">Media library</a> and then paste its full URL in the field above.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, 'upload.php');?></p>
		 
		 </div> 		
		 
		</div>
	</div><!-- end namedesc-->	
	
	<div class="postbox watupro-tab-div" id="settings" style="display:none;">
	    <div class="inside">	        
	     <h3 class="hndle"><span><?php printf(__('%s Settings', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)) ?></span> </h3> 
	    <p> <?php _e('Randomization:', 'watupro')?> <select name="randomize_questions">
				<option value="0" <?php if(empty($dquiz->randomize_questions)) echo "selected"?>><?php _e('Display questions and answers in the way I entered them','watupro')?></option>    
				<option value="1" <?php if(!empty($dquiz->randomize_questions) and $dquiz->randomize_questions==1) echo "selected"?>><?php _e('Randomize questions and answers','watupro')?></option>
				<option value="2" <?php if(!empty($dquiz->randomize_questions) and $dquiz->randomize_questions==2) echo "selected"?>><?php _e('Randomize questions but NOT answers','watupro')?></option>
				<option value="3" <?php if(!empty($dquiz->randomize_questions) and $dquiz->randomize_questions==3) echo "selected"?>><?php _e('Randomize answers but NOT questions','watupro')?></option>
	    </select>  </p>
		 <p><input type="checkbox" id="groupByCat" name="group_by_cat" value="1" <?php if(!empty($dquiz->group_by_cat)) echo "checked"?> onclick="watuPROGroupByCat(this);watuPROChangePagination(this.form.single_page.value);"> <?php _e("Show questions grouped by category (useful if you have categorized your questions)", 'watupro')?>
		 &nbsp; <input type="checkbox" id="randomizeCats" name="randomize_cats" value="1" <?php if(!empty($dquiz->randomize_cats)) echo 'checked'?> <?php if(empty($dquiz->group_by_cat)) echo 'disabled'?>> <?php _e('Randomize categories', 'watupro')?></p>  
		 
		 <div id="groupByCatSettings" style='display:<?php echo empty($dquiz->group_by_cat) ? 'none' : 'block';?>;margin-left:50px;'>
		 	<p><?php _e('When this is selected the topics (question category headings and description) will be shown before each group of questions.', 'watupro');?></p>
		 	<p><?php _e('Heading for question category:', 'watupro');?> <select name="question_category_heading_tag">
		 		<option value="h1" <?php if(!empty($advanced_settings['question_category_heading_tag']) and $advanced_settings['question_category_heading_tag'] == 'h1') echo 'selected'?>>h1</option>
		 		<option value="h2" <?php if(empty($advanced_settings['question_category_heading_tag']) or $advanced_settings['question_category_heading_tag'] == 'h2') echo 'selected'?>>h2</option>
		 		<option value="h3" <?php if(!empty($advanced_settings['question_category_heading_tag']) and $advanced_settings['question_category_heading_tag'] == 'h3') echo 'selected'?>>h3</option>
		 		<option value="h4" <?php if(!empty($advanced_settings['question_category_heading_tag']) and $advanced_settings['question_category_heading_tag'] == 'h4') echo 'selected'?>>h4</option>
		 	</select>
		 	&nbsp;
		 	<?php _e('Heading for question subcategory:', 'watupro');?> <select name="question_subcategory_heading_tag">
		 		<option value="h1" <?php if(!empty($advanced_settings['question_subcategory_heading_tag']) and $advanced_settings['question_subcategory_heading_tag'] == 'h1') echo 'selected'?>>h1</option>
		 		<option value="h2" <?php if(!empty($advanced_settings['question_subcategory_heading_tag']) and $advanced_settings['question_subcategory_heading_tag'] == 'h2') echo 'selected'?>>h2</option>
		 		<option value="h3" <?php if(empty($advanced_settings['question_subcategory_heading_tag']) or $advanced_settings['question_subcategory_heading_tag'] == 'h3') echo 'selected'?>>h3</option>
		 		<option value="h4" <?php if(!empty($advanced_settings['question_subcategory_heading_tag']) and $advanced_settings['question_subcategory_heading_tag'] == 'h4') echo 'selected'?>>h4</option>
		 	</select></p>
		 	<p id="catHeaderEveryPage" style='display:<?php echo (@$dquiz->single_page != WATUPRO_PAGINATE_PAGE_PER_CATEGORY) ? 'block' : 'none';?>'>
		 		<input type="checkbox" name="cat_header_every_page" <?php if(!empty($advanced_settings['cat_header_every_page'])) echo 'checked'?>> <?php _e('Show the category title and description on every page.', 'watupro');?>
		 	</p>
		 </div>
		 
		 <p><input type="checkbox" name="live_result" value="1" <?php if(!empty($dquiz->live_result)) echo "checked"?> onclick="this.checked ? jQuery('#liveResultNoAnswer').show() : jQuery('#liveResultNoAnswer').hide();"> <?php _e('Answer to each question can be seen immediately by pressing a button', 'watupro')?>
			<span id="liveResultNoAnswer" style='display:<?php echo empty($dquiz->live_result) ? 'none' : 'inline';?>'>
			&nbsp;
				<input type="checkbox" name="live_result_no_answer" value="1" <?php if(!empty($advanced_settings['live_result_no_answer'])) echo "checked"?>> <?php _e('Answering the question by the respondent is not required to see the result (by default it is required)', 'watupro');?>
			</span>		 
		 </p>		 
		 
		 <p id="enableSaveButton">	<input type="checkbox" name="enable_save_button" value="1" <?php if(!empty($dquiz->enable_save_button)) echo "checked"?>> <?php printf(__('Enable save button to allow users continue their %s later.', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
		 
		 <p><input type="checkbox" name="flag_for_review" value="1" <?php if(!empty($advanced_settings['flag_for_review']) and $advanced_settings['flag_for_review']=='1') echo 'checked'?>> <?php _e('Allow users to flag questions for review. In this case they will be prompted to review their flagged questions before submitting the quiz.', 'watupro')?></p>
		 
	    <p><?php _e('Set time limit of', 'watupro')?> <input type="text" name="time_limit" size="4" value="<?php echo empty($dquiz->time_limit) ? 0 : $dquiz->time_limit?>" onkeyup="wtpMaybeWarnAboutTimer(this.form);"> <?php _e('minutes (Leave it blank or enter 0 to not set any time limit.)', 'watupro')?> <a href="http://blog.calendarscripts.info/watupro-how-does-the-timer-work/" target="_blank"><?php _e('How does it work?', 'watupro')?></a>
			<div id="timerTurnsRed" style='margin-left:50px;display:<?php echo empty($dquiz->time_limit) ? 'none' : 'block';?>'>
				<?php printf(__('Timer turns red when less than %s seconds remain.', 'watupro'), '<input type="text" name="timer_turns_red" value="'.intval(empty($advanced_settings['timer_turns_red']) ? 0 : $advanced_settings['timer_turns_red']).'" size="4">');?>
				<?php printf(__('You can adjust the timer position and design at the <a href="%s" target="_blank">WatuPRO Settings page</a>.', 'watupro'), 'admin.php?page=watupro_options');?>
			</div>	    
	    </p>
	    <p><?php _e('Pull', 'watupro')?> <input type="text" name="pull_random" size="4" value="<?php echo empty($dquiz->pull_random) ? 0 : $dquiz->pull_random?>"> <?php _e('random questions', 'watupro')?> 
			[ <input type="checkbox" name="random_per_category" value="1" <?php if(!empty($dquiz->random_per_category)) echo "checked"?>> <?php _e('per category', 'watupro')?> ]   
	    <?php printf(__('each time when showing the %s (Leave it blank or enter 0 to show all questions)', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
	    
	    <p><?php _e('Show max', 'watupro')?> <input type="text" name="num_answers" size="4" value="<?php echo empty($dquiz->num_answers) ? 0 : $dquiz->num_answers?>"> <?php _e('random answers to each question. Leave blank or enter 0 to show all answers (default). The correct answer will always be shown.', 'watupro')?></p>
	    
	    <p><?php printf(__('Pull questions from %1$s random question categories in the %2$s. Leave it blank or enter 0 to use all categories.', 'watupro'),
	    	 '<input type="text" name="random_cats" size="4" value="'.(empty($advanced_settings['random_cats']) ? 0 : intval($advanced_settings['random_cats'])).'">',
	    	WATUPRO_QUIZ_WORD);?></p>
	    
	    <p><label><input type="checkbox" name="grades_by_percent" value="1" <?php if(!empty($dquiz->grades_by_percent)) echo 'checked'?>> <?php printf(__('Calculate grades by %s instead of points collected', 'watupro'), $grades_by_percent_dropdown)?></label></p>
	    
	    <p><?php _e('Allow up to', 'watupro')?> <input type="text" name="takings_by_ip" value="<?php echo empty($dquiz->takings_by_ip) ? 0 : $dquiz->takings_by_ip?>" size="4"> <?php _e('submissions by IP address. (Enter 0 for unlimited submissions)', 'watupro')?>
			<span id="limitByEmail" style='display:<?php echo empty($advanced_settings['contact_fields']['email']) ? 'none' : 'inline';?>'>
				<?php _e('and/or up to', 'watupro');?> <input type="text" name="takings_by_email" value="<?php echo empty($advanced_settings['takings_by_email']) ? 0 : $advanced_settings['takings_by_email']?>" size="4"> <?php printf(__('attempts by email address (when you ask for email address in "<a href=\"#\" %s>Ask for user contact details</a>" section)', 'watupro'), "onclick=\"watuproChangeTab(jQuery('#useremailsettingslnk'), 'useremailsettings');return false;\"");?>
			</span>	    
	    <br>
	    <?php printf(__('To limit the attempts by user account <a href="#" %s>click here</a> and select "Require user log-in". By default it will allow one attempt per user but you can select the checkbox to allow multiple attempts.', 'watupro'), "onclick=\"watuproChangeTab(jQuery('#useremailsettingslnk'), 'useremailsettings');return false;\"");?></p>		 
	    
	    <p><?php _e('Value of the submit button:', 'watupro');?> <input type="text" name="submit_button_value" size="10" value="<?php echo empty($advanced_settings['submit_button_value']) ? _wtpt(__('View Results', 'watupro')) : $advanced_settings['submit_button_value'];?>"></p>
	    
	    <?php if(!empty($difficulty_levels) and count($difficulty_levels)):?>
	    	<p><?php _e('Select only questions of this difficulty level:', 'watupro');?> 
	    	<select name="difficulty_level">
				<option value=""><?php _e('All levels', 'watupro');?></option>
				<?php foreach($difficulty_levels as $dlev):
					$selected = (!empty($advanced_settings['difficulty_level']) and $advanced_settings['difficulty_level'] == trim($dlev)) ? ' selected' : '';?>
					<option value="<?php echo trim($dlev);?>"<?php echo $selected?>><?php echo trim($dlev);?></option>
				<?php endforeach;?>
			</select> 
			<?php printf(__('Alternatively you can pass difficulty level in the shortcode like this: %s (where X is the %s ID)', 'watupro'), 
				'[watupro X difficulty_level="Easy"]', WATUPRO_QUIZ_WORD);?>			
			</p>
	    <?php endif;?>
	    
	    <p><input type="checkbox" name="is_rtl" value="1" <?php if(!empty($advanced_settings['is_rtl'])) echo 'checked';?>> <?php printf(__('This is an RTL %s. This will force right alignment and RTL direction of the text. Select this only if the RTL %s does not look correctly by default.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></p>
		 
		 <h3 class="hndle"><span><?php _e('Pagination Settings', 'watupro') ?></span> </h3> 
		 
		 <p> <?php _e("Pagination:", 'watupro')?> <select name="single_page" onchange="watuPROChangePagination(this.value);">
		 	<option value="1" <?php if($dquiz->single_page==WATUPRO_PAGINATE_ALL_ON_PAGE) echo "selected"?>><?php _e('All questions on single page', 'watupro');?></option>
		 	<option value="2" <?php if($dquiz->single_page==WATUPRO_PAGINATE_PAGE_PER_CATEGORY) echo "selected"?>><?php _e('One page per question category', 'watupro');?></option>
		 	<option value="0" <?php if($dquiz->single_page==WATUPRO_PAGINATE_ONE_PER_PAGE) echo "selected"?>><?php _e('Each question on its own page', 'watupro');?></option>
		 	<option value="3" <?php if($dquiz->single_page==WATUPRO_PAGINATE_CUSTOM_NUMBER) echo "selected"?>><?php _e('Custom number per page', 'watupro');?></option>
		 </select>
		 	<span id="watuPROCustomPerPage" style='display:<?php echo ($dquiz->single_page==WATUPRO_PAGINATE_CUSTOM_NUMBER) ? 'inline' : 'none'; ?>'><input type="text" name="custom_per_page" value="<?php echo empty($dquiz->custom_per_page) ? 0 : $dquiz->custom_per_page?>" size="4"> <?php _e('per page', 'watupro')?></span>
		 </p>  
		 
		 <div id="categoryPaginator" style='display:<?php echo (empty($dquiz->ID) or ($dquiz->single_page != WATUPRO_PAGINATE_CUSTOM_NUMBER and !empty($dquiz->group_by_cat)) ) ? 'block' : 'none';?>'>
		 	<p><input type="checkbox" name="show_category_paginator" value="1" <?php if(!empty($advanced_settings['show_category_paginator'])) echo 'checked'?> onclick="this.checked ? jQuery('#subCatPaginator').show() : jQuery('#subCatPaginator').hide();"> <?php _e('Show category-based paginator on top', 'watupro');?>
				<span id="subCatPaginator" style='display:<?php echo empty($advanced_settings['show_category_paginator']) ? 'none' : 'inline';?>'>
					<input type="checkbox" name="exclude_subcat_paginator" value="1" <?php if(!empty($advanced_settings['exclude_subcat_paginator'])) echo 'checked'?>> <?php _e('Exclude subcategories from it', 'watupro');?>
				</span>		 	
		 	</p>
		 </div>		 
		 
		 <div id="disallowPrevious" <?php if((!empty($dquiz->ID) and $single_page == WATUPRO_PAGINATE_ALL_ON_PAGE) or $dquiz->single_page==WATUPRO_PAGINATE_ALL_ON_PAGE) echo "style='display:none;'"?>>		  
		 <input type="checkbox" name="disallow_previous_button" value="1" <?php if(!empty($dquiz->disallow_previous_button)) echo "checked"?> onclick="watuPROdisallowPrevious(this);"> <?php _e('Disallow previous button', 'watupro')?> &nbsp;		 </div>
		 
		 <p><input type="checkbox" name="show_pagination" value="1" <?php if(!empty($dquiz->show_pagination)) echo "checked"?> onclick="this.checked ? jQuery('#numPaginatorPosition').show() : jQuery('#numPaginatorPosition').hide();"> <?php _e('Show numbered question paginator', 'watupro')?> &nbsp;
				<span id="numPaginatorPosition" style='display:<?php echo empty($dquiz->show_pagination) ? 'none' : 'inline';?>'>
					<?php _e('Position:', 'watupro');?>
					<select name="paginator_position">
						<option value="top"><?php _e('Top', 'watupro');?></option>
						<option value="bottom" <?php if(!empty($advanced_settings['paginator_position']) and $advanced_settings['paginator_position'] == 'bottom') echo 'selected'?>><?php _e('Bottom', 'watupro');?></option>
					</select>
					
					<?php _e('Max buttons:', 'watupro');?>
					<input type="text" name="paginator_decade" value="<?php echo ( empty($advanced_settings['paginator_decade']) or $advanced_settings['paginator_decade'] < 1) ? 10 : intval($advanced_settings['paginator_decade']);?>" size="3">
				</span>				
			</p>	
		 
		 <div id="alwaysShowSubmit" <?php if(!empty($dquiz->ID) and $dquiz->single_page==1) echo "style='display:none;'"?>>			
			<p><input type="checkbox" name="show_xofy" value="1" <?php if(!empty($advanced_settings['show_xofy']) or !isset($advanced_settings['show_xofy'])) echo "checked"; /*using !isset here for backward compatibility with the previously default setting - so on all old quizzes it gets checked by default. */?>> <?php _e('Show "Question X of Y" or "Page X of Y" text (X and Y will be replaced with the actual numbers)', 'watupro')?> &nbsp;</p>	 
			<p><input type="checkbox" name="show_progress_bar" value="1" <?php if(!empty($advanced_settings['show_progress_bar'])) echo "checked"?> onclick="this.checked ? jQuery('#progressBarPercent').show() : jQuery('#progressBarPercent').hide();"> <?php _e('Show progress bar on top', 'watupro')?>
				<span id="progressBarPercent" style='display:<?php echo empty($advanced_settings['show_progress_bar']) ? 'none' : 'inline';?>;'>
					<input type="checkbox" name="progress_bar_percent" value="1" <?php if(!empty($advanced_settings['progress_bar_percent'])) echo "checked"?>> <?php _e('Display percentage of completeness.', 'watupro');?>				
				</span>			
			</p>	 
		 	<p><input type="checkbox" name="submit_always_visible" value="1" <?php if(!empty($dquiz->submit_always_visible)) echo "checked"?>> <?php _e('Show submit button on each page', 'watupro')?></p>	 
		 </div>
		 
		 <div id="autoStoreProgress" <?php if(!empty($dquiz->ID) and $dquiz->single_page==1) echo "style='display:none;'"?>>
		 	<p><input type="checkbox" name="store_progress" value="1" <?php if(!empty($dquiz->store_progress)) echo "checked"?>> <?php _e('Automatically store user progress as they go from page to page (causes server requests)', 'watupro')?></p>
		 </div>
		 
			
			<h3 class="hndle"><span><?php _e('Questions and Answers Enumeration and Settings', 'watupro') ?></span></h3>

				<p><input type="checkbox" name="dont_display_question_numbers" value="1" <?php if(!empty($advanced_settings['dont_display_question_numbers'])) echo 'checked'?>> <?php _e('Do not display question numbers.', 'watupro')?></p>					
			
			<p><?php _e('The setting below lets you enumerate the answers to single-choice and multiple-choice questions with numbers, small letters, or capital letters.', 'watupro')?></p>
			<p><label><?php _e('Answers Enumerator:', 'watupro')?></label> <select name="enumerate_choices">
				<option value="" <?php if(empty($advanced_settings['enumerate_choices'])) echo 'selected'?>><?php _e('None', 'watupro')?></option>
				<option value="number" <?php if(!empty($advanced_settings['enumerate_choices']) and $advanced_settings['enumerate_choices'] == 'number') echo 'selected'?>><?php _e('Numbers', 'watupro')?></option>
				<option value="cap_letter" <?php if(!empty($advanced_settings['enumerate_choices']) and $advanced_settings['enumerate_choices'] == 'cap_letter') echo 'selected'?>><?php _e('Capital letters', 'watupro')?></option>
				<option value="small_letter" <?php if(!empty($advanced_settings['enumerate_choices']) and $advanced_settings['enumerate_choices'] == 'small_letter') echo 'selected'?>><?php _e('Small letters', 'watupro')?></option>
			</select></p>
			
			<p><input type="checkbox" name="accept_rating" value="1" <?php if(!empty($advanced_settings['accept_rating'])) echo 'checked';?> onclick="this.checked ? jQuery('#rateQuestionsCondition').show() : jQuery('#rateQuestionsCondition').hide();"> <?php _e("Allow users to rate questions", 'watupro');?>
				<span id="rateQuestionsCondition" style='display:<?php echo empty($advanced_settings['accept_rating']) ? 'none' : 'inline';?>'>
					<input type="checkbox" name="accept_rating_per_question" value="1" <?php if(!empty($advanced_settings['accept_rating_per_question'])) echo 'checked';?>> <?php _e("Set this individually for each question", 'watupro');?>		
				</span>			
			</p>
			
			<?php if($set_default_points):?>
				<p><?php _e('The following settings let you define what is the default number of points pre-filled in the fields for correct and incorrect answer. When you click the radio / checkbox that sets a given answer as correct / incorrect, the corresponding points will be automatically filled in the box.', 'watupro');?></p>
				<p><?php _e('Points for correct answer:', 'watupro');?> <input type="text" size="6" name="default_correct_answer_points" value="<?php echo $default_correct_answer_points?>"> <?php _e('Ex.: 1.00');?></p> 
				<p><?php _e('Points for incorrect answer:', 'watupro');?> <input type="text" size="6" name="default_incorrect_answer_points" value="<?php echo $default_incorrect_answer_points?>"> </p>
			<?php endif;?>
	   			
			<h3><span><?php printf(__('%s Category (Optional)', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></span></h3>
			
			<label><?php _e('Select category:', 'watupro')?></label> <select name="cat_id">
				<option value="0" <?php if(empty($dquiz->ID) or $dquiz->cat_id==0) echo "selected"?>><?php _e('- Uncategorized -', 'watupro')?></option>
				<?php foreach($cats as $cat):?>
					<option value="<?php echo $cat->ID?>" <?php if(!empty($dquiz->ID) and $dquiz->cat_id==$cat->ID) echo "selected"?>><?php echo stripslashes($cat->name);?></option>
					<?php foreach($cat->subs as $sub):?>
						<option value="<?php echo $sub->ID?>" <?php if(!empty($dquiz->ID) and $dquiz->cat_id==$sub->ID) echo "selected"?>>&nbsp; - <?php echo stripslashes($sub->name);?></option>
				<?php endforeach; 
				endforeach;?>		
			</select>
			<br />	
			
			<h3><span><?php _e('Anti-Spam Measures', 'watupro'); ?></span></h3>
			 <?php if(!empty($recaptcha_public) and !empty($recaptcha_private)):?>
		 	<p><input type="checkbox" name="require_captcha" value="1" <?php if(!empty($dquiz->require_captcha)) echo "checked"?>> <?php _e('Require image validation (reCaptcha) to submit the quiz', 'watupro');?></p>  
		 <?php endif;?>
		 <p><input type="checkbox" name="require_text_captcha" value="1" <?php if(!empty($advanced_settings['require_text_captcha'])) echo "checked"?>> <?php printf(__('Require question based captcha to submit the %s', 'watupro'), WATUPRO_QUIZ_WORD);?></p>	
			<p><input type="checkbox" name="use_honeypot" value="1" <?php if(!empty($advanced_settings['use_honeypot'])) echo "checked"?> onclick="this.checked ? jQuery('#honeypotWarning').show() : jQuery('#honeypotWarning').hide();"> <?php _e('Use a "honeypot" field (It is almost as efficient as captchas but is unobtrusive and does not require any action from the user)', 'watupro');?>
				<span id="honeypotWarning" style='display: <?php echo empty($advanced_settings['use_honeypot']) ? 'none' : 'inline'; ?>;color:red;'><?php _e('If you start getting unexpected "No answer to the verification question" errors you will need to switch this off.', 'watupro')?></span>			
			</p>	
			
			<h3><span><?php printf(__('%s Design Theme', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)) ?></span></h3>
			<p><?php printf(__('The default design theme for all %1$s is defined in your <a href="%2$s" target="_blank">WatuPRO Settings page</a>. You can override it on individual %3$s level here.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, 'admin.php?page=watupro_options', WATUPRO_QUIZ_WORD);?></p>
			<p><label><?php _e('Select design theme:', 'watupro')?></label>
		<select name="design_theme">		
			<option value=""><?php _e('Use the global theme', 'watupro');?></option>	
			<?php foreach($design_themes as $theme):?>
				<option value="<?php echo $theme?>" <?php if(!empty($advanced_settings['design_theme']) and $theme == $advanced_settings['design_theme']) echo 'selected'?>><?php echo $theme?></option>
			<?php endforeach;?>
		</select> </p>
			
          <h3><span><?php printf(__('Schedule %s (Optional)', 'watupro'), WATUPRO_QUIZ_WORD) ?></span></h3>
          
          <input type="checkbox" name="is_scheduled" value="1" <?php if(!empty($dquiz->is_scheduled) and $dquiz->is_scheduled==1) echo "checked"?> onclick="this.checked ? jQuery('#scheduleSettings').show() : jQuery('#scheduleSettings').hide();"> <?php printf(__('Schedule this %s', 'watupro'), WATUPRO_QUIZ_WORD)?><br>
          <br>               
			
			<div style='display:<?php echo empty($dquiz->is_scheduled) ? 'none': 'block';?>' id="scheduleSettings"><label><?php _e('Schedule from:', 'watupro')?></label> &emsp;
	                <input type="text" name="schedule_from" class="watuproDatePicker" value="<?php echo $schedule_from?>">
	                &nbsp;
	                <select name="schedule_from_hour">
	                    <?php $i=0;
	                    while ($i<24): ?>
	                        <option value="<?php echo $i?>" <?php if(date("G",strtotime($dquiz->schedule_from))==$i) echo "selected"?>><?php printf("%02d", $i); ?></option>
	                    <?php  $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_from_minute">
	                    <?php $i=0;
	                    while ($i<60):  ?>
	                        <option value="<?php echo $i?>" <?php if(date("i",strtotime($dquiz->schedule_from))==$i) echo "selected"?>><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>
							
						 &nbsp;&nbsp;&nbsp;	
	                
	                <label><?php _e('Schedule to:', 'watupro')?></label> &emsp;
	                <input type="text" name="schedule_to" class="watuproDatePicker" value="<?php echo $schedule_to?>">
	                &nbsp;
	                <select name="schedule_to_hour">
	                    <?php $i=0;
	                    while ($i<24):?>
	                        <option value="<?php echo $i?>" <?php if(date("G",strtotime($dquiz->schedule_to))==$i) echo "selected"?>><?php printf("%02d", $i); ?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_to_minute">
	                    <?php $i=0;
	                    while ($i<60): ?>
	                        <option value="<?php echo $i?>" <?php if(date("i",strtotime($dquiz->schedule_to))==$i) echo "selected"?>><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>                    
	                </select>
	           </div>     
			<p><?php if(empty($dquiz->ID)): printf(__('Once you save the %s you will be able to set individual schedules for logged in users.', 'watupro'), WATUPRO_QUIZ_WORD);
			else: echo '<a href="admin.php?page=watupro_schedule&quiz_id='.$dquiz->ID.'" target="_blank">'.__('Set individual schedules for logged in users.', 'watupro').'</a>';
			endif;?></p>
			<br />
		</div>
	</div>
	
	<div class="postbox watupro-tab-div" id="useremailsettings" style="display:none;">	 
	 <div class="inside">
	 		<h3><?php _e('User and Email Related Settings', 'watupro') ?></h3>
	    <p><input id="requieLoginChk" type="checkbox" name="require_login" value="1" <?php if(!empty($dquiz->require_login)) echo "checked"?> onclick="WatuPROLoginRequired(this.checked);toggleDontShowAnsweredQuestions(this.form);"> <?php _e("Require user log-in", 'watupro')?></p>
	    <div id="loginMode" style='margin-left:20px;display:<?php echo !empty($dquiz->require_login)?'block':'none';?>'> 
	    	<p id="oneAttemptWarning" style='color:red;display:<?php echo empty($dquiz->take_again) ? 'block' : 'none';?>'><?php printf(__('By default users will be allowed only one %s attempt. To change this select "Allow users to submit the %s multiple times" checkbox below.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></p>
	    	<fieldset>
	    	<legend><b><?php _e('Logged user options', 'watupro')?></b></legend>
	    	<p><?php printf(__('Registered <a href="users.php" target="_blank">users</a> will be able to take this %s. You can add the users yourself or let them register themselves. For the second option you need to make sure sure that "Anyone can register" is checked in your <a href="options-general.php" target="_blank">general settings</a> page.', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
	        
	        <p><input type="checkbox" name="take_again" value="1" <?php if(!empty($dquiz->take_again)) echo "checked"?> onclick="if(this.checked) {jQuery('#timesToTake').show(); jQuery('#oneAttemptWarning').hide();} else {jQuery('#timesToTake').hide();jQuery('#oneAttemptWarning').show();}"> <?php printf(__('Allow users to submit the %s multiple times:', 'watupro'), __('quiz', 'watupro'))?> 
	        		<div id='timesToTake' style='margin-left:50px;<?php if(empty($dquiz->take_again)) echo 'display:none;'?>'>
	        			<?php _e('Allow', 'watupro')?> <input type="text" size="4" name="times_to_take" value="<?php echo empty($dquiz->times_to_take) ? 0 : $dquiz->times_to_take?>"> <?php _e('times (For unlimited times enter 0)', 'watupro')?> <select name="retakings_per_period">
	        				<option value=""><?php _e('in total', 'watupro');?></option>
	        				<option value="24 hour" <?php if(!empty($advanced_settings['retakings_per_period']) and $advanced_settings['retakings_per_period']=='24 hour') echo 'selected'?>><?php _e('per 24 hours', 'watupro');?></option>
	        				<option value="1 week" <?php if(!empty($advanced_settings['retakings_per_period']) and $advanced_settings['retakings_per_period']=='1 week') echo 'selected'?>><?php _e('within a week', 'watupro');?></option>
	        				<option value="1 month" <?php if(!empty($advanced_settings['retakings_per_period']) and $advanced_settings['retakings_per_period']=='1 month') echo 'selected'?>><?php _e('within a month', 'watupro');?></option>
	        				<option value="1 year" <?php if(!empty($advanced_settings['retakings_per_period']) and $advanced_settings['retakings_per_period']=='1 year') echo 'selected'?>><?php _e('within an year', 'watupro');?></option>
	        			</select>
						<?php if(watupro_intel()):?>
							<?php _e("but require an interval of at least", 'watupro')?> <input type="text" size="4" name="retake_after" value="<?php echo empty($dquiz->retake_after) ? 0 : $dquiz->retake_after?>"> <?php printf(__('hours before the %s can be resubmitted.', 'watupro'), WATUPRO_QUIZ_WORD)?>
						<?php endif;?>
						<p><?php if(empty($grades)): echo "<b>".sprintf(__('Once you create grades, you will be able to further restrict re-submitting the %s.', 'watupro'), WATUPRO_QUIZ_WORD)."</b>";
						else:
							_e('Re-submitting is allowed only if some of the following grades is achieved on the user last attempt (leave all unchecked to not set any grade-related limitation):', 'watupro');
							foreach($grades as $grade):?>
								<input class="watupro-retake-grades" type="checkbox" name="retake_grades[]" value="<?php echo $grade->ID?>" <?php if(!empty($dquiz->retake_grades) and strstr($dquiz->retake_grades, "|".$grade->ID."|")) echo "checked"?> onclick="showHideRetakeGradesTimeLimit();"> <?php echo $grade->gtitle?> &nbsp;&nbsp;&nbsp;
							<?php endforeach;?>
							<input class="watupro-retake-grades" type="checkbox" name="retake_grades[]" value="-1" <?php if(!empty($dquiz->retake_grades) and strstr($dquiz->retake_grades, "|-1|")) echo "checked"?> onclick="showHideRetakeGradesTimeLimit();"> <?php _e('None (no grade achieved)', 'watupro');?> &nbsp;&nbsp;&nbsp;
								<div id="retakeGradesTimeLimit" style='display:<?php echo empty($dquiz->retake_grades) ? 'none' : 'block';?>;padding-left:100px;'>
									<?php printf(__('The grade-related restriction is valid only for %s days (enter 0 for no time limit)', 'watupro'), '<input type="text" size="4" name="retake_grades_expire" value="'.(empty($advanced_settings['retake_grades_expire']) ? 0 : $advanced_settings['retake_grades_expire']) .'">');?>
								</div>
							<?php endif;?></p>
							
						<p><?php printf(__('The user can reattempt this %s up to %s days after their first attempt (default is empty 0 for no limit).', 'watupro'), WATUPRO_QUIZ_WORD,
							'<input type="text" name="retake_days_limit" size="4" value="'.(empty($advanced_settings['retake_days_limit']) ? '' : $advanced_settings['retake_days_limit']).'">');?></p>	
					</div>       
	        </p>
	        
	        <p><input type="checkbox" name="restrict_by_user" value="1" <?php if(!empty($advanced_settings['restrict_by_user'])) echo 'checked'?> onclick="this.checked ? jQuery('#wtpUsersList').show() : jQuery('#wtpUsersList').hide();"> <?php printf(__('Restrict access to this %s to a list of specified users.', 'watupro'), WATUPRO_QUIZ_WORD);?> 
					<div id="wtpUsersList" style='display:<?php echo empty($advanced_settings['restrict_by_user']) ? 'none' : 'block';?>'>
						<p><?php printf(__('Enter user logins or email addresses of existing users, one per line. No one else (except admins / managers) will be able to access this %s.', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
						<textarea name="allowed_users" rows="5" cols="60"><?php echo empty($advanced_settings['allowed_users']) ? '' : @$advanced_settings['allowed_users'];?></textarea>
					</div>	        
	        </p>
	        
	        <?php do_action('watupro_quiz_form_login_required', (empty($advanced_settings['play_levels']) ? array() : $advanced_settings['play_levels']));?>	
	        
	        <p><input type="checkbox" name="no_retake_display_result" value="1" <?php if(!empty($advanced_settings['no_retake_display_result'])) echo 'checked';?>> <?php _e('When no more attempts are available display the latest result.', 'watupro');?>
					<span style='display:<?php echo empty($advanced_settings['no_retake_display_result']) ? 'none' : 'inline';?>'>
						<select name="no_retake_display_result_what">							
							<option value="grade" <?php if(!empty($advanced_settings['no_retake_display_result_what']) and $advanced_settings['no_retake_display_result_what'] == 'grade') echo 'selected'?>><?php _e('Grade title & description', 'watupro');?></option>
							<option value="gtitle" <?php if(!empty($advanced_settings['no_retake_display_result_what']) and $advanced_settings['no_retake_display_result_what'] == 'gtitle') echo 'selected'?>><?php _e('Grade title only', 'watupro');?></option>
							<option value="all" <?php if(!empty($advanced_settings['no_retake_display_result_what']) and $advanced_settings['no_retake_display_result_what'] == 'all') echo 'selected'?>><?php _e('The whole final screen', 'watupro');?></option>
						</select>
					</span>	        
	        </p>
	        
	        <p><input type="checkbox" name="always_show_description" value="1" <?php if(!empty($advanced_settings['always_show_description'])) echo 'checked';?>> <?php printf(__('Show the %s description (if available) even to non-logged in users and users with no rights to access the %s.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD);?></p>
	        
	        <p id="dontShowAnsweredQuestions" style='display:<?php echo empty($dquiz->require_login) ? 'none' : 'block'?>;'>
	        	   <input type="checkbox" name="dont_show_answered" value="1" <?php if(!empty($advanced_settings['dont_show_answered'])) echo 'checked';?> onclick="(this.checked || this.form.dont_show_correctly_answered.checked) ? jQuery('#dontShowAnsweredQuestionsRestart').show() : jQuery('#dontShowAnsweredQuestionsRestart').hide();"> <?php printf(__("Don't display questions that were previously answered by the user. Be careful with this setting - <a href='%s' target='_blank'>see why</a>", 'watupro'), 'http://blog.calendarscripts.info/watupro-dont-display-already-answered-questions-to-logged-in-user/');?> 
	        	   
	        	   <input type="checkbox" name="dont_show_correctly_answered" value="1" <?php if(!empty($advanced_settings['dont_show_correctly_answered'])) echo 'checked';?> onclick="(this.checked || this.form.dont_show_answered.checked) ? jQuery('#dontShowAnsweredQuestionsRestart').show() : jQuery('#dontShowAnsweredQuestionsRestart').hide();"> 
	        	   	 	<?php _e('Hide only correctly answered questions to allow reattempting the missed ones.', 'watupro');?>
	        
	        		<div id="dontShowAnsweredQuestionsRestart" style='display:<?php echo (!empty($advanced_settings['dont_show_answered']) or !empty($advanced_settings['dont_show_correctly_answered'])) ? 'block' : 'none';?>; margin-left: 50px;'>
	        			<input type="checkbox" name="dont_show_answered_restart" value="1" <?php if(!empty($advanced_settings['dont_show_answered_restart'])) echo 'checked';?>> 
	        	   	 	<?php _e('In case no more questions are available, start over.', 'watupro');?>
	        		</div>	   
	        </p>
	        
	        </fieldset>
	        
	        <?php if(!empty($courses) and count($courses)):?>
	        <fieldset>
	        		<legend><b><?php _e('Namaste! LMS Integration', 'watupro')?></b></legend>
	        		
	        		<p><?php printf(__('This %s will be accessible only to students enrolled in the following courses:', 'watupro'), WATUPRO_QUIZ_WORD);?> &nbsp;
	        		<?php foreach($courses as $course):?>
	        			<span style="white-space: nowrap;"><input type="checkbox" name="namaste_courses[]" value="<?php echo $course->ID?>" <?php if(!empty($dquiz->namaste_courses) and strstr($dquiz->namaste_courses, '|'.$course->ID.'|')) echo 'checked'?>> <?php echo stripslashes($course->post_title);?></span>
	        		<?php endforeach;?></p>
	        </fieldset>
	        <?php endif;?>
	        
	        <?php do_action('watupro-user-email-settings', @$dquiz->ID);?>
		</div>
		
		<div id="emailWarning" style="display:none;">
		 <?php if(!empty($email_warning)):?><p style="color:red;"><?php echo $email_warning?></p><?php endif;?>
		</div>    
		
		<p><input type="checkbox" name="email_admin" value="1" <?php if(!empty($dquiz->email_admin)) echo "checked"?> onclick="this.checked?jQuery('#wadminEmail, #emailWarning').show():jQuery('#wadminEmail, #emailWarning').hide();"> <?php printf(__("Send me email with details when someone takes the %s", 'watupro'), WATUPRO_QUIZ_WORD)?>
			<div id="wadminEmail" style='display:<?php echo !empty($dquiz->email_admin)?'block':'none'?>;margin-left:50px;'><?php _e('Email address(es) to send to:', 'watupro')?> <input type="text" name="admin_email" value="<?php echo !empty($dquiz->email_admin)?$dquiz->admin_email:get_option('admin_email');?>" size="80"> <br />
				<?php _e('Separate multiple addresses with comma and space - for example "email1@your-domain.com, email2@your-domain.com". You can also use the variables from the "Ask for user contact details" section below as long as the field will contain email address. Example: "admin@your-domain.com, %%FIELD-2%%".', 'watupro');?>
				<?php printf(__('There are even more dynamic variables you can use. <a href="%s" target="_blank">Learn everything about them here</a>.', 'watupro'), 'https://blog.calendarscripts.info/using-dynamic-variables-in-watupro-email-field/');?>
				</p>
				
				<p><?php if(empty($grades)): echo "<b>".__('Once you create grades, you will be able to further configure this.', 'watupro')."</b>";
								else:
									_e('The email will be sent only if some of the following grades is achieved (leave all unchecked to not set any grade-related limitation):', 'watupro');
									foreach($grades as $grade):?>
										<input type="checkbox" name="admin_email_grades[]" value="<?php echo $grade->ID?>" <?php if(!empty($advanced_settings['admin_email_grades']) and in_array($grade->ID, $advanced_settings['admin_email_grades'])) echo "checked"?>> <?php echo stripslashes($grade->gtitle)?> &nbsp;&nbsp;&nbsp;
									<?php endforeach;
									endif;?></p> 		
				<p><input type="checkbox" name="taker_reply_to" value="1" <?php if(!empty($advanced_settings['taker_reply_to'])) echo 'checked';?>> <?php _e('Set the email address of the respondent as a reply-to address', 'watupro');?></p>					
			</div>
		</p>
		
		<p><input type="checkbox" name="email_taker" value="1" <?php if(!empty($dquiz->email_taker)) echo "checked"?> onclick="this.checked ? jQuery('#emailTakerOptions, #emailWarning').show() : jQuery('#emailTakerOptions, #emailWarning').hide();"> <?php _e('Send email to the user with their results', 'watupro')?></p>
			<div id="emailTakerOptions" style="margin-left:20px;display:<?php echo empty($dquiz->email_taker) ? 'none' : 'block';?>">				
				<p id="emailGuestOptional" style='display:<?php echo empty($dquiz->require_login) ? 'block' : 'none';?>'><input type="checkbox" name="email_not_required" value="1" <?php if(!empty($advanced_settings['email_not_required'])) echo 'checked'?>> <?php printf(__('Entering email to receive %s results is optional for non-logged in users. (Takes effect only when you have selected "Send email to the user with their results")', 'watupro'), WATUPRO_QUIZ_WORD)?></p>
				<p><?php if(empty($grades)): echo "<b>".__('Once you create grades, you will be able to further configure this.', 'watupro')."</b>";
							else:
								_e('The email will be sent only if some of the following grades is achieved (leave all unchecked to not set any grade-related limitation):', 'watupro');
								foreach($grades as $grade):?>
									<input type="checkbox" name="email_grades[]" value="<?php echo $grade->ID?>" <?php if(!empty($advanced_settings['email_grades']) and in_array($grade->ID, $advanced_settings['email_grades'])) echo "checked"?>> <?php echo stripslashes($grade->gtitle);?> &nbsp;&nbsp;&nbsp;
								<?php endforeach;
								endif;?></p> 
			</div>				
	    	
	    	<input type="hidden" name="show_answers" value="<?php echo empty($dquiz->show_answers) ? 0 : $dquiz->show_answers?>">
		 </div>
		 		    	
	    	<div class="inside">
				<h3><?php _e('Ask for user contact details', 'watupro');?></h3>	    	
	    	
		    	<p><?php _e('For logged in users some of this data might be prepopulated.', 'watupro')?></p>
		    	
		    	<p><?php _e('Should we ask the user for contact details?', 'watupro')?> <input type="radio" name="ask_for_contact_details" value="" <?php if(empty($advanced_settings['ask_for_contact_details'])) echo 'checked'?> onclick="jQuery('#askForContactDetails').hide();"> <?php _e("Don't ask", 'watupro')?> &nbsp; <input type="radio" name="ask_for_contact_details" value="start"  <?php if(!empty($advanced_settings['ask_for_contact_details']) and $advanced_settings['ask_for_contact_details']=='start') echo 'checked'?> onclick="jQuery('#askForContactDetails').show();wtpMaybeWarnAboutTimer(this.form);"> <?php _e("Ask at the beginning", 'watupro')?> &nbsp;
		    	<input type="radio" name="ask_for_contact_details" value="end"  <?php if(!empty($advanced_settings['ask_for_contact_details']) and $advanced_settings['ask_for_contact_details']=='end') echo 'checked'?> onclick="jQuery('#askForContactDetails').show();wtpMaybeWarnAboutTimer(this.form);"> <?php _e("Ask at the end", 'watupro')?> &nbsp;</p>
		    	
		    	<div id="askForContactDetails" style='display:<?php echo empty($advanced_settings['ask_for_contact_details']) ? 'none' : 'block';?>'>
					<p id="watuProAskTimerNote" style='display:<?php echo (!empty($dquiz->time_limit) and !empty($advanced_settings['ask_for_contact_details']) and $advanced_settings['ask_for_contact_details']=='end') ? 'block' : 'none';?>'>
						<b><?php printf(__('Note: This is a timed %s and the timer will not stop until the user completes the whole %ss including their contact details. For such %s you may prefer to select "Ask at the beginning" because the timer will start after the contact details are completed.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD_PLURAL);?></b>
					</p>		    	
		    	
		    		<p><label><?php _e('Optional instructions before the contact fields:', 'watupro');?></label>
		    		<textarea name="ask_contact_intro" rows="3" cols="40" class="i18n-multilingual"><?php echo empty($advanced_settings['contact_fields']['intro_text']) ? '' : stripslashes(rawurldecode($advanced_settings['contact_fields']['intro_text']));?></textarea></p>
			    	<p><label><?php _e('Ask for email:', 'watupro')?></label> <input type="radio" name="ask_for_email" value="" <?php if(empty($advanced_settings['contact_fields']['email'])) echo 'checked'?> onclick="jQuery('#limitByEmail').hide();"> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_email" value="yes" <?php if(!empty($advanced_settings['contact_fields']['email']) and $advanced_settings['contact_fields']['email']=='yes') echo 'checked'?> onclick="jQuery('#limitByEmail').show();"> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_email" value="required" <?php if(!empty($advanced_settings['contact_fields']['email']) and $advanced_settings['contact_fields']['email']=='required') echo 'checked'?> onclick="jQuery('#limitByEmail').show();"> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_email_label" value="<?php echo empty($advanced_settings['contact_fields']['email_label']) ? __('Your email address:', 'watupro') : $advanced_settings['contact_fields']['email_label'];?>"> &nbsp; 
			    	<?php printf(__('Variable: %s', 'watupro'), '<input type="text" value="%%EMAIL%%" onclick="this.select();" readonly="readonly">')?>
			    	&nbsp;
			    	<?php printf(__('URL parameter: %s', 'watupro'), '<input type="text" value="wtp_email" onclick="this.select();" readonly="readonly" size="10">')?></p>
			    	
			    	<p><label><?php _e('Ask for name:', 'watupro')?></label> <input type="radio" name="ask_for_name" value="" <?php if(empty($advanced_settings['contact_fields']['name'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_name" value="yes" <?php if(!empty($advanced_settings['contact_fields']['name']) and $advanced_settings['contact_fields']['name']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_name" value="required" <?php if(!empty($advanced_settings['contact_fields']['name']) and $advanced_settings['contact_fields']['name']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_name_label" value="<?php echo empty($advanced_settings['contact_fields']['name_label']) ? __('Your name:', 'watupro') : $advanced_settings['contact_fields']['name_label'];?>"> 
			    	<?php printf(__('Variable: %s', 'watupro'), '<input type="text" value="%%USER-NAME%%" onclick="this.select();" readonly="readonly">')?>
			    	&nbsp;
			    	<?php printf(__('URL parameter: %s', 'watupro'), '<input type="text" value="wtp_name" onclick="this.select();" readonly="readonly" size="10">')?></p>
			    	
			    	<p><label><?php _e('Ask for company name:', 'watupro')?></label> <input type="radio" name="ask_for_company" value="" <?php if(empty($advanced_settings['contact_fields']['company'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_company" value="yes" <?php if(!empty($advanced_settings['contact_fields']['company']) and $advanced_settings['contact_fields']['company']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_company" value="required" <?php if(!empty($advanced_settings['contact_fields']['company']) and $advanced_settings['contact_fields']['company']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_company_label" value="<?php echo empty($advanced_settings['contact_fields']['company_label']) ? __('Company name:', 'watupro') : $advanced_settings['contact_fields']['company_label'];?>"> 
			    	<?php printf(__('Variable: %s', 'watupro'), '<input type="text" value="%%FIELD-COMPANY%%" onclick="this.select();" readonly="readonly">')?>
			    	&nbsp;
			    	<?php printf(__('URL parameter: %s', 'watupro'), '<input type="text" value="wtp_company" onclick="this.select();" readonly="readonly" size="10">')?></p>
			    	
			    	<p><label><?php _e('Ask for phone:', 'watupro')?></label> <input type="radio" name="ask_for_phone" value="" <?php if(empty($advanced_settings['contact_fields']['phone'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_phone" value="yes" <?php if(!empty($advanced_settings['contact_fields']['phone']) and $advanced_settings['contact_fields']['phone']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_phone" value="required" <?php if(!empty($advanced_settings['contact_fields']['phone']) and $advanced_settings['contact_fields']['phone']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_phone_label" value="<?php echo empty($advanced_settings['contact_fields']['phone_label']) ? __('Phone:', 'watupro') : $advanced_settings['contact_fields']['phone_label'];?>"> 
			    	<?php printf(__('Variable: %s', 'watupro'), '<input type="text" value="%%FIELD-PHONE%%" onclick="this.select();" readonly="readonly">')?>
			    	&nbsp;
			    	<?php printf(__('URL parameter: %s', 'watupro'), '<input type="text" value="wtp_phone" onclick="this.select();" readonly="readonly" size="10">')?></p>
			    	
			    		<p><label><?php _e('Custom field 1:', 'watupro')?></label> <input type="radio" name="ask_for_field1" value="" <?php if(empty($advanced_settings['contact_fields']['field1'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_field1" value="yes" <?php if(!empty($advanced_settings['contact_fields']['field1']) and $advanced_settings['contact_fields']['field1']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_field1" value="required" <?php if(!empty($advanced_settings['contact_fields']['field1']) and $advanced_settings['contact_fields']['field1']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_field1_label" value="<?php echo empty($advanced_settings['contact_fields']['field1_label']) ? __('Custom field 1:', 'watupro') : $advanced_settings['contact_fields']['field1_label'];?>"> 
			    	<?php printf(__('Variable: %s', 'watupro'), '<input type="text" value="%%FIELD-1%%" onclick="this.select();" readonly="readonly">')?>
			    	&nbsp;
			    	<?php printf(__('URL parameter: %s', 'watupro'), '<input type="text" value="wtp_field1" onclick="this.select();" readonly="readonly" size="10">')?>
			    	<input type="checkbox" name="field1_is_dropdown" value="1" <?php if(!empty($advanced_settings['contact_fields']['field1_is_dropdown'])) echo 'checked'?> onclick="this.checked ? jQuery('#field1DDValues').show() : jQuery('#field1DDValues').hide();"> <?php _e('Dropdown field', 'watupro');?>
						<div id="field1DDValues" style='display:<?php echo empty($advanced_settings['contact_fields']['field1_is_dropdown']) ? 'none' : 'inline';?>'>
							<?php _e('Enter drop-down values, one per line', 'watupro');?>
							<textarea name="field1_dropdown_values" size="7"><?php echo empty($advanced_settings['contact_fields']['field1_dropdown_values']) ? '' : $advanced_settings['contact_fields']['field1_dropdown_values']?></textarea>
						</div>			    	
			    	</p>
			    	
			    	<p><label><?php _e('Custom field 2:', 'watupro')?></label> <input type="radio" name="ask_for_field2" value="" <?php if(empty($advanced_settings['contact_fields']['field2'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_field2" value="yes" <?php if(!empty($advanced_settings['contact_fields']['field2']) and $advanced_settings['contact_fields']['field2']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_field2" value="required" <?php if(!empty($advanced_settings['contact_fields']['field2']) and $advanced_settings['contact_fields']['field2']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_field2_label" value="<?php echo empty($advanced_settings['contact_fields']['field2_label']) ? __('Custom field 2:', 'watupro') : $advanced_settings['contact_fields']['field2_label'];?>"> 
			    	<?php printf(__('Variable: %s', 'watupro'), '<input type="text" value="%%FIELD-2%%" onclick="this.select();" readonly="readonly">')?>
			    	&nbsp;
			    	<?php printf(__('URL parameter: %s', 'watupro'), '<input type="text" value="wtp_field2" onclick="this.select();" readonly="readonly" size="10">')?>
						<input type="checkbox" name="field2_is_dropdown" value="1" <?php if(!empty($advanced_settings['contact_fields']['field2_is_dropdown'])) echo 'checked'?> onclick="this.checked ? jQuery('#field2DDValues').show() : jQuery('#field2DDValues').hide();"> <?php _e('Dropdown field', 'watupro');?>
						<div id="field2DDValues" style='display:<?php echo empty($advanced_settings['contact_fields']['field2_is_dropdown']) ? 'none' : 'inline';?>'>
							<?php _e('Enter drop-down values, one per line', 'watupro');?>
							<textarea name="field2_dropdown_values" size="7"><?php echo empty($advanced_settings['contact_fields']['field2_dropdown_values']) ? '' : $advanced_settings['contact_fields']['field2_dropdown_values']?></textarea>
						</div>					    	
			    	</p>
			    	
			    	<p><?php _e('You can also ask the respondent to select a required checkbox for agreement to terms etc. To activate it, simply enter the text that should be shown along with the checkbox in the field below. If you leave the field empty, there will be no checkbox.', 'watupro');?> <br />
			    	<input type="text" size="100" name="ask_for_checkbox" value="<?php echo empty($advanced_settings['contact_fields']['checkbox']) ? '' : htmlentities($advanced_settings['contact_fields']['checkbox'])?>"></p>
			    	
			    	<p><label><?php _e('Value (text) of the "Start" button:', 'watupro')?></label> <input type="text" name="ask_for_start_button" value="<?php echo empty($advanced_settings['contact_fields']['start_button']) ? sprintf(__('Start %s!', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD)) : stripslashes($advanced_settings['contact_fields']['start_button'])?>">
			    	<br><?php _e('If you want to style the button, it has CSS class "watupro-start-quiz".', 'watupro');?></p>
			    	
			    	<p><b><?php printf(__('Note: the variable shown next to each field can be used in the %s "Final Page" and also in certificate contents.', 'watupro'), WATUPRO_QUIZ_WORD)?></b><br />
			    	<?php printf(__('The URL parameters can be used to pass predefined values for each of the fields by appending them to the link pointing to the %s.', 'watupro'),
			    		WATUPRO_QUIZ_WORD);?></p>
				</div>
		   </div>			
	</div>
	
	<?php if(watupro_intel()): 
			if(@file_exists(get_stylesheet_directory().'/watupro/i/exam_form_intelligence.php')) require get_stylesheet_directory().'/watupro/i/exam_form_intelligence.php';
			else require WATUPRO_PATH."/i/views/exam_form_intelligence.php";			
	endif;?>
	
	<div class="postbox watupro-tab-div" id="advanced" style="display:none;">
		<?php $_GET['exam_id'] = $dquiz->ID; 
		watupro_advanced_exam_settings();?>
	</div>
	
	<style type="text/css"> #gradecontent p{border-bottom:1px dotted #ccc;padding-bottom:3px;} #gradecontent label{padding: 5px 10px;} #gradecontent textarea{width:96%;margin-left:10px;} #gradecontent p img.gradeclose{ border:0 none; float:right; } </style>
	
	<div id="finalscreen" class="watupro-tab-div postbox" style="display:none;">				
		<div class="inside">
			<h3><?php _e('Final Screen', 'watupro') ?></h3>
		
			<?php wp_editor($final_screen, "watupro_content", array("editor_class" => 'i18n-multilingual', 'textarea_name' => 'content')); ?>
			
			<p><?php printf(__('You can use the tag %s to have different final page contents for logged in and not logged in users. If the tag is inserted inside the message the contents before it will be shown to logged in users and the content after the tag - to those who are not logged in.', 'watupro'), '<input type="text" value="{{{loggedin}}}" size="10" onclick="this.select()" readonly="readonly">');?></p>
			<?php if(function_exists('pdf_bridge_quiz_settings')):?>
			<p><input type="checkbox" name="print_pdf" value="1" <?php if(!empty($advanced_settings['print_pdf'])) echo 'checked'?> onclick="this.checked ? jQuery('#printPDF').show() : jQuery('#printPDF').hide();"> <?php printf(__('Allow users to print PDF from the final screen of the %s', 'watupro'), WATUPRO_QUIZ_WORD);?></p>
			
			<div id="printPDF" style='display:<?php echo empty($advanced_settings['print_pdf']) ?  'none' : 'block';?>;margin-left:50px;'>
				<p><?php _e('The PDF output may not look exactly the same as the final screen due to the CSS limitations of PDF converting.', 'watupro');?></p>
				<?php do_action('watupro-quiz-pdf-settings', @$advanced_settings['pdf_settings']);?>
				<p><?php printf(__('Use the shortcode %s in the Final Output box above to output the link for printing PDF. You can pass link text like this: %s or enclose some text or image inside the shortcode like this: %s.', 'watupro'), '<input type="text" value="[watupro-pdf]" onclick="this.select();" readonly="readonly">', '[watupro-pdf link_text="View PDF" target="_blank"]', '[watupro-pdf]PDF Icon[/watupro-pdf]');?></p>
			</div>			
			<?php endif;?>
			
			<p><input type="checkbox" name="delay_results" value="1" <?php if(!empty($dquiz->delay_results)) echo 'checked'?> onclick="this.checked ? jQuery('#holdResults').show() : jQuery('#holdResults').hide();"> <?php printf(__('Hold displaying the results until a date in the future. (Useful mostly for %s that require user login.)', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></p>
			
			<div id="holdResults" style='display:<?php echo empty($dquiz->delay_results) ?  'none' : 'block';?>;margin-left:50px;'>
				<p><?php _e('Results will be available after this date:', 'watupro');?> <input type="text" class="watuproDatePicker" name="delay_results_date" value="<?php echo empty($dquiz->delay_results_date) ? date('Y-m-d') : $dquiz->delay_results_date?>"></p>
				<p><input type="checkbox" name="delay_results_per_group" value="1" onclick="this.checked ? jQuery('#holdPerGroup').show() : jQuery('#holdPerGroup').hide();" <?php if(!empty($advanced_settings['delay_results_per_group'])) echo 'checked'?>> <?php printf(__('Apply this only to the selected user %s (anyone else will see the results immediately):', 'watupro'), ($use_wp_roles ? __('roles', 'watupro') : __('groups','watupro')));?>
					&nbsp;					
					<span style='display:<?php echo empty($advanced_settings['delay_results_per_group']) ? 'none' : 'inline';?>' id="holdPerGroup"><input type="checkbox" name="delay_results_groups[]" value="guest" <?php if(!empty($advanced_settings['delay_results_groups']) and in_array('guest',$advanced_settings['delay_results_groups'])) echo 'checked'?>> <?php _e('Guest / Unregistered user', 'watupro');?>
					<?php if($use_wp_roles):
						foreach($roles as $key => $r):?>
							<span style="white-space:nowrap;"><input type="checkbox" name="delay_results_groups[]" value="<?php echo $key?>" <?php if(!empty($advanced_settings['delay_results_groups']) and in_array($key,$advanced_settings['delay_results_groups'])) echo 'checked'?>> <?php echo $key;?></span>
						<?php endforeach;
					 else:
					 	foreach($groups as $group):?>
					 	<span style="white-space:nowrap;"><input type="checkbox" name="delay_results_groups[]" value="<?php echo $group->ID?>" <?php if(!empty($advanced_settings['delay_results_groups']) and in_array($group->ID, $advanced_settings['delay_results_groups'])) echo 'checked'?>> <?php echo stripslashes($group->name);?></span>
					 	<?php endforeach;
					 endif;?>				
					 </span>
				</p>
				<p><?php _e("The following content will be displayed to the user before the selected date. You can't use any of the final screen variables here, just place some static content.", 'watupro');?></p>
			<?php wp_editor((empty($dquiz->delay_results_content) ? '' : stripslashes($dquiz->delay_results_content)), "watupro_delay_results_content", array("editor_class" => 'i18n-multilingual', 'textarea_name' => 'delay_results_content')); ?>	
				<p><?php _e("Note that if you select to send email to user / admin, the email will still be sent immediately after taking the quiz. If you don't want to reveal results be careful what content you place in the email box below.", 'watupro');?></p>
			</div>
			
			<p><input type="checkbox" name="use_different_email_output" value="1" <?php if(!empty($dquiz->email_output)) echo "checked"?> onclick="this.checked?jQuery('#emailOutput').show():jQuery('#emailOutput').hide()"> <?php _e('Use different output for the email that is sent to the user/admin', 'watupro')?></p>
			
			<div id="emailOutput" style='display:<?php echo empty($dquiz->email_output)?'none':'block';?>'>
				<p><label><?php _e('Email subject:', 'watupro');?></label> <textarea name="email_subject" cols="80" rows="1"><?php echo empty($dquiz->email_subject) ? '' : stripslashes($dquiz->email_subject);?></textarea></p>
				<p><?php printf(__('If you leave it empty, default subject will be used. You can use the variable %1$s to include the %2$s name and the variable %3$s to include the user name.', 'watupro'), '%%QUIZ_NAME%%', WATUPRO_QUIZ_WORD, '%%USER-NAME%%')?></p>			
			
				<p><label><?php _e('Email contents:', 'watupro');?></label><?php wp_editor(stripslashes($dquiz->email_output),"email_output", array("editor_class" => 'i18n-multilingual')); ?></p>
				<p><?php printf(__('By default this content is used for both the email sent to user, and the email sent to admin. You can however use the %s tag to make the email contents different. The content before the %s tag will be sent to the user (if the corresponding checkbox is checked) and the content after the %s tag - to the admin.', 'watupro'), '{{{split}}}', '{{{split}}}', '{{{split}}}')?></p>
			</div>
			
			<?php if(@file_exists(get_stylesheet_directory().'/watupro/usable-variables.php')) require get_stylesheet_directory().'/watupro/usable-variables.php';
			else require WATUPRO_PATH."/views/usable-variables.php";?>
			
			<p><?php printf(__('The shortcode %s can be used to display a link or button for re-taking the quiz. Learn more in the <a href="%s" target="_blank">internal Help page</a>.', 'watupro'),
				'[watupro-retake]', 'admin.php?page=watupro_help');?></p>
		</div>
	</div>
	
	<p class="submit">
	<?php wp_nonce_field('watupro_create_edit_quiz'); ?>
	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="quiz" value="<?php echo empty($_REQUEST['quiz']) ? 0 : $_REQUEST['quiz']; ?>" />
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) @$user_ID ?>" />
	<span id="autosave"></span>
	<input type="submit" class="button-primary" name="submit" value="<?php _e('Save All Settings', 'watupro') ?>" style="font-weight: bold;" tabindex="4" />
	</p>

</form>

<?php $debug = print_r(error_get_last(),true);
	//if(!empty($debug)) echo '<p>php-error: '.esc_attr($debug).'</p>';?>
</div>

<script type="text/javascript" >
function watuPROChangePagination(val) {	
	jQuery('#alwaysShowSubmit').show();
	jQuery('#watuPROCustomPerPage').hide();
	jQuery('#autoStoreProgress').hide();
	jQuery('#categoryPaginator').hide();
	jQuery('#catHeaderEveryPage').hide();
	
	if(val != 1) {jQuery('#disallowPrevious').show(); jQuery('#autoStoreProgress').show();}
	else {jQuery('#disallowPrevious').hide();}
	
	if(val==2) {jQuery('#groupByCat').attr('checked', true); jQuery('#randomizeCats').removeAttr('disabled');}
	if(val==1) jQuery('#alwaysShowSubmit').hide();
	if(val == 3) jQuery('#watuPROCustomPerPage').show();
	
	if(val != <?php echo WATUPRO_PAGINATE_CUSTOM_NUMBER?> && jQuery('#groupByCat').prop('checked')) jQuery('#categoryPaginator').show();
	
	if(val != <?php echo WATUPRO_PAGINATE_PAGE_PER_CATEGORY?>) jQuery('#catHeaderEveryPage').show();
	
	<?php if(watupro_intel()):?>watuPROChangePagination_i(val);<?php endif;?>
	
}

jQuery(document).ready(function() {
    jQuery('.watuproDatePicker').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});

function watuproChangeTab(lnk, tab) {
	jQuery('.watupro-tab-div').hide();
	jQuery('#' + tab).show();
	
	jQuery('.nav-tab-active').addClass('nav-tab').removeClass('nav-tab-active');
	jQuery(lnk).addClass('nav-tab-active');
}

function WatuPROValidateExam(frm) {
	if(frm.name.value == '') {
		alert("<?php printf(__('Please enter %s name', 'watupro'), WATUPRO_QUIZ_WORD)?>");
		frm.name.focus();
		return false; 
	}
	
	if(frm.published_odd.checked && frm.published_odd_url.value == '') {
		alert("<?php printf(__('If the %s is published in custom field you must provide the URL to the page where it is published.', 'watupro'), WATUPRO_QUIZ_WORD)?>");
		frm.published_odd_url.focus();
		return false;
	}
	
	return true;
}

// check / uncheck "require user login" option
function WatuPROLoginRequired(status) {
	if(status) {
		jQuery('#loginMode').show();
		jQuery('#emailGuestOptional').hide();
	}
	else {
		jQuery('#loginMode').hide();
		jQuery('#emailGuestOptional').show();
	}
}

// when disallow previous button is checked
function watuPROdisallowPrevious(chk) {
	// do nothing in core WatuPRO
	<?php if(watupro_intel()):?>watuPROChangePagination_i(chk.form.single_page.value);<?php endif;?>
	return true;
}

// whether to show the warning about timer and asking for contact details at the end
function wtpMaybeWarnAboutTimer(frm) {
	if(frm.ask_for_contact_details.value == 'end' && frm.time_limit.value > 0) {
		jQuery('#watuProAskTimerNote').show();
	}
	else jQuery('#watuProAskTimerNote').hide();
	
	// if timer is above 0 also display the "turn red when X seconds or less remain" box
	if(frm.time_limit.value > 0) jQuery('#timerTurnsRed').show();
	else jQuery('#timerTurnsRed').hide();
}

// hides or shows the "dontShowAnsweredQuestions" checkbox
function toggleDontShowAnsweredQuestions(frm) {
	if(frm.require_login.checked) {
			jQuery('#dontShowAnsweredQuestions').show();
	}
	else jQuery('#dontShowAnsweredQuestions').hide();
}

// group questions by category
function watuPROGroupByCat(chk) {
	if(chk.checked) {
		chk.form.randomize_cats.disabled = false;
		jQuery('#groupByCatSettings').show();
	} 
	else {
		chk.form.randomize_cats.disabled = true;
		jQuery('#groupByCatSettings').hide();
	}
}

// show or hide the time limit on retake grades
function showHideRetakeGradesTimeLimit() {
	var anyChecked = false;
	
	jQuery('.watupro-retake-grades').each(function(i, elt){
		if(elt.checked) anyChecked = true;
	});
	
	if(anyChecked) jQuery('#retakeGradesTimeLimit').show();
	else jQuery('#retakeGradesTimeLimit').hide();
}
</script>