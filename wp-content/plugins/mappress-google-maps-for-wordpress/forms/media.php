<div class='mapp-media'>
	<div class='mapp-media-header'>
		<?php echo Mappress::get_support_links(); ?>
	</div>
	<div class='mapp-media-list-panel'>
		<div class='mapp-media-list-toolbar'>
			<input data-mapp-media='add' class='button button-primary' type='button' value='<?php esc_attr_e('New Map', 'mappress-google-maps-for-wordpress')?>' />
			<select class='mapp-media-list-type'>
				<option value='post'><?php _e('This post', 'mappress-google-maps-for-wordpress');?></option>
				<option value='all'><?php _e('All posts', 'mappress-google-maps-for-wordpress');?></option>
			</select>
			<input type='text' size="15" class='mapp-media-search' placeholder='<?php _e('Filter by title', 'mappress-google-maps-for-wordpress');?>'>
			<span class='spinner'></span>
		</div>
		<div class='mapp-list mapp-media-list'></div>
	</div>

	<div class='mapp-media-edit-panel'>
		<table class='mapp-settings'>
			<tr>
				<td><?php _e('Map ID', 'mappress-google-maps-for-wordpress');?>:</td>
				<td><span class='mapp-media-mapid'></span></td>
			</tr>

			<tr>
				<td><?php _e('Map Title', 'mappress-google-maps-for-wordpress');?>:</td>
				<td><input class='mapp-media-title' type='text' placeholder='<?php _e('Untitled', 'mappress-google-maps-for-wordpress');?>' /></td>
			</tr>

			<tr>
				<td><?php _e('Display Size', 'mappress-google-maps-for-wordpress');?>:</td>
				<td>
					<?php
						$sizes = array();
						foreach(Mappress::$options->sizes as $i => $size)
							$sizes[] = "<a href='#' class='mapp-media-size' data-width='{$size['width']}' data-height='{$size['height']}'>" . $size['width'] . 'x' . $size['height'] . "</a>";
						echo implode(' | ', $sizes);
					?>
					<input type='text' class='mapp-media-width' size='2' value='' /> x <input type='text' class='mapp-media-height' size='2' value='' />
				</td>
			</tr>

			<tr>
				<td><?php _e('Save center / zoom', 'mappress-google-maps-for-wordpress');?></td>
				<td><input type='checkbox' class='mapp-media-viewport'></td>
			</tr>
		</table>
		<div class='mapp-media-edit-toolbar'>
			<input data-mapp-media='save' class='button button-primary' type='button' value='<?php esc_attr_e('Save', 'mappress-google-maps-for-wordpress'); ?>' />
			<input data-mapp-media='cancel' class='button' type='button' value='<?php esc_attr_e('Cancel', 'mappress-google-maps-for-wordpress'); ?>' />
			<input data-mapp-media='insert' class='button' type='button' value='<?php esc_attr_e('Insert into post', 'mappress-google-maps-for-wordpress'); ?>' />
		</div>
		<?php require Mappress::$basedir . "/forms/editor.php"; ?>
	</div>
</div>

<script type='text/template' id='mapp-tmpl-media-list'>
	<div class='mapp-items'>
		<# _.forEach(items, function(item, i) { #>
			<div class='mapp-item' data-mapp-media-list='edit' data-mapp-mapid='{{ item.mapid }}'>

				<# if (type == 'all') { #>
					<# if (item.post_title) { #>{{ item.post_title }}<# } else { #><?php _e('Untitled', 'mappress-google-maps-for-wordpress');?><# } #>
					&nbsp;-&nbsp;
				<# } #>

				<# if (item.map_title) { #>{{ item.map_title }}<# } else { #><?php _e('Untitled', 'mappress-google-maps-for-wordpress');?><# } #>

				<div class='mapp-actions'>
					<a href='#' data-mapp-media-list='edit'><?php _e('Edit', 'mappress-google-maps-for-wordpress');?></a> |&nbsp;
					<a href='#' data-mapp-media-list='insert'><?php _e('Insert into post', 'mappress-google-maps-for-wordpress');?></a> |&nbsp;
					<a href='#' data-mapp-media-list='remove'><?php _e('Delete', 'mappress-google-maps-for-wordpress');?></a>
				</div>
			</div>
		<# }); #>
	</div>
	<div class='mapp-list-footer'>
		<# if (items.length == 0) { #>
			<?php _e('No maps found', 'mappress-google-maps-for-wordpress');?>
		<# } #>
		{{{more}}}
	</div>
</div>
</script>