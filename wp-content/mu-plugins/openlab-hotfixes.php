<?php

/**
 * Flush user cache after BP manually updates password.
 */
add_action( 'bp_core_activated_user', function( $user_id ) {
        wp_cache_delete( $user_id, 'users' );
} );

function cac_set_return_path_header( $phpmailer ) {
       $phpmailer->Sender = 'wordpress@openlab.citytech.cuny.edu';
       return $phpmailer;
}
add_action( 'phpmailer_init', 'cac_set_return_path_header' );

/**
 * Fix for PDF embedding in Hypothesis.
 *
 * https://github.com/hypothesis/wp-hypothesis/pull/27/
 * http://redmine.citytech.cuny.edu/issues/2115
 */
function openlab_hypothesis_hotfix() {
	if ( ! function_exists( 'add_hypothesis' ) ) {
		return;
	}

	wp_enqueue_script( 'openlab-hypothesis', home_url( 'wp-content/mu-plugins/js/hypothesis.js' ), array(), '', true );
	$uploads = wp_upload_dir();
	wp_localize_script( 'openlab-hypothesis', 'HypothesisPDF', array(
		'uploadsBase' => trailingslashit( $uploads['baseurl'] ),
	) );
}
add_action( 'wp', 'openlab_hypothesis_hotfix', 20 );

/**
 * Load scripts for Fixed TOC fixes.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		if ( ! class_exists( 'Fixedtoc_Frontend_Control' ) ) {
			return;
		}
		wp_enqueue_script( 'openlab-fixed-toc', home_url( 'wp-content/mu-plugins/js/fixed-toc.js' ), array('jquery'), OL_VERSION, true );
	}
);
