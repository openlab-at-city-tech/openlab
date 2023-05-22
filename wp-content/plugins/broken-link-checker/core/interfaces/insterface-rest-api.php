<?php
/**
 * Interface for rest api requests.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Interfaces
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Interfaces;

// Abort if called directly.
defined( 'WPINC' ) || die;

interface Rest_Api {
	/**
	 * Init function.
	 *
	 * @since 2.0.0
	 *
	 */
	public function init();

	/**
	 * Registers rest api routes.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_routes();

	/**
	 * Formats the response.
	 *
	 * @since 2.0.0
	 *
	 * @param array           $item.
	 * @param WP_REST_Request $request request object.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request );

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 2.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema();
}