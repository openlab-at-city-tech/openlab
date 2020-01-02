<?php

/**
 * todo:
 * - Add to mappings: school, department, semester (already in meta?)
 * - Add the above to the sync mechanism

/**
 * Add custom fields to group mapping.
 */
add_filter(
	'epbp_group_mapping',
	function( $mapping ) {
		$mapping['mappings']['properties']['departments'] = [
			'type' => 'keyword',
		];
		$mapping['mappings']['properties']['schools'] = [
			'type' => 'keyword',
		];
		$mapping['mappings']['properties']['offices'] = [
			'type' => 'keyword',
		];
		$mapping['mappings']['properties']['categories'] = [
			'type' => 'keyword',
		];
		$mapping['mappings']['properties']['semester'] = [
			'type' => 'keyword',
		];
		$mapping['mappings']['properties']['year'] = [
			'type' => 'long',
		];
		return $mapping;
	}
);

/**
 * Blacklist meta keys being indexed.
 */
add_filter(
	'epbp_prepare_group_meta_excluded_public_keys',
	function( $keys ) {
		return array_merge(
			[
				// Handled separately.
				'openlab_department',
				'openlab_departments',
				'openlab_office',
				'openlab_school',
				'wds_group_type',

				// No longer used.
				'wds_group_school',
				'wds_group_department',
				'wds_departments',

				// Serialized and not parseable, or otherwise not relevant for index.
				'clone_history',
				'last_import_results',
				'library_tools_enabled',
				'member_portfolios',
				'member_portfolios_display_name',
				'member_portfolios_title',
				'member_site_roles',
				'openlab_related_links_list',
				'openlab_related_links_list_enable',
				'openlab_related_links_list_heading',
				'portfolio_list_enabled',
				'portfolio_list_heading',
			],
			$keys,
		);
	}
);

/**
 * Blacklist dynamic meta keys.
 */
add_filter(
	'epbp_allow_index_group_meta_key',
	function( $allow_index, $key ) {
		// User import records.
		if ( 0 === strpos( $key, 'import_' ) ) {
			$allow_index = false;
		}

		return $allow_index;
	},
	10,
	2
);

add_filter(
	'epbp_group_query_excluded_meta_keys',
	function( $keys ) {
		$keys[] = 'openlab_school';
		$keys[] = 'openlab_department';
		$keys[] = 'wds_group_type';
		$keys[] = 'wds_semester';
		$keys[] = 'wds_year';
		return $keys;
	}
);

/**
 * OL-specific group data.
 */
add_filter(
	'epbp_group_sync_args',
	function( $args, $group_id ) {
		// Group type should come from our custom meta.
		$args['group_type'] = openlab_get_group_type( $group_id );

		// Categories.
		$categories = BPCGC_Groups_Terms::get_object_terms( $group_id, 'bp_group_categories', [] );
		$cat_slugs  = wp_list_pluck( $categories, 'slug' );

		$args['categories'] = $cat_slugs;

		// Academic units.
		$academic_units = openlab_get_group_academic_units( $group_id );

		foreach ( $academic_units as $unit_type => $units ) {
			$args[ $unit_type ] = array_map(
				function( $unit ) {
					return str_replace( '-', '_', $unit );
				},
				$units
			);
		}

		// Semester/year.
		$semester = groups_get_groupmeta( $group_id, 'wds_semester', true );
		if ( $semester ) {
			$args['semester'] = $semester;
		}

		$year = groups_get_groupmeta( $group_id, 'wds_year', true );
		if ( $year ) {
			$args['year'] = $year;
		}

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

				case 'wds_semester' :
				case 'wds_year' :
					$clean_key = substr( $mq['key'], 4 );
					$args['query']['bool']['filter'][] = [
						'term' => [
							$clean_key => $mq['value'],
						],
					];
				break;

				case 'openlab_department' :
				case 'openlab_office' :
				case 'openlab_school' :
					$academic_unit = substr( $mq['key'], 8 ) . 's';
					$args['query']['bool']['filter'][] = [
						'terms' => [
							$academic_unit => str_replace( '-', '_', [ $mq['value'] ] ),
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
					'categories' => [ $cat ],
				],
			];
		}

		return $args;
	},
	10,
	2
);
