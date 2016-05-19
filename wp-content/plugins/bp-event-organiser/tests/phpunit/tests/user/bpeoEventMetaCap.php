<?php

/**
 * @group user
 * @group cap
 */
class BPEO_Tests_User_BpeoEventMetaCap extends BPEO_UnitTestCase {
	protected $current_user;
	protected $user;

	public function setUp() {
		parent::setUp();
		$this->current_user = bp_loggedin_user_id();
	}

	public function tearDown() {
		$this->set_current_user( $this->current_user );
	}

	public function test_non_loggedin_user_can_read_public_event() {
		$e = $this->event_factory->event->create( array(
			'post_status' => 'publish',
		) );

		$this->set_current_user( 0 );
		$this->assertTrue( current_user_can( 'read_event', $e ) );
	}

	public function test_non_loggedin_user_cannot_read_private_event() {
		$e = $this->event_factory->event->create( array(
			'post_status' => 'private',
		) );

		$this->set_current_user( 0 );
		$this->assertFalse( current_user_can( 'read_event', $e ) );
	}

	public function test_non_loggedin_user_can_publish_events() {
		$this->set_current_user( 0 );
		$this->assertFalse( current_user_can( 'publish_events' ) );
	}

	public function test_loggedin_user_can_publish_events() {
		$this->user = $this->factory->user->create();
		$this->set_current_user( $this->user );

		$this->assertTrue( current_user_can( 'publish_events' ) );
	}

	public function test_loggedin_user_with_no_role_can_publish_events() {
		$this->user = $this->factory->user->create();

		// remove default role
		$user = new WP_User( $this->user );
		$user->remove_role( 'subscriber' );

		$this->set_current_user( $this->user );

		$this->assertTrue( current_user_can( 'publish_events' ) );
	}

	public function test_loggedin_user_can_edit_own_event() {
		$this->user = $this->factory->user->create();

		$this->set_current_user( $this->user );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $this->user,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$this->assertTrue( current_user_can( 'edit_event', $e ) );
	}

	public function test_loggedin_user_with_no_role_can_edit_own_event() {
		$this->user = $this->factory->user->create();

		// remove default role
		$user = new WP_User( $this->user );
		$user->remove_role( 'subscriber' );

		$this->set_current_user( $this->user );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $this->user,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$this->assertTrue( current_user_can( 'edit_event', $e ) );
	}

	public function test_loggedin_user_can_delete_own_event() {
		$this->user = $this->factory->user->create();

		$this->set_current_user( $this->user );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $this->user,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$this->assertTrue( current_user_can( 'delete_event', $e ) );
	}

	public function test_loggedin_user_with_no_role_can_delete_own_event() {
		$this->user = $this->factory->user->create();

		// remove default role
		$user = new WP_User( $this->user );
		$user->remove_role( 'subscriber' );

		$this->set_current_user( $this->user );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $this->user,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$this->assertTrue( current_user_can( 'delete_event', $e ) );
	}

	public function test_loggedin_user_cannot_delete_someone_elses_event() {
		$this->user = $this->factory->user->create();
		$u = $this->factory->user->create();

		$this->set_current_user( $this->user );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$this->assertFalse( current_user_can( 'delete_event', $e ) );
	}

	public function test_loggedin_user_with_no_role_cannot_delete_someone_elses_event() {
		$this->user = $this->factory->user->create();
		$u = $this->factory->user->create();

		// remove default role
		$user = new WP_User( $this->user );
		$user->remove_role( 'subscriber' );

		$this->set_current_user( $this->user );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$this->assertFalse( current_user_can( 'delete_event', $e ) );
	}
}
