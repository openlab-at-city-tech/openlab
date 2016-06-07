<?php

namespace OLUR;

class Friend implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$bp = buddypress();

		$counts = array(
			'student' => '',
			'faculty' => '',
			'staff' => '',
			'alumni' => '',
			'other' => '',
			'total' => '',
		);

		$all_member_types = array( 'student', 'faculty', 'staff', 'alumni', 'other' );
		if ( 'total' === $query['type'] ) {
			$member_types = $all_member_types;
		} else {
			$member_types = (array) $query['type'];
		}

		$initiator_mt_where = $this->get_member_type_where_clause( $member_types );

		$base_sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->friends->table_name} WHERE initiator_user_id IN ( SELECT user_id FROM {$bp->profile->table_name_data} xp WHERE {$initiator_mt_where} ) AND date_created >= %s AND date_created <= %s", $this->start, $this->end );

		foreach ( $counts as $count_type => &$count ) {
			$friend_mt_where = $this->get_member_type_where_clause( $count_type );
			$mt_base_sql = "$base_sql AND friend_user_id IN ( SELECT user_id FROM {$bp->profile->table_name_data} xp WHERE {$friend_mt_where} )";

			$confirmed = $wpdb->get_var( "$mt_base_sql AND is_confirmed = 1" );
			$pending   = $wpdb->get_var( "$mt_base_sql AND is_confirmed = 0" );

			$count = sprintf( '%d | %d', $confirmed, $pending );
		}

		$this->counts = $counts;
		return $this->counts;
	}

	protected function get_member_type_where_clause( $member_type ) {
		global $wpdb;

		$all_member_types = array( 'student', 'faculty', 'staff', 'alumni', 'other' );
		if ( 'total' === $member_type ) {
			$member_types = $all_member_types;
		} else {
			$member_types = (array) $member_type;
		}

		$clause = '';
		foreach ( $member_types as &$mt ) {
			$mt = $wpdb->prepare( '%s', ucwords( $mt ) );
		}
		$member_types = implode( ',', $member_types );

		$clause = "xp.field_id = 7 AND xp.value IN ({$member_types})";

		return $clause;
	}
}
