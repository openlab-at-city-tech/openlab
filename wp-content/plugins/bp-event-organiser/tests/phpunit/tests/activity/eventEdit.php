<?php

/**
 * @group activity
 */
class BPEO_Tests_Activity_EventEdit extends BPEO_UnitTestCase {
	public function test_edit_event_with_no_prior_edits_should_create_new_activity_item() {
		$u = $this->factory->user->create();

		$now = time();
		$e = $this->event_factory->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*24 ),
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish',
		) );

		$before = bpeo_get_activity_by_event_id( $e );

		// Remove throttle temporarily.
		add_filter( 'bpeo_event_edit_throttle_period', '__return_zero' );
		eo_update_event( $e, array(), array( 'post_content' => 'foo' ) );
		remove_filter( 'bpeo_event_edit_throttle_period', '__return_zero' );

		$after = bpeo_get_activity_by_event_id( $e );

		// `array_diff()` for our modern times.
		$a = array();
		foreach ( $after as $_after ) {
			foreach ( $before as $_before ) {
				if ( $_after == $_before ) {
					continue 2;
				}
			}

			$a[] = $_after;
		}

		$this->assertNotEmpty( $a );
		$this->assertEquals( $u, $a[0]->user_id );
		$this->assertEquals( 'events', $a[0]->component );
		$this->assertEquals( 'bpeo_edit_event', $a[0]->type );
		$this->assertEquals( $e, $a[0]->secondary_item_id );
	}

	public function test_edit_event_with_no_prior_edits_should_create_new_activity_item_in_connected_groups() {
		$u = $this->factory->user->create();
		$this->groups = $this->factory->group->create_many( 3 );

		// Group connections happen on 'save_post'. Whee!
		add_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$now = time();
		$e = $this->event_factory->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*24 ),
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish',
		) );

		remove_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$before = bpeo_get_activity_by_event_id( $e );

		eo_update_event( $e, array(), array( 'post_content' => 'foo' ) );

		$after = bpeo_get_activity_by_event_id( $e );

		// `array_diff()` for our modern times.
		$a = array();
		foreach ( $after as $_after ) {
			foreach ( $before as $_before ) {
				if ( $_after == $_before ) {
					continue 2;
				}
			}

			$a[] = $_after;
		}

		// Get only the group updates.
		$a = wp_list_filter( $a, array( 'component' => 'groups' ) );
		$a = array_values( $a );

		$this->assertNotEmpty( $a );
		$this->assertEquals( $u, $a[0]->user_id );
		$this->assertEquals( 'groups', $a[0]->component );
		$this->assertEquals( 'bpeo_edit_event', $a[0]->type );
		$this->assertEquals( $this->groups[0], $a[0]->item_id );
		$this->assertEquals( $e, $a[0]->secondary_item_id );

		$this->assertNotEmpty( $a );
		$this->assertEquals( $u, $a[1]->user_id );
		$this->assertEquals( 'groups', $a[1]->component );
		$this->assertEquals( 'bpeo_edit_event', $a[1]->type );
		$this->assertEquals( $this->groups[2], $a[1]->item_id );
		$this->assertEquals( $e, $a[1]->secondary_item_id );
	}

	public function test_edit_event_with_prior_edit_less_than_six_hours_old_should_not_create_now_activity_item() {
		$u = $this->factory->user->create();

		$now = time();
		$e = $this->event_factory->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*24 ),
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish',
		) );

		// Manually create edit item, 5:59 ago.
		$ago = $now - ( 60*60*6 - 60 );
		bp_activity_add( array(
			'component' => 'events',
			'type' => 'bpeo_edit_event',
			'secondary_item_id' => $e,
			'recorded_time' => date( 'Y-m-d H:i:s', $ago ),
		) );

		$before = bpeo_get_activity_by_event_id( $e );

		eo_update_event( $e, array(), array( 'post_content' => 'foo' ) );

		$after = bpeo_get_activity_by_event_id( $e );

		$this->assertEquals( $before, $after );
	}

	public function test_edit_event_with_prior_edit_more_than_six_hours_old_should_not_create_now_activity_item() {
		$u = $this->factory->user->create();

		$now = time();
		$e = $this->event_factory->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*24 ),
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish',
		) );

		// Manually create edit item, 6:01 ago.
		$ago = $now - ( 60*60*6 + 60 );
		bp_activity_add( array(
			'component' => 'events',
			'type' => 'bpeo_edit_event',
			'secondary_item_id' => $e,
			'recorded_time' => date( 'Y-m-d H:i:s', $ago ),
		) );

		$before = bpeo_get_activity_by_event_id( $e );

		eo_update_event( $e, array(), array( 'post_content' => 'foo' ) );

		$after = bpeo_get_activity_by_event_id( $e );

		// `array_diff()` for our modern times.
		$a = array();
		foreach ( $after as $_after ) {
			foreach ( $before as $_before ) {
				if ( $_after == $_before ) {
					continue 2;
				}
			}

			$a[] = $_after;
		}

		$this->assertNotEmpty( $a );
		$this->assertEquals( $u, $a[0]->user_id );
		$this->assertEquals( 'events', $a[0]->component );
		$this->assertEquals( 'bpeo_edit_event', $a[0]->type );
		$this->assertEquals( $e, $a[0]->secondary_item_id );

		$modified_event = get_post( $e );
		$this->assertEquals( $modified_event->post_modified, $a[0]->date_recorded );
	}

	public function test_action_string_for_edit_event_not_connected_to_groups() {
		$u = $this->factory->user->create();

		$now = time();
		$e = eo_insert_event( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*24 ),
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish',
		) );

		eo_update_event( $e, array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'description' => 'foo',
		) );

		$a = bpeo_get_activity_by_event_id( $e );
		$found = end( $a );

		$event = get_post( $e );

		$expected = sprintf(
			'%s edited the event %s',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) )
		);

		$this->assertSame( $expected, $a[0]->action );
	}

	public function test_action_string_for_edit_event_connected_to_groups_where_groups_are_public() {
		$u = $this->factory->user->create();
		$this->groups = array();
		$this->groups[] = $this->factory->group->create( array(
			'name' => 'aaa',
		) );
		$this->groups[] = $this->factory->group->create( array(
			'name' => 'bbb',
		) );
		$this->groups[] = $this->factory->group->create( array(
			'name' => 'ccc',
		) );

		// Group connections happen on 'save_post'. Whee!
		add_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60 ),
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish',
		) );

		remove_action( 'save_post', array( $this, 'connect_events' ), 15 );

		// Remove throttle temporarily.
		add_filter( 'bpeo_event_edit_throttle_period', '__return_zero' );
		eo_update_event( $e, array(), array( 'post_content' => 'foo' ) );
		remove_filter( 'bpeo_event_edit_throttle_period', '__return_zero' );

		$a = bpeo_get_activity_by_event_id( $e );

		$ua = $ga0 = $ga2 = false;
		foreach ( $a as $_a ) {
			if ( 'bpeo_edit_event' != $_a->type ) {
				continue;
			}

			if ( $this->groups[0] == $_a->item_id ) {
				$ga0 = $_a;
			} elseif ( $this->groups[2] == $_a->item_id ) {
				$ga2 = $_a;
			} else {
				$ua = $_a;
			}
		}

		$this->assertNotEmpty( $ua );
		$this->assertNotEmpty( $ga0 );
		$this->assertNotEmpty( $ga2 );

		$g0 = groups_get_group( array( 'group_id' => $this->groups[0] ) );
		$g2 = groups_get_group( array( 'group_id' => $this->groups[2] ) );

		$event = get_post( $e );

		// User string takes the groups in alphabetical order.
		$ua_expected = sprintf(
			'%s edited the event %s in the groups %s, %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g0 ) . 'events/' ), esc_html( $g0->name ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) )
		);
		$this->assertSame( $ua_expected, $ua->action );

		$g0_expected = sprintf(
			'%s edited the event %s in the groups %s, %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g0 ) . 'events/' ), esc_html( $g0->name ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) )
		);
		$this->assertSame( $g0_expected, $ga0->action );

		$g2_expected = sprintf(
			'%s edited the event %s in the groups %s, %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g0 ) . 'events/' ), esc_html( $g0->name ) )
		);
		$this->assertSame( $g2_expected, $ga2->action );
	}

	public function connect_events( $e ) {
		bpeo_connect_event_to_group( $e, $this->groups[0] );
		bpeo_connect_event_to_group( $e, $this->groups[2] );
	}
}
