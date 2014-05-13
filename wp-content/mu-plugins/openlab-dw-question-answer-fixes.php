<?php

/**
 * Force a permalink examination and flush on certain events
 *
 * The native flush_rewrite_rules() is not working properly for some reason.
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/1070#change-8267
 */
function openlab_dwqa_flush( $new_status, $old_status, $post ) {
	if ( ! function_exists( 'dwqa_plugin_init' ) ) {
		return;
	}

	if ( 0 !== strpos( $post->post_type, 'dwqa-' ) ) {
		return;
	}

	global $wp_rewrite;

	$flush = false;
	$current_rules = $wp_rewrite->wp_rewrite_rules();
	foreach ( $wp_rewrite->extra_rules_top as $ert_k => $ert_v ) {
		if ( ! isset( $current_rules[ $ert_k ] ) ) {
			$flush = true;
			break;
		}
	}

	flush_rewrite_rules();
}
add_action( 'transition_post_status', 'openlab_dwqa_flush', 10, 3 );
