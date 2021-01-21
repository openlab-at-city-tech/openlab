<script type='text/template' id='mapp-tmpl-edit-loop'>
	<div class='mapp-list-toggle' data-mapp-action='list-toggle'></div>
	<div class='mapp-items'>
		<# _.forEach(pois, function(poi, i) { #>
			<div class='mapp-item' data-mapp-action='open' data-mapp-poi='{{{i}}}'><# print(poi.render('item')); #></div>
		<# }); #>
	</div>
</script>

<script type='text/template' id='mapp-tmpl-edit-item'>
	<img class="mapp-icon" src="{{{poi.icon}}}">
	<div class='mapp-title'>{{{poi.title}}}</div>
</script>

<script type='text/template' id='mapp-tmpl-edit-popup'>
	<div class='mapp-poi-header'>
		<input class='mapp-poi-title' type='text' value='{{poi.title}}'>
		<# if (!poi.type) { #>
			<img data-mapp-iconpicker data-mapp-iconid='{{{poi.iconid}}}' class='mapp-icon'>
		<# } else if (poi.isPoly()) { #>
			<# var colors = poi.getTemplateColors(); #>
			<span data-mapp-colorpicker class='mapp-colorpicker-toggle' data-mapp-color='{{{colors.color}}}' data-mapp-opacity='{{{colors.opacity}}}' data-mapp-weight='{{{colors.weight}}}' tabindex='0'></span>
		<# } #>
	</div>

	<div class='mapp-poi-editor-toolbar'>
		<# if (poi.type == 'kml') { #>
			<div class='mapp-poi-kml'>
				<input class='mapp-poi-url' type='text' readonly='readonly' value='<# print( (poi.kml) ? poi.kml.url : '' );#>'/>
			</div>
		<# } #>

		<div class='mapp-poi-editor-tabs'>
			<a class='mapp-poi-visual'><?php _e('Visual', 'mappress-google-maps-for-wordpress'); ?></a> | <a class='mapp-poi-html'><?php _e('HTML', 'mappress-google-maps-for-wordpress');?></a>
			</div>
			<a href='#' class='insert-media add_media' data-editor='mapp-poi-body'><?php _e('Add Media', 'mappress-google-maps-for-wordpress');?></a>
		</div>
	</div>

	<div class='mapp-poi-main'>
		<textarea id='mapp-poi-body' class='mapp-poi-body' rows='10'>{{ poi.body }}</textarea>
	</div>

	<div class='mapp-poi-toolbar'>
		<button data-mapp-poi='save' class='button button-primary'><?php _e('Save', 'mappress-google-maps-for-wordpress'); ?></button>
		<button data-mapp-poi='cancel' class='button'><?php _e('Cancel', 'mappress-google-maps-for-wordpress'); ?></button>
		<a href='#' data-mapp-poi='remove'><?php _e('Delete', 'mappress-google-maps-for-wordpress');?></a>
	</div>
</script>