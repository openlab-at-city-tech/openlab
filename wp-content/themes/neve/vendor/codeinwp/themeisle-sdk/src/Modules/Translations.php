<?php
/**
 * The translations model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2024, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.23
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translations module for ThemeIsle SDK.
 */
class Translations extends Abstract_Module {

	const API_URL   = 'https://translations.themeisle.com/wp-json/gpb-themeisle/';
	const CACHE_KEY = 'ti_translations_data';

	/**
	 * Check if we should load module for this.
	 *
	 * @param Product $product Product to check.
	 *
	 * @return bool Should load ?
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}

		if ( $product->is_wordpress_available() ) {
			return false;
		}

		if ( ! $product->is_plugin() ) {
			return false;
		}

		return apply_filters( $product->get_slug() . '_sdk_enable_private_translations', false );
	}

	/**
	 * Load module logic.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Translations Module instance.
	 */
	public function load( $product ) {

		$this->product = $product;

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'add_translations' ], 11 );
		add_filter( 'http_request_host_is_external', [ $this, 'allow_translations_api' ], 10, 3 );

		return $this;
	}

	/**
	 * Allow external downloads for the translations API.
	 *
	 * @param bool   $external Whether the host is external.
	 * @param string $host The host being checked.
	 * @param string $url The URL being checked.
	 * @return bool
	 */
	public function allow_translations_api( $external, $host, $url ) {
		return strpos( $url, self::API_URL ) === 0 ? true : $external;
	}

	/**
	 * Get translations from API.
	 *
	 * @return bool | array
	 */
	private function get_api_translations() {
		$translation_data = $this->get_translation_data();

		return empty( $translation_data ) ? false : $translation_data;
	}

	/**
	 * Get translation data from API.
	 *
	 * @return array
	 */
	private function get_translation_data() {
		$cached = get_transient( self::CACHE_KEY );

		if ( $cached ) {
			return $cached;
		}

		$response = $this->safe_get(
			self::API_URL . 'translations',
			array(
				'timeout'   => 15, //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout, Inherited by wp_remote_get only, for vip environment we use defaults.
				'sslverify' => false,
			)
		);
		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return [];
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $data ) ) {
			return [];
		}

		set_transient( self::CACHE_KEY, $data, 12 * HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Append translations that are available via API.
	 *
	 * @param array $_transient_data The transient data.
	 *
	 * @return mixed
	 */
	public function add_translations( $_transient_data ) {
		$translations = $this->get_api_translations();

		if ( ! is_array( $translations ) ) {
			return $_transient_data;
		}

		if ( ! isset( $_transient_data->translations ) ) {
			return $_transient_data;
		}

		$installed_translations = wp_get_installed_translations( 'plugins' );

		foreach ( $translations as $translation ) {
			$translation = (array) $translation;

			if ( ! $this->is_valid_translation( $translation ) ) {
				continue;
			}

			$latest_translation = strtotime( $translation['updated'] );

			if ( ! is_int( $latest_translation ) ) {
				continue;
			}

			$existing        = (int) get_option( $this->get_translation_option_key( $translation ) );
			$has_translation = isset( $installed_translations[ $translation['slug'] ][ $translation['language'] ] );

			// If we already have the latest translation, skip.
			if ( $existing >= $latest_translation && $has_translation ) {
				continue;
			}

			$_transient_data->translations[] = $translation;

			update_option( $this->get_translation_option_key( $translation ), $latest_translation );
		}

		return $_transient_data;
	}

	/**
	 * Get the option key for storing translations.
	 *
	 * @param array $translation the translation data from the API.
	 *
	 * @return string
	 */
	private function get_translation_option_key( $translation ) {
		return $translation['slug'] . '_translation_' . $translation['language'];
	}

	/**
	 * Check if a translation is valid and applies for the current site.
	 *
	 * @param array $translation The translation data.
	 *
	 * @return bool
	 */
	private function is_valid_translation( $translation ) {
		$locales = apply_filters( 'plugins_update_check_locales', array_values( get_available_languages() ) );

		if ( ! is_array( $locales ) ) {
			return false;
		}
		$locales = array_unique( $locales );

		if ( ! isset( $translation['language'], $translation['slug'], $translation['updated'] ) ) {
			return false;
		}

		if ( ! in_array( $translation['language'], $locales, true ) ) {
			return false;
		}

		return true;
	}
}
