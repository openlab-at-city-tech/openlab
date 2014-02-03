<?php
require_once (preg_replace("/wp-content.*/ism","wp-blog-header.php",__FILE__));
require_once (preg_replace("/wp-content.*/ism","/wp-admin/includes/admin.php",__FILE__));

// Force a happy header
header("HTTP/1.1 200 OK");

// Prevent some theoretical random stuff from happening
define('IFRAME_REQUEST', true);

// Set current screen to prevent error in wp_auth_check_load()
if(function_exists('set_current_screen')) {
	set_current_screen('gf-directory');
}

// Get the GF Styles
wp_enqueue_style('gf-admin', GFCommon::get_base_url() .'/css/admin.css');

// Get the WP Styles
register_admin_color_schemes();

// Get the GF Scripts (for the Lists functionality, etc)
wp_enqueue_script('gform_gravityforms', NULL, array('jquery'));

// Generate output for IFRAME
// @see http://codex.wordpress.org/Function_Reference/wp_iframe
wp_iframe('show_table');

/**
 * Generate the output for the IFRAME
 */
function show_table() {
	if(isset($_REQUEST['leadid']) && isset($_REQUEST['form'])) {

			require_once(dirname( __FILE__ )."/gravity-forms-addons.php");

			$transient = false;
			if(isset($_REQUEST['post'])) {
				$transient = get_transient('gf_form_'.$_REQUEST['form'].'_post_'.$_REQUEST['post'].'_showadminonly');
			}
			$output = '<style>html, body { margin:0; padding: 0!important; } div.wrap { padding:.25em .5em; }</style>';
			$output .= "<div class='wrap'>";

			$leadid = (int)$_REQUEST['leadid'];

			$lightbox = apply_filters('kws_gf_directory_showadminonly_lightbox', apply_filters('kws_gf_directory_showadminonly_lightbox_'.$_REQUEST['form'], $transient, $leadid), $leadid);

			$detail = GFDirectory::process_lead_detail(false, '', $lightbox, $leadid);

			$detail = apply_filters('kws_gf_directory_detail', apply_filters('kws_gf_directory_detail_'.$leadid, $detail, $leadid), $leadid);

			$output .= $detail."</div>";

			echo $output;
	}
}