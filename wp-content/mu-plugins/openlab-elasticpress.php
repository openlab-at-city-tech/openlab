<?php

/**
 * todo:
 * - Add to mappings: school, department, semester (already in meta?)
 * - Add the above to the sync mechanism

/**
 * Blacklist meta keys being indexed.
 */
add_filter(
	'epbp_prepare_group_meta_excluded_public_keys',
	function( $keys ) {
		return array_merge(
			[
				'wds_group_type',
				'wds_group_school',
				'wds_group_department',
			],
			$keys,
		);
	}
);

/**
 * Group type should reflect what's stored in meta.
 */
add_filter(
	'epbp_group_sync_args',
	function( $args, $group_id ) {
		$args['group_type'] = openlab_get_group_type( $group_id );

		$categories = BPCGC_Groups_Terms::get_object_terms( $group_id, 'bp_group_categories', [] );
		$cat_slugs  = wp_list_pluck( $categories, 'slug' );

		$args['meta']['categories'] = $cat_slugs;

		return $args;
	},
	10,
	2
);

/**
 * Translate OL group query args into BPES standard query args.
 */
add_filter(
	'epbp_group_query_args',
	function( $args, $group_query_args ) {
		if ( empty( $group_query_args['meta_query'] ) ) {
			return $args;
		}

		foreach ( $group_query_args['meta_query'] as $mq ) {
			switch ( $mq['key'] ) {
				case 'wds_group_type' :
					$args['query']['bool']['filter'][] = [
						'term' => [
							'group_type' => $mq['value'],
						],
					];
				break;

//				case 'openlab_department' : WHY DOES THIS NOT WORK
				case 'openlab_office' :
				case 'openlab_school' :
					$args['query']['bool']['filter'][] = [
						'terms' => [
							'meta.' . $mq['key'] . '.value' => [ $mq['value'] ],
						],
					];
				break;
			}
		}

		if ( isset( $_GET['cat'] ) && ! empty( $_GET['cat'] ) ) {
			$cat = wp_unslash( $_GET['cat'] );
			$cat = sanitize_text_field( $cat );
			$args['query']['bool']['filter'][] = [
				'terms' => [
					'meta.categories' => [ $cat ],
				],
			];
		}

		return $args;
	},
	10,
	2
);
