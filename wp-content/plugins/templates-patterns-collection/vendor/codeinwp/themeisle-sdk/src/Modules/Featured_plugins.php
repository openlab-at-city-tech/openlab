<?php
/**
 * File responsible for showing plugins inside the featured tab.
 *
 * This is used to display information about limited events, such as Black Friday.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.0
 */
namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Featured_Plugins module for the ThemeIsle SDK.
 */
class Featured_Plugins extends Abstract_Module {

	/**
	 * The transient key prefix.
	 *
	 * @var string $transient_key
	 */
	private $transient_key = 'themeisle_sdk_featured_plugins_';

	/**
	 * Check if the module can be loaded.
	 *
	 * @param Product $product Product data.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}

		if ( $product->is_wordpress_available() ) {
			return false;
		}

		return ! apply_filters( 'themeisle_sdk_disable_featured_plugins', false );
	}

	/**
	 * Load the module for the selected product.
	 *
	 * @param Product $product Product data.
	 *
	 * @return void
	 */
	public function load( $product ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// bail if we already registered a filter for the plugin API.
		if ( apply_filters( 'themeisle_sdk_plugin_api_filter_registered', false ) ) {
			return;
		}
		add_filter( 'themeisle_sdk_plugin_api_filter_registered', '__return_true' );

		add_filter( 'plugins_api_result', [ $this, 'filter_plugin_api_results' ], 10, 3 );
	}

	/**
	 * Filter the plugin API results to include the featured plugins.
	 *
	 * @param object $res    The result object.
	 * @param string $action The type of information being requested from the Plugin Install API.
	 * @param object $args   Plugin API arguments.
	 *
	 * @return object
	 */
	public function filter_plugin_api_results( $res, $action, $args ) {

		if ( 'query_plugins' !== $action ) {
			return $res;
		}

		if ( ! isset( $args->browse ) || $args->browse !== 'featured' ) {
			return $res;
		}

		$featured = $this->query_plugins_by_author( $args );

		$plugins      = array_merge( $featured, (array) $res->plugins );
		$plugins      = array_slice( $plugins, 0, $res->info['results'] );
		$res->plugins = $plugins;

		return $res;
	}

	/**
	 * Query plugins by author.
	 *
	 * @param object $args The arguments for the query.
	 *
	 * @return array
	 */
	private function query_plugins_by_author( $args ) {
		$featured = [];

		$optimole_filter_slugs  = apply_filters( 'themeisle_sdk_optimole_filter_slugs', [ 'optimole-wp' ] );
		$filtered_from_optimole = $this->get_plugins_filtered_from_author( $args, $optimole_filter_slugs, 'Optimole' );
		$featured               = array_merge( $featured, $filtered_from_optimole );

		$themeisle_filter_slugs  = apply_filters( 'themeisle_sdk_themeisle_filter_slugs', [ 'otter-blocks' ] );
		$filtered_from_themeisle = $this->get_plugins_filtered_from_author( $args, $themeisle_filter_slugs );
		$featured                = array_merge( $featured, $filtered_from_themeisle );

		return $featured;
	}

	/**
	 * Get plugins filtered from an author.
	 *
	 * @param object $args          The arguments for the query.
	 * @param array  $filter_slugs  The slugs to filter.
	 * @param string $author        The author to filter.
	 *
	 * @return array
	 */
	private function get_plugins_filtered_from_author( $args, $filter_slugs = [], $author = 'Themeisle' ) {

		$cached = get_transient( $this->transient_key . $author );
		if ( $cached ) {
			return $cached;
		}

		$new_args = [
			'page'       => 1,
			'per_page'   => 36,
			'locale'     => get_user_locale(),
			'author'     => $author,
			'wp_version' => isset( $args->wp_version ) ? $args->wp_version : get_bloginfo( 'version' ),
		];

		$api = plugins_api( 'query_plugins', $new_args );
		if ( is_wp_error( $api ) ) {
			return [];
		}

		$filtered = array_filter(
			$api->plugins,
			function( $plugin ) use ( $filter_slugs ) {
				$array_plugin = (array) $plugin;
				return in_array( $array_plugin['slug'], $filter_slugs );
			}
		);

		set_transient( $this->transient_key . $author, $filtered, 12 * HOUR_IN_SECONDS );

		return $filtered;
	}
}
