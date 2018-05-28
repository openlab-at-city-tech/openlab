<?php

add_filter( 'webwork_server_site_id', function() {
	return get_current_blog_id();
} );

add_filter( 'webwork_client_site_base', function() {
	return trailingslashit( get_option( 'home' ) );

        $base = get_blog_option( 1, 'home' );
        if ( 'CT staging' === ENV_TYPE ) {
                return trailingslashit( $base ) . 'webwork-playground';
        } else {
                return trailingslashit( $base ) . 'ol-webwork';
        }
} );

add_filter( 'webwork_server_site_base', function() {
	return trailingslashit( get_option( 'home' ) );

        $base = get_blog_option( 1, 'home' );
        if ( 'CT staging' === ENV_TYPE ) {
                return trailingslashit( $base );
        } else {
                return trailingslashit( $base ) . 'ol-webwork';
        }
} );

add_filter( 'webwork_section_instructor_map', function( $map ) {
	return array(
		'MAT1275-F17-Antoine' => 'wantoine@citytech.cuny.edu',
		'MAT1275-F17-Ferguson' => 'rferguson@citytech.cuny.edu',
		'MAT1275-F17-Mujica' => 'pmujica@citytech.cuny.edu',
		'MAT1275-F17-Poirier' => 'kpoirier@citytech.cuny.edu',
		'MAT1275-F17-Saha' => 'srsaha@citytech.cuny.edu',
		'MAT1275-F17-Sirelson' => 'vsirelson@citytech.cuny.edu',
		'MAT1275EN-F17-Carley' => 'hcarley@citytech.cuny.edu',
		'MAT1275EN-F17-Ganguli' => 'sganguli@citytech.cuny.edu',
		'MAT1275EN-F17-Mingla' => 'lmingla@citytech.cuny.edu',
		'MAT1275EN-F17-Parker' => 'kparker@citytech.cuny.edu',
		'MAT1275-F17-Zeng-2pm' => 'szeng@citytech.cuny.edu',
		'MAT1275-F17-Zeng-4pm' => 'szeng@citytech.cuny.edu',
		'MAT1275-F17-Batyr-8am' => 'obatyr@citytech.cuny.edu',
		'MAT1275-F17-Batyr-10am' => 'obatyr@citytech.cuny.edu',

		'MAT1275-S18-Antoine' => 'wantoine@citytech.cuny.edu',
		'MAT1275-S18-Ayoub' => 'tayoub@citytech.cuny.edu',
		'MAT1275-S18-Chan' => 'cchan@citytech.cuny.edu',
		'MAT1275-S18-Duvvuri' => 'vduvvuri@citytech.cuny.edu',
		'MAT1275-S18-Ferguson' => 'rferguson@citytech.cuny.edu',
		'MAT1275-S18-Ganguli' => 'sganguli@citytech.cuny.edu',
		'MAT1275-S18-Ghezzi' => 'lghezzi@citytech.cuny.edu',
		'MAT1275-S18-Lime' => 'mlime@citytech.cuny.edu',
		'MAT1275-S18-Mingla-10am' => 'lmingla@citytech.cuny.edu',
		'MAT1275-S18-Mingla-8am' => 'lmingla@citytech.cuny.edu',
		'MAT1275-S18-Ovshey' => 'novshey@citytech.cuny.edu',
		'MAT1275-S18-Poirier' => 'kpoirier@citytech.cuny.edu',
		'MAT1275-S18-Rafeek' => 'rrafeek@citytech.cuny.edu',
		'MAT1275-S18-Rahaman' => 'lrahaman@citytech.cuny.edu',
		'MAT1275-S18-Rozenblyum' => 'arozenblyum@citytech.cuny.edu',
		'MAT1275-S18-Sirelson' => 'vsirelson@citytech.cuny.edu',
		'MAT1275-S18-Yeeda' => 'vyeeda@citytech.cuny.edu',
		'MAT1275-S18-Yu' => 'dmyu@citytech.cuny.edu',
		'MAT1275-S18-Zapata-1pm' => 'gzapata@citytech.cuny.edu',
		'MAT1275-S18-Zapata-9am' => 'gzapata@citytech.cuny.edu',
		'MAT1275-S18-Zeng' => 'szeng@citytech.cuny.edu',
		'MAT1275EN-S18-Berglund' => 'rberglund@citytech.cuny.edu',
		'MAT1275EN-S18-Daouki' => 'sdaouki@citytech.cuny.edu',
		'MAT1275EN-S18-Kan-10am' => 'bkan@citytech.cuny.edu',
		'MAT1275EN-S18-Kan-8am' => 'bkan@citytech.cuny.edu',
		'MAT1275EN-S18-Parker' => 'kparker@citytech.cuny.edu',
	);
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
