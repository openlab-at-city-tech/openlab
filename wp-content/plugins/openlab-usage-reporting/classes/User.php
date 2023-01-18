<?php

namespace OLUR;

class User implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$bp = buddypress();

		$user_type = $query['type'];

		$counts = array(
			'start'   => '',
			'created' => '',
			'end'     => '',
			'activea' => '',
			'activep' => 'N/A',
		);

		// Start
		$ut_term = get_term_by( 'slug', $user_type, 'bp_member_type' );
		if ( $ut_term ) {
			$ut_clause = $wpdb->prepare( "AND term_taxonomy_id = %d", $ut_term->term_taxonomy_id );
		} elseif ( $user_type === 'other' ) {
			$known_terms = [
				'student' => get_term_by( 'slug', 'student', 'bp_member_type' ),
				'faculty' => get_term_by( 'slug', 'faculty', 'bp_member_type' ),
				'staff'   => get_term_by( 'slug', 'staff', 'bp_member_type' ),
				'alumni'  => get_term_by( 'slug', 'alumni', 'bp_member_type' ),
			];

			$known_term_ids = wp_list_pluck( $known_terms, 'term_taxonomy_id' );

			$ut_clause = "AND term_taxonomy_id NOT IN (" . implode( ',', array_map( 'intval', $known_term_ids ) ) . ")";
		} else {
			$ut_clause = '';
		}

		$counts['start'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} u JOIN {$wpdb->term_relationships} tr ON ( u.ID = tr.object_id ) WHERE u.deleted != 1 AND u.spam != 1 {$ut_clause} AND u.user_registered < %s", $this->start ) );

		// End
		$counts['end'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} u JOIN {$wpdb->term_relationships} tr ON ( u.ID = tr.object_id ) WHERE u.deleted != 1 AND u.spam != 1 {$ut_clause} AND u.user_registered < %s", $this->end ) );

		// Created
		$counts['created'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} u JOIN {$wpdb->term_relationships} tr ON ( u.ID = tr.object_id ) WHERE u.deleted != 1 AND u.spam != 1 {$ut_clause} AND u.user_registered >= %s AND u.user_registered < %s", $this->start, $this->end ) );

		// Active
		$counts['activea'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT u.ID) FROM {$wpdb->users} u JOIN {$bp->activity->table_name} a ON a.user_id = u.ID JOIN {$wpdb->term_relationships} tr ON ( u.ID = tr.object_id ) WHERE u.deleted != 1 AND u.spam != 1 {$ut_clause} AND ( a.component != 'members' OR a.type != 'last_activity' ) AND a.date_recorded >= %s AND a.date_recorded <= %s", $this->start, $this->end ) );

		// Passively active (last_activity). Only count if `$end` is today.
		$end_day = date( 'Y-m-d', strtotime( $this->end ) );
		$today   = date( 'Y-m-d' );
		if ( $end_day === $today ) {
			$counts['activep'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT u.ID) FROM {$wpdb->users} u JOIN {$bp->activity->table_name} a ON a.user_id = u.ID JOIN {$wpdb->term_relationships} tr ON ( u.ID = tr.object_id ) WHERE u.deleted != 1 AND u.spam != 1 {$ut_clause} AND a.date_recorded >= %s", $this->start ) );
		}

		$this->counts = $counts;

		return $this->counts;
	}
}
