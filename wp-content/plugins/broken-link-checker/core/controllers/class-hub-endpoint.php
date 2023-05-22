<?php
/**
 * Hub endpoints controller.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Controllers
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Traits\Sanitize;

/**
 * Class Hub_Endpoint
 *
 * @package WPMUDEV_BLC\Core\Controllers
 */
abstract class Hub_Endpoint extends Base {
	use Sanitize;

	/**
	 * The name of the endpoint action.
	 *
	 * @var string
	 */
	protected $endpoint_action_name = '';

	/**
	 * The name of the endpoint action callback method.
	 *
	 * @var string
	 */
	protected $endpoint_action_callback = '';

	/**
	 * Init class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'wdp_register_hub_action', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Registers all endpoints for using Dash plugin's wdp_register_hub_action filter.
	 *
	 * @param array $actions An array of actions passed from wdp_register_hub_action filter.
	 *
	 * @return array
	 */
	public function register_endpoints( array $actions = array() ) {
		$endpoint_actions = \is_callable( array( $this, 'get_endpoint_actions' ) ) ? $this->get_endpoint_actions() : array();

		if ( ! empty( $endpoint_actions ) ) {
			$actions = wp_parse_args( $endpoint_actions, $actions );
		}

		return apply_filters( 'wpmudev_blc_hub_endpoints_actions', $actions, $this );
	}

	/**
	 * Formats the input into json response.
	 * When $success = false the input array needs to contain following keys:
	 * `code` with value string and default value ''
	 * `message` with value string and default value a generic error response message and
	 * `data` with mixed value and default value ''
	 *
	 * @param array $input An array that will be transformed to json. In case of error input array needs tocontain `code`, `message` and `data` indexes.
	 * @param bool  $success When true returns wp_send_json_success when false returns wp_send_json_error.
	 *
	 * @return void
	 */
	protected function output_formatted_response( array $input = array(), bool $success = true ) {
		if ( ! empty( $input ) ) {
			if ( $success ) {
				\wp_send_json_success( $input, 200 );
			} else {
				$input['code']    = $input['code'] ? sanitize_text_field( $input['code'] ) : 'BLC_ERROR';
				$input['message'] = $input['message'] ? \wp_kses_post( $input['message'] ) : \__esc_html( 'Something went wrong with this HTTP request', 'broken-link-checker' );
				$input['data']    = $input['data'] ? $this->sanitize_array( $input['data'] ) : '';

				\wp_send_json_error( new \WP_Error( $input['code'], $input['message'], $input['data'] ) );
			}
		}
	}

	/**
	 * Returns the endpoint's actions to be used by Dash plugin.
	 * 
	 * @return array.
	 */
	protected function get_endpoint_actions() {
		$this->setup_action_vars();

		if ( ! empty( $this->endpoint_action_name ) && ! empty( $this->endpoint_action_callback ) ) {
			return array(
				$this->endpoint_action_name => array( $this, $this->endpoint_action_callback ),
			);
		}
		
		return array();
	}

	/**
	 * Sets up the action variables.
	 */
	abstract protected function setup_action_vars();
}
