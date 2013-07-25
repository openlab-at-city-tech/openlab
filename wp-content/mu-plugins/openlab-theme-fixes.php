<?php

/**
 * Loads theme fixes for OpenLab site themes
 */

function openlab_load_theme_fixes() {
	$t = get_stylesheet();

	switch ( $t ) {
		case 'carrington-blog' :
		case 'coraline' :
		case 'wu-wei' :

			echo '<link rel="stylesheet" id="' . $t . '-fixes" type="text/css" media="screen" href="' . get_home_url() . '/wp-content/mu-plugins/theme-fixes/' . $t . '.css" />
';

			break;
	}
}
add_action( 'wp_print_styles', 'openlab_load_theme_fixes', 9999 );
