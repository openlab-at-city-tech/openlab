<# if (map.query) { #>
	<div class="mapp-list-header">
		{{{count}}} <?php _e("Results", 'mappress-google-maps-for-wordpress'); ?>
	</div>
<# } #>

<# if (map.layout != 'inline') { #>
<div class='mapp-list-toggle' data-mapp-action='list-toggle'></div>
<# } #>

<div class="mapp-items">
	<# _.forEach(pois, function(poi, i) { #>
		<# if (!poi.visible) { return; } #>
		<div class="mapp-item {{ (map.poi==poi) ? 'mapp-selected' : ''}}" data-mapp-action="open" data-mapp-poi="{{{i}}}">
			<# print(poi.render('item')); #>
		</div>
	<# }); #>
</div>
<# if (map.layout || mappl10n.options.layout == 'left') { #>
<div class="mapp-list-footer">
</div>
<# } #>