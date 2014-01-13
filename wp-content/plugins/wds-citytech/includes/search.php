<?php

/**
 * Improvements to default search
 */

function openlab_group_search_breakup( $sql, $s ) {
	global $bp;

	if ( ! empty( $s['search'] ) ) {
		// Get the search terms
		preg_match( '/%%([^%]+)%%/', $s['search'], $matches );
		if ( ! empty( $matches[1] ) ) {
			$search_terms = explode( ' ', $matches[1] );

			// No need to continue if there's only one term - BP's
			// default SQL will work fine
			if ( 1 === count( $search_terms ) ) {
				return $sql;
			}

			// Assemble the new search clause
			$match_clauses = array();
			foreach ( $search_terms as $search_term ) {
				$search_term_clean = esc_sql( like_escape( $search_term ) );
				$match_clauses[] = "( g.name LIKE '%%{$search_term_clean}%%' OR g.description LIKE '%%{$search_term_clean}%%' )";
			}

			$search_clause = ' AND ( ' . implode( ' AND ', $match_clauses ) . ' )';

			// Swap out the search clause in the SQL string as well
			// as the array (in case other plugins have modified,
			// or are going to further modify, the query)
			//
			// Though, on second thought, doesn't matter much since
			// the array is not passed by reference
			$s['search'] = $search_clause;
			$sql = preg_replace( '/AND \( g.name LIKE \'%%[^%]+%%\' OR g.description LIKE \'%%[^%]+%%\' \)/', $search_clause, $sql );
		}
	}

	return $sql;
}
add_filter( 'bp_groups_get_paged_groups_sql', 'openlab_group_search_breakup', 10, 2 );
add_filter( 'bp_groups_get_total_groups_sql', 'openlab_group_search_breakup', 10, 2 );
