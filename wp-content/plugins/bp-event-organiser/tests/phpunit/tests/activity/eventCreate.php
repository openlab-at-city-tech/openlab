<?php

/**
 * @group activity
 */
class BPEO_Tests_Activity_EventCreate extends BPEO_UnitTestCase {
	public function test_new_event_not_connected_to_group() {
		$u = $this->factory->user->create();

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$a = bpeo_get_activity_by_event_id( $e );

		$this->assertNotEmpty( $a );
		$this->assertEquals( $u, $a[0]->user_id );
		$this->assertEquals( 'events', $a[0]->component );
		$this->assertEquals( 'bpeo_create_event', $a[0]->type );
		$this->assertEquals( $e, $a[0]->secondary_item_id );
	}

	public function test_new_event_connected_to_groups() {
		$u = $this->factory->user->create();
		$this->groups = $this->factory->group->create_many( 3 );

		// Group connections happen on 'save_post'. Whee!
		add_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		remove_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$a = bpeo_get_activity_by_event_id( $e );
		$this->assertNotEmpty( $a );

		// User item.
		$this->assertEquals( $u, $a[0]->user_id );
		$this->assertEquals( 'events', $a[0]->component );
		$this->assertEquals( 'bpeo_create_event', $a[0]->type );
		$this->assertEquals( $e, $a[0]->secondary_item_id );

		// Group item.
		$this->assertEquals( $u, $a[1]->user_id );
		$this->assertEquals( 'groups', $a[1]->component );
		$this->assertEquals( 'bpeo_create_event', $a[1]->type );
		$this->assertEquals( $this->groups[0], $a[1]->item_id );
		$this->assertEquals( $e, $a[1]->secondary_item_id );
		$this->assertEquals( 1, $a[1]->hide_sitewide );

		// Group item.
		$this->assertEquals( $u, $a[2]->user_id );
		$this->assertEquals( 'groups', $a[2]->component );
		$this->assertEquals( 'bpeo_create_event', $a[2]->type );
		$this->assertEquals( $this->groups[2], $a[2]->item_id );
		$this->assertEquals( $e, $a[2]->secondary_item_id );
		$this->assertEquals( 1, $a[2]->hide_sitewide );
	}

	public function test_action_string_for_new_event_not_connected_to_groups() {
		$u = $this->factory->user->create();

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$a = bpeo_get_activity_by_event_id( $e );

		$event = get_post( $e );

		$expected = sprintf(
			'%s created the event %s',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) )
		);

		$this->assertSame( $expected, $a[0]->action );
	}

	public function test_action_string_for_new_event_connected_to_groups_where_groups_are_public() {
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
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		remove_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$a = bpeo_get_activity_by_event_id( $e );

		$ua = $ga0 = $ga2 = false;
		foreach ( $a as $_a ) {
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
			'%s created the event %s in the groups %s, %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g0 ) . 'events/' ), esc_html( $g0->name ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) )
		);
		$this->assertSame( $ua_expected, $ua->action );

		$g0_expected = sprintf(
			'%s created the event %s in the groups %s, %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g0 ) . 'events/' ), esc_html( $g0->name ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) )
		);
		$this->assertSame( $g0_expected, $ga0->action );

		$g2_expected = sprintf(
			'%s created the event %s in the groups %s, %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g0 ) . 'events/' ), esc_html( $g0->name ) )
		);
		$this->assertSame( $g2_expected, $ga2->action );
	}

	public function test_action_string_for_new_event_connected_to_groups_where_user_only_has_access_to_one_group() {
		$creator = $this->factory->user->create();
		$u = $this->factory->user->create();
		$current_user = bp_loggedin_user_id();
		$this->set_current_user( $u );

		$this->groups = array();
		$this->groups[] = $this->factory->group->create( array(
			'name' => 'aaa',
			'status' => 'private',
			'creator_id' => $creator,
		) );
		$this->groups[] = $this->factory->group->create( array(
			'name' => 'bbb',
			'creator_id' => $creator,
		) );
		$this->groups[] = $this->factory->group->create( array(
			'name' => 'ccc',
			'status' => 'private',
			'creator_id' => $creator,
		) );

		$this->add_user_to_group( $u, $this->groups[2] );

		// Group connections happen on 'save_post'. Whee!
		add_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		remove_action( 'save_post', array( $this, 'connect_events' ), 15 );

		$a = bpeo_get_activity_by_event_id( $e );

		$ua = $ga0 = $ga2 = false;
		foreach ( $a as $_a ) {
			if ( $this->groups[0] == $_a->item_id ) {
				$ga0 = $_a;
			} elseif ( $this->groups[2] == $_a->item_id ) {
				$ga2 = $_a;
			} else {
				$ua = $_a;
			}
		}

		// Ignoring $ga0 since user will never see it.
		$this->assertNotEmpty( $ua );
		$this->assertNotEmpty( $ga2 );

		$g0 = groups_get_group( array( 'group_id' => $this->groups[0] ) );
		$g2 = groups_get_group( array( 'group_id' => $this->groups[2] ) );

		$event = get_post( $e );

		$ua_expected = sprintf(
			'%s created the event %s in the group %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) )
		);
		$this->assertSame( $ua_expected, $ua->action );

		$g2_expected = sprintf(
			'%s created the event %s in the group %s.',
			sprintf( '<a href="%s">%s</a>', esc_url( bp_core_get_user_domain( $u ) ), esc_html( bp_core_get_user_displayname( $u ) ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $event ) ), esc_html( $event->post_title ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( bp_get_group_permalink( $g2 ) . 'events/' ), esc_html( $g2->name ) )
		);
		$this->assertSame( $g2_expected, $ga2->action );

		$this->set_current_user( $current_user );
	}

	public function connect_events( $e ) {
		bpeo_connect_event_to_group( $e, $this->groups[0] );
		bpeo_connect_event_to_group( $e, $this->groups[2] );
	}
}
