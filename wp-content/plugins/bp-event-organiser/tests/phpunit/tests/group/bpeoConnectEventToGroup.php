<?php

/**
 * @group group
 */
class BPEO_Tests_Group_BpeoConnectEventToGroup extends BPEO_UnitTestCase {
	public function test_should_return_error_for_non_existent_group() {
		$e = $this->event_factory->event->create();
		$connected = bpeo_connect_event_to_group( $e, 12345 );

		$this->assertWPError( $connected );
		$this->assertSame( 'group_not_found', $connected->get_error_code() );
	}

	public function test_should_return_error_for_non_existent_event() {
		$g = $this->factory->group->create();
		$connected = bpeo_connect_event_to_group( 12345, $g );

		$this->assertWPError( $connected );
		$this->assertSame( 'event_not_found', $connected->get_error_code() );
	}

	public function test_should_return_error_for_post_that_is_not_an_event() {
		$p = $this->factory->post->create();
		$g = $this->factory->group->create();
		$connected = bpeo_connect_event_to_group( $p, $g );

		$this->assertWPError( $connected );
		$this->assertSame( 'event_not_found', $connected->get_error_code() );
	}

	public function test_successful_connection() {
		$e = $this->event_factory->event->create();
		$g = $this->factory->group->create();
		$connected = bpeo_connect_event_to_group( $e, $g );

		$this->assertTrue( $connected );
		$found = bpeo_get_group_events( $g );
		$this->assertContains( $e, $found );
	}
}
