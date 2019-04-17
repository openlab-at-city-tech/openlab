<?php

/**
 * Hide update notification and update theme version
 *
 * @since  1.0
 */

add_action( 'wp_ajax_johannes_update_version', 'johannes_update_version' );

if ( !function_exists( 'johannes_update_version' ) ):
	function johannes_update_version() {
		update_option( 'johannes_theme_version', JOHANNES_THEME_VERSION );
		wp_die();
	}
endif;


/**
 * Hide welcome notification
 *
 * @since  1.0
 */

add_action( 'wp_ajax_johannes_hide_welcome', 'johannes_hide_welcome' );

if ( !function_exists( 'johannes_hide_welcome' ) ):
	function johannes_hide_welcome() {
		update_option( 'johannes_welcome_box_displayed', true );
		wp_die();
	}
endif;


?>