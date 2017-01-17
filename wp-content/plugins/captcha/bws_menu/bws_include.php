<?php
/*
* Get latest version
*/

if ( ! function_exists ( 'bws_include_init' ) ) {
	function bws_include_init( $base, $bws_menu_source = 'plugins' ) {
		global $bstwbsftwppdtplgns_options, $bstwbsftwppdtplgns_added_menu;
		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
		$wp_plugins_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : $wp_content_dir . '/plugins';

		if ( $bws_menu_source == 'plugins' ) {
			$bws_menu_dir = $wp_plugins_dir . '/' .  dirname( $base ) . '/bws_menu/bws_menu.php';
		} else {
			$bws_menu_dir = $wp_content_dir . '/themes/' . $base . '/inc/bws_menu/bws_menu.php';
		}

		$bws_menu_info = get_plugin_data( $bws_menu_dir );
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
			$all_themes = wp_get_themes();

			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( array_key_exists( $key, $all_plugins ) || array_key_exists( $key, $all_themes ) ) {
					if ( $bws_menu_version < $value && ( is_plugin_active( $key ) || preg_match( '|' . $key . '$|', get_template_directory() ) ) ) {
						if ( ! isset( $product_with_newer_menu ) )
							$product_with_newer_menu = $key;
						elseif ( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $product_with_newer_menu ] < $bstwbsftwppdtplgns_options['bws_menu']['version'][ $key ] )
							$product_with_newer_menu = $key;
					}
				} else {
					unset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $key ] );
					if ( function_exists( 'is_multisite' ) && is_multisite() )
						update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
					else
						update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				}
			}

			if ( ! isset( $product_with_newer_menu ) )
				$product_with_newer_menu = $base;

			$folder_with_newer_menu = explode( '/', $product_with_newer_menu );

			if ( array_key_exists( $product_with_newer_menu, $all_plugins ) ) {
				$bws_menu_source = 'plugins';
				$bws_menu_new_dir = $wp_plugins_dir . '/' . $folder_with_newer_menu[0];
			} else if ( array_key_exists( $product_with_newer_menu, $all_themes ) ) {
				$bws_menu_source = 'themes';
				$bws_menu_new_dir = $wp_content_dir . '/themes/' . $folder_with_newer_menu[0] . '/inc';
			} else {
				$bws_menu_new_dir = '';
			}

			if ( file_exists( $bws_menu_new_dir . '/bws_menu/bws_functions.php' ) ) {
				require_once( $bws_menu_new_dir . '/bws_menu/bws_functions.php' );
				require_once( $bws_menu_new_dir . '/bws_menu/bws_menu.php' );
			} else {
				require_once( dirname( __FILE__ ) . '/bws_menu.php' );
				require_once( dirname( __FILE__ ) . '/bws_functions.php' );
			}

			$bstwbsftwppdtplgns_added_menu = true;
		}
	}
}