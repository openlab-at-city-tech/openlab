<?php
/**
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * @category Core
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 *
 * The first PHP Library to support WPOAuth for X.com's REST API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-wp-oauth.php' );

if ( ! class_exists( 'Wpt_TwitterOAuth' ) ) {

	/**
	 * X.com WPOAuth class
	 */
	class Wpt_TwitterOAuth {
		/**
		 * Contains the last HTTP status code returned
		 *
		 * @var status code
		 */
		public $http_code;
		/**
		 * Contains the last API call.
		 *
		 * @var $url
		 */
		public $url;
		/**
		 * Set up the API root URL.
		 *
		 * @var $host
		 */
		public $host = 'https://api.twitter.com/1.1/';
		/**
		 * Set timeout default.
		 *
		 * @var $format
		 */
		public $format = 'json';
		/**
		 * Decode returned json data.
		 *
		 * @var $decode_json
		 */
		public $decode_json = false;
		/**
		 * Contains the last API call
		 *
		 * @var $last_api_call
		 */
		private $last_api_call;
		/**
		 * Contains the header
		 *
		 * @var $http_header
		 */
		public $http_header;
		/**
		 * Contains the body
		 *
		 * @var $body
		 */
		public $body;

		/**
		 * Set API URLS
		 *
		 * @return access token endpoint.
		 */
		function access_token_url() {
			return 'https://api.twitter.com/oauth/access_token';
		}

		/**
		 * Set authentication URL.
		 *
		 * @return authentication endpoint.
		 */
		function authenticate_url() {
			return 'https://api.twitter.com/oauth/authenticate';
		}

		/**
		 * Set authorization URL.
		 *
		 * @return authorization endpoint.
		 */
		function authorize_url() {
			return 'https://api.twitter.com/oauth/authorize';
		}

		/**
		 * Set request Token URL.
		 *
		 * @return request token ednpoint.
		 */
		function request_token_url() {
			return 'https://api.twitter.com/oauth/request_token';
		}

		/**
		 * Debug helpers
		 *
		 * @return last query's http code response.
		 */
		function last_status_code() {
			return $this->http_code;
		}

		/**
		 * Return last API call.
		 *
		 * @return last query API call.
		 */
		function last_api_call() {
			return $this->last_api_call;
		}

		/**
		 * Construct TwitterWPOAuth object
		 *
		 * @param string $consumer_key Consumer key.
		 * @param string $consumer_secret Consumer secret.
		 * @param string $wp_oauth_token Token.
		 * @param string $wp_oauth_token_secret Token secret.
		 */
		function __construct( $consumer_key, $consumer_secret, $wp_oauth_token = null, $wp_oauth_token_secret = null ) {
			$this->sha1_method = new WPOAuthSignatureMethod_HMAC_SHA1();
			$this->consumer    = new WPOAuthConsumer( $consumer_key, $consumer_secret );
			if ( ! empty( $wp_oauth_token ) && ! empty( $wp_oauth_token_secret ) ) {
				$this->token = new WPOAuthConsumer( $wp_oauth_token, $wp_oauth_token_secret );
			} else {
				$this->token = null;
			}
		}


		/**
		 * Get a request_token from Xcom
		 *
		 * @returns a key/value array containing WPOAuth_token and WPOAuth_token_secret
		 */
		function get_request_token() {
			$r           = $this->wp_oauth_request( $this->request_token_url() );
			$token       = $this->wp_oauth_parse_response( $r );
			$this->token = new WPOAuthConsumer( $token['WPOAuth_token'], $token['WPOAuth_token_secret'] );

			return $token;
		}

		/**
		 * Parse a URL-encoded WPOAuth response
		 *
		 * @param string $response_string String from response.
		 *
		 * @return a key/value array
		 */
		function wp_oauth_parse_response( $response_string ) {
			$r = array();
			foreach ( explode( '&', $response_string ) as $param ) {
				$pair = explode( '=', $param, 2 );
				if ( count( $pair ) !== 2 ) {
					continue;
				}
				$r[ urldecode( $pair[0] ) ] = urldecode( $pair[1] );
			}

			return $r;
		}

		/**
		 * Get the authorize URL
		 *
		 * @param array $token Token array.
		 *
		 * @returns a string
		 */
		function getauthorize_url( $token ) {
			if ( is_array( $token ) ) {
				$token = $token['WPOAuth_token'];
			}

			return $this->authorize_url() . '?WPOAuth_token=' . $token;
		}


		/**
		 * Get the authenticate URL
		 *
		 * @param array $token Token array.
		 *
		 * @returns a string
		 */
		function getauthenticate_url( $token ) {
			if ( is_array( $token ) ) {
				$token = $token['WPOAuth_token'];
			}

			return $this->authenticate_url() . '?WPOAuth_token=' . $token;
		}

		/**
		 * Exchange the request token and secret for an access token and secret, to sign API calls.
		 *
		 * @param array $token Token array.
		 *
		 * @returns array("WPOAuth_token" => the access token, "WPOAuth_token_secret" => the access secret)
		 */
		function get_access_token( $token = null ) {
			$r           = $this->wp_oauth_request( $this->access_token_url() );
			$token       = $this->wp_oauth_parse_response( $r );
			$this->token = new WPOAuthConsumer( $token['WPOAuth_token'], $token['WPOAuth_token_secret'] );

			return $token;
		}

		/**
		 * Wrapper for POST requests
		 *
		 * @param string $url URL.
		 * @param array  $parameters Request params.
		 *
		 * @return decoded response.
		 */
		function post( $url, $parameters = array() ) {
			$response = $this->wp_oauth_request( $url, $parameters, 'POST' );
			if ( 'json' === $this->format && $this->decode_json ) {
				return json_decode( $response );
			}

			return $response;
		}

		/**
		 * Wrapper for MEDIA requests
		 *
		 * @param string $url URL.
		 * @param array  $parameters Request params.
		 *
		 * @return decoded response.
		 */
		function media( $url, $parameters = array() ) {
			$response = $this->wp_oauth_request( $url, $parameters, 'MEDIA' );
			if ( 'json' === $this->format && $this->decode_json ) {
				return json_decode( $response );
			}

			return $response;
		}

		/**
		 * Wrapper for GET requests
		 *
		 * @param string $url URL.
		 * @param array  $parameters Request params.
		 *
		 * @return decoded response.
		 */
		function get( $url, $parameters = array() ) {
			$response = $this->wp_oauth_request( $url, $parameters, 'GET' );
			if ( 'json' === $this->format && $this->decode_json ) {
				return json_decode( $response );
			}

			return $response;
		}

		/**
		 * Handles a status update that includes an image.
		 *
		 * @param string $url Target URL.
		 * @param array  $args Array of arguments to send.
		 *
		 * @return boolean
		 */
		function handle_media_request( $url, $args = array() ) {
			// Load tmhOAuth for Media uploads only when needed: https://github.com/themattharris/tmhOAuth.
			// It's not possible to upload media using WP_HTTP, so this needs to use cURL.
			if ( ! class_exists( 'tmhOAuth' ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'class-tmhoauth.php' );
			}
			$auth = $args['auth'];
			if ( ! $auth ) {
				$ack = get_option( 'app_consumer_key' );
				$acs = get_option( 'app_consumer_secret' );
				$ot  = get_option( 'oauth_token' );
				$ots = get_option( 'oauth_token_secret' );
			} else {
				$ack = get_user_meta( $auth, 'app_consumer_key', true );
				$acs = get_user_meta( $auth, 'app_consumer_secret', true );
				$ot  = get_user_meta( $auth, 'oauth_token', true );
				$ots = get_user_meta( $auth, 'oauth_token_secret', true );
			}

			$connect    = array(
				'consumer_key'    => $ack,
				'consumer_secret' => $acs,
				'user_token'      => $ot,
				'user_secret'     => $ots,
			);
			$tmh_oauth  = new TmhOAuth( $connect );
			$media_id   = $args['media'];
			$attachment = $args['attachment'];

			/**
			 * Add alt attributes to uploaded Twitter images.
			 */
			$alt_text = get_post_meta( $attachment, '_wp_attachment_image_alt', true );
			$alt_text = apply_filters( 'wpt_uploaded_image_alt', $alt_text, $attachment );
			if ( '' !== $alt_text ) {
				$image_alt = json_encode(
					array(
						'media_id' => $media_id,
						'alt_text' => array(
							'text' => $alt_text,
						),
					)
				);
				$tmh_oauth->request(
					'POST',
					$url,
					$image_alt,
					true,
					true,
					array(
						'content-type' => 'application/json',
					)
				);
			}

			return $media_id;
		}

		/**
		 * Format and sign an WPOAuth / API request
		 *
		 * @param string $url Target URL.
		 * @param array  $args Arguments for signing.
		 * @param string $method Method type.
		 *
		 * @return Request.
		 */
		function wp_oauth_request( $url, $args = array(), $method = null ) {
			// Handle media requests using tmhOAuth library.
			if ( 'MEDIA' === $method ) {
				return $this->handle_media_request( $url, $args );
			}

			if ( empty( $method ) ) {
				$method = empty( $args ) ? 'GET' : 'POST';
			}
			$req = WP_Oauth_Request::from_consumer_and_token( $this->consumer, $this->token, $method, $url, $args );
			$req->sign_request( $this->sha1_method, $this->consumer, $this->token );

			$response = false;
			$url      = null;

			switch ( $method ) {
				case 'GET':
					$url      = $req->to_url();
					$response = wp_remote_get( $url );
					break;
				case 'POST':
					// TODO: if JSON, need to authenticate, pass bearer authentication as header in query.
					// TODO: add content-type when JSON.
					$url      = $req->get_normalized_http_url();
					$args     = wp_parse_args( $req->to_postdata() );
					$response = wp_remote_post(
						$url,
						array(
							'body'    => $args,
							'timeout' => 30,
						)
					);
					break;
			}

			if ( is_wp_error( $response ) ) {
				return false;
			}
			$this->http_code     = $response['response']['code'];
			$this->body          = json_decode( $response['body'] );
			$this->last_api_call = $url;
			$this->format        = 'json';
			$this->http_header   = $response['headers'];

			return $response['body'];
		}
	}
}
