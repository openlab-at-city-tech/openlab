<?php

namespace OLUR;

class Activity implements Counter {
	use CounterTools;

	public function query( $query ) {
		global $wpdb;

		$bp = buddypress();

		$counts = array();

		$base_where = $wpdb->prepare( "a.component = %s AND a.type = %s AND a.date_recorded >= %s AND a.date_recorded < %s", $query['component'], $query['type'], $this->start, $this->end );
		$counts['events'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->activity->table_name} a WHERE $base_where" );
		$counts['users'] = $wpdb->get_var( "SELECT COUNT(DISTINCT a.user_id) FROM {$bp->activity->table_name} a WHERE $base_where" );

		$member_types = array( 'student', 'faculty', 'staff', 'alumni', 'other' );
		foreach ( $member_types as $mt ) {
			$mt_where = $this->get_member_type_where_clause( $mt );
			$counts[ $mt ] = $wpdb->get_var( "SELECT COUNT(DISTINCT a.user_id) FROM {$bp->activity->table_name} a JOIN {$bp->profile->table_name_data} xp ON xp.user_id = a.user_id WHERE $base_where $mt_where" );
		}

		$group_types = array( 'course', 'club', 'project' );

		if ( 'blogs' === $query['component'] ) {
			$gt_base = "SELECT COUNT(DISTINCT a.item_id) FROM {$bp->activity->table_name} a JOIN {$bp->groups->table_name_groupmeta} gm ON a.item_id = gm.meta_value JOIN {$bp->groups->table_name_groupmeta} gm2 ON gm.group_id = gm2.group_id WHERE $base_where AND gm.meta_key = 'wds_bp_group_site_id' AND gm2.meta_key = 'wds_group_type'";

			$counts['items'] = $wpdb->get_var( "$gt_base AND gm2.meta_value IN ('course','club','project')" );
			foreach ( $group_types as $gt ) {
				$counts[ $gt ] = $wpdb->get_var( $wpdb->prepare( "$gt_base AND gm2.meta_value = %s", $gt ) );
			}
		} elseif ( 'groups' === $query['component'] ) {
			$gt_base = "SELECT COUNT(DISTINCT a.item_id) FROM {$bp->activity->table_name} a JOIN {$bp->groups->table_name_groupmeta} gm ON a.item_id = gm.group_id WHERE $base_where AND gm.meta_key = 'wds_group_type'";

			$counts['items'] = $wpdb->get_var( "$gt_base AND gm.meta_value IN ('course','club','project')" );
			foreach ( $group_types as $gt ) {
				$counts[ $gt ] = $wpdb->get_var( $wpdb->prepare( "$gt_base AND gm.meta_value = %s", $gt ) );
			}
		}

		$this->counts = array_map( 'intval', $counts );
		return $this->counts;
	}

	protected function get_member_type_where_clause( $member_type ) {
		global $wpdb;

		$clause = '';
		if ( 'total' !== $member_type ) {
			$clause = $wpdb->prepare( " AND field_id = 7 AND value = %s", ucwords( $member_type ) );
		}

		return $clause;
	}

	protected function get_bloggroup_type_where_clause( $group_type ) {
		global $wpdb;

		$clause = '';
		if ( 'total' !== $group_type ) {
			$clause = $wpdb->prepare( " AND field_id = 7 AND value = %s", ucwords( $group_type ) );
		}

		return $clause;
	}
}
