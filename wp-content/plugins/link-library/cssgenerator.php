<?php

function link_library_generate_css( $my_link_library_plugin ) {
	$options = '';
	$genoptions = '';

	$genoptions = get_option( 'LinkLibraryGeneral' );
	$genoptions = wp_parse_args( $genoptions, ll_reset_gen_settings( 'return' ) );

	header("Content-type: text/css");

	if ( isset( $genoptions['fullstylesheet'] ) ) {
		echo stripslashes( $genoptions['fullstylesheet'] );
	}

	for ( $i = 1; $i <= $genoptions['numberstylesets']; $i++ ) {
		$settingsid = intval( $i );

		$settingsname = 'LinkLibraryPP' . $settingsid;
		$options = get_option( $settingsname );
		$options = wp_parse_args( $options, ll_reset_options( 1, 'list', 'return' ) );

		if ( !empty( $options['stylesheet'] ) ) {
			echo stripslashes( $options['stylesheet'] ) . "\n";
		}
	}

	exit;
}
