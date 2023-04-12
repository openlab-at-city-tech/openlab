<?php

namespace WeBWorK\Server\Vote;

/**
 * Vote query.
 *
 * @since 1.0.0
 */
class Query {
	protected $r;

	public function __construct( $args ) {
		$this->r = array_merge(
			array(
				'item_id'         => null,
				'item_id__in'     => null,
				'user_id'         => null,
				'user_id__not_in' => null,
			),
			$args
		);
	}

	/**
	 * Get votes.
	 *
	 * @since 1.0.0
	 *
	 * @return array|int
	 */
	public function get( $type = 'object' ) {
		global $wpdb;

		$fields = 'count' === $type ? 'COUNT(*)' : '*';

		$table_name = $wpdb->get_blog_prefix() . 'webwork_votes';

		$sql = "SELECT $fields FROM $table_name";

		$where = array();

		if ( null !== $this->r['user_id'] ) {
			$where[] = $wpdb->prepare( 'user_id = %d', $this->r['user_id'] );
		}

		if ( null !== $this->r['item_id'] ) {
			$where[] = $wpdb->prepare( 'item_id = %d', $this->r['item_id'] );
		} elseif ( is_array( $this->r['item_id__in'] ) ) {
			if ( empty( $this->r['item_id__in'] ) ) {
				$item_id__in = 0;
			} else {
				$item_id__in = implode( ',', array_map( 'intval', $this->r['item_id__in'] ) );
			}

			$where[] = "item_id IN ($item_id__in)";
		}

		if ( is_array( $this->r['user_id__not_in'] ) ) {
			$user_id__not_in = implode( ',', array_map( 'intval', $this->r['user_id__not_in'] ) );
			$where[]         = "user_id NOT IN ({$user_id__not_in})";
		}

		if ( $where ) {
			$sql .= ' WHERE ' . implode( ' AND ', $where );
		}

		if ( 'count' === $type ) {
			$found = $wpdb->get_var( $sql );
			return (int) $found;
		} else {
			$votes = $wpdb->get_results( $sql );
			return $votes;
		}
	}
}
