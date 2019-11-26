<div class='mapp-tp-editor'>
	<div class='mapp-tp-master'>
		<?php echo Mappress_Controls::select('', $templates, 'map-tmpl-poi', array('class' => 'mapp-tp-select')); ?>
		<?php echo Mappress_Controls::button('', __('Edit', 'mappress-google-maps-for-wordpress'), array('class' => 'button button-primary', 'data-mapp-action' => 'edit')); ?>
	</div>
	<div class='mapp-tp-detail'></div>
</div>

<script type='text/html' id='mapp-tmpl-tp-master'>
	<div class='mapp-tp-master'>
		<select class='mapp-tp-select'>
		<# _.each(collection, function(template, name) { #>
			<option <# print( ( model && model.name == name) ? 'selected' : ''); #> value='{{name}}'>
				<# if (template.exists) print ('*'); #> {{template.label}}
			</option>
		<# }); #>
		</select>
		<input type='button' class='button button-primary' data-mapp-action='edit' value='<?php _e('Edit', 'mappress-google-maps-for-wordpress');?>'>

	</div>
</script>

<script type='text/html' id='mapp-tmpl-tp-detail'>
	<div class='mapp-tp-name'>{{ model.name }} <# if (!model.exists) { #>(<?php _e('New', 'mappress-google-maps-for-wordpress');?>)<# } else { #>(<?php _e('Custom', 'mappress-google-maps-for-wordpress');?>)<# } #></div>
	<div class='mapp-tp-path'>{{ model.path }}</div>
	<div class='mapp-tabs'>
		<div class='mapp-tab-label mapp-active' class='mapp-active'><?php _e('Editor', 'mappress-google-maps-for-wordpress');?></div>
		<div class='mapp-tab-label'><?php _e('Default', 'mappress-google-maps-for-wordpress');?></div>
		<div class='mapp-tab mapp-tab-content mapp-active'>
			<# _.each(tokens, function (token, i) { #>
				<code data-mapp-action='insert' data-mapp-insert='{{{i}}}' title='{{{i}}}'>{{{token}}}</code>
			<# }); #>
			<hr/>
			<textarea class='mapp-tp-content'>{{{ model.content }}}</textarea>
		</div>
		<div class='mapp-tab mapp-tab-standard'>
			<textarea readonly class='mapp-tp-standard'>{{{ model.standard }}}</textarea>
		</div>
	</div>
	<div class='mapp-tp-toolbar'>
		<input type='button' class='button button-primary' data-mapp-action='save' value='<?php _e('Save', 'mappress-google-maps-for-wordpress');?>'>
		<input type='button' class='button' data-mapp-action='cancel' value='<?php _e('Cancel', 'mappress-google-maps-for-wordpress');?>'>
		<# if (model.exists) { #>
			<a href='#' data-mapp-action='destroy'><?php _e('Delete', 'mappress-google-maps-for-wordpress');?></a>
		<# } #>
	</div>
</script>
