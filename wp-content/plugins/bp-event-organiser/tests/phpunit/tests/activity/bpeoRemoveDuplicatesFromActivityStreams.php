<?php

/**
 * @group activity
 */
class BPEO_Tests_Activity_BpeoRemoveDuplicatesFromActivityStream extends BPEO_UnitTestCase {
	static $activities = array();
	static $events = array();
	static $groups = array();
	static $users = array();

	public static function setUpBeforeClass() {
		$remote_addr = null;
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$remote_addr = $_SERVER['REMOTE_ADDR'];
		}

		$_SERVER['REMOTE_ADDR'] = '';

		$bpf = new BP_UnitTest_Factory();
		$eof = new EO_UnitTest_Factory();
		$now = time();

		for ( $i = 0; $i <= 4; $i++ ) {
			self::$activities[] = $bpf->activity->create( array(
				'recorded_time' => date( 'Y-m-d H:i:s', $now - 60*60*$i ),
			) );
		}

		self::$users[] = $bpf->user->create();
		self::$users[] = $bpf->user->create();
		self::$groups = $bpf->group->create_many( 3, array(
			'creator_id' => self::$users[1],
		) );

		// $events[0] is connected only to $groups[0].
		add_action( 'save_post', array( __CLASS__, 'connect_events_to_group_0' ), 15 );
		self::$events[] = $eof->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*5 ),
			'post_author' => self::$users[0],
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
		) );
		remove_action( 'save_post', array( __CLASS__, 'connect_events_to_group_0' ), 15 );

		self::$events[] = $eof->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*6 ),
			'post_author' => self::$users[0],
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
		) );

		// $events[2] is connected to $groups[0] and $groups[2].
		add_action( 'save_post', array( __CLASS__, 'connect_events_to_groups_0_and_2' ), 15 );
		self::$events[] = $eof->event->create( array(
			'post_date' => date( 'Y-m-d H:i:s', $now - 60*60*7 ),
			'post_author' => self::$users[0],
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
		) );
		remove_action( 'save_post', array( __CLASS__, 'connect_events_to_groups_0_and_2' ), 15 );

		for ( $i = 7; $i <= 11; $i++ ) {
			self::$activities[] = $bpf->activity->create( array(
				'recorded_time' => date( 'Y-m-d H:i:s', $now - 60*60*$i ),
			) );
		}

		self::commit_transaction();

		if ( is_null( $remote_addr ) ) {
			unset( $_SERVER['REMOTE_ADDR'] );
		} else {
			$_SERVER['REMOTE_ADDR'] = $remote_addr;
		}
	}

	public static function tearDownAfterClass() {
		global $wpdb, $bp;
		$aids = $wpdb->get_col( "SELECT id FROM {$bp->activity->table_name}" );
		foreach ( $aids as $activity ) {
			bp_activity_delete_by_activity_id( $activity );
		}

		foreach ( self::$events as $event ) {
			wp_delete_post( $event, true );
		}

		foreach ( self::$groups as $group ) {
			groups_delete_group( $group );
		}

		foreach ( self::$users as $user ) {
			if ( is_multisite() ) {
				wpmu_delete_user( $user );
			} else {
				wp_delete_user( $user );
			}
		}

		self::commit_transaction();
	}

	public static function connect_events_to_group_0( $e ) {
		bpeo_connect_event_to_group( $e, self::$groups[0] );
	}

	public static function connect_events_to_groups_0_and_2( $e ) {
		bpeo_connect_event_to_group( $e, self::$groups[0] );
		bpeo_connect_event_to_group( $e, self::$groups[2] );
	}

	public function test_no_activity_should_be_removed_when_none_is_related_to_events() {
		$found = bp_activity_get( array(
			'page' => 1,
			'per_page' => 3,
		) );

		$expected = array( self::$activities[0], self::$activities[1], self::$activities[2] );
		$this->assertEquals( $expected, wp_list_pluck( $found['activities'], 'id' ) );
	}

	public function test_duplicate_activity_should_be_removed_from_user_stream() {
		bp_has_activities( array(
			'page' => 1,
			'per_page' => 5,
			'user_id' => self::$users[0],
			'show_hidden' => true,
		) );

		$found = $GLOBALS['activities_template'];
		unset( $GLOBALS['activities_template'] );

		foreach ( $found->activities as $f ) {
			$this->assertSame( 'events', $f->component );
		}

		$this->assertEquals( self::$events, wp_list_pluck( $found->activities, 'secondary_item_id' ) );
	}

	public function test_duplicate_activity_should_be_removed_from_group_stream() {
		bp_has_activities( array(
			'page' => 1,
			'per_page' => 5,
			'object' => buddypress()->groups->id,
			'primary_id' => self::$groups[0],
			'show_hidden' => true,
		) );

		$found = $GLOBALS['activities_template'];
		unset( $GLOBALS['activities_template'] );

		$expected = array( self::$events[0], self::$events[2] );
		$this->assertEquals( $expected, wp_list_pluck( $found->activities, 'secondary_item_id' ) );
	}

	/**
	 * This will happen rarely?
	 */
	public function test_duplicate_activity_should_be_removed_from_my_groups_feed() {
		bp_has_activities( array(
			'page' => 1,
			'per_page' => 6,
			'action' => 'bpeo_create_event',
			'show_hidden' => true,
		) );

		$found = $GLOBALS['activities_template'];
		unset( $GLOBALS['activities_template'] );

		$this->assertSame( 3, count( $found->activities ) );
		$this->assertEquals( self::$events, wp_list_pluck( $found->activities, 'secondary_item_id' ) );
	}

	public function test_slots_left_by_removed_duplicates_should_be_backfilled() {
		bp_has_activities( array(
			'page' => 1,
			'per_page' => 13,
			'show_hidden' => true,
		) );

		$found = $GLOBALS['activities_template'];
		unset( $GLOBALS['activities_template'] );

		$this->assertSame( 13, count( $found->activities ) );

		// Make sure we've backfilled the correct ones.
		$expected_last = array( self::$activities[7], self::$activities[8], self::$activities[9] );
		$found_last = array_slice( $found->activities, -3 );
		$this->assertEquals( $expected_last, wp_list_pluck( $found_last, 'id' ) );
	}
	// What activity is posted when an event is added to a group after it's been created? Just 'edited'?
}
