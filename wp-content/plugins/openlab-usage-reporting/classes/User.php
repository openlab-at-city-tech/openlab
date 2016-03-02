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
			'activea' => '',
			'activep' => 'N/A',
		);

		$bp = buddypress();

		$ut_subquery = "SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = 7 AND value IN ({$user_type_in})";

		// Start
		$counts['start'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} u WHERE u.deleted != 1 AND u.spam != 1 AND u.ID IN ({$ut_subquery}) AND u.user_registered < %s", $this->start ) );

		// End
		$counts['end'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} u WHERE u.deleted != 1 AND u.spam != 1 AND u.ID IN ({$ut_subquery}) AND u.user_registered < %s", $this->end ) );

		// Created
		$counts['created'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} u WHERE u.deleted != 1 AND u.spam != 1 AND u.ID IN ({$ut_subquery}) AND u.user_registered >= %s AND u.user_registered < %s", $this->start, $this->end ) );

		// Active
		$counts['activea'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT u.ID) FROM {$wpdb->users} u JOIN {$bp->activity->table_name} a ON a.user_id = u.ID WHERE u.deleted != 1 AND u.spam != 1 AND u.ID IN ({$ut_subquery}) AND ( a.component != 'members' OR a.type != 'last_activity' ) AND a.date_recorded >= %s AND a.date_recorded <= %s", $this->start, $this->end ) );

		// Passively active (last_activity). Only count if `$end` is today.
		$end_day = date( 'Y-m-d', strtotime( $this->end ) );
		$today   = date( 'Y-m-d' );
		if ( $end_day === $today ) {
			$counts['activep'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT u.ID) FROM {$wpdb->users} u JOIN {$bp->activity->table_name} a ON a.user_id = u.ID WHERE u.deleted != 1 AND u.spam != 1 AND u.ID IN ({$ut_subquery}) AND a.date_recorded >= %s", $this->start, $this->end ) );
		}

		$this->counts = $counts;

		return $this->counts;
	}
}
