<?php
/**
 * Controls the adding of styles.
 *
 * @package Genesis
 * @todo Document this file
 */

add_action('get_header', 'genesis_load_styles');
function genesis_load_styles() {

}

add_action('admin_init', 'genesis_load_admin_styles');
function genesis_load_admin_styles() {
	wp_enqueue_style('genesis_admin_css', GENESIS_CSS_URL.'/admin.css');
}