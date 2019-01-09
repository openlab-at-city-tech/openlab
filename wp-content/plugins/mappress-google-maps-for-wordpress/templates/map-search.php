<div class='mapp-search'>
	<input class='mapp-places' type='text' placeholder='<?php _e('Search', 'mappress-google-maps-for-wordpress'); ?>'/>
	<?php if (Mappress::ssl()) : ?>
		<div class='mapp-geolocate' data-mapp-action='geolocate' title='<?php _e('Your Location', 'mappress-google-maps-for-wordpress');?>'></div>
	<?php endif; ?>
</div>
