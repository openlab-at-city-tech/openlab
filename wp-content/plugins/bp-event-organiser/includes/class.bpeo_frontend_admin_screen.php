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
	}

	/**
	 * Enqueue additional scripts.
	 */
	protected function enqueue_scripts() {
		// remove EO's frontend Google Maps code and use admin version
		wp_deregister_script( 'eo_GoogleMap' );
		wp_register_script( 'eo_GoogleMap', '//maps.googleapis.com/maps/api/js?sensor=false&language=' . substr( get_locale(), 0, 2 ) );

		// manually queue up EO's scripts
		eventorganiser_register_scripts();
		eventorganiser_add_admin_scripts( 'post.php' );
	}
}
