<?php

namespace OLUR;

class User implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		// @todo "total"
		$user_type = $query['type'];

		$counts = array(
			'start'   => '',
			'created' => '',
			'end'     => '',
		);

		$bp = buddypress();
		$ut_subquery = $wpdb->prepare( "SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = 7 AND value = %s", $user_type );

		// Start
		$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID IN ({$ut_subquery}) AND user_registered < %s", $this->start ) );

		// End
		$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID IN ({$ut_subquery}) AND user_registered < %s", $this->end ) );

		// Created
		$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID IN ({$ut_subquery}) AND user_registered >= %s AND user_registered < %s", $this->start, $this->end ) );

		$this->counts = array_map( 'intval', $counts );
		return $this->counts;
	}
}
