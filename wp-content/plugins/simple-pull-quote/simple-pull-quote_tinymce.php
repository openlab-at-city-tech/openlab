<?php
$wc_ct = new SPQ_Tinymce();
// init process for button control
add_action('init', array($wc_ct, 'spq_addbuttons'));

class SPQ_Tinymce {

	function spq_addbuttons() {
		// Don't bother doing this stuff if the current user lacks permissions
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;

		// Add only in Rich Editor mode
		if (get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array($this, 'add_spq_tinymce_plugin'));
			add_filter('mce_buttons', array($this, 'register_spq_button'));
		}
	}
	
	//Add the TinyMCE Button
	function register_spq_button($buttons) {
		array_push($buttons, "separator", "spq");
		return $buttons;
	}

	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_spq_tinymce_plugin($plugin_array) {
		$plugin_array['spq'] = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)). '/tinymce/editor_plugin.js';
		return $plugin_array;
	}

}