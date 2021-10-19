<div class="wrap watupro-wrap">
	<h1><?php _e('Add/Edit Custom Design Theme', 'watupro');?></h1>
	
	<p><?php _e('The theme should contain only CSS and nothing else - no opening and closing CSS tags. You can use some of the existing themes in watupro/css/themes folder as a guide.', 'watupro');?></p>
	
	<p><a href="admin.php?page=watupro_design_themes"><?php _e('Back to themes', 'watupro');?></a></p>
	
	<form method="post" onsubmit="return watuproValidateForm(this);">
		<p><label><?php _e('Theme name:', 'watupro');?></label> <?php if(empty($theme->ID)):?><input type="text" size="30" name="name" value="<?php echo empty($theme->ID) ? '' : stripslashes($theme->name);?>"> <?php _e('(Only letters, numbers and underscore)', 'watupro'); else:?><b><?php echo stripslashes($theme->name);?></b><?php endif;?></p>
		<p><?php _e('CSS code of the theme:', 'watupro');?><br>
		<textarea rows="20" cols="100" name="css"><?php echo empty($theme->ID) ? '' : stripslashes($theme->css)?></textarea></p>
		<p>
			<input type="submit" name="ok" class="button-primary" value="<?php _e('Save theme','watupro');?>">
			<?php if(!empty($theme->ID)):?>
				<input type="button" class="button" value="<?php _e('Delete', 'watupro');?>" onclick="confirmDelTheme(this.form);">
				<input type="hidden" name="del" value="0">
			<?php endif;?>
		</p>
		<?php wp_nonce_field('watupro_themes');?>
	</form>
</div>

<script type="text/javascript" >
function watuproValidateForm(frm) {
	if(frm.name.value == '') {
		alert("<?php _e('Please enter theme name', 'watupro');?>");
		frm.name.focus();
		return false;
	}
	
	return true;
}

function confirmDelTheme(frm) {
	if(confirm("<?php _e('Are you sure?', 'watupro')?>")) {
		frm.del.value=1;
		frm.submit();
	}
}
</script>