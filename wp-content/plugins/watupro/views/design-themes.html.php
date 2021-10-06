<div class="wrap">
	<h1><?php _e('Manage Custom Design Themes', 'watupro');?></h1>
	
	<p><?php printf(__('You can create your own design themes and then apply them to all or selected %s. These will be available in addition to the built-in themes that come with WatuPRO.','watupro'), WATUPRO_QUIZ_WORD_PLURAL);?><br>
	<b><?php _e('You should copy the CSS from the default theme (watupro/css/themes/default.css) and add to it. Do not remove any CSS definitions unless you know what you are doing.', 'watupro');?></b></p>
	
	<p><a href="admin.php?page=watupro_design_themes&action=add"><?php _e('Create a new theme', 'watupro');?></a></p>
	
	<?php if(!count($themes)):?>
		<p><?php _e('You have not created any custom design themes.', 'watupro');?></p></div>
	<?php return false; 
	endif;?>
	
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('Theme name', 'watupro');?></th><th><?php _e('Edit', 'watupro');?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($themes as $theme):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr>
					<td><?php echo stripslashes($theme->name);?></td>
					<td><a href="admin.php?page=watupro_design_themes&action=edit&id=<?php echo $theme->ID?>"><?php _e('Edit', 'watupro');?></a></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>