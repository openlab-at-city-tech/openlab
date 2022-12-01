<?php
/**
 * The abstract class for module definition.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Loader
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.0.0
 */

namespace ThemeisleSDK\Common;

use ThemeisleSDK\Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Abstract_Module.
 *
 * @package ThemeisleSDK\Common
 */
abstract class Abstract_Module {
	/**
	 * Product which use the module.
	 *
	 * @var Product $product Product object.
	 */
	protected $product = null;

	/**
	 * Can load the module for the selected product.
	 *
	 * @param Product $product Product data.
	 *
	 * @return bool Should load module?
	 */
	abstract public function can_load( $product );

	/**
	 * Bootstrap the module.
	 *
	 * @param Product $product Product object.
	 */
	abstract public function load( $product );

	/**
	 * Check if the product is from partner.
	 *
	 * @param Product $product Product data.
	 *
	 * @return bool Is product from partner.
	 */
	public function is_from_partner( $product ) {

		foreach ( Module_Factory::$domains as $partner_domain ) {
			if ( strpos( $product->get_store_url(), $partner_domain ) !== false ) {
				return true;
			}
		}

		return array_key_exists( $product->get_slug(), Module_Factory::$slugs );
	}

	/**
	 * Wrapper for wp_remote_get on VIP environments.
	 *
	 * @param string $url Url to check.
	 * @param array  $args Option params.
	 *
	 * @return array|\WP_Error
	 */
	public function safe_get( $url, $args = array() ) {
		return function_exists( 'vip_safe_wp_remote_get' )
			? vip_safe_wp_remote_get( $url )
			: wp_remote_get( //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get, Already used.
				$url,
				$args
			);
	}
}
