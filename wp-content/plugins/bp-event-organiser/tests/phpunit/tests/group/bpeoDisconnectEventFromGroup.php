<?php

/**
 * @group group
 */
class BPEO_Tests_Group_BpeoDisconnectEventFromGroup extends BPEO_UnitTestCase {
	public function test_should_return_error_for_non_existent_group() {
		$e = $this->event_factory->event->create();
		$connected = bpeo_disconnect_event_from_group( $e, 12345 );

		$this->assertWPError( $connected );
		$this->assertSame( 'group_not_found', $connected->get_error_code() );
	}

	public function test_should_return_error_for_non_existent_event() {
		$g = $this->factory->group->create();
		$connected = bpeo_disconnect_event_from_group( 12345, $g );

		$this->assertWPError( $connected );
		$this->assertSame( 'event_not_found', $connected->get_error_code() );
	}

	public function test_should_return_error_for_post_that_is_not_an_event() {
		$p = $this->factory->post->create();
		$g = $this->factory->group->create();
		$connected = bpeo_disconnect_event_from_group( $p, $g );

		$this->assertWPError( $connected );
		$this->assertSame( 'event_not_found', $connected->get_error_code() );
	}

	public function test_should_return_error_for_event_that_is_not_linked_to_group() {
		$e = $this->event_factory->event->create();
		$g1 = $this->factory->group->create();
		$g2 = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g1 );

		$disconnected = bpeo_disconnect_event_from_group( $e, $g2 );

		$this->assertWPError( $disconnected );
		$this->assertSame( 'event_not_found_for_group', $disconnected->get_error_code() );
	}

	public function test_successful_disconnect() {
		$e = $this->event_factory->event->create();
		$g = $this->factory->group->create();
		$connected = bpeo_connect_event_to_group( $e, $g );

		$this->assertTrue( $connected );
		$found = bpeo_get_group_events( $g );
		$this->assertContains( $e, $found );

		$this->assertTrue( bpeo_disconnect_event_from_group( $e, $g ) );
		$found = bpeo_get_group_events( $g );
		$this->assertNotContains( $e, $found );
	}
}
