<?php
/**
 * Class TrustedLoginClientTest
 *
 * @package TEC\Common\TrustedLogin\Client
 */

namespace TEC\Common\TrustedLogin;

use WP_UnitTestCase;
use WP_Error;

class TrustedLoginSiteAccessTest extends WP_UnitTestCase {

	/**
	 * @var SiteAccess $site_access
	 */
	private $site_access;

	private $default_config;

	private $config;

	private $logging;

	public static $functions_not_exist = array();

	public static $openssl_crypto_strong = true;

	public function setUp(): void {
		parent::setUp();

		$this->default_config = array(
			'role' => 'editor',
			'caps'     => array(
				'add' => array(
					'manage_options' => 'we need this to make things work real gud',
					'edit_posts'     => 'Access the posts that you created',
				),
			),
			'webhook_url'    => 'https://www.example.com/endpoint/',
			'auth'           => array(
				'api_key'  => '9946ca31be6aa948', // Public key for encrypting the securedKey
				/** Not setting 'license_key' on purpose; will be tested in {see @testGetAccessKey} */
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

		$this->config = new Config( $this->default_config );
		$this->logging = new Logging( $this->config );
	}

	/**
	 * @covers \TEC\Common\TrustedLogin\SiteAccess::get_access_key()
	 */
	public function testGetAccessKey() {

		$site_access = new \TEC\Common\TrustedLogin\SiteAccess( $this->config, $this->logging );
		$access_key = $site_access->get_access_key();

		$this->assertNotEmpty( $access_key );
		$this->assertIsString( $access_key );
		$this->assertSame( 64, strlen( $access_key ), 'Should be a 64-character key when no license exists' );


		$config_array = $this->default_config;
		$config_array['auth']['license_key'] = 'my custom key';
		$config = new Config( $config_array );
		$site_access_with_license = new \TEC\Common\TrustedLogin\SiteAccess( $config, $this->logging );
		$access_key_with_license = $site_access_with_license->get_access_key();

		$this->assertNotEmpty( $access_key_with_license );
		$this->assertIsString( $access_key_with_license );
		$this->assertSame( 64, strlen( $access_key_with_license ), 'Should be a 64-character key when no license exists' );

		// Make sure the access keys are different!
		$this->assertNotSame( $access_key, $access_key_with_license );
	}
}
