<style type="text/css">
<?php watupro_resp_table_css(1000);?>
</style>

<div class="wrap watupro-wrap">
<h2><?php printf(__("Manage %s", 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL)); ?></h2>

<?php watupro_display_alerts(); ?>
<?php if(!watupro_intel() or WatuPROIMultiUser :: check_access('settings_access', true)):?>
	<p><?php _e('Go to', 'watupro')?> <a href="admin.php?page=watupro_options"><?php _e('Watu PRO Settings', 'watupro')?></a></p>
<?php endif;?>

<p><a href="admin.php?page=watupro_exam&amp;action=new"><?php printf(__("Create New %s", 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></a></p>

<?php if(!empty($chained_notice)):?>
<p><b><span style="color:red;">Notice:</span> you are using an outdated version of the Chained Logic addon. Please <a href="https://blog.calendarscripts.info/chained-quiz-logic-free-add-on-for-watupro/" target="_blank">download the latest</a>, uninstall your current Chained Logic plugin and install the new one. There will be no loss of data or setting during this.</b></p>
<?php endif;?>

<form method="get" action="admin.php">
<input type="hidden" name="page" value="watupro_exams">
	<p><?php _e('Filter by category:', 'watupro')?> <select name="cat_id">
		<option value="-1"><?php _e('All Categories', 'watupro')?></option>
		<option value="0" <?php if(isset($_GET['cat_id']) and $_GET['cat_id'] === '0') echo 'selected'?>><?php _e('Uncategorized', 'watupro')?></option>
		<?php foreach($cats as $cat):?>
			<option value="<?php echo $cat->ID?>" <?php if(!empty($_GET['cat_id']) and $_GET['cat_id'] == $cat->ID) echo 'selected'?>><?php echo $cat->name?></option>
			<?php foreach($cat->subs as $sub):?>
				<option value="<?php echo $sub->ID?>" <?php if(!empty($_GET['cat_id']) and $_GET['cat_id'] == $sub->ID) echo 'selected'?>> - <?php echo stripslashes($sub->name);?></option>
			<?php endforeach;?>
		<?php endforeach;?>	
	</select>
	&nbsp;
	<?php _e('title contains:', 'watupro')?> <input type="text" name="title" value="<?php echo empty($_GET['title']) ? '' : esc_attr($_GET['title'])?>">
	<?php _e('comments contain:', 'watupro')?> <input type="text" name="comments" value="<?php echo empty($_GET['comments']) ? '' : esc_attr($_GET['comments'])?>">
	<?php _e('ID is:', 'watupro')?> <input type="text" name="exam_id" value="<?php echo empty($_GET['exam_id']) ? '' : intval($_GET['exam_id'])?>" size="4">
	<?php _e('and tagged as:', 'watupro')?> <input type="text" name="filter_tag" value="<?php echo empty($_GET['filter_tag']) ? '' : esc_attr($_GET['filter_tag'])?>"> 
	<input type="submit" value="<?php printf(__('Filter %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?>">
	
	<?php if(!empty($filter_sql)):?><input type="button" value="<?php _e('Clear filters', 'watupro')?>" onclick="window.location='admin.php?page=watupro_exams'"><?php endif;?></p>
</form>

<form method="post" action="admin.php?page=watupro_exams">
   <p><?php printf(__('%s per page:', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD_PLURAL));?> <select name="page_limit" onchange="this.form.submit();">
      <option value="10" <?php if($page_limit == 10) echo 'selected'?>>10</option>
      <option value="20" <?php if($page_limit == 20) echo 'selected'?>>20</option>
      <option value="50" <?php if($page_limit == 50) echo 'selected'?>>50</option>
      <option value="100" <?php if($page_limit == 100) echo 'selected'?>>100</option>
   </select></p>
   
   <input type="hidden" name="reset_page_limit" value="1">
</form>

<form method="post">
<table class="widefat watupro-table">
	<thead>
	<tr>
		<th><input type="checkbox" onclick="WatuPROSelectAll(this);"></th>
		<th scope="col"><div style="text-align: center;"><a href="admin.php?page=watupro_exams&dir=<?php echo $odir?>&ob=Q.ID<?php echo $filter_params?>"><?php _e('ID', 'watupro') ?></a></div></th>
		<th scope="col"><a href="admin.php?page=watupro_exams&dir=<?php echo $odir?>&ob=Q.name<?php echo $filter_params?>"><?php _e('Title', 'watupro') ?></a></th>
        <th scope="col"><?php _e('Embed Code', 'watupro') ?></th>
		<?php if(empty($low_memory_mode) or $low_memory_mode != 1):?><th scope="col"><a href="admin.php?page=watupro_exams&dir=<?php echo $odir?>&ob=question_count<?php echo $filter_params?>"><?php _e('No. questions', 'watupro') ?></a></th><?php endif;?>
		<th scope="col"><a href="admin.php?page=watupro_exams&dir=<?php echo $odir?>&ob=added_on<?php echo $filter_params?>"><?php _e('Created on', 'watupro') ?></a></th>
		<th scope="col"><a href="admin.php?page=watupro_exams&dir=<?php echo $odir?>&ob=cat<?php echo $filter_params?>"><?php _e('Category', 'watupro') ?></a></th>
		<th scope="col"><?php _e('View Results', 'watupro') ?></th>
		<?php if($multiuser_access != 'view' and $multiuser_access != 'group_view' and $multiuser_access != 'view_approve' and $multiuser_access != 'group_view_approve'):?>
			<th scope="col"><?php _e('Manage Questions', 'watupro') ?></th>
			<th scope="col"><?php _e('Manage Grades', 'watupro') ?></th>
			<th scope="col"><?php _e('Edit/Delete', 'watupro') ?></th>
		<?php endif;?>
	</tr>
	</thead>

	<tbody id="the-list">
<?php
if ($count):
	foreach($exams as $quiz):
		$advanced_settings = unserialize(stripslashes($quiz->advanced_settings));
		if(empty($class)) $class = null;
		$class = ('alternate' == $class) ? '' : 'alternate';
		print "<tr id='quiz-{$quiz->ID}' class='$class'>\n";
		?>
		<td><input type="checkbox" name="qids[]" value="<?php echo $quiz->ID?>" class="qids" onclick="toggleMassActions();"></td>
		<td scope="row" style="text-align: center;"><?php echo $quiz->ID ?></td>
		<td><?php if(!empty($quiz->post) and empty($quiz->published_odd)) echo "<a href='".get_permalink($quiz->post->ID)."' target='_blank'>"; 
		if(!empty($quiz->published_odd)) echo "<a href='".$quiz->published_odd_url."' target='_blank'>";
		else echo "<span onmouseover=\"jQuery('#noHyperlink".$quiz->ID."').show();\">";
		echo apply_filters('watupro_qtranslate', stripslashes($quiz->name));
		if(!empty($quiz->post) or !empty($quiz->published_odd)) echo "</a>";
		else echo '</span><br><a href="https://calendarscripts.info/watupro/howto.html?tab=er&q=emergency14" target="_blank" id="noHyperlink'.$quiz->ID.'" style="display:none;" onmouseout="jQuery(this).hide()">'.sprintf(__('No %s hyperlink?', 'watupro'), WATUPRO_QUIZ_WORD).'</a>';
		if(empty($quiz->is_active)) echo "<br><i>".__('(Inactive)', 'watupro')."</i>";
		if(!empty($advanced_settings['admin_comments'])):
			echo "<p>".nl2br(stripslashes(rawurldecode($advanced_settings['admin_comments'])))."</p>";
		endif;
		if(!empty($advanced_settings['dont_store_taking']) or !empty($advanced_settings['store_taking_only_logged'])):
			if(!empty($advanced_settings['dont_store_taking'])): echo "<p><span style='color:red;'>".sprintf(__('This %s will not store user data!', 'watupro'), WATUPRO_QUIZ_WORD)."</span><br>"; endif;
			if(empty($advanced_settings['dont_store_taking'])): echo "<p><span style='color:red;'>".sprintf(__('This %s will store only logged in user data!', 'watupro'), WATUPRO_QUIZ_WORD)."</span><br>"; endif;
			_e('You can change this at the Advanced Settings tab', 'watupro')."</p>";
		endif;
		if(!empty($quiz->tags) and $quiz->tags != '||'):
		 $tags = explode("|", trim(stripslashes($quiz->tags), '|'));
		 echo "<p>".__('Tags:', 'watupro').' ';
		 foreach($tags as $tag) echo '<a href="admin.php?page=watupro_exams&filter_tag='.$tag.'">'.$tag.'</a> &nbsp;';
		 echo '<p>';
		endif;?></td>
        <td><input type="text" size="14" value="[watupro <?php echo $quiz->ID ?>]" onclick="this.select();" readonly></td>
		<?php if(empty($low_memory_mode) or $low_memory_mode != 1):?><td><?php echo empty($quiz->reuse_questions_from) ? $quiz->question_count : sprintf(__('Reuses from other %s(s)', 'watupro'), WATUPRO_QUIZ_WORD)?></td><?php endif;?>
		<td><?php echo date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quiz->added_on)) ?><br>
		<?php printf(__('By %s', 'watupro'), $quiz->author ? $quiz->author : __('admin', 'watupro'))?></td>
		<td><?php echo $quiz->cat?$quiz->cat:__("Uncategorized", 'watupro');?></td>
		<td><a href="admin.php?page=watupro_takings&exam_id=<?php echo $quiz->ID;?>"><?php if(empty($low_memory_mode) or $low_memory_mode != 1): printf(__('Taken %d times', 'watupro'),$quiz->taken); else: _e('view results', 'watupro'); endif;?></a></td>
		<?php if($multiuser_access != 'view' and $multiuser_access != 'group_view' and $multiuser_access != 'view_approve' and $multiuser_access != 'group_view_approve'):?>
			<td><a href='admin.php?page=watupro_questions&amp;quiz=<?php echo $quiz->ID?>' class='edit'><?php _e('Questions', 'watupro')?></a></td>
			<td><a href='admin.php?page=watupro_grades&amp;quiz=<?php echo $quiz->ID?>' class='edit'><?php if(empty($quiz->is_personality_quiz)) _e('Grades', 'watupro');
			else _e('Personality types', 'watupro');?></a></td>
			<td><a href='admin.php?page=watupro_exam&amp;quiz=<?php echo $quiz->ID?>&amp;action=edit' class='edit'><?php _e('Edit', 'watupro'); ?></a>
			&nbsp;|&nbsp; <a href="<?php echo wp_nonce_url('admin.php?page=watupro_exams&amp;action=delete&amp;quiz='.$quiz->ID, 'delete_quiz', 'delete_nonce')?>" class='delete' onclick="return confirm('<?php echo  addslashes(sprintf(__("You are about to delete this %s? This will delete all the questions and answers within this %s. Press 'OK' to delete and 'Cancel' to stop.", 'watupro'), WATUPRO_QUIZ_WORD, WATUPRO_QUIZ_WORD))?>');"><?php _e('Delete', 'watupro')?></a></td>
		<?php endif;?>	
		</tr>
