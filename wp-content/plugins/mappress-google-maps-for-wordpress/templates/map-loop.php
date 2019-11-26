<# if (map.query) { #>
	<div class="mapp-list-header">
		{{{count}}} <?php _e("Results", 'mappress-google-maps-for-wordpress'); ?>
	</div>
<# } #>

<div class="mapp-items">
	<# _.forEach(pois, function(poi, i) { #>
		<# if (!poi.visible) { return; } #>
		<div class="mapp-item" data-mapp-action="open" data-mapp-poi="{{{i}}}">
			<# print(poi.render('item')); #>
		</div>
	<# }); #>
</div>