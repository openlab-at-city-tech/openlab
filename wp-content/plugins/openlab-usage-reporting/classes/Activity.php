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

		$member_types = array( 'student', 'faculty', 'staff', 'alumni', 'other' );
		$mt_where = $this->get_member_type_where_clause( $member_types );
		$counts['users'] = $wpdb->get_var( "SELECT COUNT( DISTINCT a.user_id ) FROM {$bp->activity->table_name} a JOIN {$bp->profile->table_name_data} xp ON xp.user_id = a.user_id WHERE $base_where $mt_where" );

		foreach ( $member_types as $mt ) {
			$mt_where = $this->get_member_type_where_clause( $mt );
			$counts[ $mt ] = $wpdb->get_var( "SELECT COUNT(DISTINCT a.user_id) FROM {$bp->activity->table_name} a JOIN {$bp->profile->table_name_data} xp ON xp.user_id = a.user_id WHERE $base_where $mt_where" );
		}

		$group_types = array( 'course', 'club', 'project', 'eportfolio', 'portfolio' );

		// Heaven help us.
		if ( 'groups' === $query['component'] ) {
			$gt_base = "SELECT COUNT(DISTINCT a.item_id) FROM {$bp->activity->table_name} a JOIN {$bp->groups->table_name_groupmeta} gm ON a.item_id = gm.group_id WHERE $base_where AND gm.meta_key = 'wds_group_type'";

			$counts['items'] = $wpdb->get_var( "$gt_base AND gm.meta_value IN ('course','club','project','portfolio')" );
			foreach ( $group_types as $gt ) {
				$sql = $wpdb->prepare( "$gt_base AND gm.meta_value = %s", $gt );
				if ( 'portfolio' === $gt ) {
					$mt_where = $this->get_member_type_where_clause( array( 'faculty', 'staff' ) );
					$sql = str_replace( 'WHERE', "JOIN {$wpdb->usermeta} um ON um.meta_value = a.item_id JOIN {$bp->profile->table_name_data} xp ON um.user_id = xp.user_id WHERE um.meta_key = 'portfolio_group_id' AND", $sql );
					$sql = "$sql $mt_where";
				} elseif ( 'eportfolio' === $gt ) {
					$mt_where = $this->get_member_type_where_clause( array( 'student', 'alumni' ) );
					$sql = str_replace( 'WHERE', "JOIN {$wpdb->usermeta} um ON um.meta_value = a.item_id JOIN {$bp->profile->table_name_data} xp ON um.user_id = xp.user_id WHERE um.meta_key = 'portfolio_group_id' AND", $sql );
					$sql = str_replace( 'eportfolio', 'portfolio', $sql );
					$sql = "$sql $mt_where";
				}

				$counts[ $gt ] = $wpdb->get_var( $sql );
			}
		}

		$this->counts = array_map( 'intval', $counts );
		return $this->counts;
	}

	protected function get_member_type_where_clause( $member_type ) {
		global $wpdb;

		$clause = '';
		$member_types = (array) $member_type;
		foreach ( $member_types as &$mt ) {
			$mt = $wpdb->prepare( '%s', ucwords( $mt ) );
		}
		$member_types = implode( ',', $member_types );

		$clause = " AND xp.field_id = 7 AND xp.value IN ({$member_types})";

		return $clause;
	}
}
