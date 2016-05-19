<?php

/**
 * @group group
 */
class BPEO_Tests_Group_BpeoGetEventGroups extends BPEO_UnitTestCase {
	public function test_should_return_empty_array_for_event_with_no_groups() {
		$e = $this->event_factory->event->create();
		$g = $this->factory->group->create();

		$this->assertSame( array(), bpeo_get_event_groups( $e ) );
	}

	public function test_should_return_connected_groups() {
		$events = $this->event_factory->event->create_many( 3 );
		$groups = $this->factory->group->create_many( 3 );

		bpeo_connect_event_to_group( $events[0], $groups[0] );
		bpeo_connect_event_to_group( $events[1], $groups[1] );

		$this->assertSame( array( $groups[1] ), bpeo_get_event_groups( $events[1] ) );
	}
}
