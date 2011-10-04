<?php
/**
 * Controls the adding of scripts.
 *
 * @package Genesis
 * @todo Document functions in load-scripts.php
 */

add_action('get_header', 'genesis_load_scripts');
/**
 * This function loads front-end JS files
 *
 */
function genesis_load_scripts() {
	if (is_singular() && get_option('thread_comments') && comments_open())
		wp_enqueue_script('comment-reply');

	// Load superfish and our common JS (in the footer, and only if necessary)
	if( genesis_get_option('nav_superfish') || genesis_get_option('subnav_superfish') ||
		is_active_widget(0,0, 'menu-categories') || is_active_widget(0,0, 'menu-pages') ) {

			wp_enqueue_script('superfish', GENESIS_JS_URL.'/menu/superfish.js', array('jquery'), '1.4.8', TRUE);
			wp_enqueue_script('superfish-args', GENESIS_JS_URL.'/menu/superfish.args.js', array('superfish'), PARENT_THEME_VERSION, TRUE);

	}
}

add_action('admin_init', 'genesis_load_admin_scripts');
/**
 * This function loads the admin JS files
 *
 */
function genesis_load_admin_scripts() {
	add_thickbox();
	wp_enqueue_script('theme-preview');
	wp_enqueue_script('genesis_admin_js', GENESIS_JS_URL.'/admin.js');
	$params = array(
            'category_checklist_toggle' => __( 'Select / Deselect All', 'genesis' )
	);
	wp_localize_script( 'genesis_admin_js', 'genesis', $params );
}