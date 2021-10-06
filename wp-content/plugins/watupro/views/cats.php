<style type="text/css">
<?php watupro_resp_table_css(600);?>
</style>

<div class="wrap watupro-wrap">
	<h1><?php printf(__('Watu PRO %s Categories', 'watupro'), ucfirst(WATUPRO_QUIZ_WORD))?></h1>
	
	<?php if(!empty($parent->ID)):?>
		<h2><?php printf(__('Managing Subcategories of "%s"', 'watupro'), stripslashes($parent->name));?></h2>
		<p><a href="admin.php?page=watupro_cats"><?php _e('Back to manage main categories', 'watupro');?></a></p>
	<?php endif;?>

	<p><?php printf(__('Categories can be used to organize your %s by topic. The most useful part of this is that you can limit the access to categories for the different', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?> <a href="admin.php?page=watupro_groups"><?php _e('user groups', 'watupro')?></a>.</p>

	<p><a href="admin.php?page=watupro_cats&do=add&parent_id=<?php echo $parent_id?>"><?php _e('Click here to add new category', 'watupro')?></a></p>
	
	<?php if(count($cats)):?>
		<table class="widefat watupro-table">
			<thead>		        
				<tr><th><?php _e('ID', 'watupro')?></th><th><?php _e('Category Name', 'watupro')?></th><th><?php printf(__('Shortcode for %s list', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></th>
				<?php if(empty($parent->ID)):?>
					<th><?php _e('Subcategories', 'watupro');?></th>
				<?php endif;?>
				<th><?php _e('Accessible to', 'watupro');?></th><th><?php _e('Edit', 'watupro')?></th></tr>
		</thead>
		<tbody>
			<?php foreach($cats as $cat):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>">
				<td><?php echo $cat->ID?></td>
				<td><?php echo stripslashes($cat->name);?></a></td>
				<td><input type="text" value="[watuprolist cat_id=<?php echo $cat->ID?>]" onclick="this.select();" readonly="readonly"></td>	
				<?php if(empty($parent->ID)):?>
					<td><a href="admin.php?page=watupro_cats&parent_id=<?php echo $cat->ID?>"><?php _e('Manage', 'watupro');?></a></td>
				<?php endif;?>
				<td><?php echo $cat->allowed_to;?></td>	
				<td><a href="admin.php?page=watupro_cats&do=edit&id=<?php echo $cat->ID?>&parent_id=<?php echo $parent_id?>"><?php _e('Edit', 'watupro')?></a></td></tr>
			<?php endforeach;?>
		</tbody>
		</table>
	<?php else:?>
    <p><?php _e('You have not created any categories yet.', 'watupro')?> <a href="admin.php?page=watupro_cats&do=add&parent_id=<?php echo $parent_id?>"><?php _e('Click here', 'watupro')?></a> <?php _e('to create one.', 'watupro')?></p>
	<?php endif;?>
	
	<h2><?php printf(__('Shortcodes to list published %s', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></h2>
	
	<p><b><?php printf(__('The shortcodes will list only %1$s %2$s. This means that you have to publish each individual %3$s and only then it will appear in the list.', 'watupro'), '<span style="color:red;">'.__('published', 'watupro').'</span>', WATUPRO_QUIZ_WORD_PLURAL, WATUPRO_QUIZ_WORD);?></b></p>
	
	<p><?php printf(__('To list all published %s in the system you can use the shortcode', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?> <input type="text" value='[watuprolist cat_id="ALL"]' onclick="this.select();" readonly="readonly"> </p>
	<p><?php printf(__('To list all published uncategorized %s you can use the shortcode', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?> <input type="text" value='[watuprolist cat_id=0]' onclick="this.select();" readonly="readonly"></p>
	<p><?php printf(__('To list %s from only one category use the shortcodes given in the table above.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
	<p><?php printf(__('To list %s from multiple categories with one shortcode just separate their IDs with commas, like this:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?> <input type="text" value='[watuprolist cat_id="1,2"]' onclick="this.select();" readonly="readonly"></p>
	
	<p><?php printf(__('To include %s from subcategories add the attribute "include_subcats=1" to the shortcode. Example:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?> <input type="text" value='[watuprolist cat_id=2 include_subcats=1]' onclick="this.select();" readonly="readonly" size="30"></p>	
	
	<p><?php printf(__('You can use the same logic to limit the %s shown in user dashboard. Just add category ID(s) to the shortcode like this:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?> <b>[WATUPRO-MYEXAMS 1]</b> or <b>[WATUPRO-MYEXAMS 2,3,5]</b> <?php _e('To sort them by title, or latest on top, add "title" or "latest" to the shortcode like this:', 'watupro')?> <b>[WATUPRO-MYEXAMS 2,3,5 title]</b></p>
	<p><?php printf(__('These shortcodes also accept third argument that allows you to specify the order of listing %s:', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL);?></p>
	
	<ol>
		<li><b>title</b> <?php _e('to order them by title, alphabetically. Example:', 'watupro')?> <b>[WATUPROLIST ALL title]</b></li>
		<li><b>latest</b> <?php _e('to order the most recent on top. Example:', 'watupro')?> <b>[WATUPROLIST 1 latest]</b></li>
	</ol>
	
	<p><?php printf(__('By default all shortcodes list the %s sorted in the order of creation, oldest on top.', 'watupro'), WATUPRO_QUIZ_WORD_PLURAL)?></p>
</div>

<script type="text/javascript">
<?php watupro_resp_table_js();?>
</script>