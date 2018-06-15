<?php

require BPEO_PATH . '/includes/wp-frontend-admin-screen/wp-frontend-admin-screen.php';

/**
 * Magic class to get the WP admin post interface working with BPEO.
 */
class BPEO_Frontend_Admin_Screen extends WP_Frontend_Admin_Screen {
	/**
	 * Our post type.
	 *
	 * @var string
	 */
	public static $post_type = 'event';

	/**
	 * String setter method.
	 */
	protected function strings() {
		return array(
			'created' => __( 'Event successfully created', 'bp-event-organiser' ),
			'updated' => __( 'Event updated', 'bp-event-organiser' ),
			'title_placeholder' => __( 'Enter Event Title', 'bp-event-organiser' ),
			'media_insert_into_post' => __( 'Insert into event', 'bp-event-organiser' )
		);
	}

	/**
	 * Do stuff before the screen is rendered.
	 */
	protected function before_screen() {
		// load up EO's edit functions
		require EVENT_ORGANISER_DIR . 'event-organiser-edit.php';
	}

	/**
	 * Do stuff before saving.
	 */
	protected function before_save() {
		// Initialize the EO errors global.
		global $EO_Errors;
		if ( ! is_wp_error( $EO_Errors ) ) {
			$EO_Errors = new WP_Error();
		}

		// add EO save hook
		add_action( 'save_post', 'eventorganiser_details_save' );
	}

	/**
	 * Do stuff before displaying the edit interface.
	 */
	protected function before_display() {
		// load up EO's metabox
		if ( function_exists( 'eventorganiser_edit_init' ) ) {
			eventorganiser_edit_init();
		}

		// Shortcake - Remove 'Add Post Element' button.
		if ( class_exists( 'Shortcode_UI', false ) && is_callable( array( Shortcode_UI::get_instance(), 'action_media_buttons' ) ) ) {
			remove_action( 'media_buttons', array( Shortcode_UI::get_instance(), 'action_media_buttons' ) );
		}
	}

	/**
	 * Enqueue additional scripts.
	 */
	protected function enqueue_scripts() {
		$version = defined( 'EVENT_ORGANISER_VER' ) ? EVENT_ORGANISER_VER : false;
		$ext = '';
		$rtl = is_rtl() ? '-rtl' : '';

		// remove EO's frontend Google Maps code and use admin version
		wp_deregister_script( 'eo_GoogleMap' );
		wp_register_script( 'eo_GoogleMap', '//maps.googleapis.com/maps/api/js?sensor=false&language=' . substr( get_locale(), 0, 2 ) );

		// register our custom timepicker
		wp_deregister_script( 'eo-time-picker' );
		wp_deregister_script( 'eo-timepicker' );

		/*
		 * jquery-timepicker
		 *
		 * @license MIT
		 * @link    https://github.com/jonthornton/jquery-timepicker
		 */
		wp_register_script( 'eo-timepicker', BUDDYPRESS_EVENT_ORGANISER_URL . "assets/js/jquery.timepicker{$ext}.js", array(
			'jquery',
		), $version, true );

		/*
		 * Datepair.js
		 *
		 * @license MIT
		 * @link    https://github.com/jonthornton/Datepair.js
		 */
		wp_register_script( 'eo-datepair', BUDDYPRESS_EVENT_ORGANISER_URL . "assets/js/datepair{$ext}.js", array(), $version, true );
		wp_register_script( 'eo-jquery-datepair', BUDDYPRESS_EVENT_ORGANISER_URL . "assets/js/jquery.datepair{$ext}.js", array(
			'eo-datepair',
			'jquery',
		), $version, true );

		// Timepicker CSS
		wp_register_style( 'eo-timepicker', BUDDYPRESS_EVENT_ORGANISER_URL . "assets/css/jquery.timepicker{$ext}.css", array(), $version );

		// Deregister eo_event and add our custom version.
		// @see https://github.com/stephenharris/Event-Organiser/pull/308
		wp_deregister_script( 'eo_event' );
		wp_register_script( 'eo_event', BUDDYPRESS_EVENT_ORGANISER_URL . "assets/js/event{$ext}.js",array(
			'jquery',
			'jquery-ui-datepicker',
			'eo-timepicker',
			'eo-jquery-datepair',
			'eo-venue-util',
			'jquery-ui-autocomplete',
			'jquery-ui-widget',
			'jquery-ui-button',
			'jquery-ui-position',
		),$version,true);

		/* Admin styling */
		wp_deregister_style( 'eventorganiser-style' );
		wp_register_style( 'eventorganiser-style', EVENT_ORGANISER_URL."css/eventorganiser-admin-style{$rtl}{$ext}.css", array(
			'eventorganiser-jquery-ui-style',
			'eo-timepicker'
		), $version );

		// Manually queue up EO's scripts
		eventorganiser_register_scripts();
		eventorganiser_add_admin_scripts( 'post.php' );
	}
}
