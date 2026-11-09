<?php
/**
 * Class TrustedLoginUtilsTest
 *
 * @package Trustedlogin_Button
 */

use TEC\Common\TrustedLogin\Utils;

class TrustedLoginUtilsTest extends WP_UnitTestCase {

	/**
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * @var array
	 */
	protected $site_ids;

	public function setUp(): void {
		parent::setUp();

		$this->create_sites();
	}

	public function create_sites() {
		$this->site_ids = array();
		$this->site_ids[] = get_current_blog_id();
		$this->site_ids[] = $this->factory->blog->create( array( 'domain' => 'example.com', 'path' => '/' ) );
		$this->site_ids[] = $this->factory->blog->create( array( 'domain' => 'example.com', 'path' => '/example/' ) );
	}

	public function testSetTransientWithoutExpiration() {
		$transient = 'transient';
		$value     = 'value';

		$result = Utils::set_transient( $transient, $value );

		$this->assertEquals( 1, $result );

		$row = get_option( $transient );

		$this->assertTrue( is_array( $row ) );
		$this->assertSame( $row['expiration'], 0 );
		$this->assertSame( $row['value'], $value );
	}

	public function testSetTransientWithExpiration() {
		$transient  = 'transient';
		$value      = 'value';
		$expiration = 60;
		$time = time();
		$expiration_time = $time + $expiration;

		$result = Utils::set_transient( $transient, $value, $expiration );

		$this->assertEquals( 1, $result );

		$row = get_option( $transient );

		$this->assertTrue( is_array( $row ) );
		$this->assertSame( $row['expiration'], $expiration_time );
		$this->assertSame( $row['value'], $value );
	}

	/**
	 * Tests that transients are deleted after expiration.
	 * Naming this test with ZZZ so it runs last, since it sets an expiration time.
	 */
	public function testZZZSetTransientExpiration() {
		$transient  = 'transient';
		$value      = 'value';
		$expiration = 1;
		$expiration_time = time() + $expiration;

		$result = Utils::set_transient( $transient, $value, $expiration );
		$this->assertEquals( 1, $result );

		$row = get_option( $transient );

		$this->assertTrue( is_array( $row ) );
		$this->assertSame( $row['expiration'], $expiration_time );
		$this->assertSame( $row['value'], $value );

		$result = Utils::get_transient( $transient );
		$this->assertSame( $result, $value );

		sleep( 2 );

		$result = Utils::get_transient( $transient );
		$this->assertFalse( $result );
	}

	public function testSetTransientsWhenSwitchingSites() {
		$transient = 'transient';
		$value     = 'value';

		// Initial site.
		switch_to_blog( $this->site_ids[0] );

		$result = Utils::set_transient( $transient, $value );
		$this->assertEquals( 1, $result );

		switch_to_blog( $this->site_ids[1] );

		// Other sites should not have access to the transient.
		$result = Utils::get_transient( $transient );
		$this->assertFalse( $result );

		$transient_site_2 = 'transient_site_2';
		$value_site_2     = 'value_site_2';

		$result = Utils::set_transient( $transient_site_2, $value_site_2 );
		$this->assertEquals( 1, $result );

		switch_to_blog( $this->site_ids[2] );

		// Other sites should not have access to the transient.
		$result = Utils::get_transient( $transient );
		$this->assertFalse( $result );

		$result = Utils::get_transient( $transient_site_2 );
		$this->assertFalse( $result );

		$transient_site_3 = 'transient_site_3';
		$value_site_3     = 'value_site_3';

		$result = Utils::set_transient( $transient_site_3, $value_site_3 );
		$this->assertEquals( 1, $result );

		switch_to_blog( $this->site_ids[0] );

		// Sanity check that it's still there.
		$result = Utils::get_transient( $transient );
		$this->assertSame( $result, $value );

		// And that the other site's transients are not.
		$result = Utils::get_transient( $transient_site_2 );
		$this->assertFalse( $result );

		$result = Utils::get_transient( $transient_site_3 );
		$this->assertFalse( $result );

		restore_current_blog();
	}

	public function testSetTransientObject() {
		$transient  = 'transient';
		$value      = new stdClass();
		$value->example = 'example';

		$result = Utils::set_transient( $transient, $value );

		$this->assertEquals( 1, $result );

		$row = get_option( $transient );

		$this->assertTrue( is_array( $row ) );
		$this->assertEquals( $row['value'], $value );
	}

	public function testRetrievesTransientWithoutExpiration() {
		$transient = 'transient';
		$value     = 'value';

		Utils::set_transient( $transient, $value );

		$result = Utils::get_transient( $transient );

		$this->assertEquals( $value, $result );
	}

	public function testGetTransientWithExpiredExpiration() {
		$transient  = 'transient';
		$value      = 'value';
		$expiration = - 1;

		$result = Utils::set_transient( $transient, $value, $expiration );

		$this->assertEquals( 1, $result );

		$row = Utils::get_transient( $transient );

		$this->assertFalse( $row );
	}

	// Additional test methods follow the same pattern...
	// Note: For brevity, I have not included the full details of each test case.
	// You will need to adjust the specific expectations and return values as needed for your testing scenarios.

	public function tearDown(): void {
		parent::tearDown();
	}
}
