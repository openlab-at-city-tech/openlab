<?php
/**
 * Class TrustedLoginClientTest
 *
 * @package TEC\Common\TrustedLogin\Client
 */

namespace TEC\Common\TrustedLogin;

use WP_UnitTestCase;
use WP_Error;

class TrustedLoginRemoteTest extends WP_UnitTestCase {

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var Remote
	 */
	private $remote;

	public function setUp(): void {
		parent::setUp();

		$config = array(
			'role'           => 'editor',
			'caps'           => array(
				'add' => array(
					'manage_options' => 'we need this to make things work real gud',
					'edit_posts'     => 'Access the posts that you created',
				),
			),
			'webhook_url'    => 'https://www.example.com/endpoint/',
			'auth'           => array(
				'api_key'     => '9946ca31be6aa948', // Public key for encrypting the securedKey
				'license_key' => 'my custom key',
			),
			'decay'          => WEEK_IN_SECONDS,
			'vendor'         => array(
				'namespace'   => 'gravityview',
				'title'       => 'GravityView',
				'email'       => 'support@gravityview.co',
				'website'     => 'https://gravityview.co',
				'support_url' => 'https://gravityview.co/support/', // Backup to redirect users if TL is down/etc
				'logo_url'    => '', // Displayed in the authentication modal
			),
			'reassign_posts' => true,
		);

		$this->config = new Config( $config );

		$this->remote = new Remote( $this->config, new Logging( $this->config ) );
	}

	/**
	 * @param string $name Method to set to accessible
	 * @param string $reflection_class Class to reflect
	 *
	 * @return \ReflectionMethod
	 * @throws \ReflectionException
	 */
	private function _get_public_method( $name, $reflection_class = '\TEC\Common\TrustedLogin\Remote' ) {

		$Reflection = new \ReflectionClass( $reflection_class );
		$method     = $Reflection->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}

	/**
	 * @param string $name Property to set to accessible
	 * @param string $reflection_class Class to reflect
	 *
	 * @return \ReflectionProperty
	 * @throws \ReflectionException
	 */
	private function _get_public_property( $name, $reflection_class = '\TEC\Common\TrustedLogin\Remote' ) {

		$Reflection = new \ReflectionClass( $reflection_class );
		$prop       = $Reflection->getProperty( $name );
		$prop->setAccessible( true );

		return $prop;
	}

	/**
	 * @covers \TEC\Common\TrustedLogin\Remote::send()
	 */
	public function test_api_send() {

		$this->assertWPError( $this->remote->send( 'any-path', 'any data', 'not supported http method' ) );

		$that = &$this;

		// Make sure the body has been removed from methods that don't support it
		add_filter(
			'http_request_args',
			$filter_args = function ( $parsed_args, $url ) use ( $that ) {
				$that->assertNull( $parsed_args['body'] );
				return $parsed_args;
			},
			10,
			2
		);

		unset( $that );

		$uppercase = $this->remote->send( 'sites', 'any data', 'head' );

		// If this failed, it's for some network reason, not because of the reason we're testing.
		if ( is_wp_error( $uppercase ) ) {
			$this->assertNotEquals( 'invalid_method', $uppercase->get_error_code(), 'The method failed to auto-uppercase methods.' );
		}

		$head_request = $this->remote->send( 'sites', 'any data', 'HEAD' );

		if ( is_wp_error( $head_request ) ) {
			$this->assertNotWPError( $head_request, $head_request->get_error_code() . ': ' . $head_request->get_error_message() );
		}

		remove_filter( 'http_request_args', $filter_args );

		// Make sure that POST and DELETE are able to sent body and that the body is properly formatted
		add_filter(
			'http_request_args',
			$filter_args = function ( $parsed_args, $url ) {
				$this->assertEquals( json_encode( array( 'test', 'array' ) ), $parsed_args['body'] );
				return $parsed_args;
			},
			10,
			2
		);

		$this->assertNotWPError( $this->remote->send( 'sites', array( 'test', 'array' ), 'POST' ) );
		$this->assertNotWPError( $this->remote->send( 'sites', array( 'test', 'array' ), 'DELETE' ) );

		remove_filter( 'http_request_args', $filter_args );
	}

