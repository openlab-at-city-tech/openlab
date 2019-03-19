<?php

/**
 * Hide update notification and update theme version
 *
 * @since  1.0
 */

add_action('wp_ajax_typology_update_version', 'typology_update_version');

if(!function_exists('typology_update_version')):
function typology_update_version(){
	update_option('typology_theme_version', TYPOLOGY_THEME_VERSION);
	die();
}
endif;


/**
 * Hide welcome notification
 *
 * @since  1.0
 */

add_action('wp_ajax_typology_hide_welcome', 'typology_hide_welcome');

if(!function_exists('typology_hide_welcome')):
function typology_hide_welcome(){
	update_option('typology_welcome_box_displayed', true);
	die();
}
endif;


?>