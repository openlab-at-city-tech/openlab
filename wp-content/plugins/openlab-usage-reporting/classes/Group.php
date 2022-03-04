<?php

namespace OLUR;

class Group implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$group_type   = $query['type'];
		$group_status = $query['status'];

		$counts = array(
			'start'   => '',
			'created' => '',
			'end'     => '',
			'activea' => '',
			'activep' => 'N/A',
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
		$counts['start'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) {$status_sql} AND date_created < %s", $this->start ) );

		// End
		$counts['end'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) {$status_sql} AND date_created < %s", $this->end ) );

		// Created
		$counts['created'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) {$status_sql} AND date_created >= %s AND date_created < %s", $this->start, $this->end ) );

		// Active
		$counts['activea'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT g.id) FROM {$bp->groups->table_name} g JOIN {$bp->activity->table_name} a ON a.item_id = g.id WHERE a.component = 'groups' AND g.id IN ({$gt_subquery}) {$status_sql} AND a.date_recorded >= %s AND a.date_recorded <= %s", $this->start, $this->end ) );

		$this->counts = $counts;
		return $this->counts;
	}
}
