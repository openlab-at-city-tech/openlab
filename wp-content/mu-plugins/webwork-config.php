<?php

add_filter( 'webwork_server_site_id', function() {
	return get_current_blog_id();
} );

add_filter( 'webwork_client_site_base', function() {
        $base = get_blog_option( 1, 'home' );
        if ( 'CT staging' === ENV_TYPE ) {
                return trailingslashit( $base ) . 'webwork-playground';
        } else {
                return trailingslashit( $base ) . 'ol-webwork';
        }
} );

add_filter( 'webwork_server_site_base', function() {
        $base = get_blog_option( 1, 'home' );
        if ( 'CT staging' === ENV_TYPE ) {
                return trailingslashit( $base );
        } else {
                return trailingslashit( $base ) . 'ol-webwork';
        }
} );

/**
 * Update the associated group's last_activity when new content is posted.
 */
function openlab_webwork_bump_group_on_activity( $post_id ) {
        // We do this weird parsing because the request comes from the API,
        // and we need to maintain compat with openlabdev.org.
        $client_site_url = apply_filters( 'webwork_client_site_base', '' );
        $parts = parse_url( $client_site_url );
        $site = get_site_by_path( $parts['host'], $parts['path'] );
        if ( ! $site ) {
                return;
        }

        $group_id = openlab_get_group_id_by_blog_id( $site->blog_id );
        if ( ! $group_id ) {
                return;
        }

        groups_update_groupmeta( $group_id, 'last_activity', bp_core_current_time() );
}
add_action( 'save_post_webwork_question', 'openlab_webwork_bump_group_on_activity' );
add_action( 'save_post_webwork_response', 'openlab_webwork_bump_group_on_activity' );
