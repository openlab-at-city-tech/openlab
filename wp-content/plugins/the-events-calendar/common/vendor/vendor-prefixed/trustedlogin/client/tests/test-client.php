<?php
/**
 * Class TrustedLoginClientTest
 *
 * @package TEC\Common\TrustedLogin\Client
 */

namespace TEC\Common\TrustedLogin;

use WP_UnitTestCase;
use WP_Error;

class TrustedLoginClientTest extends WP_UnitTestCase {

	/**
	 * @var \TEC\Common\TrustedLogin\Client
	 */
	private $TrustedLogin;

	/**
	 * @var \ReflectionClass
	 */
	private $TrustedLoginReflection;

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var \TEC\Common\TrustedLogin\Logging
	 */
	private $logging;

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

		$this->TrustedLogin = new Client( $this->config );

		$this->TrustedLoginReflection = new \ReflectionClass( '\TEC\Common\TrustedLogin\Client' );

		$this->logging = $this->_get_public_property( 'logging' )->getValue( $this->TrustedLogin );
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * @param $name
	 *
	 * @return \ReflectionMethod
	 * @throws \ReflectionException
	 */
	private function _get_public_method( $name ) {

		$method = $this->TrustedLoginReflection->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}

	private function _get_public_property( $name ) {

		$prop = $this->TrustedLoginReflection->getProperty( $name );
		$prop->setAccessible( true );

		return $prop;
	}

	/**
	 * @covers \TEC\Common\TrustedLogin\SiteAccess::get_license_key
	 */
	public function test_get_license_key() {

		$site_access = $this->_get_public_property( 'site_access' )->getValue( $this->TrustedLogin );

		$this->assertSame( $this->config->get_setting( 'auth/license_key' ), $site_access->get_license_key() );

	}

	/**
	 * @covers \TEC\Common\TrustedLogin\Client::grant_access()
	 */
	public function test_grant_access_bad_user_cap() {
		$current = $this->factory->user->create_and_get( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $current->ID );
		$trustedlogin = new Client( $this->config );
		$expect_403   = $trustedlogin->grant_access();
		$error_data   = $expect_403->get_error_data();
		$error_code   = isset( $error_data['error_code'] ) ? $error_data['error_code'] : null;

		$this->assertEquals( $error_code, 403 );

		wp_set_current_user( 0 );
	}
}
