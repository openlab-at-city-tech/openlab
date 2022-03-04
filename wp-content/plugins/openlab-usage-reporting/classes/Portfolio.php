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
			'activea' => '',
			'activep' => 'N/A',
		);

		$bp = buddypress();
		$gt_subquery = "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = 'portfolio'";
		$ut_subquery = $wpdb->prepare( "SELECT p.user_id FROM {$bp->profile->table_name_data} p WHERE p.field_id = 7 AND p.value = %s", ucwords( $user_type ) );

		$portfolios_of_user_type_subquery = "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'portfolio_group_id' AND a.user_id IN (" . $ut_subquery . ")";

		$status_join  = '';
		$status_where = '';
		if ( $group_status && 'any' !== $group_status ) {
			$statuses = array();
			foreach ( (array) $group_status as $status ) {
				$statuses[] = $wpdb->prepare( '%s', $status );
			}
			$status_join  = "JOIN {$bp->groups->table_name} g ON ( g.id = a.item_id )";
			$status_where = "AND a.item_id IN (SELECT id FROM {$bp->groups->table_name} WHERE status IN ( " . implode( ', ', $statuses ) . " ))";
		}

		// Start
		$counts['start'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g {$status_join} WHERE g.id IN ({$gt_subquery}) AND g.id IN ({$portfolios_of_user_type_subquery}) {$status_where} AND g.date_created < %s", $this->start ) );

		// End
		$counts['end'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g WHERE g.id IN ({$gt_subquery}) AND g.id IN ({$portfolios_of_user_type_subquery}) {$status_where} AND g.date_created < %s", $this->end ) );

		// Created
		$counts['created'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g WHERE g.id IN ({$gt_subquery}) AND g.id IN ({$portfolios_of_user_type_subquery}) {$status_where} AND g.date_created >= %s AND g.date_created < %s", $this->start, $this->end ) );

		// Active
		$counts['activea'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT a.item_id) FROM {$bp->activity->table_name} a WHERE a.component = 'groups' AND a.item_id IN ({$gt_subquery}) AND a.item_id IN ({$portfolios_of_user_type_subquery}) {$status_where} AND a.date_recorded >= %s AND a.date_recorded < %s", $this->start, $this->end ) );

		_b( $wpdb->prepare( "SELECT COUNT(DISTINCT a.item_id) FROM {$bp->activity->table_name} a WHERE a.component = 'groups' AND a.item_id IN ({$gt_subquery}) AND a.item_id IN ({$portfolios_of_user_type_subquery}) {$status_where} AND a.date_recorded >= %s AND a.date_recorded < %s", $this->start, $this->end ) );




		$this->counts = $counts;
		return $this->counts;
	}
}
