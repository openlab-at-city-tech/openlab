<?php

namespace OLUR;

class Portfolio implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$user_type = $query['type'];
		$group_status = $query['status'];

		$counts = array(
			'start'   => '',
			'created' => '',
			'end'     => '',
		);

		$bp = buddypress();
		$gt_subquery = "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = 'portfolio'";
		$ut_subquery = $wpdb->prepare( "SELECT p.user_id FROM {$bp->profile->table_name_data} p WHERE p.field_id = 7 AND p.value = %s", ucwords( $user_type ) );

		$statuses = array();
		foreach ( (array) $group_status as $status ) {
			$statuses[] = $wpdb->prepare( '%s', $status );
		}
		$status_sql = implode( ', ', $statuses );

		// Start
		$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g INNER JOIN {$wpdb->usermeta} um ON (g.id = um.meta_value AND um.meta_key = 'portfolio_group_id') WHERE g.id IN ({$gt_subquery}) AND um.user_id IN ({$ut_subquery}) AND g.status IN ({$status_sql}) AND g.date_created < %s", $this->start ) );

		// End
		$counts['end']   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g INNER JOIN {$wpdb->usermeta} um ON (g.id = um.meta_value AND um.meta_key = 'portfolio_group_id') WHERE g.id IN ({$gt_subquery}) AND um.user_id IN ({$ut_subquery}) AND g.status IN ({$status_sql}) AND g.date_created < %s", $this->end ) );

		// Created
		$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g INNER JOIN {$wpdb->usermeta} um ON (g.id = um.user_id AND um.meta_key = 'portfolio_group_id') WHERE g.id IN ({$gt_subquery}) AND um.user_id IN ({$ut_subquery}) AND g.status IN ({$status_sql}) AND g.date_created >= %s AND g.date_created < %s", $this->start, $this->end ) );

		$this->counts = array_map( 'intval', $counts );
		return $this->counts;
	}
}
