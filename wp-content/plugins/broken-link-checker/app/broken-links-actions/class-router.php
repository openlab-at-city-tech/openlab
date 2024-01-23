<?php
/**
 * Parent class to be used by hub endpoints that handle link actions.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Links_Queue\Model as Queue;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Scheduled_Events\Edit_Links\Controller as Schedule;
use WPMUDEV_BLC\Core\Traits\Sanitize;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Hub_Endpoint\Edit_Link
 */
class Router extends Base {
	/**
	 * Use Sanitize trait.
	 */
	use Sanitize;

	/**
	 * Directs the endpoint according to params.
	 *
	 * @param string|null $action
	 *
	 * @return array
	 */
	public function direct_endpoint( string $action = null ) {
		if ( ! empty( Settings::instance()->get( 'use_legacy_blc_version' ) ) ) {
			$response['error_code'] = 'blc_local_mode';
			$response['message']    = sprintf(
				//translators: %1$s: Open a tag  %2$s: Close a tag.
				esc_html__( 'BLC plugin is set to Local Engine and can not perform any Cloud Engine action. Please make sure plugin is set to %1$sCloud Engine%2$s', 'broken-link-checker' ) ,
				'<a href="' .  admin_url( 'admin.php?page=blc_dash' ) . '">',
				'</a>'
			);

			return $response;
		}
		/**
		 * Input format:
		 * links => array(
		 *      {LINK_KEY_1} => array(
		 *          link => {LINK_URL},
		 *          new_link => {NEW_LINK_URL} OR unlink => true OR nofollow => true,
		 *          full_site => Bool (Default false. Value set by UI option if origins count >= FIXED_LIMIT),
		 *          origins => array() (list of origins),
		 *      ),
		 *      {LINK_KEY_2} => array(
		 *          link => {LINK_URL_2},
		 *          new_link => {NEW_LINK_URL_2} OR unlink => true OR nofollow => true,
		 *          full_site => Bool (Default false),
		 *          origins => array(),
		 *      ),
		 * )
		 */
		$input_json     = file_get_contents( 'php://input' );
		$use_subsite    = false;
		$prepared_input = $this->prepare_input( $input_json, $action );
		$response       = array(
			'data_received'   => true,
			'completed'       => false,
			'links_processed' => null,
			'total_links'     => null,
			'remaining_links' => null,
		);

		if ( is_wp_error( $prepared_input ) ) {
			$response['error_code'] = $prepared_input->get_error_code();
			$response['message']    = $prepared_input->get_error_message();

			return $response;
		} else if ( ! is_array( $prepared_input ) ) {
			//$response['success']    = false;
			$response['error_code'] = 'blc_unknown_error';
			$response['message']    = esc_html__( 'Something went wrong', 'broken-link-checker' );

			return $response;
		}

		// At this point we have checked if it multisite and if site id is valid in `normalize_data()`;
		if ( ! empty( $prepared_input['site_id'] ) ) {
			$use_subsite = true;

			switch_to_blog( $prepared_input['site_id'] );
		}

		if ( ! empty( $action ) ) {
			if ( ! empty( $prepared_input['links'] ) ) {
				foreach ( $prepared_input['links'] as $key => $link ) {
					$prepared_input['links'][$key][$action] = 'true';
				}
			}
		}

		$result = $this->push_to_queue( $prepared_input );

		if ( $use_subsite ) {
			restore_current_blog();
		}

		if ( is_wp_error( $result ) ) {
			$response['error_code'] = $result->get_error_code();
			$response['message']    = $result->get_error_message();

			return $response;
		}

		return wp_parse_args( $result, $response );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed|array|WP_Error
	 */
	public function push_to_queue( array $data = array() ) {
		if ( empty( $data['links'] ) ) {
			return array(
				'success'    => false,
				'completed'  => false,
				'error_code' => 'blc_link_execution_error',
				'message'    => esc_html__( 'Empty link', 'broken-link-checker' ),
			);
		}

		$first_link = Queue::instance()->queue_is_empty();

		Queue::instance()->push( $data );
		// Run schedule in 5 seconds after pushing.
		//Schedule::instance()->setup( 5 );

		// If Queue is empty, run first link immediately and schedule the rest links.
		if ( $first_link ) {
			Schedule::instance()->process_scheduled_event();
		} else {
			Schedule::instance()->setup( 5 );
		}

		return array(
			'success'         => true,
			'completed'       => false,
			'links_processed' => Queue::instance()->links_processed(),
			'total_links'     => Queue::instance()->total_links(),
		);

	}

	/**
	 * Prepares input json to be used for editing links.
	 */
	public function prepare_input( string $json_data = '', string $action = '' ) {
		$schema           = $this->get_sanitize_schema( $action );
		$normalized_input = $this->normalize_data( $json_data, $schema );

		if ( is_wp_error( $normalized_input ) ) {
			return $normalized_input;
		}

		$links = $normalized_input['links'] ?? null;

		if ( empty( $links ) ) {
			return new \WP_Error(
				'blc-link-action-no-links',
				esc_html__(
					'Aborting as there are no links',
					'broken-link-checker'
				)
			);
		}

		return $normalized_input;
	}

	/**
	 * Provides the schema that the requested input should have in order to be escaped properly.
	 *
	 * @return string[]
	 */
	public function get_sanitize_schema( string $action = '' ) {
		$schema = array(
			//'site_id'     => 'int',
			'link'      => 'url',
			//'target_tags' => 'attr',
			'origins'   => 'url',
			'full_site' => 'bool',
		);

		if ( 'edit' === $action ) {
			$schema['new_link'] = 'url';
		}

		return apply_filters(
			'wpmudev_blc_link_actions_sanitization_schema',
			$schema,
			$action,
			$this
		);
	}

	/**
	 * Ensures that requested input is present and sanitizes.
	 *
	 * @param string $json_data
	 * @param array $schema
	 *
	 * @return array|\WP_Error
	 */
	public function normalize_data( string $json_data = '', array $schema = array() ) {
		if ( empty( $json_data ) || empty( $schema ) ) {
			Utilities::log( 'BLC_LINK_ACTION_ERROR - Missing input conversion params.' );

			return new \WP_Error(
				'blc-link-action-failed',
				esc_html__(
					'Missing input conversion params',
					'broken-link-checker'
				)
			);
		}

		$raw_input = json_decode( $json_data, true );

		// Make sure input is valid json.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			Utilities::log( 'BLC_LINK_ACTION_ERROR - Exiting because input was not valid json string.' );

			return new \WP_Error(
				'blc-link-action-failed',
				esc_html__(
					'Invalid input json string',
					'broken-link-checker'
				)
			);
		}

		$input_params = $raw_input['params'];
		$input_links  = $input_params['links'] ?? array();
		$params       = array();

		// Check if there is any missing param.
		if ( ! empty( $input_links ) ) {
			foreach ( $input_links as $input_link_key => $input_link ) {
				foreach ( $schema as $schema_key => $schema_scheme ) {

					if ( ! isset( $input_link[ $schema_key ] ) ) {
						return new \WP_Error(
							'blc-link-action-normalization-error',
							sprintf(
								//translators: %1$s: The keyname of the schema that will be used to sanitize.
								esc_html__(
									'Links params do not follow schema. Key `%1$s` missing',
									'broken-link-checker'
								),
								$schema_key
							)
						);
					}

					if ( 'bool' === $schema_scheme ) {
						$input_link[ $schema_key ] = boolval( $input_link[ $schema_key ] );
					}

					$params['links'][ $input_link_key ][ $schema_key ] = $this->sanitize_params( $input_link[ $schema_key ], $schema_scheme );

					if ( json_encode( $params['links'][ $input_link_key ][ $schema_key ] ) !== json_encode( $input_link[ $schema_key ] ) ) {
						return new \WP_Error(
							'blc-link-action-sanitization-error',
							sprintf(
								//translators: %1$s: The schema's key name.
								esc_html__(
									'Links param `%1$s` potentially malicious',
									'broken-link-checker'
								),
								$schema_key
							)
						);
					}
				}

				$params['links'][ $input_link_key ]['link'] = sanitize_url( Utilities::make_link_relative( $input_link['link'] ) );

			}
		}

		if ( isset( $input_params['site_id'] ) && is_multisite() ) {
			if ( ! is_numeric( $input_params['site_id'] ) || ! Utilities::valid_subsite_id( $input_params['site_id'] ) ) {
				return new \WP_Error(
					'blc-link-action-invalid-subsite-id',
					esc_html__(
						'Invalid sub-site id',
						'broken-link-checker'
					)
				);
			}

			$params['site_id'] = intval( $input_params['site_id'] );
		}

		return $params;
	}

	/**
	 * Sanitize input by scheme (string, url etc).
	 *
	 * @param $param
	 * @param string $scheme
	 *
	 * @return array|array[]|mixed|string[]|string[][]
	 */
	public function sanitize_params( $param = null, string $scheme = null ) {
		if ( empty( $param ) ) {
			return $param;
		}

		if ( is_object( $param ) ) {
			$param = (array) $param;
		}

		if ( is_array( $param ) ) {
			$param = empty( $param ) ? $param : array_map(
				function ( $input ) use ( $scheme ) {
					if ( is_array( $input ) ) {
						return $this->sanitize_params( $input, $scheme );
					}

					return $this->sanitize( $input, $scheme );
				},
				$param
			);
		} else {
			$param = $this->sanitize( $param, $scheme );
		}

		return $param;
	}

	/**
	 * Sets the endpoint's action vars to be used by Dash plugin.
	 */
	protected function setup_action_vars() {
	}
}
