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

		$join  = [];
		$where = [];

		if ( 'total' !== $query['type'] ) {
			$ut_term = get_term_by( 'slug', $query['type'], 'bp_member_type' );
			if ( $ut_term ) {
				$join[]  = "JOIN {$wpdb->term_relationships} tr ON (tr.object_id = f.initiator_user_id)";
				$where[] = $wpdb->prepare( "tr.term_taxonomy_id = %d", $ut_term->term_taxonomy_id );
			}
		}

		$where[] = $wpdb->prepare( "date_created >= %s AND date_created <= %s", $this->start, $this->end );

		foreach ( $counts as $count_type => &$count ) {
			$type_join  = $join;
			$type_where = $where;

			if ( 'total' !== $count_type ) {
				$friend_mt_term = get_term_by( 'slug', $count_type, 'bp_member_type' );

				if ( $friend_mt_term ) {
					$type_join[]    = "JOIN {$wpdb->term_relationships} tr2 ON (tr2.object_id = f.friend_user_id)";
					$type_where[]   = $wpdb->prepare( "tr2.term_taxonomy_id = %d", $friend_mt_term->term_taxonomy_id );
				}
			}

			$mt_base_sql = "SELECT COUNT(*) FROM {$bp->friends->table_name} f " . implode( ' ', $type_join ) . " WHERE " . implode( ' AND ', $type_where );

			$confirmed = $wpdb->get_var( "$mt_base_sql AND is_confirmed = 1" );
			$pending   = $wpdb->get_var( "$mt_base_sql AND is_confirmed = 0" );

			$count = sprintf( '%d | %d', $confirmed, $pending );
		}

		$this->counts = $counts;
		return $this->counts;
	}
}
