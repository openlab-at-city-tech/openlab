<div class="wrap watupro-wrap">
	<?php if(empty($in_default_grades)):?>
		<h1><?php if(empty($exam->is_personality_quiz)) printf(__('Manage Grades in "%s"', 'watupro'), apply_filters('watupro_qtranslate', stripslashes($exam->name)));
		else printf(__('Manage Personality Types in "%s"', 'watupro'), stripslashes($exam->name));?></h1>
	
		<p><a href="admin.php?page=watupro_exams"><?php printf(__("Back to %s", 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></a>
		| <a href="admin.php?page=watupro_exam&quiz=<?php echo $exam->ID?>&action=edit"><?php printf(__('Edit %s', 'watupro'), WATUPRO_QUIZ_WORD)?></a>
		| <a href="admin.php?page=watupro_questions&quiz=<?php echo $exam->ID?>"><?php _e('Manage Questions', 'watupro')?></a>	
		</p>
		
		<form method="post">
			<p><input type="checkbox" name="reuse_default_grades" value="1" <?php if($exam->reuse_default_grades) echo 'checked'?> onclick="this.form.submit();"> <?php printf(__('This %s will reuse the <a href="%s" target="_blank">default grades</a>.', 'watupro'), WATUPRO_QUIZ_WORD, 'admin.php?page=watupro_default_grades')?> <?php printf(__('(Alternatively you can <a href="%s">copy the default grades</a> into the %s.)', 'watupro'), "admin.php?page=watupro_grades&quiz=".$exam->ID."&amp;copy_default_grades=1", __('quiz', 'watupro'))?></p>
			<input type="hidden" name="set_reuse_default_grades" value="1">
			<?php wp_nonce_field('watupro_grades');?>
		</form>
		<form method="post">
			<div style='display:<?php echo (empty($exam->reuse_default_grades) and count($cats) and (!watupro_intel() or empty($exam->is_personality_quiz))) ? 'block' : 'none';?>'>
				<p><input type="checkbox" name="final_grade_depends_on_cats" value="1" <?php if(!empty($advanced_settings['final_grade_depends_on_cats'])) echo 'checked'?> onclick="this.form.submit();"> <?php printf(__('The final grade in this %s will depend on the performance on different question categories (<a href="%s" target="_blank">what does this mean?</a>)', 'watupro'), WATUPRO_QUIZ_WORD, 'http://blog.calendarscripts.info/test-grade-based-on-question-category-performance-watupro/')?> <?php _e('Creating individual per-category grades is possible but not required.', 'watupro');?></p>
				<p style='display:<?php echo (empty($exam->reuse_default_grades) and count($cats) and !empty($advanced_settings['final_grade_depends_on_cats'])) ? 'block' : 'none';?>;margin-left:25px;'>
					<input type="checkbox" name="calculate_dependent_ignore_empty_cats" value="1" <?php if(!empty($advanced_settings['calculate_dependent_ignore_empty_cats'])) echo 'checked'?> onclick="this.form.submit();"> 
						<?php printf(__('Ignore specific category requirements if questions from that category were not present in the %s. This may happen if you pull random questions etc.', 'watupro'), WATUPRO_QUIZ_WORD);?> 
				</p>
			</div>
			<input type="hidden" name="set_final_grade_depends" value="1">
			<?php wp_nonce_field('watupro_grades');?>
		</form>
		
		<p><strong><?php printf(__('Confused? Learn <a href="%s" target="_blank">all about WatuPRO grading system.</a>', 'watupro'), 'http://calendarscripts.info/watupro/grading.html');?></strong></p>
		
		<?php if($exam->reuse_default_grades):?>
			<h2><?php printf(__('This %s reuses the default grades', 'watupro'), WATUPRO_QUIZ_WORD)?></h2>
			
			<p><?php printf(__('Any change you apply to the default grades will be applied to this %s as well. If you want to use the default grades but have your copy in this %s, you can <a href="%s">copy the default grades</a> into it instead.', 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD, "admin.php?page=watupro_grades&quiz=".$exam->ID."&amp;copy_default_grades=1");?></p>			
		</div><!-- end wrap-->
		<?php return true; 
		endif;?>
	<?php else:?>
		<h1><?php _e('Manage Default Grades', 'watupro')?></h1>
		
		<p><?php printf(__('These grades / results can be reused or copied in any of your %s.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?> <b><?php printf(__('Note that the grades will not be automatically applied to new %s. You need to actually go to Grades page for the %s and select that it will reuse or copy the default grades.', 'watupro'),
			WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD);?></b></p>
	<?php endif;?>

	<?php if(count($cats) and (!watupro_intel() or empty($exam->is_personality_quiz))):?>
		<form method="post">
			<p><?php _e('Manage grades / results for', 'watupro')?> <select name="cat_id" onchange="this.form.submit();">
				<option value="0" <?php if($cat_id == 0) echo 'selected'?>><?php printf(__('- The Whole %s -', 'watupro'), __('Quiz', 'watupro'))?></option>
				<?php foreach($cats as $cat):?>
					<option value="<?php echo $cat->ID?>" <?php if($cat_id == $cat->ID) echo "selected"?>><?php echo __('Category:','watupro').' '.$cat->name?></option>
				<?php endforeach;?>			
			</select></p>
			
			<?php if(!empty($cat_id) and empty($in_default_grades)):?>
				<p><a href="#" onclick="jQuery('#gradecatDesign').toggle();return false;"><?php _e('Design the common category grade output for this quiz', 'watupro')?></a></p>
				<div id="gradecatDesign" style='display:<?php echo empty($_POST['save_design'])?'none':'block';?>;'>
				   <h2><?php _e('Design the common category grade output for this quiz', 'watupro')?></h2>
					<p><strong><?php _e('Note: you are currently managing category-specific grades. These can be displayed at the final screen using the %%CATGRADES%% variable. All of them will be shown in loop at the place of the variable. In the box below you can design how each of the category grades will look.', 'watupro')?></strong></p>
					<p><strong><?php printf(__('This design is the same for all question categories in this %s.', 'watupro'), WATUPRO_QUIZ_WORD)?></strong></p>
										
					<?php echo wp_editor(empty($exam->ID) ? stripslashes($gradecat_design) : stripslashes($exam->gradecat_design), 'gradecat_design', array("editor_class" => 'i18n-multilingual'));?>
					
					<p><?php _e('You can use several of the already known variables: <strong>%%CORRECT%%, %%WRONG%%, %%EMPTY%%, %%TOTAL%%, %%POINTS%%, %%PERCENTAGE%%, %%GTITLE%%, %%GDESC%%</strong>. The variable <strong>%%CATEGORY%%</strong> will be replaced by the category name and the variable <strong>%%CATDESC%%</strong> - with the category description. You can also use the variable <b>%%CATEGORY-ID%%</b> to display the ID or pass it to custom shortcodes.', 'watupro')?></p>
					<p><?php printf(__('You can also manually craft the output for each category instead of doing it in loop. <a href="%s" target="_blank">Learn how here</a>.', 'watupro'), 'http://blog.calendarscripts.info/using-category-grades-in-watu-pro/#manual')?></p>
					<p><?php _e('When displaying the category results / grades on the final screen, order them:', 'watupro')?> 
						<select name="gradecat_order" onchange="this.value == 'same' ? jQuery('#sortByPoints').hide() : jQuery('#sortByPoints').show();">
							<option value="same"><?php printf(__('The same way categories with questions were ordered in the %s','watupro'), __('quiz', 'watupro'))?></option>
							<option value="best" <?php if(!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order'] == 'best') echo 'selected'?>><?php printf(__('From the best to worst result','watupro'), __('quiz', 'watupro'))?></option>
							<option value="worst" <?php if(!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order'] == 'worst') echo 'selected'?>><?php printf(__('From the worst to best result','watupro'), __('quiz', 'watupro'))?></option>
						</select>
						<?php printf(__('Limit to only the first %s results (leave blank to show all categories)', 'watupro'), '<input type="text" name="gradecat_limit" size="4" value="'.@$advanced_settings['gradecat_limit'].'">');?></p>
					
					<p id="sortByPoints" style='display:<?php echo (!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order'] != 'same') ? 'block' : 'none';?>'>
						<input type="checkbox" name="sort_catgrades_by_points" value="1" <?php if(!empty($advanced_settings['sort_catgrades_by_points'])) echo 'checked'?>> <?php _e('Sort by absolute number of points instead of % correct answers.', 'watupro')?>
					</p>					
					
					<p><input type="checkbox" name="exclude_survey_from_catgrades" value="1" <?php if(!empty($advanced_settings['exclude_survey_from_catgrades'])) echo 'checked'?>> <?php _e('Exclude categories which have only survey questions.', 'watupro');?></p>	
					
					<p><input type="checkbox" name="sum_subcats_catgrades" value="1" <?php if(!empty($advanced_settings['sum_subcats_catgrades'])) echo 'checked'?> onclick="this.checked ? this.form.subcats_catgrades_include.disabled = false : this.form.subcats_catgrades_include.disabled = true;"> <?php _e('Sum up subcategory points into their parent categories. In this case subcategories will be excluded from %%CATGRADES%% variable loop unless you select this:', 'watupro');?>
					<input type="checkbox" name="subcats_catgrades_include" value="1" <?php if(!empty($advanced_settings['subcats_catgrades_include'])) echo 'checked'?>> <?php _e('Include the subcategories in the loop', 'watupro');?></p>	

					<p><input type="checkbox" name="always_calculate_catgrades" value="1" <?php if(!empty($advanced_settings['always_calculate_catgrades'])) echo 'checked'?>> <?php _e('Always calculate category grades. Normally to save server resources this is done only if category grade related variables are used in the final screen. Here you can change this in case you need to use the data in certificates or custom code.', 'watupro');?></p>						
					
					<?php wp_nonce_field('watupro_gradecat_design');?>	
					<p align="center"><input type="submit" value="<?php _e('Save the Design', 'watupro')?>" name="save_design" class="button-primary"></p>
				</div>	
			<?php endif;?>	
		</form>
	<?php else:?>
		<p><?php _e('If you create <a href="admin.php?page=watupro_question_cats">question categories</a> you will be able to create category-based grades as well.', 'watupro')?></p>
	<?php endif;?>
	
	<?php if(!empty($in_default_grades)):?>
	<p><?php _e('Currently managing grades for', 'watupro')?> <select name="grades_by_percent" onchange="window.location='admin.php?page=watupro_default_grades&percentage_based=' + this.value;">
				<option value="0" <?php if(empty($_GET['percentage_based']) or $_GET['percentage_based'] != '1') echo 'selected'?>><?php _e('points based calculation', 'watupro')?></option>
				<option value="1" <?php if(!empty($_GET['percentage_based']) and $_GET['percentage_based'] == '1') echo 'selected'?>><?php _e('percentage based calculation', 'watupro')?></option>
			</select><br>
			 <?php printf(__('This is not a setting - it is just a selector that lets you switch between managing grades for percent based or point based quizzes. The selection whether a quiz is point based or percent based is done on the Edit %s page itself.', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD));?><br>
			 <?php _e('"Percentage based calculation" means that the grade will be calculated based on the achieved percent correct answers OR percent points from the maximum possible points on the quiz. This is a setting available on per-quiz basis.', 'watupro');?></p>
   <?php endif;?>
	<hr>	
	
	
	<h2><?php if(empty($exam->is_personality_quiz)) _e('Add New Grade', 'watupro');
	else _e('Add New Personality Type', 'watupro');?></h2>
	
	<form method="post" class="watupro-form" onsubmit="return validateGrade(this);">
		<div class="watupro-padded">
			<div><?php _e('Title:', 'watupro')?> <input type="text" name="gtitle" id="gtitle" size="60" class="i18n-multilingual"></div>
			<div><?php _e('Description:', 'watupro')?> <?php echo wp_editor('', 'gdescription', array("editor_class" => 'i18n-multilingual', 'editor_height'=>150, 'textarea_name'=>'gdescription'))?></div>
			<div style='display:<?php echo (!empty($exam->is_personality_quiz) and empty($cat_id)) ? 'none' : 'block'; ?>'>
			<?php if($exam->grades_by_percent): 
					if(!empty($advanced_settings['grades_by_percent_mode']) and $advanced_settings['grades_by_percent_mode'] == 'max_points'): _e('Assign this grade when <b>achieved % of maximum points</b> is from', 'watupro');
					else :_e('Assign this grade when <b>% correct answers</b> is from', 'watupro'); endif;			 
			else: _e('Assign this grade when the <b>number of points</b> that user has collected are from', 'watupro');
			endif;?>
			<input type="text" name="gfrom" size="5"> <?php _e('to', 'watupro')?> <input type="text" name="gto" size="5">
				<?php if(!empty($advanced_settings['final_grade_depends_on_cats']) and empty($exam->reuse_default_grades) and count($cats) and empty($cat_id)):?>
					<?php _e('Enter zeros or leave BOTH fields blank if you want to use only per-category requirements but not also global requirement.', 'watupro');?>
					<h3><?php _e('Per-Category Requirements:', 'watupro');?></h3>
					<?php foreach($cats as $cat):?>
						<label><?php printf(__('Category "%s":', 'watupro'), stripslashes($cat->name));?>
						<?php _e('from', 'watupro');?></label> <input type="text" name="from_<?php echo $cat->ID?>" size="5"> <?php _e('to', 'watupro')?> <input type="text" name="to_<?php echo $cat->ID?>" size="5"> <br />
					<?php endforeach;?>
					<p><?php _e("You can enter from-to for each category. If you don't want to set requirements for given category just leave tis BOTH fields blank or enter 0 in them.", 'watupro');?></p>
					<hr>
				<?php endif;?>
			</div>
			
			<?php if(!empty($exam->is_personality_quiz) and empty($cat_id)):?>
					<p><?php _e("When creating global personality types for a personality quiz you don't need to set from/to points or % correct answers. Instead of this the grade is assigned to the choices your user make on questions.", 'watupro')?></p>
				<?php endif;?>			
			
			<?php if(!empty($cnt_certificates)):?>
				<div><label><?php _e('Upon achieving this grade / result assign the following certificate:', 'watupro')?></label> <select name="certificate_id">
				<option value="0"><?php _e("- Don't assign certificate", 'watupro')?></option>
				<?php foreach($certificates as $certificate):?>
					<option value="<?php echo $certificate->ID;?>"><?php echo stripslashes($certificate->title);?></option>
				<?php endforeach;?>
				</select></div>
			<?php endif;?>
			<p><?php _e('If this grade is achieved redirect to URL (optional):', 'watupro');?> <input type="text" size="40" name="redirect_url"></p>
			<?php if(!empty($integrate_moolamojo)):?>
				<p><?php printf(__("If this grade is achieved transfer %s of virtual credits to user's MoolaMojo balance", 'watupro'), '<input type="text" name="moola" size="4">');?></p>
			<?php endif;?>
			<div align="center"><input type="submit" value="<?php _e('Add This Grade / Result', 'watupro')?>" class="button-primary"></div>
		</div>
		<input type="hidden" name="add" value="1">
		<input type="hidden" name="cat_id" value="<?php echo $cat_id?>">	
		<?php wp_nonce_field('watupro_grade');?>
	</form>
	
	<hr>
	<?php if(count($grades)):?>
	<h2><?php if(empty($exam->is_personality_quiz)) _e('Edit Existing Grades', 'watupro');
	else _e('Edit Existing Personality Types', 'watupro');?></h2>
	<?php endif;?>
	
	<?php foreach($grades as $grade):?>
		<form method="post" class="watupro-form" onsubmit="return validateGrade(this);">
			<div class="watupro-padded">
				<div><b><?php printf(__('ID: %d', 'watupro'), $grade->ID);?></b></div>
				<div><?php _e('Title:', 'watupro')?> <input type="text" name="gtitle" id="gtitle<?php echo $grade->ID?>" size="80" value="<?php echo htmlentities(stripslashes($grade->gtitle))?>" class="i18n-multilingual"></div>
				<div><?php _e('Description:', 'watupro')?> <?php echo wp_editor(stripslashes($grade->gdescription), 'gdescription'.$grade->ID, array("editor_class" => 'i18n-multilingual', 'editor_height'=>150))?></div>
				<div style="display:<?php echo (!empty($exam->is_personality_quiz) and empty($cat_id)) ? 'none' : 'block'; ?>">
					<?php if($exam->grades_by_percent): 
					   if(!empty($advanced_settings['grades_by_percent_mode']) and $advanced_settings['grades_by_percent_mode'] == 'max_points'): _e('Assign this grade when achieved % of maximum points is from', 'watupro');
						else :_e('Assign this grade when <b>% correct answers</b> is from', 'watupro'); endif;				 
					else: _e('Assign this grade when the <b>number of points</b> that user has collected are from', 'watupro');
					endif;?>
					<input type="text" name="gfrom" size="5" value="<?php echo $grade->gfrom?>"> <?php _e('to', 'watupro')?> <input type="text" name="gto" size="5" value="<?php echo $grade->gto?>">			
					<?php if(!empty($advanced_settings['final_grade_depends_on_cats']) and empty($exam->reuse_default_grades) and count($cats) and empty($cat_id)):
						$catreqs = unserialize(stripslashes($grade->category_requirements));?>
					<?php _e('Enter zeros or leave BOTH fields blank if you want to use only per-category requirements but not also global requirement.', 'watupro');?>
					<h3><?php _e('Per-Category Requirements:', 'watupro');?></h3>
					<?php foreach($cats as $cat):?>
						<label><?php printf(__('Category "%s":', 'watupro'), stripslashes($cat->name));?>
						<?php _e('from', 'watupro');?></label> <input type="text" name="from_<?php echo $cat->ID?>" size="5" value="<?php echo @$catreqs[$cat->ID]['from']?>"> <?php _e('to', 'watupro')?> <input type="text" name="to_<?php echo $cat->ID?>" size="5" value="<?php echo @$catreqs[$cat->ID]['to']?>"> <br />
					<?php endforeach;?>
					<p><?php _e("You can enter from-to for each category. If you don't want to set requirements for given category just leave tis BOTH fields blank or enter 0 in them.", 'watupro');?></p>
					<hr>
				<?php endif;?>						
				</div>
				
				<?php if(!empty($exam->is_personality_quiz) and empty($cat_id)):?>
					<p><?php _e("When creating global personality types for a personality quiz you don't need to set from/to points or % correct answers. Instead of this the grade is assigned to the choices your user make on questions.", 'watupro')?></p>
				<?php endif;?>
				
				<?php if(!empty($cnt_certificates)):?>
					<div><label><?php _e('Upon achieving this grade / result assign the following certificate:', 'watupro')?></label> <select name="certificate_id">
					<option value="0" <?php if(empty($row->ID) or $row->certificate_id==0) echo "selected"?>><?php _e("- Don't assign certificate", 'watupro')?></option>
					<?php foreach($certificates as $certificate):?>
						<option value="<?php echo $certificate->ID;?>" <?php if(!empty($grade->ID) and $grade->certificate_id==$certificate->ID) echo "selected"?>><?php echo $certificate->title;?></option>
					<?php endforeach;?>
					</select></div>
				<?php endif;?>
				<p><?php _e('If this grade is achieved redirect to URL (optional):', 'watupro');?> <input type="text" size="40" name="redirect_url" value="<?php echo $grade->redirect_url;?>"></p>
				<?php if(!empty($integrate_moolamojo)):?>
				<p><?php printf(__("If this grade is achieved transfer %s of virtual credits to user's MoolaMojo balance", 'watupro'), '<input type="text" name="moola" size="4" value="'.$grade->moola.'">');?></p>
			<?php endif;?>
				<?php if($in_default_grades and $multiuser_access == 'own' and $grade->editor_id != $user_ID):?>
					<p><?php _e('This default grade is not created by you and you have no permissions to edit or delete it.', 'watupro')?></p>
				<?php else:?>
					<div align="center"><input type="submit" value="<?php _e('Save', 'watupro')?>" class="button-primary">
					<input type="button" value="<?php _e('Delete', 'watupro')?>" onclick="confirmDelGrade(this.form);" class="button"></div>
				<?php endif;?>	
			</div>
			<input type="hidden" name="id" value="<?php echo $grade->ID?>">
			<input type="hidden" name="save" value="1">
			<input type="hidden" name="del" value="0">
			<input type="hidden" name="cat_id" value="<?php echo $cat_id?>">	
			<?php wp_nonce_field('watupro_grade');?>
		</form>
		
		<hr>
	<?php endforeach;?>
</div>
<script type="text/javascript" >
function validateGrade(frm) {
	if(frm.gtitle.value=="") {
		alert("<?php _e('Please enter grade title','watupro')?>");
		frm.gtitle.focus();
		return false;
	}
	
	<?php if(empty($exam->is_personality_quiz)):?>
	if(frm.gfrom.value=="" || isNaN(frm.gfrom.value)) {
		alert("<?php _e('Please enter number','watupro')?>");
		frm.gfrom.focus();
		return false;
	}
	
	if(frm.gto.value=="" || isNaN(frm.gto.value)) {
		alert("<?php _e('Please enter number','watupro')?>");
		frm.gto.focus();
		return false;
	}
	
	<?php if(!empty($exam->grades_by_percent)):?>
		if(frm.gfrom.value < 0) {
			alert("<?php _e('For % based calculation the lowest possible value is 0%.','watupro')?>");
			frm.gfrom.focus();
			return false;
		}	
		
		if(frm.gto.value > 100) {
			alert("<?php _e('For % based calculation the highest possible value is 100%.','watupro')?>");
			frm.gfrom.focus();
			return false;
		}	
		<?php endif; // endif grades by percent	
	endif;?>
	return true;
}

function confirmDelGrade(frm) {
	if(confirm("<?php _e('Are you sure?', 'watupro')?>")) {
		frm.del.value=1;
		frm.submit();
	}
}
</script>