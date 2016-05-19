<?php

/**
 * @group query
 * @group group
 */
class BPEO_Tests_BpeoFilterQueryForBpGroup extends BPEO_UnitTestCase {
	public function test_should_not_modify_tax_query_when_single_post_type_is_not_event() {
		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => 'page',
		) );

		$this->assertSame( array(), $q->tax_query->queries );
	}

	public function test_should_not_modify_tax_query_when_event_is_not_in_multiple_post_type() {
		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => array( 'page', 'post' ),
		) );

		$this->assertSame( array(), $q->tax_query->queries );
	}

	public function test_should_not_modify_tax_query_when_no_bp_group_param_is_provided() {
		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => array( 'event' ),
		) );

		$this->assertSame( array(), $q->tax_query->queries );
	}

	public function test_bp_group_empty_array_should_always_return_no_events() {
		$groups = $this->factory->group->create();
		$events = $this->event_factory->event->create();

		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => 'event',
			'bp_group' => array(),
			'showpastevents' => true,
		) );

		$this->assertEqualSets( array(), $q->posts );
	}

	public function test_single_bp_group_with_connected_events() {
		$groups = $this->factory->group->create_many( 2 );
		$events = $this->event_factory->event->create_many( 2 );

		bpeo_connect_event_to_group( $events[1], $groups[1] );

		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => 'event',
			'bp_group' => $groups[1],
			'showpastevents' => true,
		) );

		$this->assertEqualSets( array( $events[1] ), $q->posts );
	}

	public function test_single_bp_group_with_no_connected_events() {
		$groups = $this->factory->group->create_many( 2 );
		$events = $this->event_factory->event->create_many( 2 );

		bpeo_connect_event_to_group( $events[1], $groups[1] );

		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => 'event',
			'bp_group' => $groups[0],
			'showpastevents' => true,
		) );

		$this->assertSame( array(), $q->posts );
	}

	public function test_multiple_bp_group_with_connected_events() {
		$groups = $this->factory->group->create_many( 3 );
		$events = $this->event_factory->event->create_many( 3 );

		bpeo_connect_event_to_group( $events[1], $groups[1] );
		bpeo_connect_event_to_group( $events[2], $groups[2] );

		$q = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => 'event',
			'bp_group' => $groups,
			'showpastevents' => true,
		) );

		$this->assertEqualSets( array( $events[1], $events[2] ), $q->posts );
	}
}
