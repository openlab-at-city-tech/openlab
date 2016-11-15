<?php

/**
 * @group group
 * @group cap
 */
class BPEO_Tests_Group_BpeoEventMetaCap extends BPEO_UnitTestCase {
	protected $current_user;
	protected $user;

	public function setUp() {
		parent::setUp();
		$this->current_user = bp_loggedin_user_id();
	}

	public function tearDown() {
		$this->set_current_user( $this->current_user );
	}

	public function test_loggedin_non_group_member_can_read_public_event() {
		$e = $this->event_factory->event->create( array(
			'post_status' => 'public',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->user = $this->factory->user->create();
		$this->set_current_user( $this->user );

		$this->assertTrue( current_user_can( 'read_event', $e ) );
	}

	public function test_loggedin_non_group_member_cannot_read_private_event() {
		$e = $this->event_factory->event->create( array(
			'post_status' => 'private',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->user = $this->factory->user->create();
		$this->set_current_user( $this->user );

		$this->assertFalse( current_user_can( 'read_event', $e ) );
	}

	public function test_loggedin_group_member_can_read_public_event() {
		$e = $this->event_factory->event->create( array(
			'post_status' => 'public',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->user = $this->factory->user->create();
		$this->set_current_user( $this->user );
		$this->add_user_to_group( $this->user, $g );

		$this->assertTrue( current_user_can( 'read_event', $e ) );
	}

	public function test_loggedin_group_member_can_read_private_event_when_member_of_at_least_one_connected_group() {
		$e = $this->event_factory->event->create( array(
			'post_status' => 'private',
		) );

		$groups = $this->factory->group->create_many( 2 );
		bpeo_connect_event_to_group( $e, $groups[0] );
		bpeo_connect_event_to_group( $e, $groups[1] );

		$this->user = $this->factory->user->create();
		$this->set_current_user( $this->user );
		$this->add_user_to_group( $this->user, $groups[1] );

		$this->assertTrue( current_user_can( 'read_event', $e ) );
	}

	public function test_loggedin_group_member_can_edit_own_event() {
		$this->user = $this->factory->user->create();

		$e = $this->event_factory->event->create( array(
			'post_author' => $this->user,
			'post_status' => 'public',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->set_current_user( $this->user );
		$this->add_user_to_group( $this->user, $g );

		$this->assertTrue( current_user_can( 'edit_event', $e ) );
	}

	public function test_loggedin_group_member_cannot_edit_another_group_event() {
		$this->user = $this->factory->user->create();
		$u = $this->factory->user->create();

		$e = $this->event_factory->event->create( array(
			'post_author' => $u,
			'post_status' => 'public',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->set_current_user( $this->user );
		$this->add_user_to_group( $this->user, $g );
		$this->add_user_to_group( $u, $g );

		$this->assertFalse( current_user_can( 'edit_event', $e ) );
	}

	public function test_loggedin_group_member_can_delete_own_event() {
		$this->user = $this->factory->user->create();

		$e = $this->event_factory->event->create( array(
			'post_author' => $this->user,
			'post_status' => 'public',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->set_current_user( $this->user );
		$this->add_user_to_group( $this->user, $g );

		$this->assertTrue( current_user_can( 'delete_event', $e ) );
	}

	public function test_loggedin_group_member_cannot_delete_another_group_event() {
		$this->user = $this->factory->user->create();
		$u = $this->factory->user->create();

		$e = $this->event_factory->event->create( array(
			'post_author' => $u,
			'post_status' => 'public',
		) );

		$g = $this->factory->group->create();
		bpeo_connect_event_to_group( $e, $g );

		$this->set_current_user( $this->user );
		$this->add_user_to_group( $this->user, $g );
		$this->add_user_to_group( $u, $g );

		$this->assertFalse( current_user_can( 'delete_event', $e ) );
	}
}
