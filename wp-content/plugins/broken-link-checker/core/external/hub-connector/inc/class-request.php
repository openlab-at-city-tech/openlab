<?php
/**
 * API request class
 *
 * Handles all the API requests to Hub.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

defined( 'WPINC' ) || die;

use Exception;
use WP_Error;

/**
 * Class Request.
 */
class Request {

	/**
	 * Request max timeout.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $timeout = 15;

	/**
	 * Header arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $headers = array();

	/**
	 * POST arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $post_args = array();

	/**
	 * GET arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $get_args = array();

	/**
	 * Request constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Set request timeout.
	 *
	 * @since 1.0.0
	 *
	 * @param int $timeout Request timeout (seconds).
	 */
	public function set_timeout( $timeout ) {
		$this->timeout = $timeout;
	}

	/**
	 * Add a new request argument for POST requests.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  Argument name.
	 * @param string $value Argument value.
	 */
	public function add_post_argument( $name, $value ) {
		$this->post_args[ $name ] = $value;
	}

	/**
	 * Add a new request argument for GET requests.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  Argument name.
	 * @param string $value Argument value.
	 */
	public function add_get_argument( $name, $value ) {
		$this->get_args[ $name ] = $value;
	}

	/**
	 * Add a new request argument for GET requests.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  Argument name.
	 * @param string $value Argument value.
	 */
	public function add_header_argument( $name, $value ) {
		$this->headers[ $name ] = $value;
	}

	/**
	 * Make a POST request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Endpoint route.
	 * @param bool   $auth Should attach API key?.
	 * @param array  $data Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function post( $path, $auth = false, $data = array() ) {
		try {
			return $this->request( $path, $auth, $data );
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Make a GET request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Endpoint route.
	 * @param bool   $auth Should attach API key?.
	 * @param array  $data Data array.
	 *
	 * @return mixed|WP_Error
	 */
	public function get( $path, $auth = false, $data = array() ) {
		try {
			return $this->request( $path, $auth, $data, 'get' );
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Add API key for authorization to request.
	 *
	 * Optionally if hub site id is found, include that too.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url    URL of request.
	 * @param string $method Request method (post or get).
	 *
	 * @return string
	 */
	private function set_api_key( $url, $method = 'post' ) {
		$key     = API::get()->get_api_key();
		$site_id = Data::get()->hub_site_id();

		if ( ! empty( $key ) ) {
			if ( 'post' === $method ) {
				$this->add_post_argument( 'api_key', $key );
			} else {
				// Set API key if not already set.
				if ( false === strpos( $url, '/' . $key ) ) {
					$url .= '/' . $key;
				}
				if ( ! empty( $site_id ) ) {
					$this->add_get_argument( 'site_id', $site_id );
				}
			}
		}

		return $url;
	}

	/**
	 * Make an HTTP request using WP.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path   API endpoint route.
	 * @param bool   $auth   Should attach API key?.
	 * @param array  $data   Data array.
	 * @param string $method Request method.
	 *
	 * @return array|WP_Error
	 */
	private function request( $path, $auth = false, $data = array(), $method = 'post' ) {
		$url = API::get()->rest_url( $path );

		if ( $auth ) {
			$url = $this->set_api_key( $url, $method );
		}

		// Add URL params.
		$url = add_query_arg( $this->get_args, $url );

		// Default request options.
		$args = array(
			'user-agent' => 'WPMUDEV Hub Connector Client/' . \WPMUDEV_HUB_CONNECTOR_VERSION . ' (+' . network_site_url() . ')',
			'headers'    => $this->headers,
			'sslverify'  => defined( '\WPMUDEV_API_SSLVERIFY' ) ? \WPMUDEV_API_SSLVERIFY : false,
			'method'     => strtoupper( $method ),
			'timeout'    => $this->timeout,
		);

		switch ( strtolower( $method ) ) {
			case 'post':
				if ( is_array( $data ) ) {
					$data = array_merge( $data, $this->post_args );
				}

				$args['body'] = $data;

				$response = wp_remote_post( $url, $args );
				break;
			case 'get':
				// If data is set for get request add it to URL.
				$url = add_query_arg( $data, $url );

				$response = wp_remote_get( $url, $args );
				break;
			default:
				$response = wp_remote_request( $url, $args );
				break;
		}

		return $response;
	}
}