<?php endforeach;?>
	<tr><td colspan="8"><p><strong><?php _e('To publish any of the existing tests simply copy the "Embed code" shown in the table above and paste it in a post or page of your blog.', 'watupro');?><br />
	<span style="color: red;"><?php _e('Please do not use more than one of these shortcodes in a single post or page.', 'watupro')?></span></strong></p></td></tr>	
<?php else:?>
	<tr>
		<td colspan="7"><?php _e('No tests found.', 'watupro') ?></td>
	</tr>
<?php endif;?>
	</tbody>
</table>

<div align="center" style="display:none;" id="massQuizActions"><p>
	<?php if(count($cats)): 
		printf(__('Change category of selected %s to:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?> <select name="mass_cat_id">			
			<?php foreach($cats as $cat):?>
				<option value="<?php echo $cat->ID?>"><?php echo stripslashes($cat->name);?></option>
				<?php foreach($cat->subs as $sub):?>
					<option value="<?php echo $sub->ID?>"> - <?php echo stripslashes($sub->name);?></option>
				<?php endforeach;?>
			<?php endforeach;?>
			</select>
	<?php endif; // end if count($cats)?>
 	<input type="submit" name="mass_change_category" value="<?php _e('Assign selected category', 'watupro')?>" class="button-primary">	
 	&nbsp;
	<input type="submit" name="mass_activate" onclick="if(!confirm('<?php _e('Are you sure?', 'watupro')?>')) return false;" value="<?php _e('Activate Selected', 'watupro')?>" class="button-primary">
	&nbsp;
	<input type="submit" name="mass_deactivate" onclick="if(!confirm('<?php _e('Are you sure?', 'watupro')?>')) return false;" value="<?php _e('Deactivate Selected', 'watupro')?>" class="button-primary">
	&nbsp;
	</div>

	<?php wp_nonce_field('watupro_exams');?>
	</form>

</form>

<?php if($count):?>
	<p align="center"> <?php if($offset > 0):?><a href="admin.php?page=watupro_exams&dir=<?php echo $dir?>&ob=<?php echo $ob?>&offset=<?php echo ($offset - $page_limit)?><?php echo $filter_params?>"><?php echo _wtpt(__('Previous page', 'watupro'));?></a><?php endif;?>
	&nbsp;
	 <?php if(($offset + $page_limit) < $count):?><a href="admin.php?page=watupro_exams&dir=<?php echo $dir?>&ob=<?php echo $ob?>&offset=<?php echo ($offset + $page_limit)?><?php echo $filter_params?>"><?php echo _wtpt(__('Next page', 'watupro'))?></a><?php endif;?> </p>
<?php endif;?>

	<p align="center"><i><?php echo "WatuPRO version ".watupro_get_version()?></i></p>	
</div>

<script type="text/javascript">
<?php watupro_resp_table_js();?>

function WatuPROSelectAll(chk) {
	if(chk.checked) {
		jQuery(".qids").attr('checked',true);
	}
	else {
		jQuery(".qids").removeAttr('checked');
	}
	
	toggleMassActions();
}

// shows or hides the mass delete button
function toggleMassActions() {
	var len = jQuery(".qids:checked").length;
	
	if(len) jQuery('#massQuizActions').show();
	else jQuery('#massQuizActions').hide();
}
</script>