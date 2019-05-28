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
