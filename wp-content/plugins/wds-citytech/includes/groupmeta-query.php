<?php

/**
 * This is a quick and dirty class to work around the fact that bp_has_groups() does not have a
 * meta_query parameter (or even an 'in' parameter). Usage:
 *
 * 1) Just before you fire up your bp_has_groups() loop, instantiate the BP_Groups_Meta_Filter
 *    class, passing a parameter that's an array of keys/values to filter by
 * 2) Do your groups loop as normal
 * 3) When you've closed the bp_has_groups() loop (endif;), call the method remove_filters() just
 *    to be safe.
 *
 * EXAMPLE
 * Here's how you would run a bp_has_groups() loop that would only show groups that had the meta
 * key/value pairs: 'favorite_gum' => 'Juicy Fruit' & 'favorite_turtle' => 'Donatello'
 *
 *     $filters = array(
 *         'favorite_gum' => 'Juicy Fruit',
 *         'favorite_turtle' => 'Donatello'
 *     );
 *     $meta_filter = new BP_Groups_Meta_Filter( $filters );
 *
 *     // Note that you can pass whatever arguments you want to bp_has_groups(), as usual
 *     if ( bp_has_groups() ) :
 *         while ( bp_groups() ) :
 *             bp_the_group();
 *             // Do your template stuff here
 *         endwhile;
 *     endif;
 *
 *     // Make sure that other loops on the page are clean
 *     $meta_filter->remove_filters();
 */
class BP_Groups_Meta_Filter {
	protected $key;
	protected $value;
	protected $group_ids = array();

	function __construct( $filters = array() ) {
		$this->filters = $filters;

		$this->setup_group_ids();

		add_filter( 'bp_groups_get_paged_groups_sql', array( &$this, 'filter_sql' ) );
		add_filter( 'bp_groups_get_total_groups_sql', array( &$this, 'filter_sql' ) );
	}

	function setup_group_ids() {
		global $wpdb, $bp;

		$sql = "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE ";

		$join_clauses  = array();
		$where_clauses = array();
		$counter = 1;
		$filter_count = count( $this->filters );
		foreach( $this->filters as $key => $value ) {
			$table_shortname = 'gmf' . $counter;

			$join_sql = $counter > 1 ? " LEFT JOIN {$bp->groups->table_name_groupmeta} {$table_shortname} ON gmf1.group_id = {$table_shortname}.group_id " : "{$bp->groups->table_name_groupmeta} {$table_shortname}";
			$join_clauses[]  = $join_sql;

			$where_clauses[] = $wpdb->prepare( "{$table_shortname}.meta_key = %s AND {$table_shortname}.meta_value = %s", $key, $value );
			$counter++;
		}

		if ( !empty( $where_clauses ) ) {
			$sql = "SELECT gmf1.group_id FROM " . implode( ' ', $join_clauses ) . " WHERE " . implode( ' AND ', $where_clauses );
		} else {
			$sql = $wpdb->get_results( "SELECT id FROM {$bp->groups->table_name} WHERE 1 = 0" );
		}

		$this->group_ids = wp_parse_id_list( $wpdb->get_col( $sql ) );
	}

	function get_group_ids() {
		return $this->group_ids;
	}

	function filter_sql( $sql ) {
		$group_ids = $this->get_group_ids();

		$sql_a = explode( 'WHERE', $sql );

		if ( !empty( $group_ids ) ) {
			$new_sql = $sql_a[0] . 'WHERE g.id IN (' . implode( ',', $group_ids ) . ') AND ' . $sql_a[1];
		} else {
			$new_sql = $sql_a[0] . 'WHERE 1 = 0';
		}

		return $new_sql;
	}

	function remove_filters() {
		remove_filter( 'bp_groups_get_paged_groups_sql', array( &$this, 'filter_sql' ) );
		remove_filter( 'bp_groups_get_total_groups_sql', array( &$this, 'filter_sql' ) );
	}
}

?>
