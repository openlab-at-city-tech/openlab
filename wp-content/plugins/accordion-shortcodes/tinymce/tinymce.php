<?php

// Make sure to not redeclare the class
if (!class_exists('Accordion_Shortcode_Tinymce_Extensions')) :

class Accordion_Shortcode_Tinymce_Extensions {

	/**
	 * Class constructor
	 * Adds the button hooks when the admin panel initializes.
	 */
	function __construct() {
		add_action('admin_init', array($this, 'button_hooks'));
		add_action('admin_head', array($this, 'admin_head'));
	}



	/**
	 * Load the plugin and register the buttons
	 */
	public function button_hooks() {
		if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
			add_filter('mce_external_plugins', array($this, 'add_tinymce_plugin'));
			add_filter('mce_buttons', array($this, 'register_buttons'));
		}
	}



	/**
	 * Register the accordion shortcodes buttons plugin
	 */
	public function add_tinymce_plugin($plugin_array) {
		$plugin_array['accordionShortcodesExtensions'] = plugins_url('accordion-shortcodes/tinymce/tinymce-plugin.js');

		return $plugin_array;
	}



	/**
	 * Register the accordion shortcode buttons
	 */
	public function register_buttons($buttons) {
		$newButtons = array(
			'AccordionShortcode',
			'AccordionItemShortcode'
		);

		// Place the buttons before the "insert more" button
		array_splice($buttons, 12, 0, $newButtons);

		return $buttons;
	}



	/**
	 * Localize MCE buttons and labels
	 */
	public function admin_head() {
		if (defined('AS_COMPATIBILITY') && AS_COMPATIBILITY) {
			$prefix = 'as-';
		}
		else {
			$prefix = '';
		} ?>

		<script type="text/javascript">
			var accordionShortcodesPrefix = '<?php echo $prefix; ?>';
		</script>
	<?php }

}

endif;
