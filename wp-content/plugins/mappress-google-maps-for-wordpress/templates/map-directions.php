<div class='mapp-directions'>
	<span class='mapp-close' data-mapp-action='dir-cancel'></span>
	<?php if (Mappress::is_ssl()) : ?>
		<a href='#' class='mapp-myloc' data-mapp-action='dir-myloc'><?php _e('My location', 'mappress-google-maps-for-wordpress'); ?></a>
	<?php endif; ?>
	<div>
		<input class='mapp-dir-addr mapp-dir-saddr' tabindex='1'/>
		<span data-mapp-action='dir-swap' class='mapp-dir-arrows'></span>
	</div>

	<div>
		<input class='mapp-dir-addr mapp-dir-daddr' tabindex='2'/>
	</div>

	<div class='mapp-dir-toolbar'>
		<span class='mapp-button-submit' data-mapp-action='dir-get'><?php esc_html_e('Get Directions', 'mappress-google-maps-for-wordpress'); ?></span>
		<span class='mapp-spinner'></span>
	</div>
	<div class='mapp-dir-renderer'></div>
</div>