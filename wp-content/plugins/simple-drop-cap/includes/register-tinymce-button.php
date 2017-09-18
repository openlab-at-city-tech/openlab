<?php
$wpsdc_options = get_option( 'wpsdc_options' );
if ( $wpsdc_options['option_enable_all_posts'] != '1' ) {
	// add drop cap tinymce button if WP version >= 3.9
	global $wp_version;
	if ( version_compare( $wp_version, '3.9', '>=' ) ) {
		// action for tinymce button
		add_action( 'admin_head', 'wpsdc_tinymce_button_init' );
	}


	// function for adding tinymce button
	function wpsdc_tinymce_button_init()
	{
		// check user permission
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG editor is enabled
		if ( get_user_option( 'rich_editing' ) == 'true' ) {

			// add filter to register tinymce plugins
			add_filter( 'mce_external_plugins', 'wpsdc_add_tinymce_plugin' );

			// add filter to add buttons to tinymce toolbar
			add_filter( 'mce_buttons', 'wpsdc_add_tinymce_button' );
		}
	}

	// function for mce_external_plugins callback
	function wpsdc_add_tinymce_plugin( $plugin_array )
	{
		// add js file into the editor
		$plugin_array['wpsdc_button'] = plugin_dir_url( __FILE__ ) . 'js/shortcode-button.js' ;
		return $plugin_array;
	}

	// function for mce_buttons callback
	function wpsdc_add_tinymce_button( $buttons )
	{
		// insert the button to the $buttons array
		array_push( $buttons, 'wpsdc_button' );
		return $buttons;
	}

	add_action( 'admin_enqueue_scripts', 'wpsdc_load_admin_sytles' );

	function wpsdc_load_admin_sytles()
	{
		wp_enqueue_style( 'wpsdc_admin', plugin_dir_url( WPSDC_PLUGIN_FILE ) . 'includes/css/admin-styles.css', array(), '1.2.7' );
	}
}