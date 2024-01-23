<?php
/**
 * REST API: Gutenberg_REST_Block_Patterns_Controller class
 *
 * @package    Gutenberg
 * @subpackage REST_API
 */

/**
 * Core class used to access block patterns via the REST API.
 *
 * @since 6.4.0
 *
 * @see WP_REST_Controller
 */
class Gutenberg_REST_Block_Patterns_Controller extends WP_REST_Block_Patterns_Controller {
	/**
	 * Prepare a raw block pattern before it gets output in a REST API response.
	 *
	 * @todo In the long run, we'd likely want to have a filter in the `WP_Block_Patterns_Registry` class
	 * instead to allow us plugging in code like this.
	 *
	 * @param array           $item    Raw pattern as registered, before any changes.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$response = parent::prepare_item_for_response( $item, $request );

		// Run the polyfill for Block Hooks only if it isn't already handled in WordPress core.
		if ( function_exists( 'traverse_and_serialize_blocks' ) ) {
			return $response;
		}

		$data = $response->get_data();

		if ( empty( $data['content'] ) ) {
			return $response;
		}

		$blocks          = parse_blocks( $data['content'] );
		$data['content'] = gutenberg_serialize_blocks( $blocks ); // Serialize or render?

		return rest_ensure_response( $data );
	}
}
