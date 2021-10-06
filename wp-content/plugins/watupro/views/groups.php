<div class="wrap watupro-wrap">
	<h1><?php _e("Watu PRO User Groups", 'watupro')?></h1>

	<p><?php _e('User groups are optional. They help you to organize your students or users in different classes. Then if you wish you can leave some of your', 'watupro')?> <a href="admin.php?page=watupro_cats"><?php printf(__('%s categories', 'watupro'), WATUPRO_QUIZ_WORD)?></a> <?php printf(__('accessible to only some user groups and thus diversify the %s delivered to your users.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
	
	<p><a href="https://blog.calendarscripts.info/user-groups-in-watupro/" target="_blank"><?php _e('Learn everything about the user groups here', 'watupro');?></a></p>
	
	<form method="post">
	<p><input type="checkbox" name="use_wp_roles" value='1' <?php if(!empty($use_wp_roles)) echo 'checked';?> onclick="this.form.submit();"> <?php _e('Use the WordPress User Roles instead of creating user groups (recommended for better integration with other plugins).', 'watupro');?></p>
	<input type="hidden" name="roles_to_groups" value="1">
	</form>
	
	<div id="watuproUserGroups" style="display:<?php echo empty($use_wp_roles)?'block':'none;'?>">

		<form method="post" onsubmit="return validateGroup(this);">
		<p class="watupro-wrap">
			<label><?php _e('Group name:', 'watupro')?> </label> <input type="text" name="name"> 
			<input type="checkbox" name="is_def" value="1"> <?php _e('Assign by default', 'watupro')?>		
			<input type="submit" name="add" value="<?php _e('Add Group', 'watupro')?>" class="button-primary">
		</p>
		</form>
		
		<?php foreach($groups as $group):?>
			<form method="post" onsubmit="return validateGroup(this);">
			<p class="watupro-wrap">
				<label><?php _e('ID:', 'watupro');?> <?php echo $group->ID?></label>
				|
				<label><?php _e('Group name:', 'watupro')?> </label> <input type="text" name="name" value="<?php echo $group->name?>"> 
				<input type="checkbox" name="is_def" value="1" <?php if($group->is_def) echo "checked"?>> <?php _e('Assign by default', 'watupro')?>					
				<input type="submit" name="save" value="<?php _e('Save', 'watupro')?>" class="button-primary">
				<input type="button" value="<?php _e('Delete', 'watupro')?>" onclick="confirmDelGroup(this.form);" class="button">			
				&nbsp;
				<a href="admin.php?page=watupro_group_assign&group_id=<?php echo $group->ID?>"><?php _e('Mass assign users', 'watupro');?></a>
			</p>
			<input type="hidden" name="del" value="0">
			<input type="hidden" name="id" value="<?php echo $group->ID?>">
			</form>
		<?php endforeach;?>
		
		<p class="note"><?php _e('If "Assign by default" is checked, the group will be assigned to every user who registers in your blog.', 'watupro')?></p>
		<form method="post">
		<p><input type="checkbox" name="select_group_on_signup" value="1" <?php if(get_option('watupro_select_group_on_signup')==1) echo 'checked'?>> <?php _e('Allow users to select user group on the registration page', 'watupro')?>
			<input type="submit" name="signup_options" value="<?php _e('Save', 'watupro');?>" class="button-primary">
		</p>
		</form>
	</div>
</div>

<script type="text/javascript" >
function validateGroup(frm)
{
	if(frm.name.value=="")
	{
		alert("<?php _e('Please enter group name', 'watupro')?>");
		frm.name.focus();
		return false;
	}
}

function confirmDelGroup(frm)
{
	if(confirm("<?php _e('Are you sure?', 'watupro')?>"))
	{
		frm.del.value=1;
		frm.submit();
	}
}
</script>