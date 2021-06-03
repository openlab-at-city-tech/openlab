<?php
/**
 * WP OAuth class adapted from Abraham.
 *
 * @package     WP to Twitter
 * @author      Joe Dolson
 * @copyright   2012-2018 Joe Dolson
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPOAuthException' ) ) {

	/**
	 * Generic OAuth class
	 */
	class WP_OAuth {
		// Honestly, this is only here so I don't have to rename the file.
	}
	/**
	 * Generic exception class
	 */
	class WPOAuthException extends Exception {
		// pass.
	}

	/**
	 * Create Consumer key
	 */
	class WPOAuthConsumer {
		/**
		 * Contains the user's Consumer key.
		 *
		 * @var consumer key
		 */
		public $key;
		/**
		 * Contains the user's consumer secret.
		 *
		 * @var secret
		 */
		public $secret;

		/**
		 * Constructor.
		 *
		 * @param string $key Key.
		 * @param string $secret Secret.
		 * @param string $callback_url Response sent to.
		 */
		function __construct( $key, $secret, $callback_url = null ) {
			$this->key          = $key;
			$this->secret       = $secret;
			$this->callback_url = $callback_url;
		}

		/**
		 * Generate consumer string.
		 */
		function __toString() {
			return "OAuthConsumer[key=$this->key,secret=$this->secret]";
		}
	}

	/**
	 * Create consumer token.
	 */
	class WPOAuthToken {
		/**
		 * Access token
		 *
		 * @var token
		 */
		public $key;
		/**
		 * Access secret.
		 *
		 * @var secret
		 */
		public $secret;

		/**
		 * Construct token.
		 *
		 * @param string $key = the token.
		 * @param string $secret = the token secret.
		 */
		function __construct( $key, $secret ) {
			$this->key    = $key;
			$this->secret = $secret;
		}

		/**
		 * Generates the basic string serialization of a token that a server
		 * would respond to request_token and access_token calls with
		 *
		 * @return string Oauth serialization.
		 */
		function to_string() {
			return 'oauth_token=' . WPOAuthUtil::urlencode_rfc3986( $this->key ) . '&oauth_token_secret=' . WPOAuthUtil::urlencode_rfc3986( $this->secret );
		}

		/**
		 * Return string.
		 */
		function __toString() {
			return $this->to_string();
		}
	}

	/**
	 * A class for implementing a Signature Method
	 * See section 9 ("Signing Requests") in the spec
	 */
	abstract class WPOAuthSignatureMethod {
		/**
		 * Needs to return the name of the Signature Method (ie HMAC-SHA1)
		 *
		 * @return string
		 */
		abstract public function get_name();

		/**
		 * Build up the signature
		 * NOTE: The output of this function MUST NOT be urlencoded.
		 * the encoding is handled in OAuthRequest when the final
		 * request is serialized
		 *
		 * @param OAuthRequest  $request OAuth Request object.
		 * @param OAuthConsumer $consumer OAuth Consumer key.
		 * @param OAuthToken    $token OAuth Consumer token.
		 *
		 * @return string
		 */
		abstract public function build_signature( $request, $consumer, $token );

		/**
		 * Verifies that a given signature is correct
		 *
		 * @param OAuthRequest  $request Request.
		 * @param OAuthConsumer $consumer Consumer key.
		 * @param OAuthToken    $token Auth token.
		 * @param string        $signature Signature.
		 *
		 * @return bool
		 */
		public function check_signature( $request, $consumer, $token, $signature ) {
			$built = $this->build_signature( $request, $consumer, $token );

			return $built === $signature;
		}
	}

	/**
	 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
	 * where the Signature Base String is the text and the key is the concatenated values (each first
	 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
	 * character (ASCII code 38) even if empty.
	 * - Chapter 9.2 ("HMAC-SHA1")
	 */
	class WPOAuthSignatureMethod_HMAC_SHA1 extends WPOAuthSignatureMethod {
		/**
		 * Signature method.
		 */
		function get_name() {
			return 'HMAC-SHA1';
		}

		/**
		 * Build a signature.
		 *
		 * @param object $request Request object.
		 * @param object $consumer Consumer object.
		 * @param string $token Token.
		 *
		 * @return base 64 signature.
		 */
		public function build_signature( $request, $consumer, $token ) {
			$base_string          = $request->get_signature_base_string();
			$request->base_string = $base_string;

			$key_parts = array( $consumer->secret, ( $token ) ? $token->secret : '' );
			$key_parts = WPOAuthUtil::urlencode_rfc3986( $key_parts );
			$key       = implode( '&', $key_parts );

			return base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) );
		}
	}

	/**
	 * The PLAINTEXT method does not provide any security protection and SHOULD only be used
	 * over a secure channel such as HTTPS. It does not use the Signature Base String.
	 *   - Chapter 9.4 ("PLAINTEXT")
	 */
	class WPOAuthSignatureMethod_PLAINTEXT extends WPOAuthSignatureMethod {
		/**
		 * Plaintext method.
		 */
		public function get_name() {
			return 'PLAINTEXT';
		}

		/**
		 * The oauth_signature is set to the concatenated encoded values of the Consumer Secret and
		 * Token Secret, separated by a '&' character (ASCII code 38), even if either secret is
		 * empty. The result MUST be encoded again.
		 *   - Chapter 9.4.1 ("Generating Signatures")
		 *
		 * @param object $request Request object.
		 * @param object $consumer Consumer object.
		 * @param string $token Token.
		 *
		 * @return signature.
		 *
		 * Please note that the second encoding MUST NOT happen in the SignatureMethod, as
		 * OAuthRequest handles this!
		 */
		public function build_signature( $request, $consumer, $token ) {
			$key_parts = array( $consumer->secret, ( $token ) ? $token->secret : '' );

			$key_parts            = WPOAuthUtil::urlencode_rfc3986( $key_parts );
			$key                  = implode( '&', $key_parts );
			$request->base_string = $key;

			return $key;
		}
	}

	/**
	 * The RSA-SHA1 signature method uses the RSASSA-PKCS1-v1_5 signature algorithm as defined in
	 * [RFC3447] section 8.2 (more simply known as PKCS#1), using SHA-1 as the hash function for
	 * EMSA-PKCS1-v1_5. It is assumed that the Consumer has provided its RSA public key in a
	 * verified way to the Service Provider, in a manner which is beyond the scope of this
	 * specification.
	 * - Chapter 9.3 ("RSA-SHA1")
	 */
	abstract class WPOAuthSignatureMethod_RSA_SHA1 extends WPOAuthSignatureMethod {
		/**
		 * Return method.
		 */
		public function get_name() {
			return 'RSA-SHA1';
		}

		/**
		 * Up to the SP to implement this lookup of keys. Possible ideas are:
		 * ((1) do a lookup in a table of trusted certs keyed off of consumer.
		 * (2) fetch via http using a url provided by the requester.
		 * (3) some sort of specific discovery code based on request.
		 *
		 * @param Object $request Request.
		 *
		 * Either way should return a string representation of the certificate.
		 */
		protected abstract function fetch_public_cert( &$request );

		/**
		 * Up to the SP to implement this lookup of keys. Possible ideas are:
		 * (1) do a lookup in a table of trusted certs keyed off of consumer.
		 *
		 * @param Object $request Request.
		 *
		 * @return Either way should return a string representation of the certificate.
		 */
		protected abstract function fetch_private_cert( &$request );

		/**
		 * Build a signature object.
		 *
		 * @param object $request Request object.
		 * @param object $consumer Consumer object.
		 * @param string $token Token.
		 *
		 * @return Encoded signature.
		 */
		public function build_signature( $request, $consumer, $token ) {
			$base_string          = $request->get_signature_base_string();
			$request->base_string = $base_string;

			// Fetch the private key cert based on the request.
			$cert = $this->fetch_private_cert( $request );

			// Pull the private key ID from the certificate.
			$privatekeyid = openssl_get_privatekey( $cert );

			// Sign using the key.
			$ok = openssl_sign( $base_string, $signature, $privatekeyid );

			// Release the key resource.
			openssl_free_key( $privatekeyid );

			return base64_encode( $signature );
		}

		/**
		 * Verify a signature object.
		 *
		 * @param object $request Request object.
		 * @param object $consumer Consumer object.
		 * @param string $token Token.
		 * @param string $signature Signature.
		 *
		 * @return boolean acceptance.
		 */
		public function check_signature( $request, $consumer, $token, $signature ) {
			$decoded_sig = base64_decode( $signature );

			$base_string = $request->get_signature_base_string();

			// Fetch the public key cert based on the request.
			$cert = $this->fetch_public_cert( $request );

			// Pull the public key ID from the certificate.
			$publickeyid = openssl_get_publickey( $cert );

			// Check the computed signature against the one passed in the query.
			$ok = openssl_verify( $base_string, $decoded_sig, $publickeyid );

			// Release the key resource.
			openssl_free_key( $publickeyid );

			return 1 === $ok;
		}
	}

	/**
	 * Construct and send the OAuth Request to the target URL.
	 */
	class WP_Oauth_Request {
		/**
		 * Query parameters
		 *
		 * @var parameters
		 */
		private $parameters;

		/**
		 * HTTP query method.
		 *
		 * @var http_method
		 */
		private $http_method;

		/**
		 * Target URL
		 *
		 * @var http_url
		 */
		private $http_url;

		/**
		 * Base string - base for signature string.
		 *
		 * @var base_string
		 */
		public $base_string;

		/**
		 * Version.
		 *
		 * @var version
		 */
		public static $version = '1.0';

		/**
		 * POST input - source of input.
		 *
		 * @var post_input
		 */
		public static $post_input = 'php://input';

		/**
		 * Constructor function. Build properties.
		 *
		 * @param string $http_method Method.
		 * @param string $http_url URL.
		 * @param array  $parameters Query parameters; will be combined with POST data.
		 */
		function __construct( $http_method, $http_url, $parameters = array() ) {
			$parameters        = array_merge( WPOAuthUtil::parse_parameters( parse_url( $http_url, PHP_URL_QUERY ) ), $parameters );
			$this->parameters  = $parameters;
			$this->http_method = $http_method;
			$this->http_url    = $http_url;
		}

		/**
		 * Attempt to build up a request from what was passed to the server
		 *
		 * @param string $http_method Method.
		 * @param string $http_url URL.
		 * @param array  $parameters Query parameters.
		 *
		 * @return WP_Oauth_Request object.
		 */
		public static function from_request( $http_method = null, $http_url = null, $parameters = null ) {
			$scheme = ( ! isset( $_SERVER['HTTPS'] ) || 'on' !== $_SERVER['HTTPS'] ) ? 'http' : 'https';
			if ( null === $http_url ) {
				$http_url = $scheme . '://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			}
			if ( null === $http_method ) {
				$http_method = $_SERVER['REQUEST_METHOD'];
			}

			// We weren't handed any parameters, so let's find the ones relevant to this request.
			// If you run XML-RPC or similar you should use this to provide your own parsed parameter-list.
			if ( ! $parameters ) {
				// Find request headers.
				$request_headers = WPOAuthUtil::get_headers();

				// Parse the query-string to find GET parameters.
				$parameters = WPOAuthUtil::parse_parameters( $_SERVER['QUERY_STRING'] );

				// It's a POST request of the proper content-type, so parse POST.
				// parameters and add those overriding any duplicates from GET.
				$content_type = isset( $request_headers['Content-Type'] ) ? $request_headers['Content-Type'] : '';
				if ( 'POST' === $http_method && strstr( $content_type, 'application/x-www-form-urlencoded' ) ) {
					$post_data  = WPOAuthUtil::parse_parameters(
						file_get_contents( self::$post_input )
					);
					$parameters = array_merge( $parameters, $post_data );
				}

				// We have a Authorization-header with OAuth data. Parse the header.
				// and add those overriding any duplicates from GET or POST.
				$authorization = isset( $request_headers['Authorization'] ) ? $request_headers['Authorization'] : '';
				if ( 'OAuth ' === substr( $authorization, 0, 6 ) ) {
					$header_parameters = WPOAuthUtil::split_header(
						$request_headers['Authorization']
					);
					$parameters        = array_merge( $parameters, $header_parameters );
				}
			}
			return new WP_Oauth_Request( $http_method, $http_url, $parameters );
		}

		/**
		 * Helper function to set up the request
		 *
		 * @param object $consumer Consumer token.
		 * @param object $token API token.
		 * @param string $http_method Method.
		 * @param string $http_url URL.
		 * @param array  $parameters Query parameters.
		 *
		 * @return object WP_Oauth_Request object.
		 */
		public static function from_consumer_and_token( $consumer, $token, $http_method, $http_url, $parameters = array() ) {
			$defaults = array(
				'oauth_version'      => WP_Oauth_Request::$version,
				'oauth_nonce'        => WP_Oauth_Request::generate_nonce(),
				'oauth_timestamp'    => WP_Oauth_Request::generate_timestamp(),
				'oauth_consumer_key' => $consumer->key,
			);
			if ( $token ) {
				$defaults['oauth_token'] = $token->key;
			}

			$parameters = array_merge( $defaults, $parameters );

			return new WP_Oauth_Request( $http_method, $http_url, $parameters );
		}

		/**
		 * Construct parameters for query.
		 *
		 * @param string  $name Parameter name string.
		 * @param string  $value Parameter value.
		 * @param boolean $allow_duplicates Should we allow duplicate parameter names.
		 */
		public function set_parameter( $name, $value, $allow_duplicates = true ) {
			if ( $allow_duplicates && isset( $this->parameters[ $name ] ) ) {
				// We have already added parameter(s) with this name, so add to the list.
				if ( is_scalar( $this->parameters[ $name ] ) ) {
					// This is the first duplicate, so transform scalar (string).
					// into an array so we can add the duplicates.
					$this->parameters[ $name ] = array( $this->parameters[ $name ] );
				}

				$this->parameters[ $name ][] = $value;
			} else {
				$this->parameters[ $name ] = $value;
			}
		}

		/**
		 * Get a parameter by name.
		 *
		 * @param string $name Parameter name.
		 *
		 * @return Parameter value.
		 */
		public function get_parameter( $name ) {
			return isset( $this->parameters[ $name ] ) ? $this->parameters[ $name ] : null;
		}

		/**
		 * Get all current parameters.
		 *
		 * @return Parameters.
		 */
		public function get_parameters() {
			return $this->parameters;
		}

		/**
		 * Remove a parameter.
		 *
		 * @param string $name Parameter name.
		 */
		public function unset_parameter( $name ) {
			unset( $this->parameters[ $name ] );
		}

		/**
		 * The request parameters, sorted and concatenated into a normalized string.
		 *
		 * @return string
		 */
		public function get_signable_parameters() {
			// Grab all parameters.
			$params = $this->parameters;

			// Remove oauth_signature if present.
			// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.").
			if ( isset( $params['oauth_signature'] ) ) {
				unset( $params['oauth_signature'] );
			}

			return WPOAuthUtil::build_http_query( $params );
		}

		/**
		 * Returns the base string of this request
		 *
		 * The base string defined as the method, the url
		 * and the parameters (normalized), each urlencoded
		 * and the concated with &.
		 */
		public function get_signature_base_string() {
			$parts = array(
				$this->get_normalized_http_method(),
				$this->get_normalized_http_url(),
				$this->get_signable_parameters(),
			);

			$parts = WPOAuthUtil::urlencode_rfc3986( $parts );

			return implode( '&', $parts );
		}

		/**
		 * Just uppercases the http method
		 */
		public function get_normalized_http_method() {
			return strtoupper( $this->http_method );
		}

		/**
		 * Parses the url and rebuilds it to be
		 * scheme://host/path
		 */
		public function get_normalized_http_url() {
			$parts = parse_url( $this->http_url );

			$port   = isset( $parts['port'] ) ? $parts['port'] : false;
			$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : '';
			$host   = isset( $parts['host'] ) ? $parts['host'] : '';
			$path   = isset( $parts['path'] ) ? $parts['path'] : '';

			if ( ! $port ) {
				$port = ( 'https' === $scheme ) ? '443' : '80';
			}

			if ( ( 'https' === $scheme && '443' !== (string) $port ) || ( 'http' === $scheme && '80' !== (string) $port )
			) {
				$host = "$host:$port";
			}

			return "$scheme://$host$path";
		}

		/**
		 * Builds a url usable for a GET request
		 */
		public function to_url() {
			$post_data = $this->to_postdata();
			$out       = $this->get_normalized_http_url();
			if ( $post_data ) {
				$out .= '?' . $post_data;
			}

			return $out;
		}

		/**
		 * Builds the data one would send in a POST request
		 */
		public function to_postdata() {
			return WPOAuthUtil::build_http_query( $this->parameters );
		}

		/**
		 * Builds the Authorization: header
		 *
		 * @param string $realm If realm not null.
		 * @throws WPOAuthException Exception message.
		 *
		 * @return Header string.
		 */
		public function to_header( $realm = null ) {
			$first = true;
			if ( $realm ) {
				$out   = 'Authorization: OAuth realm="' . WPOAuthUtil::urlencode_rfc3986( $realm ) . '"';
				$first = false;
			} else {
				$out = 'Authorization: OAuth';
			}

			$total = array();
			foreach ( $this->parameters as $k => $v ) {
				if ( 'oauth' !== substr( $k, 0, 5 ) ) {
					continue;
				}
				if ( is_array( $v ) ) {
					throw new WPOAuthException( 'Arrays not supported in headers' );
				}
				$out  .= ( $first ) ? ' ' : ',';
				$out  .= WPOAuthUtil::urlencode_rfc3986( $k ) . '="' . WPOAuthUtil::urlencode_rfc3986( $v ) . '"';
				$first = false;
			}

			return $out;
		}

		/**
		 * Convert object to URL string.
		 *
		 * @return string URL.
		 */
		public function __toString() {
			return $this->to_url();
		}

		/**
		 * Sign the OAuth request.
		 *
		 * @param string $signature_method Method to use to sign.
		 * @param object $consumer Consumer object.
		 * @param object $token Token object.
		 */
		public function sign_request( $signature_method, $consumer, $token ) {
			$this->set_parameter(
				'oauth_signature_method',
				$signature_method->get_name(),
				false
			);
			$signature = $this->build_signature( $signature_method, $consumer, $token );
			$this->set_parameter( 'oauth_signature', $signature, false );
		}

		/**
		 * Create the OAuth signature.
		 *
		 * @param string $signature_method Method to use to sign.
		 * @param object $consumer Consumer object.
		 * @param object $token Token object.
		 *
		 * @return signature.
		 */
		public function build_signature( $signature_method, $consumer, $token ) {
			$signature = $signature_method->build_signature( $this, $consumer, $token );

			return $signature;
		}

		/**
		 * Util function: current timestamp
		 *
		 * @return current time.
		 */
		private static function generate_timestamp() {
			// make sure that timestamp is in UTC.
			date_default_timezone_set( 'UTC' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set

			return time();
		}

		/**
		 * Util function: current nonce
		 *
		 * @return md5 string.
		 */
		private static function generate_nonce() {
			$mt   = microtime();
			$rand = mt_rand();

			return md5( $mt . $rand ); // md5s look nicer than numbers.
		}
	}

	/**
	 * Query to OAuth server.
	 */
	class WPOAuthServer {
		/**
		 * Limit on timestamp inconsistencies.
		 *
		 * @var $timestamp_threshold
		 */
		protected $timestamp_threshold = 300; // in seconds, five minutes.
		/**
		 * Version
		 *
		 * @var $version
		 */
		protected $version = '1.0';           // hi blaine.
		/**
		 * Array of methods usable.
		 *
		 * @var $signature_methods
		 */
		protected $signature_methods = array();

		/**
		 * Storage variable.
		 *
		 * @var $data_store
		 */
		protected $data_store;

		/**
		 * Build data store.
		 *
		 * @param object $data_store Data store.
		 */
		function __construct( $data_store ) {
			$this->data_store = $data_store;
		}

		/**
		 * Add a signature method.
		 *
		 * @param object $signature_method Signature method.
		 */
		public function add_signature_method( $signature_method ) {
			$this->signature_methods[ $signature_method->get_name() ] = $signature_method;
		}

		/**
		 * Process a request_token request
		 *
		 * @param object $request Request object.
		 *
		 * @return new token.
		 */
		public function fetch_request_token( &$request ) {
			$this->get_version( $request );

			$consumer = $this->get_consumer( $request );

			// no token required for the initial token request.
			$token = null;

			$this->check_signature( $request, $consumer, $token );

			// Rev A change.
			$callback  = $request->get_parameter( 'oauth_callback' );
			$new_token = $this->data_store->new_request_token( $consumer, $callback );

			return $new_token;
		}

		/**
		 * Process an access_token request
		 *
		 * @param object $request Request object.
		 *
		 * @return new token.
		 */
		public function fetch_access_token( &$request ) {
			$this->get_version( $request );

			$consumer = $this->get_consumer( $request );

			// requires authorized request token.
			$token = $this->get_token( $request, $consumer, 'request' );

			$this->check_signature( $request, $consumer, $token );

			// Rev A change.
			$verifier  = $request->get_parameter( 'oauth_verifier' );
			$new_token = $this->data_store->new_access_token( $token, $consumer, $verifier );

			return $new_token;
		}

		/**
		 * Verify an api call, checks all the parameters
		 *
		 * @param object $request Request object.
		 *
		 * @return consumer & token.
		 */
		public function verify_request( &$request ) {
			$this->get_version( $request );
			$consumer = $this->get_consumer( $request );
			$token    = $this->get_token( $request, $consumer, 'access' );
			$this->check_signature( $request, $consumer, $token );

			return array( $consumer, $token );
		}

		/**
		 * Version 1
		 *
		 * @param object $request Request.
		 * @throws WPOAuthException Exception message.
		 *
		 * @return Oauth version.
		 */
		private function get_version( &$request ) {
			$version = $request->get_parameter( 'oauth_version' );
			if ( ! $version ) {
				// Service Providers MUST assume the protocol version to be 1.0 if this parameter is not present.
				// Chapter 7.0 ("Accessing Protected Resources").
				$version = '1.0';
			}
			if ( $version !== $this->version ) {
				throw new WPOAuthException( "OAuth version '$version' not supported" );
			}

			return $version;
		}

		/**
		 * Figure out the signature with some defaults
		 *
		 * @param object $request Request.
		 * @throws WPOAuthException Exception message.
		 *
		 * @return signature methods.
		 */
		private function get_signature_method( &$request ) {
			$signature_method = $request->get_parameter( 'oauth_signature_method' );

			if ( ! $signature_method ) {
				// According to chapter 7 ("Accessing Protected Resources") the signature-method.
				// parameter is required, and we can't just fallback to PLAINTEXT.
				throw new WPOAuthException( 'No signature method parameter. This parameter is required' );
			}

			if ( ! in_array( $signature_method, array_keys( $this->signature_methods ), true ) ) {
				throw new WPOAuthException(
					"Signature method '$signature_method' not supported " .
					'try one of the following: ' .
					implode( ', ', array_keys( $this->signature_methods ) )
				);
			}

			return $this->signature_methods[ $signature_method ];
		}

		/**
		 * Try to find the consumer for the provided request's consumer key
		 *
		 * @param object $request Request.
		 * @throws WPOAuthException Exception message.
		 *
		 * @return consumer.
		 */
		private function get_consumer( &$request ) {
			$consumer_key = $request->get_parameter( 'oauth_consumer_key' );
			if ( ! $consumer_key ) {
				throw new WPOAuthException( 'Invalid consumer key' );
			}

			$consumer = $this->data_store->lookup_consumer( $consumer_key );
			if ( ! $consumer ) {
				throw new WPOAuthException( 'Invalid consumer' );
			}

			return $consumer;
		}

		/**
		 * Try to find the token for the provided request's token key
		 *
		 * @param object $request Request.
		 * @param object $consumer Consumer.
		 * @param string $token_type Type of token being handled.
		 * @throws WPOAuthException Exception message.
		 *
		 * @return Oauth version.
		 */
		private function get_token( &$request, $consumer, $token_type = 'access' ) {
			$token_field = $request->get_parameter( 'oauth_token' );
			$token       = $this->data_store->lookup_token( $consumer, $token_type, $token_field );
			if ( ! $token ) {
				throw new WPOAuthException( "Invalid $token_type token: $token_field" );
			}

			return $token;
		}

		/**
		 * All-in-one function to check the signature on a request
		 * should guess the signature method appropriately
		 *
		 * @param object $request Request.
		 * @param object $consumer Consumer.
		 * @param object $token Token.
		 * @throws WPOAuthException Exception message.
		 */
		private function check_signature( &$request, $consumer, $token ) {
			// this should probably be in a different method.
			$timestamp = $request->get_parameter( 'oauth_timestamp' );
			$nonce     = $request->get_parameter( 'oauth_nonce' );

			$this->check_timestamp( $timestamp );
			$this->check_nonce( $consumer, $token, $nonce, $timestamp );

			$signature_method = $this->get_signature_method( $request );

			$signature = $request->get_parameter( 'oauth_signature' );
			$valid_sig = $signature_method->check_signature(
				$request,
				$consumer,
				$token,
				$signature
			);

			if ( ! $valid_sig ) {
				throw new WPOAuthException( 'Invalid signature' );
			}
		}

		/**
		 * Check that the timestamp is new enough
		 *
		 * @param string $timestamp Time stamp.
		 * @throws WPOAuthException Exception message.
		 */
		private function check_timestamp( $timestamp ) {
			if ( ! $timestamp ) {
				throw new WPOAuthException(
					'Missing timestamp parameter. The parameter is required'
				);
			}

			// verify that timestamp is recentish.
			$now = time();
			if ( abs( $now - $timestamp ) > $this->timestamp_threshold ) {
				throw new WPOAuthException(
					"Expired timestamp, yours $timestamp, ours $now"
				);
			}
		}

		/**
		 * Check that the nonce is not repeated
		 *
		 * @param string $consumer Consumer.
		 * @param string $token Token.
		 * @param string $nonce Nonce.
		 * @param string $timestamp Timestamp.
		 * @throws WPOAuthException Exception message.
		 */
		private function check_nonce( $consumer, $token, $nonce, $timestamp ) {
			if ( ! $nonce ) {
				throw new WPOAuthException( 'Missing nonce parameter. The parameter is required' );
			}

			// verify that the nonce is uniqueish.
			$found = $this->data_store->lookup_nonce(
				$consumer,
				$token,
				$nonce,
				$timestamp
			);
			if ( $found ) {
				throw new WPOAuthException( "Nonce already used: $nonce" );
			}
		}

	}

	/**
	 * Handle data storage.
	 */
	class WPOAuthDataStore {
		/**
		 * Look up the current consumer.
		 *
		 * @param string $consumer_key Key.
		 */
		function lookup_consumer( $consumer_key ) {
			// implement me.
		}
		/**
		 * Look up the current token.
		 *
		 * @param object $consumer Consumer object.
		 * @param string $token_type Token type.
		 * @param string $token Token.
		 */
		function lookup_token( $consumer, $token_type, $token ) {
			// implement me.
		}
		/**
		 * Look up the current nonce.
		 *
		 * @param object $consumer Consumer object.
		 * @param object $token Token.
		 * @param string $nonce None.
		 * @param string $timestamp Timestamp.
		 */
		function lookup_nonce( $consumer, $token, $nonce, $timestamp ) {
			// implement me.
		}
		/**
		 * Get a new request token.
		 *
		 * @param object $consumer Consumer.
		 * @param string $callback URL.
		 */
		function new_request_token( $consumer, $callback = null ) {
			// return a new token attached to this consumer.
		}
		/**
		 * Get a new access token.
		 *
		 * @param object $token Token.
		 * @param object $consumer Consumer.
		 * @param string $verifier Verifier parameter.
		 */
		function new_access_token( $token, $consumer, $verifier = null ) {
			// return a new access token attached to this consumer.
			// for the user associated with this token if the request token.
			// is authorized.
			// should also invalidate the request token.
		}

	}

	/**
	 * Utility procedures.
	 */
	class WPOAuthUtil {
		/**
		 * Encode as rfc3986.
		 *
		 * @param string $input Any string input.
		 *
		 * @return encoded input.
		 */
		public static function urlencode_rfc3986( $input ) {
			if ( is_array( $input ) ) {
				return array_map( array( 'WPOAuthUtil', 'urlencode_rfc3986' ), $input );
			} elseif ( is_scalar( $input ) ) {
				return str_replace(
					'+',
					' ',
					str_replace( '%7E', '~', rawurlencode( $input ) )
				);
			} else {
				return '';
			}
		}


		/**
		 * This decode function isn't taking into consideration the above
		 * modifications to the encoding process. However, this method doesn't
		 * seem to be used anywhere so leaving it as is.
		 *
		 * @param string $string An encoded string.
		 *
		 * @return $string A decoded string.
		 */
		public static function urldecode_rfc3986( $string ) {
			return urldecode( $string );
		}

		/**
		 * Utility function for turning the Authorization: header into.
		 * parameters, has to do some unescaping.
		 * Can filter out any non-oauth parameters if needed (default behaviour).
		 *
		 * @param string  $header Header string.
		 * @param boolean $only_allow_oauth_parameters Strip off non-oauth params.
		 *
		 * @return Return parameters array.
		 */
		public static function split_header( $header, $only_allow_oauth_parameters = true ) {
			$pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
			$offset  = 0;
			$params  = array();
			while ( preg_match( $pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset ) > 0 ) {
				$match          = $matches[0];
				$header_name    = $matches[2][0];
				$header_content = ( isset( $matches[5] ) ) ? $matches[5][0] : $matches[4][0];
				if ( preg_match( '/^oauth_/', $header_name ) || ! $only_allow_oauth_parameters ) {
					$params[ $header_name ] = WPOAuthUtil::urldecode_rfc3986( $header_content );
				}
				$offset = $match[1] + strlen( $match[0] );
			}

			if ( isset( $params['realm'] ) ) {
				unset( $params['realm'] );
			}

			return $params;
		}

		/**
		 * Helper to try to sort out headers for people who aren't running apache
		 *
		 * @return headers
		 */
		public static function get_headers() {
			if ( function_exists( 'apache_request_headers' ) ) {
				// we need this to get the actual Authorization: header.
				// because apache tends to tell us it doesn't exist.
				$headers = apache_request_headers();

				// sanitize the output of apache_request_headers because.
				// we always want the keys to be Cased-Like-This and arh().
				// returns the headers in the same case as they are in the request.
				$out = array();
				foreach ( $headers as $key => $value ) {
					$key         = str_replace(
						' ',
						'-',
						ucwords( strtolower( str_replace( '-', ' ', $key ) ) )
					);
					$out[ $key ] = $value;
				}
			} else {
				// otherwise we don't have apache and are just going to have to hope.
				// that $_SERVER actually contains what we need.
				$out = array();
				if ( isset( $_SERVER['CONTENT_TYPE'] ) ) {
					$out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
				}
				if ( isset( $_ENV['CONTENT_TYPE'] ) ) {
					$out['Content-Type'] = $_ENV['CONTENT_TYPE'];
				}

				foreach ( $_SERVER as $key => $value ) {
					if ( substr( $key, 0, 5 ) === 'HTTP_' ) {
						// this is chaos, basically it is just there to capitalize the first.
						// letter of every word that is not an initial HTTP and strip HTTP.
						// code from przemek.
						$key         = str_replace(
							' ',
							'-',
							ucwords( strtolower( str_replace( '_', ' ', substr( $key, 5 ) ) ) )
						);
						$out[ $key ] = $value;
					}
				}
			}

			return $out;
		}

		/**
		 * This function takes a input like a=b&a=c&d=e and returns the parsed
		 * parameters like this
		 * array('a' => array('b','c'), 'd' => 'e')
		 *
		 * @param string $input URL query string.
		 *
		 * @return array of parameters.
		 */
		public static function parse_parameters( $input ) {
			if ( ! isset( $input ) || ! $input ) {
				return array();
			}

			$pairs = explode( '&', $input );

			$parsed_parameters = array();
			foreach ( $pairs as $pair ) {
				$split     = explode( '=', $pair, 2 );
				$parameter = WPOAuthUtil::urldecode_rfc3986( $split[0] );
				$value     = isset( $split[1] ) ? WPOAuthUtil::urldecode_rfc3986( $split[1] ) : '';

				if ( isset( $parsed_parameters[ $parameter ] ) ) {
					// We have already recieved parameter(s) with this name, so add to the list.
					// of parameters with this name.
					if ( is_scalar( $parsed_parameters[ $parameter ] ) ) {
						// This is the first duplicate, so transform scalar (string) into an array.
						// so we can add the duplicates.
						$parsed_parameters[ $parameter ] = array( $parsed_parameters[ $parameter ] );
					}

					$parsed_parameters[ $parameter ][] = $value;
				} else {
					$parsed_parameters[ $parameter ] = $value;
				}
			}

			return $parsed_parameters;
		}

		/**
		 * Built an HTTP query string from parameters.
		 *
		 * @param array $params Query parameters.
		 *
		 * @return string query string.
		 */
		public static function build_http_query( $params ) {
			if ( ! $params ) {
				return '';
			}

			// Urlencode both keys and values.
			$keys   = WPOAuthUtil::urlencode_rfc3986( array_keys( $params ) );
			$values = WPOAuthUtil::urlencode_rfc3986( array_values( $params ) );
			$params = array_combine( $keys, $values );

			// Parameters are sorted by name, using lexicographical byte value ordering.
			// Ref: Spec: 9.1.1 (1).
			uksort( $params, 'strcmp' );

			$pairs = array();
			foreach ( $params as $parameter => $value ) {
				if ( is_array( $value ) ) {
					// If two or more parameters share the same name, they are sorted by their value.
					// Ref: Spec: 9.1.1 (1).
					natsort( $value );
					foreach ( $value as $duplicate_value ) {
						$pairs[] = $parameter . '=' . $duplicate_value;
					}
				} else {
					$pairs[] = $parameter . '=' . $value;
				}
			}
			// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61).
			// Each name-value pair is separated by an '&' character (ASCII code 38).
			return implode( '&', $pairs );
		}
	}
} // class_exists check.
