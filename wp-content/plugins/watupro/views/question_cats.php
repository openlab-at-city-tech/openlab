<div class="wrap watupro-wrap">
	<?php if(empty($_GET['parent_id'])):?>
		<h1><?php _e('Manage Question Categories', 'watupro')?></h1>
	<?php else:?>
		<h1><?php printf(__('Manage Subcategories of Category "%s"', 'watupro'), stripslashes($parent->name));?></h1>
	<?php endif;?>
	
	<?php if(!empty($error)):?>
	<div class="watupro-error"><?php echo $error?></div>
	<?php endif;?>

	<p><?php _e('Question categories are optional. You can use them to organize large tests, to group and paginate the questions by category etc. They can also be added on the fly at the time of adding or editing a question.', 'watupro')?></a>.</p>
	
	<p><strong><?php _e('Category description is optional.', 'watupro')?></strong> <?php _e('If provided it will be shown when the questions in the test are <strong>grouped</strong> by category', 'watupro')?></p>
	
	<?php if(empty($_GET['parent_id'])):?>
		<p><a href="admin.php?page=watupro_question_cats&do=add"><?php _e('Create new question category', 'watupro')?></a></p>
	<?php else:?>
		<p><a href="admin.php?page=watupro_question_cats&do=add&parent_id=<?php echo $_GET['parent_id']?>"><?php _e('Create new subcategory', 'watupro')?></a>
		| <a href="admin.php?page=watupro_question_cats"><?php _e('Back to main categories', 'watupro');?></a></p>
		
		<p><?php _e('Subcategories are useful for organizational purposes. They are not different than main categories: if you are grouping questions by category, or pulling random questions per category, subcategories will be treated equally with the main categories.', 'watupro');?></p>
	<?php endif;?>	
	
	<?php if(count($cats)):?>
		<table class="widefat">
			<tr><th><a href="admin.php?page=watupro_question_cats&ob=ID&dir=<?php echo $odir?>"><?php _e('ID', 'watupro')?></a></th><th><a href="admin.php?page=watupro_question_cats&ob=name&dir=<?php echo $odir?>"><?php _e('Category name and description', 'watupro')?></a></th>
			<?php if(empty($_GET['parent_id'])):?>
				<th><?php _e('Manage Subcategories', 'watupro');?></th>
			<?php endif;?>			
			<th><?php _e('Edit/Delete', 'watupro')?></th></tr>
			<?php foreach($cats as $cat):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>"><td><?php echo $cat->ID?></td>
				<td><h3><?php echo stripslashes(apply_filters('watupro_qtranslate', $cat->name));?></h3>
				<?php echo stripslashes(apply_filters('watupro_qtranslate', $cat->description));?></td>
				<?php if(empty($_GET['parent_id'])):?>
					<td><a href="admin.php?page=watupro_question_cats&parent_id=<?php echo $cat->ID?>"><?php printf(__('Manage (%d)', 'watupro'), $cat->num_subs);?></a></td>
				<?php endif;?>
				<td><a href="admin.php?page=watupro_question_cats&do=edit&id=<?php echo $cat->ID?>&parent_id=<?php echo @$_GET['parent_id']?>"><?php _e('Edit', 'watupro')?></a>
				| <a href="#" onclick="watuproConfirmDelete(<?php echo $cat->ID?>);return false;"><?php _e('Delete', 'watupro')?></a></td></tr>
			<?php endforeach;?>
		</table>
	<?php endif;?>	
</div>

<script type="text/javascript" >
function watuproConfirmDelete(id) {
   if(confirm("<?php _e('Are you sure? All questions that use the category will be now uncategorized.', 'watupro')?>")) {
   		window.location = 'admin.php?page=watupro_question_cats&del=1&parent_id=<?php echo @$_GET['parent_id']?>&id='+id;
   }
}
</script>