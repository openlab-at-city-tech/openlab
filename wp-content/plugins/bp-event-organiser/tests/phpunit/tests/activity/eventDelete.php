<?php

/**
 * @group activity
 */
class BPEO_Tests_Activity_EventDelete extends BPEO_UnitTestCase {
	public function test_delete_event_not_connected_to_group() {
		$u = $this->factory->user->create();

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		wp_delete_post( $e );

		$a = bpeo_get_activity_by_event_id( $e );
		$d = wp_list_filter( $a, array( 'type' => 'bpeo_delete_event' ) );
		$this->assertNotEmpty( $d );
	}

	/**
	 * @group bbg
	 */
	public function test_delete_event_connected_to_groups() {
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

		wp_delete_post( $e );

		$a = bpeo_get_activity_by_event_id( $e );
		$a = wp_list_filter( $a, array( 'type' => 'bpeo_delete_event' ) );
		$a = array_values( $a );

		$this->assertNotEmpty( $a );

		// User item.
		$this->assertEquals( $u, $a[0]->user_id );
		$this->assertEquals( 'events', $a[0]->component );
		$this->assertEquals( 'bpeo_delete_event', $a[0]->type );
		$this->assertEquals( $e, $a[0]->secondary_item_id );

		// Group item.
		$this->assertEquals( $u, $a[1]->user_id );
		$this->assertEquals( 'groups', $a[1]->component );
		$this->assertEquals( 'bpeo_delete_event', $a[1]->type );
		$this->assertEquals( $this->groups[0], $a[1]->item_id );
		$this->assertEquals( $e, $a[1]->secondary_item_id );
		$this->assertEquals( 1, $a[1]->hide_sitewide );

		// Group item.
		$this->assertEquals( $u, $a[2]->user_id );
		$this->assertEquals( 'groups', $a[2]->component );
		$this->assertEquals( 'bpeo_delete_event', $a[2]->type );
		$this->assertEquals( $this->groups[2], $a[2]->item_id );
		$this->assertEquals( $e, $a[2]->secondary_item_id );
		$this->assertEquals( 1, $a[2]->hide_sitewide );
	}

	public function connect_events( $e ) {
		bpeo_connect_event_to_group( $e, $this->groups[0] );
		bpeo_connect_event_to_group( $e, $this->groups[2] );
	}
}
