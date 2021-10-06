<style type="text/css">
<?php watupro_resp_table_css(900);?>
#TB_window {
  min-width:60% !important;
}
</style>

<?php if(!$in_shortcode):?>
<div class="wrap watupro-wrap">
	<?php if(!empty($exam->ID)):?>
		<h1><?php printf(__('Users who took %s "%s"', 'watupro'), WATUPRO_QUIZ_WORD, stripslashes(apply_filters('watupro_qtranslate', $exam->name)));?></h1>
	<?php else:?>
		<h1><?php printf(__('All %s Results', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));?></h1>
	<?php endif;?>	
	
	<?php if(!empty($_GET['msg'])):?>
		<p class="watupro-success"><?php echo $_GET['msg']?></p>
	<?php endif;?>

	<?php if(!empty($exam->ID)):?>
		<p><a href="admin.php?page=watupro_exams"><?php printf(__('Back to %s list', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></a> 
	&nbsp;
	<a href="edit.php?page=watupro_exam&quiz=<?php echo $exam->ID?>&action=edit"><?php printf(__('Edit this %s', 'watupro'), WATUPRO_QUIZ_WORD)?></a></p>
	<?php endif; // if(!empty($exam->ID))?>	
		<p>		
		<a href="#" onclick="jQuery('#filterForm').toggle('slow');return false;"><?php _e('Filter/search these records', 'watupro')?></a> 
		
		| <a href="admin.php?page=watupro_takings&exam_id=<?php echo empty($exam->ID) ? '' : $exam->ID?>&ob=<?php echo $ob?>&dir=<?php echo $dir;?>&<?php echo $filters_url;?>&export=1&noheader=1"><?php _e('Export this page', 'watupro')?><?php if($display_filters):?> <?php _e('(Filters apply)', 'watupro')?><?php endif;?></a>
		<?php if(empty($exam->ID)):?>
		<?php echo '<br /><b>'.sprintf(__('Note that fields from "Ask user for contact details" section cannot be exported from this page because they are different in each %s. To export them you must select a specific %s and export then.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD).'</b>';
		endif;?>
	<?php if(!empty($exam->ID)):?>		
		| <a href="admin.php?page=watupro_takings&exam_id=<?php echo $exam->ID?>&ob=<?php echo $ob?>&dir=<?php echo $dir;?>&<?php echo $filters_url;?>&export=1&details=1&noheader=1"><?php _e('Export with details', 'watupro')?><?php if($display_filters):?> <?php _e('(Filters apply)', 'watupro')?><?php endif;?></a>
		| <a href="admin.php?page=watupro_questions_feedback&quiz_id=<?php echo $exam->ID?>"><?php _e('Feedback on questions', 'watupro')?></a>
		<?php if(watupro_module('reports')):?>| <a href="admin.php?page=watupro_question_stats&exam_id=<?php echo $exam->ID?>"><?php _e('Stats per question', 'watupro')?></a>
		| <a href="admin.php?page=watupro_cat_stats&exam_id=<?php echo $exam->ID?>"><?php _e('Stats per category & tag', 'watupro')?></a>
		| <a href="admin.php?page=watupro_question_chart&exam_id=<?php echo $exam->ID?>"><?php _e('Chart by grade', 'watupro')?></a>
		| <a href="admin.php?page=watupro_cross_table&exam_id=<?php echo $exam->ID?>"><?php _e('Cross Tabulation', 'watupro')?></a><?php endif;?> 	
		<?php if(watupro_intel() and !empty($exam->fee) and $exam->fee > 0):?>
		| <a href="admin.php?page=watupro_payments&exam_id=<?php echo $exam->ID?>"><?php _e('View Payments', 'watupro')?></a>
		<?php endif;?>
		<?php do_action('watupro_takings_page_links', '&exam_id='.$exam->ID.'&ob='.$ob.'&dir='.$dir.'&'.$filters_url);?></p>
		<p><?php printf(__('Export files are currently delimited by "%s". You can change the delimiter at <a href="%s">WatuPRO Settings</a> page.', 'watupro'), get_option('watupro_csv_delim'), 'admin.php?page=watupro_options');?> </p>
		
		<?php if($in_progress):?><p><a href="admin.php?page=watupro_takings&exam_id=<?php echo $exam->ID?>"><?php _e('Back to completed results', 'watupro')?></a></p><?php endif;
		if(!empty($num_unfinished) and $multiuser_access!= 'view' and $multiuser_access != 'group_view'):?><p><a href="admin.php?page=watupro_takings&exam_id=<?php echo $exam->ID?>&in_progress=1"><?php printf(__('There are %d unfinished attempt(s).','watupro'), $num_unfinished);?></a></p><?php endif;?>
	<?php endif; // if(!empty($exam->ID))?>
			<p><?php _e('Shortcode to publish a simplified version of this page:', 'watupro');?> <input type="text" size="30" value='[watupro-takings quiz_id=<?php echo intval(@$exam->ID)?>]' readonly="readonly" onclick="this.select();">
			<?php printf(__('You can apply filters to this and allow public access. <a href="%s" target="_blank">Learn more</a>.', 'watupro'), 'http://blog.calendarscripts.info/filters-for-the-watupro-takings-shortcode/');?></p>
	
	<div id="filterForm" style='display:<?php echo $display_filters?'block':'none';?>;margin-bottom:10px;padding:5px;' class="widefat">
	<form method="get" class="watupro" action="admin.php">
	<input type="hidden" name="page" value="watupro_takings">
		<div><label><?php echo ucfirst(WATUPRO_QUIZ_WORD)?></label> <select name="exam_id" onchange="wtpPopulateGrades(this.value, this.form);">
			<option value="0"><?php printf(__('- All %s -', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></option>
			<?php foreach($dd_quizzes as $q):
				$selected = (!empty($exam->ID) and $q->ID == $exam->ID) ? ' selected' : '';?>
				<option value="<?php echo $q->ID?>"<?php echo $selected?>><?php echo stripslashes(apply_filters('watupro_qtranslate', $q->name));?></option>
			<?php endforeach;?>
		</select></div>
		
		<div id="quizCatFilter" style='display:<?php echo empty($exam->ID) ? 'block' : 'none';?>'><label><?php printf(__('%s Category', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></label> <select name="quiz_cat_id">
			<option value="0"><?php _e('- All Categories -', 'watupro');?></option>
			<?php foreach($quiz_cats as $quiz_cat):?>
					<option value="<?php echo $quiz_cat->ID?>" <?php if(!empty($_GET['quiz_cat_id']) and $quiz_cat->ID == $_GET['quiz_cat_id']) echo "selected"?>><?php echo stripslashes($quiz_cat->name);?></option>
					<?php foreach($quiz_cat->subs as $sub):?>
						<option value="<?php echo $sub->ID?>" <?php if(!empty($_GET['quiz_cat_id']) and $_GET['quiz_cat_id'] == $sub->ID) echo "selected"?>>&nbsp; - <?php echo stripslashes($sub->name);?></option>
				<?php endforeach; 
				endforeach;?>	
		</select></div>
			
		<div><label><?php _e('Username', 'watupro')?></label> <select name="dnf">
			<option value="equals" <?php if(empty($_GET['dnf']) or $_GET['dnf']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['dnf']) and $_GET['dnf']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['dnf']) and $_GET['dnf']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['dnf']) and $_GET['dnf']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="dn" value="<?php echo empty($_GET['dn']) ? '' : esc_attr($_GET['dn'])?>"></div>
		<div><label><?php _e('Email', 'watupro')?></label> <select name="emailf">
			<option value="equals" <?php if(empty($_GET['emailf']) or $_GET['emailf']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['emailf']) and $_GET['emailf']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['emailf']) and $_GET['emailf']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['emailf']) and $_GET['emailf']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="email" value="<?php echo empty($_GET['email']) ? '' : esc_attr($_GET['email'])?>"></div>
		<?php if(!empty($advanced_settings['ask_for_contact_details'])): 
		if(!empty($advanced_settings['contact_fields']['company'])):?>
			<div><label><?php echo rawurldecode($advanced_settings['contact_fields']['company_label']);?></label> <select name="companyf">
			<option value="equals" <?php if(empty($_GET['companyf']) or $_GET['companyf']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['companyf']) and $_GET['companyf']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['companyf']) and $_GET['companyf']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['companyf']) and $_GET['companyf']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="field_company" value="<?php echo empty($_GET['field_company']) ? '' : stripslashes(esc_attr($_GET['field_company']))?>"></div>
		<?php endif;
		if(!empty($advanced_settings['contact_fields']['phone'])):?>
			<div><label><?php echo rawurldecode($advanced_settings['contact_fields']['phone_label']);?></label> <select name="phonef">
			<option value="equals" <?php if(empty($_GET['phonef']) or $_GET['phonef']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['phonef']) and $_GET['phonef']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['phonef']) and $_GET['phonef']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['phonef']) and $_GET['phonef']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="field_phone" value="<?php echo empty($_GET['field_phone']) ? '' : stripslashes(esc_attr($_GET['field_phone']))?>"></div>
		<?php endif;
		if(!empty($advanced_settings['contact_fields']['field1'])):?>
			<div><label><?php echo rawurldecode($advanced_settings['contact_fields']['field1_label']);?></label> <select name="field1f">
			<option value="equals" <?php if(empty($_GET['field1f']) or $_GET['field1f']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['field1f']) and $_GET['field1f']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['field1f']) and $_GET['field1f']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['field1f']) and $_GET['field1f']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="field_1" value="<?php echo empty($_GET['field_1']) ? '' : stripslashes(esc_attr($_GET['field_1']))?>"></div>
		<?php endif;
		if(!empty($advanced_settings['contact_fields']['field2'])):?>
			<div><label><?php echo rawurldecode($advanced_settings['contact_fields']['field2_label']);?></label> <select name="field2f">
			<option value="equals" <?php if(empty($_GET['field2f']) or $_GET['field2f']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['field2f']) and $_GET['field2f']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['field2f']) and $_GET['field2f']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['field2f']) and $_GET['field2f']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="field_2" value="<?php echo empty($_GET['field_2']) ? '' : stripslashes(esc_attr($_GET['field_2']))?>"></div>
		<?php endif;
		endif; // end if ask for contact ?>
		<div><label><?php _e('IP Address', 'watupro')?></label> <select name="ipf">
			<option value="equals" <?php if(empty($_GET['ipf']) or $_GET['ipf']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="starts" <?php if(!empty($_GET['ipf']) and $_GET['ipf']=='starts') echo "selected"?>><?php _e('Starts with', 'watupro')?></option>
			<option value="ends" <?php if(!empty($_GET['ipf']) and $_GET['ipf']=='ends') echo "selected"?>><?php _e('Ends with', 'watupro')?></option>
			<option value="contains" <?php if(!empty($_GET['ipf']) and $_GET['ipf']=='contains') echo "selected"?>><?php _e('Contains', 'watupro')?></option>
		</select> <input type="text" name="ip" value="<?php echo empty($_GET['ip']) ? '' : stripslashes(esc_attr($_GET['ip']))?>"></div>
		<div><label><?php _e('Date Taken', 'watupro')?></label> <select name="datef" onchange="this.value == 'range' ? jQuery('#wtpDate2').show() : jQuery('#wtpDate2').hide();">
			<option value="equals" <?php if(empty($_GET['datef']) or $_GET['datef']=='equals') echo "selected"?>><?php _e('Equals', 'watupro')?></option>
			<option value="before" <?php if(!empty($_GET['datef']) and $_GET['datef']=='before') echo "selected"?>><?php _e('Is before', 'watupro')?></option>
			<option value="after" <?php if(!empty($_GET['datef']) and $_GET['datef']=='after') echo "selected"?>><?php _e('Is after', 'watupro')?></option>			
			<option value="range" <?php if(!empty($_GET['datef']) and $_GET['datef']=='range') echo "selected"?>><?php _e('Range', 'watupro')?></option>
		</select> <input type="text" name="date" value="<?php echo empty($_GET['date']) ? '' : stripslashes(esc_attr($_GET['date']))?>"> <i>YYYY-MM-DD</i>
			<span id="wtpDate2" style='display:<?php echo (empty($_GET['datef']) or $_GET['datef']!='range') ? 'none' : 'inline';?>'>
				- <input type="text" name="date2" value="<?php echo empty($_GET['date2']) ? '' : stripslashes(esc_attr($_GET['date2']))?>"> <i>YYYY-MM-DD</i>
			</span>
		</div>
		<div><label><?php _e('Points received', 'watupro')?></label> <select name="pointsf">
			<option value="equals" <?php if(empty($_GET['pointsf']) or $_GET['pointsf']=='equals') echo "selected"?>><?php _e('Equal', 'watupro')?></option>
			<option value="less" <?php if(!empty($_GET['pointsf']) and $_GET['pointsf']=='less') echo "selected"?>><?php _e('Are less than', 'watupro')?></option>
			<option value="more" <?php if(!empty($_GET['pointsf']) and $_GET['pointsf']=='more') echo "selected"?>><?php _e('Are more than', 'watupro')?></option>			
		</select> <input type="text" name="points" value="<?php echo empty($_GET['points']) ? '' : stripslashes(esc_attr($_GET['points']))?>"></div>
		
		<div><label><?php _e('% correct answers', 'watupro')?></label> <select name="percentf">
			<option value="equals" <?php if(empty($_GET['percentf']) or $_GET['percentf']=='equals') echo "selected"?>><?php _e('Equal', 'watupro')?></option>
			<option value="less" <?php if(!empty($_GET['percentf']) and $_GET['percentf']=='less') echo "selected"?>><?php _e('Is less than', 'watupro')?></option>
			<option value="more" <?php if(!empty($_GET['percentf']) and $_GET['percentf']=='more') echo "selected"?>><?php _e('Is more than', 'watupro')?></option>			
		</select> <input type="text" name="percent_correct" value="<?php echo empty($_GET['percent_correct']) ? '' : stripslashes(esc_attr($_GET['percent_correct']))?>"></div>		
		
		<?php if(!empty($grades)):?>
			<div style='display:<?php echo empty($exam->ID) ? 'none' : 'block';?>' id="wtpSelectGrade"><label><?php _e('Grade equals', 'watupro')?></label> <select name="grade" id="wtpGradeSelector">
			<option value="" <?php if(empty($_GET['grade'])) echo "selected"?>>------</option>
			<?php foreach($grades as $grade):?>
				<option value="<?php echo $grade->ID?>" <?php if(!empty($_GET['grade']) and $_GET['grade']==$grade->ID) echo "selected"?>><?php echo $grade->gtitle;?></option>
			<?php endforeach;?>
			</select></div>
		<?php endif;?>		
		
		<div><label><?php _e('User role is', 'watupro')?></label> <select name="role">
		<option value=""><?php _e('Any role', 'watupro')?></option>
		<?php foreach($roles as $key => $role):?>
			<option value="<?php echo $key?>" <?php if(!empty($_GET['role']) and $_GET['role']==$key) echo 'selected'?>><?php echo _x($role['name'],'User role', 'watupro')?></option>
		<?php endforeach;?>		
		</select></div>
		
		<div><label><?php _e('User or guest', 'watupro')?></label> <select name="loggedin">
		<option value=""><?php _e('Both users and guests', 'watupro')?></option>
		<option value="yes" <?php if(!empty($_GET['loggedin']) and $_GET['loggedin'] == 'yes') echo 'selected';?>><?php _e('Only logged in users', 'watupro')?></option>
		<option value="no" <?php if(!empty($_GET['loggedin']) and $_GET['loggedin'] == 'no') echo 'selected';?>><?php _e('Only guests', 'watupro')?></option>
		</select></div>
		
		<?php if(!get_option('watupro_use_wp_roles') and sizeof($groups)):?>
			<div><label><?php _e('User is in group', 'watupro')?></label> <select name="ugroup">
			<option value=""><?php _e('Any group', 'watupro')?></option>
			<?php foreach($groups as $group):?>
				<option value="<?php echo $group->ID?>" <?php if(!empty($_GET['ugroup']) and $_GET['ugroup']==$group->ID) echo 'selected'?>><?php echo stripslashes($group->name)?></option>
			<?php endforeach;?>		
			</select></div>
		<?php endif;?>		
		
		<?php if(!empty($namaste_courses)):?>
			<div><label><?php _e('Student in course:', 'watupro')?></label> <select name="namaste_course_id">
			<option value=""><?php _e('Any course', 'watupro')?></option>
			<?php foreach($namaste_courses as $namaste_course):?>
				<option value="<?php echo $namaste_course->ID?>" <?php if(!empty($_GET['namaste_course_id']) and $_GET['namaste_course_id'] == $namaste_course->ID) echo 'selected'?>><?php echo stripslashes($namaste_course->post_title)?></option>
			<?php endforeach;?>		
			</select></div>
		<?php endif;?>
		
		<?php if(count($source_urls)):?>
			<div><label><?php _e('Submitted from page:', 'watupro')?></label> <select name="source_url">
			<option value=""><?php _e('Any source', 'watupro')?></option>
			<?php foreach($source_urls as $source_url):?>
				<option value="<?php echo $source_url->source_url?>" <?php if(!empty($_GET['source_url']) and $_GET['source_url'] == $source_url->source_url) echo 'selected'?>><?php echo $source_url->source_url?></option>
			<?php endforeach;?>		
			</select></div>
		<?php endif;?>
		
		<div><p><input type="checkbox" name="filter_by_question" value="1" <?php if(!empty($_GET['filter_by_question'])) echo 'checked'?> onclick="WatuPROFilterByQuestion(this);"> <?php _e('Filter by answer to a speicfic question (currently only single-choice questions)', 'watupro');?></p></div>
		
		<div id="filterByQuestion">
		<?php if(!empty($_GET['filter_by_question'])):
		// simply call watupro_ajax and trick it with the POST case			
		watupro_ajax('question_filter', false);
		endif;?>		
		</div>
		
		<div><input type="submit" value="<?php _e('Search/Filter', 'watupro')?>" class="button-primary">
		<input type="button" value="<?php _e('Clear Filters', 'watupro')?>" onclick="window.location='admin.php?page=watupro_takings&exam_id=<?php echo @$exam->ID;?>';" class="button"></div>
	</form>
	</div>
	<?php endif; // end if not in shortcode
	if(!count($takings)):?>
		<p><?php _e('There are no records that match your search criteria', 'watupro')?></p>
	<?php else:?>

		<?php if(!$in_shortcode):?>
		<form method="post" action="admin.php?page=watupro_takings&ob=<?php echo $ob?>&dir=<?php echo $dir?>&<?php echo $filters_url;?>&exam_id=<?php echo empty($_GET['exam_id']) ? 0 :  intval($_GET['exam_id']);?>">
		   <p><?php _e('Records per page', 'watupro');?> <select name="per_page" onchange="this.form.submit();">
		      <option value="10" <?php if($per_page == 10) echo 'selected'?>>10</option>
		      <option value="20" <?php if($per_page == 20) echo 'selected'?>>20</option>
		      <option value="50" <?php if($per_page == 50) echo 'selected'?>>50</option>
		      <option value="100" <?php if($per_page == 100) echo 'selected'?>>100</option>
		      <option value="500" <?php if($per_page == 500) echo 'selected'?>>500</option>
		   </select></p>
		   
		   <input type="hidden" name="reset_page_limit" value="1">
		</form>
		<?php endif;?>	
	
		<form method="post">
		<table class="<?php echo $in_shortcode ? 'watupro-table' : 'watupro-table widefat' ?><?php if(!empty($atts['classes'])) echo ' '.$atts['classes']?>">
		<thead>			
			<tr>
				<?php if(!$in_shortcode or (is_user_logged_in() and (current_user_can('watupro_manage_exams') or current_user_can('manage_options')))):?>	
					<?php if(!isset($atts['show_id']) or !empty($atts['show_id'])):?><th><input type="checkbox" onclick="WatuPROSelectAll(this);"></th>
					<th><a href="<?php echo $target_url?>&ob=ID&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('ID', 'watupro');?></a></th><?php endif;?>
				<?php endif;?>
				<?php if(empty($exam->ID)):?>
					<th><a href="<?php echo $target_url?>&ob=exam_name&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php printf(__('%s Name', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></a></th>
				<?php endif;?>
				<?php if(!isset($atts['show_name']) or !empty($atts['show_name'])):?><th><a href="<?php echo $target_url?>&ob=display_name&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('Name', 'watupro')?></a></th><?php endif;?>
				<?php if(!isset($atts['show_email']) or !empty($atts['show_email'])):?><th><a href="<?php echo $target_url?>&ob=user_email&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('Email', 'watupro')?></a></th><?php endif;?>
				<?php if(isset($atts['show_user_id']) and !empty($atts['show_user_id'])):?><th><a href="<?php echo $target_url?>&ob=user_id&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('User ID', 'watupro')?></a></th><?php endif;?>
				<?php if(!$in_shortcode):?><th><a href="<?php echo $target_url?>&ob=ip&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e("IP", 'watupro')?></a></th><?php endif;?>
				<th><a href="<?php echo $target_url?>&ob=date&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('Date', 'watupro')?></a></th>
				<?php if(!isset($atts['show_points']) or !empty($atts['show_points'])):?><th><a href="<?php echo $target_url?>&ob=points&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('Points', 'watupro')?></a></th><?php endif;?>
				<?php if(!isset($atts['show_percent']) or !empty($atts['show_percent'])):?><th><a href="<?php echo $target_url?>&ob=percent_correct&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('% correct', 'watupro')?></a></th><?php endif;?>
				<?php if(!isset($atts['show_percent_points']) or !empty($atts['show_percent_points'])):?><th><a href="<?php echo $target_url?>&ob=percent_of_max&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('% of points', 'watupro')?></a></th><?php endif;?>
				<?php if(!isset($atts['show_grade']) or !empty($atts['show_grade'])):?><th><a href="<?php echo $target_url?>&ob=result&dir=<?php echo $odir;?>&<?php echo $filters_url;?>"><?php _e('Grade', 'watupro')?></a>
				<?php if($has_catgrades and empty($atts['public'])):?>[<a href="#" onclick="jQuery('.watupro_global_grade').toggle();jQuery('.watupro_cat_grade').toggle();return false;"><?php _e('Toggle per category', 'watupro');?></a>]<?php endif;?>	
				<?php if($has_personalities and empty($atts['public'])):?>[<a href="#" onclick="jQuery('.watupro_global_grade').toggle();jQuery('.watupro_personality_breakup').toggle();return false;"><?php _e('Toggle per personality', 'watupro');?></a>]<?php endif;?>	
				<?php endif?>
				<?php if(!empty($grade_info) and !$in_shortcode) echo '<br><span style="font-size:small;">'.$grade_info.'</span>';?>
				</th>
				<?php if(!isset($atts['show_time']) or !empty($atts['show_time'])):?><th><?php _e('Time spent', 'watupro')?></th><?php endif;?>
				<?php if(!$in_shortcode or (is_user_logged_in() and (current_user_can('watupro_manage_exams') or current_user_can('manage_options')))):?>				
				<?php if(!isset($atts['show_details']) or !empty($atts['show_details'])):?><th><?php _e('Details', 'watupro')?></th><?php endif;?>
					<?php if($multiuser_access != 'view' and $multiuser_access != 'group_view' and $multiuser_access != 'view_approve' and $multiuser_access != 'group_view_approve'):?>
						<?php if(!isset($atts['show_delete']) or !empty($atts['show_delete'])):?>
							<th><?php _e('Delete', 'watupro')?></th><?php endif;?>
						<?php endif; // end if $atts['show_delete']?>	
				<?php endif;?>	
			</tr>
		</thead>
		<tbody>
		<?php foreach($takings as $taking):
			$GLOBALS['watupro_view_taking_id'] = $taking->ID;
			$taking_name_braces = empty($taking->name) ? "" : "<br>(".stripslashes($taking->name).")"; // used to show for logged in users
			if(empty($class)): $class = 'alternate'; else: $class = ''; endif;			
			$taking_email = empty($taking->email) ? $taking->user_email : $taking->email;
			if(empty($taking->name) and $taking->user_id) $taking->name = $taking->display_name;?>
			<tr id="taking<?php echo $taking->ID?>" class="<?php echo $class?>">
			<?php if(!$in_shortcode or (is_user_logged_in() and (current_user_can('watupro_manage_exams') or current_user_can('manage_options')))):?>
				<?php if(!isset($atts['show_id']) or !empty($atts['show_id'])):?>
					<td><input type="checkbox" name="tids[]" value="<?php echo $taking->ID?>" class="tids" onclick="toggleMassDelete();"></td>
					<td><?php echo $taking->ID?></td><?php endif;?>
				<?php endif;?>
			<?php if(empty($exam->ID)):?>
				<td><a href="admin.php?page=watupro_takings&exam_id=<?php echo $taking->exam_id?>"><?php echo stripslashes(apply_filters('watupro_qtranslate', $taking->exam_name))?></a></td>
			<?php endif;?>
			<?php if(!isset($atts['show_name']) or !empty($atts['show_name'])):?><td><?php $user_link = class_exists('WTPReports') ? "admin.php?page=watupro_reports&user_id=".$taking->user_id : "admin.php?page=my_watupro_exams&user_id=" . $taking->user_id; 
			echo ($taking->user_id and !$in_shortcode) ? "<a href='".$user_link."' target='_blank'>".$taking->display_name."</a>" . $taking_name_braces : (empty($taking->name) ? "N/A" : stripslashes($taking->name)); 
			if(!empty($taking->contact_data) and (!$in_shortcode or !empty($atts['show_contact_data']))) echo '<br>'.stripslashes($taking->contact_data);
			if(!empty($taking->user_groups) and !$in_shortcode) echo '<br>'.sprintf(__('User groups: %s', 'watupro'), $taking->user_groups);?></td><?php endif;?>
			<?php if(!isset($atts['show_email']) or !empty($atts['show_email'])):?><td><?php echo !empty($taking_email) ? "<a href='mailto:".$taking_email."'>".$taking_email."</a>" : "N/A"?></td><?php endif;?>
			<?php if(isset($atts['show_user_id']) and !empty($atts['show_user_id'])):?><td><?php echo !empty($taking_email) ? $taking->user_id : "N/A"?></td><?php endif;?>
			<?php if(!$in_shortcode):?><td><?php echo $gdpr ? substr($taking->ip, 0, 8).'xxx' : $taking->ip;?></td><?php endif;?>
			<td><?php echo date_i18n($dateformat.' '.$timeformat, strtotime(($taking->end_time == '2000-01-01 00:00:00') ? $taking->date : $taking->end_time));
			if(!$in_shortcode and !empty($taking->source_url) and !empty($advanced_settings['save_source_url'])) printf('<br>'.__('Source: %s', 'watupro'), $taking->source_url);?></td>
			<?php if(!isset($atts['show_points']) or !empty($atts['show_points'])):?><td><?php echo $taking->in_progress ? __('N/A', 'watupro') : $taking->points;?></td><?php endif;?>
			<?php if(!isset($atts['show_percent']) or !empty($atts['show_percent'])):?><td><?php echo $taking->in_progress ? __('N/A', 'watupro') : sprintf(__('%d%%', 'watupro'), $taking->percent_correct);?><br>
			<?php printf(__('%d correct, %d wrong, and %d unanswered', 'watupro'), $taking->num_correct, $taking->num_wrong, $taking->num_empty);?></td><?php endif;?>
			<?php if(!isset($atts['show_percent_points']) or !empty($atts['show_percent_points'])):?><td><?php printf(__('%d%%', 'watupro'), $taking->percent_of_max);?></td><?php endif;?>
			<?php if(!isset($atts['show_grade']) or !empty($atts['show_grade'])):?><td><div class="watupro_global_grade"><?php if($in_shortcode): $taking->result = stripslashes($taking->grade_title); endif; 
			echo $taking->result ? preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", apply_filters('watupro_content', apply_filters('watupro_qtranslate', $taking->result))) : _e('N/A', 'watupro');
			if(trim(strip_tags($taking->result)) == __('None', 'watupro') and empty($none_info)):
				if(empty($exam->ID) or count($grades)): printf(' '.__('(<a href="%s" target="_blank">Why?</a>)', 'watupro'), 'https://blog.calendarscripts.info/receiving-grade-none-or-unexpected-grade-watupro/');
				else: printf(' '.__('(<a href="%s">Define grades</a>)', 'watupro'), 'admin.php?page=watupro_grades&quiz='.$taking->exam_id);
				endif; // end if/else count($grades)
				$none_info = true;
			endif;?></div>
			<?php if($has_catgrades):?>
			<div class="watupro_cat_grade" style="display:none;"><?php $catgrades = unserialize(stripslashes($taking->catgrades_serialized));
				if(is_array($catgrades)):?>
				<table class="widefat">
					<tr><th><?php _e('Category', 'watupro');?></th><th><?php _e('% correct', 'watupro');?></th><th><?php _e('Points', 'watupro');?></th><th><?php _e('Grade', 'watupro');?></th></tr>
					<?php foreach($catgrades as $catgrade):
						if(empty($cls)) $cls = 'alternate';
						else $cls = '';?>
						<tr class="<?php echo $cls;?>"><td><?php echo stripslashes($catgrade['name']);?></td><td><?php echo $catgrade['percent'];?>%</td><td><?php echo $catgrade['points'];?></td>
						<td><?php echo $catgrade['gtitle'] ? stripslashes($catgrade['gtitle']) : __('N/a', 'watupro');?></td></tr>
					<?php endforeach;?>	
				</table>
				<?php endif; // end if is_array($catgrades);?>
			</div>
			<?php endif; // end if $has_catfrades?>
			
			<?php if($has_personalities):?>
			<div class="watupro_personality_breakup" style="display:none;"><?php $personality_array = $taking->personalities;
				if(is_array($personality_array)):?>
				<table class="widefat">
					<tr><th><?php _e('Personality', 'watupro');?></th><th><?php _e('Points', 'watupro');?></th></tr>
					<?php foreach($grades as $personality_type):
						if($personality_type->is_cumulative_grade) continue;
						// find the points for this taking
						$personality_type_points = 0;
						foreach($personality_array as $pers_id => $val) {
							if($pers_id == $personality_type->ID) $personality_type_points = $val;
						}
						if(empty($cls)): $cls = 'alternate'; else: $cls = ''; endif;?>						
						<tr class="<?php echo $cls;?>"><td><?php echo stripslashes($personality_type->gtitle);?></td><td><?php echo $personality_type_points;?></td></tr>
					<?php endforeach;?>	
				</table>
				<?php endif; // end if is_array($personality_array);?>
			</div>
			<?php endif; // end if $has_personalities?>
							
			
			</td>
			<?php endif; // end if showing grade (shortcode condition);?>
			<?php if(!isset($atts['show_time']) or !empty($atts['show_time'])):?>
				<td><?php echo WTPRecord :: time_spent_human(WTPRecord :: time_spent($taking));
				if(!empty($taking->auto_submitted)): echo '<br><i>'.__('Automatically submitted', 'watupro').'<br>'.__('(ran out of time)', 'watupro'); endif;
				if(!empty($taking->timer_log)):?><br><a href="#" onclick="jQuery('#timerLog<?php echo $taking->ID?>').toggle();return false;"><?php _e('View timer log', 'watupro');?></a>
					<div style="display:none;" id="timerLog<?php echo $taking->ID?>"><?php echo stripslashes($taking->timer_log);?></div>
				<?php endif;?></td>
			<?php endif;?>
			<?php if(!$in_shortcode or (is_user_logged_in() and (current_user_can('watupro_manage_exams') or current_user_can('manage_options')))):?>		
				<?php if(!isset($atts['show_details']) or !empty($atts['show_details'])):?>				
					<td><?php if($taking->in_progress): _e('N/A', 'watupro'); else:
						if($in_shortcode and !empty($atts['details_no_popup'])):
						$taking_url = add_query_arg('watupro_taking_id', $taking->ID, $_GET['current_url']);?>
						<a href="<?php echo $taking_url?>"><?php _e('view', 'watupro')?></a>
						<?php else: // popup
						?><a href="#" onclick="WatuPRO.takingDetails('<?php echo $taking->ID?>');return false;"><?php _e('view', 'watupro')?></a>
					<?php endif; // end if no popup 
					if(watupro_intel() and $multiuser_access != 'view' and $multiuser_access != 'group_view'):
						if($in_shortcode):
							$current_url = esc_url_raw($atts['current_url']);
							$edit_url = add_query_arg(array('watupro_edit_taking' => 1, 'exam_id' => $exam->ID, 'taking_id' => $taking->ID, 'goto'=>urlencode($current_url)), $current_url);		
						else: $edit_url = 'admin.php?page=watupro_edit_taking&id=' . $taking->ID;
						endif;?>
					/ <a href="<?php echo $edit_url?>"><?php _e('edit', 'watupro')?></a>
					<?php if(!empty($taking->last_edited)) printf("<br>".__('Last edited on %s', 'watupro'), date($dateformat, strtotime($taking->last_edited)));  
						endif;// end if Intelligence enabled
					endif;// end if not in progress?>		
					</td>
				<?php endif; // end if $atts['show_details'];?>	
				
				<?php if($multiuser_access != 'view' and $multiuser_access != 'group_view' and $multiuser_access != 'view_approve' and $multiuser_access != 'group_view_approve'):?>
						<?php if(!isset($atts['show_delete']) or !empty($atts['show_delete'])):?>
							<td><a href="#" onclick="deleteTaking(<?php echo $taking->ID?>);return false;"><?php _e('delete', 'watupro')?></a></td>
						<?php endif; // end if $atts['show_delete']?>
					<?php endif; // end if allowed to see this link by access rules ?>
				<?php endif;?>
			</tr>
		<?php endforeach;?>
		</tbody>
		</table>
		
		<div align="center" style="display:none;" id="massDeleteTakings"><p>
			<input class="button" type="submit" name="mass_delete" onclick="if(!confirm('<?php _e('Are you sure?', 'watupro')?>')) return false;" value="<?php _e('Delete Selected', 'watupro')?>">
		</p></div>
		<?php wp_nonce_field('watupro_takings');?>
		</form>
		
		<?php if($per_page != -1):?>
		<p><?php _e('Showing', 'watupro')?> <?php echo ($offset+1)?> - <?php echo ($offset+$per_page)>$count?$count:($offset+$per_page)?> <?php _e('from', 'watupro')?> <?php echo $count;?> <?php _e('records', 'watupro')?></p>
		<?php endif;?>
		
		<p align="center">
		<?php if($offset>0):?>
			<a href="<?php echo $target_url?>offset=<?php echo $offset-$per_page;?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&<?php echo $filters_url;?>"><?php _e('previous page', 'watupro')?></a>
		<?php endif;?>
		&nbsp;
		<?php if($per_page != -1 and $count>($offset+$per_page)):?>
			<a href="<?php echo $target_url?>offset=<?php echo $offset+$per_page;?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&<?php echo $filters_url;?>"><?php _e('next page', 'watupro')?></a>
		<?php endif;?>
		</p>
		
		<?php if(!$in_shortcode and !empty($exam->ID) and $multiuser_access == 'all' and empty($class_manager_sql)):
		if(empty($_GET['in_progress'])):?>
		<form method="post" onsubmit="return validateCleanup(this)">
			<p><input type="checkbox" name="yesiamsure" value="1" onclick="this.checked ? jQuery('#cleanupBtn').show() : jQuery('#cleanupBtn').hide()"> <?php printf(__('Show me a button to cleanup all submitted data on this %s.', 'watupro'), WATUPRO_QUIZ_WORD)?>
			<?php if(!empty($filter_sql)):?> <span style="color:red;font-weight: bold;"><?php _e('Filters apply', 'watupro');?></span><?php endif;?></p> 
			
			<div  style="display:none;" id="cleanupBtn">
				<p><?php _e('Cleaning up all data may affect user levels and points, and the reports. Alternatively you can just blank out the data which will keep all user points and reports and will only remove the textual data from some fields. This will reduce less DB space but will keep most of the things intact.', 'watupro')?></p>
				<?php if(!empty($filter_sql)):?>
				<p><?php _e('Note: you have currently filtered the results on this page. Only the data that respects your filters will be cleaned up. If you wish to clean up all data, reset the filters first.', 'watupro');?></p>
				<?php endif;?>
				<p style="color:red;font-weight:bold;"><?php _e('These operations cannot be undone!', 'watupro')?></p>
				<p><input type="submit" name="blankout" value="<?php _e('Blank out data', 'watupro')?>" class="button-primary">
				<input type="submit" name="cleanup" value="<?php _e('Cleanup all data', 'watupro')?>" class="button-primary"></p>
			</div>
			<?php wp_nonce_field('watupro_cleanup');?>
		</form>
	<?php else: // in progress - show cleanup button for in progress attempts ?>
	 <form method="post" onsubmit="return validateCleanup(this)">
	 	<p><input type="checkbox" value="1" name="yesiamsure" value="1" onclick="this.checked ? jQuery('#cleanupUnfinishedBtn').show() : jQuery('#cleanupUnfinishedBtn').hide()"> <?php printf(__('Show me a button to delete all unfinished attempts on this %s.', 'watupro'), WATUPRO_QUIZ_WORD)?></p> 
	 	<div  style="display:none;" id="cleanupUnfinishedBtn">
		 <p style="color:red;font-weight:bold;"><?php _e('These operations cannot be undone!', 'watupro')?></p>
	 		<p><input type="submit" value="<?php _e('Delete all unfinished attempts', 'watupro');?>" class="button-primary"></p>
	 	</div>	
	 	<input type="hidden" name="delete_unfinished" value="1">
	 	<?php wp_nonce_field('watupro_unfinished');?>
	 </form>
	<?php endif; // end if/else in progress 
	endif; // end if there are takings ?>	
	
<?php if(!$in_shortcode):?></div><?php endif;?>

<?php //$debug = print_r(error_get_last(),true);
	//if(!empty($debug)) echo '<p>php-error: '.esc_attr($debug).'</p>';?>

<script type="text/javascript">
<?php if(empty($atts['public'])):?>
function deleteTaking(id) {
	// delete taking data by ajax and remove the row with jquery
	if(!confirm("Are you sure?")) return false;
	
	data={"action":'watupro_delete_taking', "id": id};
	jQuery.get(ajaxurl, data, function(msg) {
		if(msg!='') {
			alert(msg);
			return false;
		}
			
		// empty msg means success, remove the row
		jQuery('#taking'+id).remove();
	});	
}

function validateCleanup(frm) {
	if(confirm("<?php _e('Are you sure? This operation cannot be undone!', 'watupro')?>")) return true;
	return false;
}

// populates the grades drop-down depending on the selected exam
function wtpPopulateGrades(examID, frm) {
	if(examID > 0) {
		jQuery('#wtpSelectGrade').show();
		jQuery('#quizCatFilter').hide();
	}
	else {
		jQuery('#wtpSelectGrade').hide();
		jQuery('#quizCatFilter').show();
	}  
	
	var data = {"exam_id": examID, 'action': 'watupro_ajax', 'do': 'select_grades'};
	
	jQuery.post("<?php echo admin_url('admin-ajax.php')?>", data, function(msg) {
		jQuery('#wtpGradeSelector').html(msg);
	});
	
	WatuPROFilterByQuestion(frm.filter_by_question);
}

<?php endif; ?>
</script>
<?php endif; // end if not in shortcode ?>

<div id="takingDiv"></div>

<script type="text/javascript">
<?php if(empty($atts['public'])):?>
function WatuPROSelectAll(chk) {
	if(chk.checked) {
		jQuery(".tids").attr('checked',true);
	}
	else {
		jQuery(".tids").removeAttr('checked');
	}
	
	toggleMassDelete();
}

// shows or hides the mass delete button
function toggleMassDelete() {
	var len = jQuery(".tids:checked").length;
	
	if(len) jQuery('#massDeleteTakings').show();
	else jQuery('#massDeleteTakings').hide();
}

function WatuPROFilterByQuestion(chk) {
	if(!chk.checked) {
		jQuery('#filterByQuestion').html('');
		return false;
	}
	
	jQuery('#filterByQuestion').html('<img src="<?php echo WATUPRO_URL.'/img/loading.gif'?>">');
	
	// send ajax request to get the questions and create drop-downs
	url = watupro_i18n.ajax_url;
	data = {'action' : 'watupro_ajax', "exam_id": chk.form.exam_id.value, 'do': 'question_filter'};
	jQuery.post(url, data, function(msg){
		jQuery('#filterByQuestion').html(msg);
	});
}
<?php endif;?>
<?php watupro_resp_table_js();?>
</script>