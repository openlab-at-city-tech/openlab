<?php

/**
 * Customizations for openlab-modules.
 */

/**
 * Tell openlab-modules to use our custom completion message type.
 */
add_filter(
	'openlab_modules_completion_message_type',
	function( $type ) {
		return 'openlab_email';
	}
);

/**
 * Process module completion of openlab_email type.
 */
add_action(
	'openlab_modules_section_complete',
	function( $post_id, $module_id, $message_type ) {
		if ( 'openlab_email' !== $message_type ) {
			return;
		}

		// Get current user info
		$user = wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			return;
		}

		$user_email = $user->user_email;
		$display_name = $user->display_name;

		// Get post information
		$post = get_post( $post_id );
		$is_module = ( $post_id === $module_id );

		// Create email subject
		$subject = sprintf(
			// translators: 1. Module title.
			__( 'Well done! You have completed a section of the module: %s', 'openlab-modules' ),
			get_the_title( $module_id )
		);

		// Build email content
		$module_info = sprintf(
			__( 'Module: %1$s %2$s', 'openlab-modules' ),
			get_the_title( $module_id ),
			get_permalink( $module_id )
		);

		$section_info = '';
		if ( ! $is_module ) {
			$section_info = sprintf(
				__( 'Section: %1$s %2$s', 'openlab-modules' ),
				get_the_title( $post ),
				get_permalink( $post )
			);
		}

		// Build the complete message
		$message = sprintf(
			'Hi %1$s,

	You have completed the following:
	%2$s
	%3$s
	Well done!',
			$display_name,
			$module_info,
			$section_info
		);

		// Set up headers for BCC
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Bcc: jreitz@citytech.cuny.edu'
		);

		// Send the email
		wp_mail( $user_email, $subject, $message, $headers );
	},
	10,
	3
);