	/**
	 * @throws \ReflectionException
	 * @covers \TEC\Common\TrustedLogin\Remote::build_api_url
	 */
	public function test_build_api_url() {

		$method = $this->_get_public_method( 'build_api_url' );

		$this->assertEquals( \TEC\Common\TrustedLogin\Remote::API_URL, $method->invoke( $this->remote ) );

		$this->assertEquals( \TEC\Common\TrustedLogin\Remote::API_URL . 'pathy-path', $method->invoke( $this->remote, 'pathy-path' ) );

		add_filter(
			'trustedlogin/not-my-namespace/api_url',
			function () {
				return 'https://www.google.com';
			}
		);

		$this->assertEquals( \TEC\Common\TrustedLogin\Remote::API_URL . 'pathy-path', $method->invoke( $this->remote, 'pathy-path' ) );

		remove_all_filters( 'trustedlogin/not-my-namespace/api_url' );

		add_filter(
			'trustedlogin/gravityview/api_url',
			function () {
				return 'https://www.google.com';
			}
		);

		$this->assertEquals( 'https://www.google.com/pathy-path', $method->invoke( $this->remote, 'pathy-path' ) );

		remove_all_filters( 'trustedlogin/gravityview/api_url' );

		try {
			$this->assertEquals( \TEC\Common\TrustedLogin\Remote::API_URL, $method->invoke( $this->remote, array( 'not-a-string' ) ) );
		} catch ( \Exception $e ) {
			$this->assertEquals( 'Endpoint must be a string.', $e->getMessage() );
			$this->assertEquals( 400, $e->getCode() );
		}
	}

	/**
	 * @covers \TEC\Common\TrustedLogin\Remote::handle_response()
	 */
	public function test_handle_response() {

		// No JSON at all.
		$this->assertWPError( $this->remote->handle_response( array( 'body' => '' ) ) );
		$this->assertSame( 'invalid_response', $this->remote->handle_response( array( 'body' => '' ) )->get_error_code() );

		// Missing JSON response body.
		$this->assertWPError( $this->remote->handle_response( array( 'body' => '{ example: "JSON" }' ) ) );
		$this->assertSame( 'missing_response_body', $this->remote->handle_response( array( 'response' => array( 'code' => 200 ), 'body' => '' ) )->get_error_code() );

		// Verify error response codes
		$error_codes = array(
			'unauthenticated'  => 401,
			'invalid_token'    => 403,
			'not_found'        => 404,
			'unavailable'      => 500,
			'invalid_response' => '',
		);

		foreach ( $error_codes as $error_code => $response_code ) {
			$invalid_code_response = array(
				'body'     => 'Not Empty',
				'response' => array(
					'code' => $response_code,
				),
			);

			$handled_response = $this->remote->handle_response( $invalid_code_response );

			$this->assertWPError( $handled_response );
			$this->assertSame( $error_code, $handled_response->get_error_code(), $response_code . ' should have triggered ' . $error_code );
		}

		// Verify invalid JSON
		$invalid_json_response = array(
			'body'     => 'Not JSON, that is for sure.',
			'response' => array(
				'code' => 200,
			),
		);

		$handled_response = $this->remote->handle_response( $invalid_json_response );

		$this->assertWPError( $handled_response );
		$this->assertSame( 'invalid_response', $handled_response->get_error_code(), $response_code . ' should have triggered ' . $error_code );
		$this->assertSame( $invalid_json_response, $handled_response->get_error_data( 'invalid_response' ) );

		// Finally, VALID JSON
		$valid_json_response = array(
			'body'     => '{"message":"This works"}',
			'response' => array(
				'code' => 200,
			),
		);

		$handled_response = $this->remote->handle_response( $valid_json_response );
		$this->assertNotWPError( $handled_response );
		$this->assertSame( array( 'message' => 'This works' ), $handled_response );

		$handled_response = $this->remote->handle_response( $valid_json_response, 'message' );
		$this->assertNotWPError( $handled_response );
		$this->assertSame( array( 'message' => 'This works' ), $handled_response );

		$handled_response = $this->remote->handle_response( $valid_json_response, array( 'missing_key' ) );
		$this->assertWPError( $handled_response );
		$this->assertSame( 'missing_required_key', $handled_response->get_error_code() );
	}
}
