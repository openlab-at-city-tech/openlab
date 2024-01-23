<?php

namespace Imagely\NGG\DataMapper;

class Manager {

	public static function register_hooks() {
		$self = new Manager();
		add_filter( 'posts_request', [ $self, 'set_custom_wp_query' ], 50, 2 );
		add_filter( 'posts_fields', [ $self, 'set_custom_wp_query_fields' ], 50, 2 );
		add_filter( 'posts_where', [ $self, 'set_custom_wp_query_where' ], 50, 2 );
		add_filter( 'posts_groupby', [ $self, 'set_custom_wp_query_groupby' ], 50, 2 );
	}

	/**
	 * Sets a custom SQL query for the WP_Query class, when the Custom Post DataMapper implementation is used
	 *
	 * @param string    $sql
	 * @param \WP_Query $wp_query
	 * @return string
	 */
	public function set_custom_wp_query( $sql, $wp_query ) {
		if ( $wp_query->get( 'datamapper' ) ) {
			// Set the custom query.
			if ( ( $custom_sql = $wp_query->get( 'custom_sql' ) ) ) {
				$sql = $custom_sql;
			}
			// Perhaps we're to initiate a delete query instead?
			elseif ( $wp_query->get( 'is_delete' ) ) {
				$sql = preg_replace( '/^SELECT.*FROM/i', 'DELETE FROM', $sql );
			}

			if ( $wp_query->get( 'debug' ) ) {
				var_dump( $sql );
			}
		}

		return $sql;
	}

	/**
	 * Sets custom fields to select from the database
	 *
	 * @param string    $fields
	 * @param \WP_Query $wp_query
	 * @return string
	 */
	public function set_custom_wp_query_fields( $fields, $wp_query ) {
		if ( $wp_query->get( 'datamapper' ) ) {
			if ( ( $custom_fields = $wp_query->get( 'fields' ) ) && $custom_fields != 'ids' ) {
				$fields = $custom_fields;
			}
		}

		return $fields;
	}

	/**
	 * Sets custom where clauses for a query
	 *
	 * @param string   $where
	 * @param WP_Query $wp_query
	 * @return string
	 */
	public function set_custom_wp_query_where( $where, $wp_query ) {
		if ( $wp_query->get( 'datamapper' ) ) {
			$this->add_post_title_where_clauses( $where, $wp_query );
			$this->add_post_name_where_clauses( $where, $wp_query );
		}

		return $where;
	}

	/**
	 * Adds additional group by clauses to the SQL query
	 *
	 * @param string    $group_by
	 * @param \WP_Query $wp_query
	 * @return string
	 */
	public function set_custom_wp_query_groupby( $group_by, $wp_query ) {
		$retval           = $group_by;
		$group_by_columns = $wp_query->get( 'group_by_columns' );
		if ( $group_by_columns ) {
			$retval  = str_replace( 'GROUP BY', '', $retval );
			$columns = explode( ',', $retval );
			foreach ( array_reverse( $columns ) as $column ) {
				array_unshift( $group_by_columns, trim( $column ) );
			}
			$retval = 'GROUP BY ' . implode( ', ', $group_by_columns );
		} elseif ( $wp_query->get( 'datamapper' ) ) {
			// Not all mysql servers allow access to create temporary tables which are used when doing GROUP BY
			// statements; this can potentially ruin basic queries. If no group_by_columns is set AND the query originates
			// within the datamapper we strip the "GROUP BY" clause entirely in this filter.
			$retval = '';
		}

		return $retval;
	}

	/**
	 * Formats the value of used in a WHERE IN SQL clause for use in the WP_Query where clause
	 *
	 * @param string|array $values
	 * @return string
	 */
	public function format_where_in_value( $values ) {
		if ( is_string( $values ) && strpos( $values, ',' ) !== false ) {
			$values = explode( ', ', $values );
		} elseif ( ! is_array( $values ) ) {
			$values = [ $values ];
		}

		// Quote the titles.
		foreach ( $values as $index => $value ) {
			$values[ $index ] = "'{$value}'";
		}

		return implode( ', ', $values );
	}

	/**
	 * Adds post_title to the where clause
	 *
	 * @param string   $where
	 * @param WP_Query $wp_query
	 * @return string
	 */
	public function add_post_title_where_clauses( &$where, &$wp_query ) {
		global $wpdb;

		// Handle post_title query var.
		if ( ( $titles = $wp_query->get( 'post_title' ) ) ) {
			$titles = $this->format_where_in_value( $titles );
			$where .= " AND {$wpdb->posts}.post_title IN ({$titles})";
		} elseif ( ( $value = $wp_query->get( 'post_title__like' ) ) ) {
			// Handle post_title_like query var.
			$where .= " AND {$wpdb->posts}.post_title LIKE '{$value}'";
		}

		return $where;
	}

	/**
	 * Adds post_name to the where clause
	 *
	 * @param string    $where
	 * @param \WP_Query $wp_query
	 */
	public function add_post_name_where_clauses( &$where, &$wp_query ) {
		global $wpdb;

		if ( ( $name = $wp_query->get( 'page_name__like' ) ) ) {
			$where .= " AND {$wpdb->posts}.post_name LIKE '{$name}'";
		} elseif ( ( $names = $wp_query->get( 'page_name__in' ) ) ) {
			$names  = $this->format_where_in_value( $names );
			$where .= " AND {$wpdb->posts}.post_name IN ({$names})";
		}
	}
}
