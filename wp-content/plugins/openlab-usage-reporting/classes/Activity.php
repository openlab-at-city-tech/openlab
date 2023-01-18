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

		$counts['users'] = $wpdb->get_var( "SELECT COUNT( DISTINCT a.user_id ) FROM {$bp->activity->table_name} a JOIN {$bp->profile->table_name_data} xp ON xp.user_id = a.user_id WHERE $base_where" );

		$member_types = [ 'student', 'faculty', 'staff', 'alumni', 'non-city-tech' ];

		foreach ( $member_types as $mt ) {
			$mt_where = $this->get_member_type_where_clause( $mt );

			$mt_join = '';
			if ( $mt_where ) {
				$mt_join = "JOIN {$wpdb->term_relationships} tr ON (a.user_id = tr.object_id)";
				$mt_where = " AND $mt_where";
			}

			$counts[ $mt ] = $wpdb->get_var( "SELECT COUNT(DISTINCT a.user_id) FROM {$bp->activity->table_name} a {$mt_join} WHERE $base_where $mt_where" );
		}

		$group_types = array( 'course', 'club', 'project', 'eportfolio', 'portfolio' );

		// Heaven help us.
		if ( 'groups' === $query['component'] ) {
			$gt_base = "SELECT COUNT(DISTINCT a.item_id) FROM {$bp->activity->table_name} a JOIN {$bp->groups->table_name_groupmeta} gm ON (a.item_id = gm.group_id) WHERE $base_where AND gm.meta_key = 'wds_group_type'";

			$counts['items'] = $wpdb->get_var( "$gt_base AND gm.meta_value IN ('course','club','project','portfolio')" );
			foreach ( $group_types as $gt ) {
				$sql = $wpdb->prepare( "$gt_base AND gm.meta_value = %s", $gt );
				if ( 'portfolio' === $gt ) {
					$mt_where = $this->get_member_type_where_clause( array( 'faculty', 'staff' ) );
					$mt_join = '';
					if ( $mt_where ) {
						$mt_join = "JOIN {$wpdb->term_relationships} tr ON (a.user_id = tr.object_id)";
					}

					$sql = str_replace( 'WHERE', "JOIN {$wpdb->usermeta} um ON (um.meta_value = a.item_id) {$mt_join} WHERE um.meta_key = 'portfolio_group_id' AND", $sql );

					if ( $mt_where ) {
						$sql .= " AND $mt_where";
					}
				} elseif ( 'eportfolio' === $gt ) {
					$mt_where = $this->get_member_type_where_clause( array( 'student', 'alumni' ) );
					$mt_join = '';
					if ( $mt_where ) {
						$mt_join = "JOIN {$wpdb->term_relationships} tr ON (a.user_id = tr.object_id)";
					}
					$sql = str_replace( 'WHERE', "JOIN {$wpdb->usermeta} um ON (um.meta_value = a.item_id) {$mt_join} WHERE um.meta_key = 'portfolio_group_id' AND", $sql );
					$sql = str_replace( 'eportfolio', 'portfolio', $sql );

					if ( $mt_where ) {
						$sql .= " AND $mt_where";
					}
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

		$tt_ids = array_map(
			function( $mt ) use ( $wpdb ) {
				$mt_term = get_term_by( 'slug', $mt, 'bp_member_type' );
				if ( $mt_term ) {
					return $wpdb->prepare( '%d', $mt_term->term_taxonomy_id );
				}

				return null;
			},
			$member_types
		);

		$tt_ids = array_filter( $tt_ids );

		if ( ! $tt_ids ) {
			return $clause;
		}

		$tt_ids_sql = implode( ',', $tt_ids );

		$clause = "tr.term_taxonomy_id IN ({$tt_ids_sql})";

		return $clause;
	}
}
