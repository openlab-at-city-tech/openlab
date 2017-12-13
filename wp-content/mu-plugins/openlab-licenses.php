<?php

function openlab_gravityperks_license( $setting ) {
	if ( is_admin() ) {
		GravityPerks::flush_license();
	}

	if ( ! is_array( $setting ) ) {
		$setting = array();
	}

	if ( empty( $setting['license_key'] ) && defined( 'GRAVITYPERKS_LICENSE_KEY' ) ) {
		$setting['license_key'] = GRAVITYPERKS_LICENSE_KEY;
	}

	return $setting;
}
add_filter( 'site_option_gwp_settings', 'openlab_gravityperks_license' );
add_filter( 'default_site_option_gwp_settings', 'openlab_gravityperks_license' );


