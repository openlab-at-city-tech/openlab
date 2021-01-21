<script type='text/template' id='mapp-tmpl-mce'>
	<div class='mapp-mce'>
		<div class='mapp-mce-list-panel mapp-open'>
			<div class='mapp-mce-header'>
				<div class='mapp-mce-header-left'>
					<h1><?php _e('Select a map', 'mappress-google-maps-for-wordpress');?></h1>
					<input data-mapp-mce='add' class='button' type='button' value='<?php esc_attr_e('Add New', 'mappress-google-maps-for-wordpress')?>' />
				</div>
				<?php echo Mappress::get_support_links(); ?>
			</div>
			<div class='mapp-mce-filter-block'>
				<div class='mapp-mce-search-block'>
					<?php _e('Search', 'mappress-google-maps-for-wordpress'); ?>
					<input type='text' size="15" class='mapp-mce-search' placeholder='<?php _e('post title', 'mappress-google-maps-for-wordpress');?>'>
					<span class='spinner'></span>
				</div>
				<div class='mapp-mce-types-block'>
					<label><input type='radio' name='mapp-mce-list-type' checked value='post'><?php _e('Attached to this post', 'mappress-google-maps-for-wordpress');?></label>
					<label><input type='radio' name='mapp-mce-list-type' value='all'><?php _e('All maps', 'mappress-google-maps-for-wordpress');?></label>
				</div>
			</div>
			<div class='mapp-mce-list'></div>
			<div class='mapp-mce-toolbar'>
				<input data-mapp-mce='close' class='button' type='button' value='<?php esc_attr_e('Close', 'mappress-google-maps-for-wordpress'); ?>' />
			</div>
		</div>

		<div class='mapp-mce-edit-panel'>
			<div class='mapp-mce-header'>
				<input class='mapp-mce-title' type='text' placeholder='<?php _e('Untitled', 'mappress-google-maps-for-wordpress');?>' />
				<?php echo Mappress::get_support_links(); ?>
			</div>
			<div class='mapp-mce-settings'>
				<div class='mapp-mce-setting'>
					<div class='mapp-mce-label'><?php _e('Size', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-mce-sizes'>
						<?php
							$sizes = array();
							foreach(Mappress::$options->sizes as $i => $size)
								$sizes[] = "<a href='#' class='mapp-mce-size' data-width='{$size['width']}' data-height='{$size['height']}'>" . $size['width'] . 'x' . $size['height'] . "</a>";
							echo implode(' | ', $sizes);
						?>
					</div>
					<div class='mapp-mce-custom'>
						<input type='text' class='mapp-mce-width' size='2' value='' /> x <input type='text' class='mapp-mce-height' size='2' value='' />
					</div>
				</div>
				<div class='mapp-mce-setting'>
					<div class='mapp-mce-label'><?php _e('Map ID', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-mce-mapid'></div>
				</div>
			</div>
			<div class='mapp-edit'></div>
			<div class='mapp-mce-toolbar'>
				<input data-mapp-mce='save' class='button button-primary' type='button' value='<?php esc_attr_e('Save', 'mappress-google-maps-for-wordpress'); ?>' />
				<input data-mapp-mce='cancel' class='button' type='button' value='<?php esc_attr_e('Cancel', 'mappress-google-maps-for-wordpress'); ?>' />
				<label class='mapp-mce-viewport-block'>
					<input type='checkbox' class='mapp-mce-viewport'>
					<?php _e('Save center / zoom', 'mappress-google-maps-for-wordpress');?>
				</label>
			</div>
		</div>
	</div>
</script>

<script type='text/template' id='mapp-tmpl-mce-list'>
	<div class='mapp-mce-list-header'>
		<div class='mapp-mce-col-map-title'><?php _e('Map title', 'mappress-google-maps-for-wordpress');?></div>
		<div class='mapp-mce-col-mapid'><?php _e('Map ID', 'mappress-google-maps-for-wordpress');?></div>
		<# if (type == 'all') { #>
			<div class='mapp-mce-col-post-title'><?php _e('Attached to', 'mappress-google-maps-for-wordpress');?></div>
		<# } #>
		<div class='mapp-mce-col-actions'><?php _e('Action', 'mappress-google-maps-for-wordpress');?></div>
	</div>
	<div class='mapp-mce-items'>
		<# _.forEach(items, function(item, i) { #>
			<# var className = (item.mapid == selected) ? 'mapp-mce-item mapp-selected' : 'mapp-mce-item'; #>
			<div class='{{className}}' data-mapp-mce-list='edit' data-mapp-mapid='{{ item.mapid }}'>
				<div class='mapp-mce-col-map-title'>
					<# if (item.map_title) { #>{{ item.map_title }}<# } else { #><?php _e('Untitled', 'mappress-google-maps-for-wordpress');?><# } #>
				</div>

				<div class='mapp-mce-col-mapid'>{{item.mapid}}</div>

				<# if (type == 'all') {
					var postTitle = (item.post_title) ? item.post_title : '<?php _e('Untitled', 'mappress-google-maps-for-wordpress');?>';
					postTitle = (item.postid == mappl10n.options.postid) ? '<?php _e('Current post', 'mappress-google-maps-for-wordpress');?>' : postTitle;
					var link = '';
					if (item.postid > 0) {
						link = (item.postid == mappl10n.options.postid) ? postTitle : '<a href="' + mappl10n.options.editurl + '?post=' + item.postid + '&action=edit" target="_blank">' + postTitle + '</a>';
					} else {
						link = '<?php _e('Unattached', 'mappress-google-maps-for-wordpress');?>';
					}
				#>
					<div class='mapp-mce-col-post-title'>{{{link}}}</div>
				<# } #>

				<div class='mapp-mce-col-actions'>
					<a href='#' data-mapp-mce-list='insert'><?php _e('Insert into post', 'mappress-google-maps-for-wordpress');?></a> |&nbsp;
					<a href='#' data-mapp-mce-list='remove'><?php _e('Delete', 'mappress-google-maps-for-wordpress');?></a>
				</div>
			</div>
		<# }); #>
	</div>
	<div class='mapp-mce-list-footer'>
		<# if (items.length == 0) { #>
			<?php _e('No maps found', 'mappress-google-maps-for-wordpress');?>
		<# } #>
	</div>
</div>
</script>