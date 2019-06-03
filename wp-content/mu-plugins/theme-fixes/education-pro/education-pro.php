<?php

/**
 * Disable auto-update support for the theme.
 *
 * We manage the theme independently. This also prevents 'Updates' section from appearing
 * on the theme's Settings panel.
 */
remove_theme_support( 'genesis-auto-updates' );

/**
 * Remove unused Settings metaboxes.
 */
add_action(
	'load-toplevel_page_genesis',
	function() {
		remove_meta_box( 'genesis-theme-settings-adsense', get_current_screen(), 'main'  );
		remove_meta_box( 'genesis-theme-settings-scripts', get_current_screen(), 'main'  );
	},
	50
);
