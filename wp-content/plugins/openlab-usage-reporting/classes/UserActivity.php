<?php

namespace OLUR;

class UserActivity implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$bp = buddypress();

		$mt_join = '';
		$mt_where = $this->get_member_type_where_clause( $query['member_type'] );
		if ( $mt_where ) {
			$mt_join = " JOIN {$bp->profile->table_name_data} xp ON xp.user_id = a.user_id ";
		}

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->activity->table_name} a $mt_join WHERE a.component = %s AND a.type = %s AND a.date_recorded >= %s AND a.date_recorded < %s $mt_where", $query['component'], $query['type'], $this->start, $this->end ) );

		$this->counts = array( intval( $count ) );
		return $this->counts;
	}
}
