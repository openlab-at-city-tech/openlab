<div class="wrap watupro-wrap">
	<?php if(empty($_GET['parent_id'])):?>
		<h1><?php printf(__('%s Question Category', 'watupro'), empty($_GET['id']) ? __('Add', 'watupro') : __('Edit watupro'))?></h1>
		<p><a href="admin.php?page=watupro_question_cats"><?php _e('Back to question categories', 'watupro')?></a></p>
	<?php else:?>
		<h1><?php printf(__('%s Subcategory Under "%s"', 'watupro'), empty($_GET['id']) ? __('Add', 'watupro') : __('Edit watupro'), apply_filters('watupro_qtranslate', stripslashes($parent->name)))?></h1>
		<p><a href="admin.php?page=watupro_question_cats&parent_id=<?php echo intval($_GET['parent_id'])?>"><?php _e('Back to all subcategories', 'watupro')?></a></p>
	<?php endif;?>		

	<form method="post" id="watuPROQCatForm" onsubmit="return watuPROValidate(this);">
	<div >
		<p ><label><strong><?php _e('Category name:', 'watupro')?></strong></label>
		 <input type="text" name="name" size="30" value="<?php echo stripslashes(@$cat->name)?>" class="i18n-multilingual">
		</p> 
		<p>
				<label><strong><?php _e('Category description:', 'watupro')?></strong></label><br>				
				<?php wp_editor(stripslashes(@$cat->description), 'description', array("editor_class" => 'i18n-multilingual'))?></p>
				
		<p style="display:none;"><?php _e('Optional icon / image URL:', 'watupro');?> <input type="text" name="icon" value="<?php echo empty($cat->ID) ? '' : $cat->icon;?>" size="100"><br>
		<?php printf(__('If provided it will be used instead of the category name when displaying the optional category paginator in %1$s. URLs of the uploaded images can be found in your <a href="%2$s" target="_blank">Media gallery</a>', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL, 'upload.php');?></p>		
	
		<p><input type="checkbox" name="exclude_from_reports" value="1" <?php if(!empty($cat->exclude_from_reports)) echo 'checked'?>> <?php _e('Exclude from reports and result exports', 'watupro');?></p>
		<p>
			<input type="submit" value="<?php _e('Save Category', 'watupro')?>"  class="button-primary">
	      <input type="submit" value="<?php _e('Save &amp; Add New', 'watupro')?>" name="save_and_new" class="button-primary">
	</div>
	  <?php wp_nonce_field('watupro_qcat');?>
	  <input type="hidden" name="ok" value="1">
	</form>
	
</div>

<script type="text/javascript" >
function watuPROValidate(frm) {
	if(frm.name.value == '') {
		alert("<?php _e('Please enter category name', 'watupro')?>");
		frm.name.focus();
		return false;
	}
}	
</script>