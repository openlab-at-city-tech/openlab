<?php
/**
 * Loader for the ThemeIsleSDK
 *
 * Logic for loading always the latest SDK from the installed themes/plugins.
 *
 * @package     ThemeIsleSDK
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
// Current SDK version and path.
$themeisle_sdk_version = '3.2.32';
$themeisle_sdk_path    = dirname( __FILE__ );

global $themeisle_sdk_max_version;
global $themeisle_sdk_max_path;

// If this is the latest SDK and it comes from a theme, we should load licenser separately.
$themeisle_sdk_relative_licenser_path = '/src/Modules/Licenser.php';

global $themeisle_sdk_abs_licenser_path;
if ( ! is_file( $themeisle_sdk_path . $themeisle_sdk_relative_licenser_path ) && ! empty( $themeisle_sdk_max_path ) && is_file( $themeisle_sdk_max_path . $themeisle_sdk_relative_licenser_path ) ) {
	$themeisle_sdk_abs_licenser_path = $themeisle_sdk_max_path . $themeisle_sdk_relative_licenser_path;
	add_filter( 'themeisle_sdk_required_files', 'themeisle_sdk_load_licenser_if_present' );
}

if ( ( is_null( $themeisle_sdk_max_path ) || version_compare( $themeisle_sdk_version, $themeisle_sdk_max_path ) == 0 ) &&
	apply_filters( 'themeisle_sdk_should_overwrite_path', false, $themeisle_sdk_path, $themeisle_sdk_max_path ) ) {
	$themeisle_sdk_max_path = $themeisle_sdk_path;
}

if ( is_null( $themeisle_sdk_max_version ) || version_compare( $themeisle_sdk_version, $themeisle_sdk_max_version ) > 0 ) {
	$themeisle_sdk_max_version = $themeisle_sdk_version;
	$themeisle_sdk_max_path    = $themeisle_sdk_path;
}

// load the latest sdk version from the active Themeisle products.
if ( ! function_exists( 'themeisle_sdk_load_licenser_if_present' ) ) :
	/**
	 * Always load the licenser, if present.
	 *
	 * @param array $to_load Previously files to load.
	 */
	function themeisle_sdk_load_licenser_if_present( $to_load ) {
		global $themeisle_sdk_abs_licenser_path;
		$to_load[] = $themeisle_sdk_abs_licenser_path;

		return $to_load;
	}
endif;

// load the latest sdk version from the active Themeisle products.
if ( ! function_exists( 'themeisle_sdk_load_latest' ) ) :
	/**
	 * Always load the latest sdk version.
	 */
	function themeisle_sdk_load_latest() {
		/**
		 * Don't load the library if we are on < 5.4.
		 */
		if ( version_compare( PHP_VERSION, '5.4.32', '<' ) ) {
			return;
		}
		global $themeisle_sdk_max_path;
		require_once $themeisle_sdk_max_path . '/start.php';
	}
endif;
add_action( 'init', 'themeisle_sdk_load_latest' );

if ( ! function_exists( 'tsdk_utmify' ) ) {
	/**
	 * Utmify a link.
	 *
	 * @param string $url URL to add utms.
	 * @param string $area Area in page where this is used ( CTA, image, section name).
	 * @param string $location Location, such as customizer, about page.
	 *
	 * @return string
	 */
	function tsdk_utmify( $url, $area, $location = null ) {
		static $current_page = null;
		if ( $location === null && $current_page === null ) {
			global $pagenow;
			$screen       = function_exists( 'get_current_screen' ) ? get_current_screen() : $pagenow;
			$current_page = isset( $screen->id ) ? $screen->id : ( ( $screen === null ) ? 'non-admin' : $screen );
			$current_page = sanitize_key( str_replace( '.php', '', $current_page ) );
		}
		$location = $location === null ? $current_page : $location;
		$content  = sanitize_key(
			trim(
				str_replace(
					[
						'https://',
						'themeisle.com',
						'/themes/',
						'/plugins/',
						'/upgrade',
					],
					'',
					$url 
				),
				'/' 
			) 
		);
		return esc_url_raw(
			add_query_arg(
				[
					'utm_source'   => 'wpadmin',
					'utm_medium'   => $location,
					'utm_campaign' => $area,
					'utm_content'  => $content,
				],
				$url 
			) 
		);
	}

	add_filter( 'tsdk_utmify', 'tsdk_utmify', 10, 3 );
}


if ( ! function_exists( 'tsdk_lstatus' ) ) {
	/**
	 * Check license status.
	 *
	 * @param string $file Product basefile.
	 *
	 * @return string Status.
	 */
	function tsdk_lstatus( $file ) {
		return \ThemeisleSDK\Modules\Licenser::status( $file );
	}
}
if ( ! function_exists( 'tsdk_lis_valid' ) ) {
	/**
	 * Check if license is valid.
	 *
	 * @param string $file Product basefile.
	 *
	 * @return bool Validness.
	 */
	function tsdk_lis_valid( $file ) {
		return \ThemeisleSDK\Modules\Licenser::is_valid( $file );
	}
}
if ( ! function_exists( 'tsdk_lplan' ) ) {
	/**
	 * Get license plan.
	 *
	 * @param string $file Product basefile.
	 *
	 * @return string Plan.
	 */
	function tsdk_lplan( $file ) {
		return \ThemeisleSDK\Modules\Licenser::plan( $file );
	}
}

if ( ! function_exists( 'tsdk_lkey' ) ) {
	/**
	 * Get license key.
	 *
	 * @param string $file Product basefile.
	 *
	 * @return string Key.
	 */
	function tsdk_lkey( $file ) {
		return \ThemeisleSDK\Modules\Licenser::key( $file );
	}
}
