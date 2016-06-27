<?php

/**
 * @group activity
 */
class BPEO_Tests_Activity_BpeoGetActivityByEventId extends BPEO_UnitTestCase {
	public function test_should_return_empty_array_for_nonexistent_post_id() {
		$this->assertSame( array(), bpeo_get_activity_by_event_id( 12345 ) );
	}

	public function test_should_return_empty_array_when_secondary_item_id_doesnt_match() {
		$this->factory->activity->create( array(
			'component' => 'events',
			'type' => 'bpeo_create_event',
			'secondary_item_id' => 54321,
		) );

		$this->assertSame( array(), bpeo_get_activity_by_event_id( 12345 ) );
	}

	public function test_should_return_empty_array_when_secondary_item_id_matches_but_component_is_wrong() {
		$this->factory->activity->create( array(
			'component' => 'foo',
			'type' => 'bpeo_create_event',
			'secondary_item_id' => 12345,
		) );

		$this->assertSame( array(), bpeo_get_activity_by_event_id( 12345 ) );
	}

	public function test_should_return_empty_array_when_secondary_item_id_matches_but_type_is_unexpected() {
		$this->factory->activity->create( array(
			'component' => 'events',
			'type' => 'foo',
			'secondary_item_id' => 12345,
		) );

		$this->assertSame( array(), bpeo_get_activity_by_event_id( 12345 ) );
	}

	public function test_should_return_single_event_not_associated_with_a_group() {
		$a = $this->factory->activity->create( array(
			'component' => 'events',
			'type' => 'bpeo_create_event',
			'secondary_item_id' => 12345,
		) );

		$found = bpeo_get_activity_by_event_id( 12345 );

		$this->assertEquals( array( $a ), wp_list_pluck( $found, 'id' ) );
	}

	public function test_should_return_single_event_associated_with_a_group() {
		$a = $this->factory->activity->create( array(
			'component' => 'groups',
			'type' => 'bpeo_create_event',
			'secondary_item_id' => 12345,
		) );

		$found = bpeo_get_activity_by_event_id( 12345 );

		$this->assertEquals( array( $a ), wp_list_pluck( $found, 'id' ) );
	}

	public function test_should_return_multiple_events() {
		$a1 = $this->factory->activity->create( array(
			'component' => 'groups',
			'type' => 'bpeo_create_event',
			'secondary_item_id' => 12345,
		) );

		$a2 = $this->factory->activity->create( array(
			'component' => 'events',
			'type' => 'bpeo_create_event',
			'secondary_item_id' => 12345,
		) );

		$found = bpeo_get_activity_by_event_id( 12345 );

		$this->assertEquals( array( $a1, $a2 ), wp_list_pluck( $found, 'id' ) );
	}
}
