<?php

namespace OpenLab\Favorites\Favorite;

use OpenLab\Favorites\Schema;
use OpenLab\Favorites\Favorite\Favorite;

class Query {
	public static function get_results( $args = [] ) {
		global $wpdb;

		$r = array_merge(
			[
				'user_id'  => null,
				'group_id' => null,
			],
			$args
		);

		$table_name = Schema::get_table_name();

		$sql = array(
			'select' => "SELECT id FROM {$table_name}",
			'where'  => array(),
			'order'  => '',
			'limits' => '',
		);

		if ( ! is_null( $r['user_id'] ) ) {
			$sql['where']['user_id'] = $wpdb->prepare( 'user_id = %d', $r['user_id'] );
		}

		if ( ! is_null( $r['group_id'] ) ) {
			$sql['where']['group_id'] = $wpdb->prepare( 'group_id = %d', $r['group_id'] );
		}

		$where = '';
		if ( $sql['where'] ) {
			$sql['where'] = ' WHERE ' . implode( ' AND ', $sql['where'] );
		}

		$query = $sql['select'] . $sql['where'] . $sql['order'] . $sql['limits'];

		$results = $wpdb->get_col( $query );
		$retval = array();

		foreach ( $results as $found_id ) {
			$i = new Favorite( (int) $found_id );
			$retval[ $found_id ] = $i;
		}

		// For now, hardcoding the order to alphabetical by name.
		uasort(
			$retval,
			function( $a, $b ) {
				return strcmp( $a->get_group_name(), $b->get_group_name() );
			}
		);


		return $retval;
	}
}
