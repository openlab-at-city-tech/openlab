<?php

/**
 * @group user
 * @group calendar
 */
class BPEO_Tests_Calendar extends BPEO_UnitTestCase {
	public function test_eo_get_event_fullcalendar_should_add_whitelisted_shortcode_attributes_to_script_params() {
		eo_get_event_fullcalendar( array(
			'bp_group' => 5,
			'bp_displayed_user_id' => 6,
			'foo' => 7,
		) );

		EventOrganiser_Shortcodes::print_script();

		global $wp_scripts;
		$data = $wp_scripts->get_data( 'eo_front', 'data' );

		$this->assertContains( '"bp_group":5', $data );
		$this->assertContains( '"bp_displayed_user_id":6', $data );
		$this->assertNotContains( '"foo"', $data );
	}

	public function test_bpeo_get_my_calendar_events_should_contain_friend_events() {
		$users = $this->factory->user->create_many( 3 );

		friends_add_friend( $users[0], $users[1], true );

		$e1 = $this->event_factory->event->create( array(
			'post_author' => $users[1],
		) );

		$e2 = $this->event_factory->event->create( array(
			'post_author' => $users[2],
		) );

		$found = bpeo_get_my_calendar_event_ids( $users[0] );

		$this->assertContains( $e1, $found );
		$this->assertNotContains( $e2, $found );
	}

	/**
	 * @group private
	 */
	public function test_bpeo_get_my_calendar_events_should_contain_public_friend_events() {
		$users = $this->factory->user->create_many( 3 );

		friends_add_friend( $users[0], $users[1], true );

		$e1 = $this->event_factory->event->create( array(
			'post_author' => $users[1],
		) );

		$e2 = $this->event_factory->event->create( array(
			'post_author' => $users[1],
			'post_status' => 'private'
		) );

		$e3 = $this->event_factory->event->create( array(
			'post_author' => $users[2],
		) );

		$found = bpeo_get_my_calendar_event_ids( $users[0] );

		$this->assertContains( $e1, $found );
		$this->assertNotContains( $e2, $found );
		$this->assertNotContains( $e3, $found );
	}

	public function test_bpeo_get_my_calendar_events_should_contain_own_events() {
		$users = $this->factory->user->create_many( 3 );

		$e1 = $this->event_factory->event->create( array(
			'post_author' => $users[1],
		) );

		$e2 = $this->event_factory->event->create( array(
			'post_author' => $users[0],
		) );

		$found = bpeo_get_my_calendar_event_ids( $users[0] );

		$this->assertNotContains( $e1, $found );
		$this->assertContains( $e2, $found );
	}

	public function test_bpeo_get_my_calendar_events_should_contain_group_events() {
		$users = $this->factory->user->create_many( 2 );
		$groups = $this->factory->group->create_many( 2 );

		$this->add_user_to_group( $users[0], $groups[1] );

		$e1 = $this->event_factory->event->create();
		$e2 = $this->event_factory->event->create();

		bpeo_connect_event_to_group( $e1, $groups[0] );
		bpeo_connect_event_to_group( $e2, $groups[1] );

		$found = bpeo_get_my_calendar_event_ids( $users[0] );

		$this->assertNotContains( $e1, $found );
		$this->assertContains( $e2, $found );
	}

	public function test_bp_displayed_user_id_should_be_converted_to_post__in_param() {
		$u = $this->factory->user->create();

		$u_e1 = $this->event_factory->event->create( array( 'post_author' => $u ) );

		$groups = $this->factory->group->create_many( 3 );
		$this->add_user_to_group( $u, $groups[0] );
		$this->add_user_to_group( $u, $groups[1] );

		$g0_e1 = $this->event_factory->event->create();
		$g1_e1 = $this->event_factory->event->create();
		$g1_e2 = $this->event_factory->event->create();
		$g2_e1 = $this->event_factory->event->create();

		bpeo_connect_event_to_group( $g0_e1, $groups[0] );
		bpeo_connect_event_to_group( $g1_e1, $groups[1] );
		bpeo_connect_event_to_group( $g1_e2, $groups[1] );
		bpeo_connect_event_to_group( $g2_e1, $groups[2] );

		$other_users = $this->factory->user->create_many( 3 );
		friends_add_friend( $u, $other_users[0], true );
		friends_add_friend( $u, $other_users[1], true );

		$f0_e1 = $this->event_factory->event->create( array( 'post_author' => $other_users[0] ) );
		$f0_e2 = $this->event_factory->event->create( array( 'post_author' => $other_users[0] ) );
		$f1_e1 = $this->event_factory->event->create( array( 'post_author' => $other_users[1] ) );
		$f2_e1 = $this->event_factory->event->create( array( 'post_author' => $other_users[2] ) );

		// Should find everything but the g2 and f2 events.
		$found = get_posts( array(
			'post_type' => 'event',
			'fields' => 'ids',
			'showpastevents' => true,
			'bp_displayed_user_id' => $u,
			'posts_per_page' => -1,
		) );

		$expected = array( $u_e1, $g0_e1, $g1_e1, $g1_e2, $f0_e1, $f0_e2, $f1_e1 );

		$this->assertEqualSets( $expected, $found );
	}
}
