<?php

namespace OLUR;

class Group implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$retval = array(
			'results' => array(),
			'label' => $this->get_label( $query ),
		);

		$group_type = $query['type'];
		$group_status = $query['status'];

		$counts = array(
			'start'   => '',
			'created' => '',
			'deleted' => '',
			'end'     => '',
		);

		$bp = buddypress();
		$gt_subquery = $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = %s", $group_type );

		$statuses = array();
		foreach ( (array) $group_status as $status ) {
			$statuses[] = $wpdb->prepare( '%s', $status );
		}
		$status_sql = implode( ', ', $statuses );

		// Start
		$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) AND status IN ({$status_sql}) AND date_created < %s", $this->start ) );

		// End
		$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) AND status IN ({$status_sql}) AND date_created < %s", $this->end ) );

		// Created
		$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) AND status IN ({$status_sql}) AND date_created >= %s AND date_created < %s", $this->start, $this->end ) );

		$retval['results'] = array_map( 'intval', $counts );

		return $retval;
	}

	public function get_label( $query ) {
		return sprintf(
			'group_%s_%s_counts',
			$query['type'],
			$query['status']
		);
	}
}
