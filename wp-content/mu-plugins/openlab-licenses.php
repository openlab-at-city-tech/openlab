<?php

add_filter( 'site_option_gwp_settings', function( $setting ) {
	if ( is_array( $setting ) && empty( $setting['license_key'] ) && defined( 'GRAVITYPERKS_LICENSE_KEY' ) ) {
		$setting['license_key'] = GRAVITYPERKS_LICENSE_KEY;
	}

	return $setting;
} );
