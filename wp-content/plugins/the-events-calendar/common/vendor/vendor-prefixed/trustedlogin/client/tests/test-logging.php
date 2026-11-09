<?php
/**
 * Class TrustedLoginLoggingTest
 *
 * @package TEC\Common\TrustedLogin\Client
 */

namespace TEC\Common\TrustedLogin;

use WP_UnitTestCase;
use WP_Error;

class TrustedLoginLoggingTest extends WP_UnitTestCase {

	/**
	 * @var array
	 */
	private $config_array;

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

		$this->config_array = $config;
	}

	/**
	 * @covers \TEC\Common\TrustedLogin\Logging::is_enabled()
	 * @throws \Exception
	 */
	public function test_is_enabled() {

		$default = $this->config_array;

		$config = new Config( $default );

		$logging_disabled = new Logging( $config );

		$this->assertFalse( $logging_disabled->is_enabled() );

		add_filter( 'trustedlogin/' . $config->ns() . '/logging/enabled', '__return_true' );

		$this->assertTrue( $logging_disabled->is_enabled() );

		remove_filter( 'trustedlogin/' . $config->ns() . '/logging/enabled', '__return_true' );

		$this->assertFalse( $logging_disabled->is_enabled() );

		$enabled                       = $default;
		$enabled['logging']['enabled'] = true;

		$config_enabled = new Config( $enabled );

		$logging_enabled = new Logging( $config_enabled );

		$this->assertTrue( $logging_enabled->is_enabled() );

		add_filter( 'trustedlogin/' . $config->ns() . '/logging/enabled', '__return_false' );

		$this->assertFalse( $logging_enabled->is_enabled() );

		remove_filter( 'trustedlogin/' . $config->ns() . '/logging/enabled', '__return_false' );

		$this->assertTrue( $logging_enabled->is_enabled() );

		add_filter(
			'trustedlogin/' . $config->ns() . '/logging/enabled',
			function () {
				return new \WP_Error( 'asdasdsad', 'not boolean' );
			}
		);

		$this->assertTrue( $logging_enabled->is_enabled(), 'The WP_Error should be seen as true when cast as boolean' );

		remove_all_filters( 'trustedlogin/' . $config->ns() . '/logging/enabled' );

		$weird_settings = $default;

		$weird_settings['logging']['enabled'] = array( 'asdasddasd' );
		$weird_config                         = new Config( $weird_settings );
		$logging_enabled                      = new Logging( $weird_config );
		$this->assertTrue( $logging_enabled->is_enabled(), 'array had content; !empty() should have returned true' );

		$weird_settings['logging']['enabled'] = array();
		$weird_config                         = new Config( $weird_settings );
		$logging_enabled                      = new Logging( $weird_config );
		$this->assertFalse( $logging_enabled->is_enabled(), 'empty array should have been seen as empty' );
	}
}
