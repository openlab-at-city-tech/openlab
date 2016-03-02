<?php

namespace OLUR;

class Group implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$group_type = $query['type'];
                $group_status = $query['status'];

		$counts = array(
			'start'   => '',
			'created' => '',
			'end'     => '',
		);

		$bp = buddypress();
		$gt_subquery = $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = %s", $group_type );

		$status_sql = '';
		if ( $group_status && 'any' !== $group_status ) {
			$statuses = array();
			foreach ( (array) $group_status as $status ) {
				$statuses[] = $wpdb->prepare( '%s', $status );
			}
			$status_sql = "AND status IN ( " . implode( ', ', $statuses ) . " )";
		}

		// Start
		$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) {$status_sql} AND date_created < %s", $this->start ) );

		// End
		$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) {$status_sql} AND date_created < %s", $this->end ) );

		// Created
		$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) {$status_sql} AND date_created >= %s AND date_created < %s", $this->start, $this->end ) );

		$this->counts = array_map( 'intval', $counts );
		return $this->counts;
	}
}
