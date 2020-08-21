<?php

/**
 * Improvements to default search
 */

function openlab_group_search_breakup( $sql, $s, $r ) {
	static $user_matches = [];
	global $wpdb, $bp;

	if ( empty( $r['search_terms'] ) ) {
		return $sql;
	}

	preg_match_all( '/"(?:\\\\.|[^\\\\"])*"|\S+/', $r['search_terms'], $matches );

	$search_columns_allowed = [ 'name', 'description' ];
	if ( ! empty( $r['search_columns'] ) ) {
		$search_columns = array_intersect( $r['search_columns'], $search_columns_allowed );
	} else {
		$search_columns = $search_columns_allowed;
	}

	$match_clauses = [];

	$join_contact_tables = false;

	foreach ( $matches[0] as $term ) {
		// Quotes have served their purpose and can be trimmed.
		$search = trim( $term, '\'"' );

		$leading_wild  = ( ltrim( $search, '*' ) !== $search );
		$trailing_wild = ( rtrim( $search, '*' ) !== $search );
		if ( $leading_wild && $trailing_wild ) {
			$wild = 'both';
		} elseif ( $leading_wild ) {
			$wild = 'leading';
		} elseif ( $trailing_wild ) {
			$wild = 'trailing';
		} else {
			// Default is to wrap in wildcard characters.
			$wild = 'both';
		}
		$search = trim( $search, '*' );

		$term_clauses = [];

		// Match users, for creator/contact/faculty searches.
		$users_query = new BP_User_Query(
			[
				'search_terms'    => $search,
				'populate_extras' => false,
			]
		);

		if ( ! empty( $users_query->user_ids ) ) {
			$join_contact_tables = true;

			$user_ids_sql = implode( ',', array_map( 'intval', $users_query->user_ids ) );

			// Creator may not be public information
			// $term_clauses[] = $wpdb->prepare( "( g.creator_id IN ({$user_ids_sql}) )" );
			$term_clauses[] = "( groupcontact.meta_key IN ( 'primary_faculty', 'additional_faculty', 'group_contact' ) AND groupcontact.meta_value IN ({$user_ids_sql}) )";
//			$term_clauses[] = "( additionalfaculty.meta_value IN ({$user_ids_sql}) )";
//			$term_clauses[] = "( groupcontact.meta_value IN ({$user_ids_sql}) )";
		}

		// Rebuild the 'name' and 'description' clause - borrowed from BP.
		$searches      = array();
		$leading_wild  = ( 'leading' == $wild || 'both' == $wild ) ? '%' : '';
		$trailing_wild = ( 'trailing' == $wild || 'both' == $wild ) ? '%' : '';
		$wildcarded    = $leading_wild . bp_esc_like( $search ) . $trailing_wild;

		foreach ( $search_columns as $search_column ) {
			$searches[] = $wpdb->prepare( "g.$search_column LIKE %s", $wildcarded );
		}

		$term_clauses[] = '(' . implode( ' OR ', $searches ) . ')';

		$match_clauses[] = '(' . implode( ' OR ', $term_clauses ) . ')';
	}

	$search_clause = '( ' . implode( ' AND ', $match_clauses ) . ' )';

	// Swap out the search clause in the SQL string as well
	// as the array (in case other plugins have modified,
	// or are going to further modify, the query)
	$old_where = $s['where'];
	$new_where = preg_replace( '/\(name LIKE[^)]+\)/', $search_clause, $old_where );
	$sql       = str_replace( $old_where, $new_where, $sql );

	if ( $join_contact_tables ) {
		$old_from = $s['from'];
		$new_from = $s['from'] . " LEFT JOIN {$bp->groups->table_name_groupmeta} groupcontact ON ( groupcontact.group_id = g.id )";
		$sql      = str_replace( $old_from, $new_from, $sql );
	}

	return $sql;
}
add_filter( 'bp_groups_get_paged_groups_sql', 'openlab_group_search_breakup', 10, 3 );
add_filter( 'bp_groups_get_total_groups_sql', 'openlab_group_search_breakup', 10, 3 );
