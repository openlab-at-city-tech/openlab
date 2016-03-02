<?php

namespace OLUR;

class User implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$user_type = $query['type'];
                if ( 'total' === $user_type ) {
                        $user_type = array( 'Student', 'Faculty', 'Staff', 'Alumni', 'Other' );
                } else {
                        $user_type = (array) $query['type'];
                }

                foreach ( $user_type as &$u ) {
                        $u = $wpdb->prepare( '%s', $u );
                }
                $user_type_in = implode( ',', $user_type );

		$counts = array(
			'start'   => '',
			'created' => '',
			'end'     => '',
		);

		$bp = buddypress();

		$ut_subquery = "SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = 7 AND value IN ({$user_type_in})";

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
