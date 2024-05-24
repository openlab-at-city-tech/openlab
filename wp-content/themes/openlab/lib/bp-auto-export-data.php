<?php
/**
 * Auto-generate BP data export without admin intervention.
 *
 * Does require a mod to the 'request-confirmed' block of the /members/
 * single/settings/data.php template part.
 *
 * @link https://redmine.gc.cuny.edu/issues/8074
 */

namespace CAC\BPAutoExportData;

/**
 * Screen handler.
 */
add_action( 'bp_actions', function() {
	if ( ! bp_is_settings_component() && ! bp_is_current_action( 'data' ) ) {
		return;
	}

	// Enqueue JS.
	add_action( 'wp_enqueue_scripts', function() {
		wp_enqueue_script( 'cac-bp-auto-export-data', get_stylesheet_directory_uri() .  '/js/bp-auto-export-data.js', array( 'jquery' ) );
		wp_enqueue_style( 'cac-bp-auto-export-data', get_stylesheet_directory_uri() . '/css/bp-auto-export-data.css', array( 'dashicons' ) );
	} );

	// String changer.
	add_action( 'bp_before_member_settings_template', function() {
		add_filter( 'gettext', __NAMESPACE__ . '\\modify_strings', 10, 2 );
	}, 999 );

	// Remove string changer and add our custom field used for AJAX.
	add_action( 'bp_after_member_settings_template', function() {
		remove_filter( 'gettext', __NAMESPACE__ . '\\modify_strings', 10 );

		printf( '<input type="hidden" id="bp-auto-export" name="bp-auto-export" value="%s" />',
			wp_create_nonce( 'bp-auto-export-' . bp_displayed_user_id() )
		);
	}, 0 );
} );

/** FILTER HOOKS *********************************************************/

/**
 * Modify some strings used on the "Settings > Data" page.
 */
function modify_strings( $retval, $original ) {
	switch ( $original ) {
		case 'If you want to make a request, please click on the button below:' :
		case 'Please click on the button below to make a new request.' :
			return 'Please click on the button below and wait for the export to finish to obtain a copy of your data. If you navigate away from this page, you will have to restart the export process again.';
			break;

		case 'Request personal data export' :
			return 'Generate data export';
			break;

		default :
			return $retval;
			break;
	}
}

/**
 * Meta cap bypasser.
 */
function pass_check( $caps, $cap ) {
	if ( 'export_others_personal_data' !== $cap ) {
		return $caps;
	}

	return array( 'exist' );
}

/** AJAX HOOKS ***********************************************************/

/**
 * Generate a data request via AJAX.
 *
 * This is usually done in bp_settings_action_data(), but we need to do this
 * via AJAX so we can boot up WP's data export process on the frontend.
 */
add_action( 'wp_ajax_bp-data-export', function() {
	check_ajax_referer( 'bp-auto-export-' . bp_displayed_user_id(), 'security' );

	$request = bp_settings_get_personal_data_request();

	// We already have an existing request, but wasn't completed.
	if ( $request && 'request-confirmed' === $request->status ) {
		$request_id = $request->ID;

	// Create the user request.
	} else {
		$request_id = wp_create_user_request( buddypress()->displayed_user->userdata->user_email, 'export_personal_data' );

		if ( is_wp_error( $request_id ) ) {
			wp_send_json_error( $request_id->get_error_message() );
		} elseif ( ! $request_id ) {
			wp_send_json_error( 'We were unable to generate the data export request.' );
		}

		// This does the actual auto-confirmation for our data request.
		/** This hook is documented in /wp-login.php */
		do_action( 'user_request_action_confirmed', $request_id );
	}

	/** This filter is documented in wp-admin/includes/ajax-actions.php */
	$exporters = apply_filters( 'wp_privacy_personal_data_exporters', array() );

	// Send request ID, nonce + exporter count for our custom export generator.
	wp_send_json_success( array(
		'request_id'     => $request_id,
		'nonce'          => wp_create_nonce( 'wp-privacy-export-personal-data-' . $request_id ),
		'exporter_count' => count( $exporters )
	) );
} );

/**
 * Hook into data export process to override WP's meta cap restriction.
 */
add_action( 'wp_ajax_wp-privacy-export-personal-data', function() {
	if ( empty( $_POST ) || empty( $_POST['bpae'] ) ) {
		return;
	}

	// Check our custom nonce before proceeding.
	$verify = check_ajax_referer( 'bp-auto-export-' . bp_displayed_user_id(), 'bpae', false );
	if ( ! $verify ) {
		return;
	}

	add_filter( 'map_meta_cap', __NAMESPACE__ . '\\pass_check', 10, 2 );
}, -999 );

/**
 * Register our 'sites' exporter.
 */
add_filter(
	'wp_privacy_personal_data_exporters',
	function( $exporters ) {
		$exporters['openlab_sites'] = array(
			'exporter_friendly_name' => 'Sites',
			'callback'               => __NAMESPACE__ . '\\openlab_sites_exporter',
		);

		return $exporters;
	}
);

/**
 * Exporter for 'sites'.
 */
function openlab_sites_exporter( $email_address, $page = 1 ) {
	$user_id = email_exists( $email_address );
	if ( ! $user_id ) {
		return array(
			'data' => array(),
			'done' => true,
		);
	}

	$all_site_ids = get_blogs_of_user( $user_id );

	// We'll export 5 sites at a time.
	$batch_size = 5;
	$site_ids   = array_slice( $all_site_ids, ( $page - 1 ) * $batch_size, $batch_size, true );

	// If the plugin is not active on secondary sites, we'll get SQL errors.
	remove_filter( 'terms_clauses', 'TO_apply_order_filter', 10 );

	$data_to_export = [];
	foreach ( $site_ids as $site_id => $site ) {
		switch_to_blog( $site_id );

		$user_posts = get_posts( array(
			'author'         => $user_id,
			'posts_per_page' => -1,
		) );

		foreach ( $user_posts as $post ) {
			$post_data = [
				[
					'name' => 'Post Title',
					'value' => $post->post_title,
				],
				[
					'name' => 'Post URL',
					'value' => get_permalink( $post->ID ),
				],
				[
					'name' => 'Post Content',
					'value' => $post->post_content,
				],
			];

			$data_to_export[] = array(
				'group_id'    => 'openlab_site_' . $site_id,
				'group_label' => sprintf( 'Site Data: %s (%s)', $site->blogname, $site->siteurl ),
				'item_id'     => 'openlab_sites_' . $site_id . '_post_' . $post->ID,
				'data'        => $post_data,
			);
		}

		restore_current_blog();
	}

	add_filter( 'terms_clauses', 'TO_apply_order_filter', 10, 3 );

	return array(
		'data' => $data_to_export,
		'done' => count( $site_ids ) < $batch_size,
	);
}
