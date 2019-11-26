<?php

/**
 * todo:
 * - Add to mappings: school, department, semester (already in meta?)
 * - Add the above to the sync mechanism

/**
 * Group type should reflect what's stored in meta.
 */
add_filter(
	'epbp_group_sync_args',
	function( $args, $group_id ) {
		$args['group_type'] = openlab_get_group_type( $group_id );
		return $args;
	},
	10,
	2
);

/**
 * Translate OL group type into BP group type for the query.
 */
add_filter(
	'epbp_group_query_args',
	function( $args, $group_query_args ) {
		if ( empty( $group_query_args['meta_query'] ) ) {
			return $args;
		}

		foreach ( $group_query_args['meta_query'] as $mq ) {
			if ( 'wds_group_type' !== $mq['key'] ) {
				continue;
			}

			$args['query']['bool']['filter'][] = [
				'term' => [
					'group_type' => $mq['value'],
				],
			];

			break;
		}

		return $args;
	},
	10,
	2
);
