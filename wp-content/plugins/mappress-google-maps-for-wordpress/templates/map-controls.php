<div class='mapp-menu-toggle' data-mapp-action='menu-toggle' title='<?php _e('Menu', 'mappress-google-maps-for-wordpress');?>'></div>
<div class='mapp-menu'>
	<div class='mapp-menu-item' data-mapp-action='center'><?php _e('Center map', 'mappress-google-maps-for-wordpress');?></div>
	<# if (mappl10n.options.engine != 'leaflet') { #>
		<div class='mapp-menu-separator'></div>
		<div class='mapp-menu-item' data-mapp-action='layer' data-mapp-layer='traffic'><?php _e('Traffic', 'mappress-google-maps-for-wordpress');?></div>
		<div class='mapp-menu-item' data-mapp-action='layer' data-mapp-layer='bicycling'><?php _e('Bicycling', 'mappress-google-maps-for-wordpress');?></div>
		<div class='mapp-menu-item' data-mapp-action='layer' data-mapp-layer='transit'><?php _e('Transit', 'mappress-google-maps-for-wordpress');?></div>
	<# } #>
</div>
