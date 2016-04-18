<form action='#'>
	<div>
		<a href='#' class='mapp-travelmode mapp-travelmode-on' title='<?php esc_html_e('By car', 'mappress'); ?>'><span class='mapp-dir-icon mapp-dir-car'></span></a>
		<a href='#' class='mapp-travelmode' title='<?php esc_html_e('Public Transit', 'mappress'); ?>'><span class='mapp-dir-icon mapp-dir-transit'></span></a>
		<a href='#' class='mapp-travelmode' title='<?php esc_html_e('Walking', 'mappress'); ?>'><span class='mapp-dir-icon mapp-dir-walk'></span></a>
		<a href='#' class='mapp-travelmode' title='<?php esc_html_e('Bicycling', 'mappress'); ?>'><span class='mapp-dir-icon mapp-dir-bike'></span></a>
	</div>


	<div class='mapp-route'>
		<a href='#' class='mapp-myloc'><?php _e('My location', 'mappress'); ?></a>

		<div>
			<span class='mapp-dir-icon mapp-dir-a'></span>
			<input class='mapp-dir-saddr' tabindex='1'/>
			<a href='#' class='mapp-dir-swap'><span class='mapp-dir-icon mapp-dir-arrows' title='<?php _e ('Swap start and end', 'mappress'); ?>'></span></a>

		</div>
		<div class='mapp-dir-saddr-err'></div>

		<div>
			<span class='mapp-dir-icon mapp-dir-b'></span>
			<input class='mapp-dir-daddr' tabindex='2'/>
		</div>
		<div class='mapp-dir-daddr-err'></div>
	</div>

	<div style='margin-top: 10px;'>
		<input type='submit' class='mapp-dir-get' value='<?php esc_html_e('Get Directions', 'mappress'); ?>'/>
		<a href='#' class='mapp-dir-print'><?php _e('Print', 'mappress'); ?></a>
		&nbsp;<a href='#' class='mapp-dir-close'><?php _e('Close', 'mappress'); ?></a>
		<span class='mapp-spinner' style='display:none'></span>
	</div>
</form>

<div class='mapp-dir-renderer'></div>