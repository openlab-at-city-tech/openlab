<?php
/*
* Get latest version
*/

if ( ! function_exists ( 'bws_include_init' ) ) {
	function bws_include_init( $base ) {
		global $bstwbsftwppdtplgns_options, $bstwbsftwppdtplgns_added_menu;
		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$bws_menu_info = get_plugin_data( dirname( dirname( plugin_dir_path( __FILE__ ) ) ) . '/' . dirname( $base ) . '/bws_menu/bws_menu.php' );
		$bws_menu_version = $bws_menu_info["Version"];

		if ( ! isset( $bstwbsftwppdtplgns_options ) ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', array() );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', array() );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}
		}

		if ( isset( $bstwbsftwppdtplgns_options['bws_menu_version'] ) ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			unset( $bstwbsftwppdtplgns_options['bws_menu_version'] );
			if ( function_exists( 'is_multisite' ) && is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			require_once( dirname( __FILE__ ) . '/bws_menu.php' );
			require_once( dirname( __FILE__ ) . '/bws_functions.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] ) || $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] < $bws_menu_version ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			if ( function_exists( 'is_multisite' ) && is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			require_once( dirname( __FILE__ ) . '/bws_menu.php' );
			require_once( dirname( __FILE__ ) . '/bws_functions.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_added_menu ) ) {
			$all_plugins = get_plugins();
			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( array_key_exists( $key, $all_plugins ) ) {
					if ( $bws_menu_version < $value && is_plugin_active( $base ) ) {
						if ( ! isset( $plugin_with_newer_menu ) )
							$plugin_with_newer_menu = $key;
						elseif ( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $plugin_with_newer_menu ] < $bstwbsftwppdtplgns_options['bws_menu']['version'][ $key ] )
							$plugin_with_newer_menu = $key;
					}
				} else {
					unset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $key ] );
					if ( function_exists( 'is_multisite' ) && is_multisite() )
						update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
					else
						update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				}
			}
			if ( ! isset( $plugin_with_newer_menu ) )
				$plugin_with_newer_menu = $base;
			$plugin_with_newer_menu = explode( '/', $plugin_with_newer_menu );
			$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? basename( WP_CONTENT_DIR ) : 'wp-content';

			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' ) ) {
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' );
			} else {
				require_once( dirname( __FILE__ ) . '/bws_menu.php' );
			}

			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_functions.php' ) ) {
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_functions.php' );
			} else {
				require_once( dirname( __FILE__ ) . '/bws_functions.php' );
			}

			$bstwbsftwppdtplgns_added_menu = true;
		}		
	}
}