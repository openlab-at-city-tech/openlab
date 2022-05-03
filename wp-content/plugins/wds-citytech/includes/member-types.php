<?php

/**
 * Member Types functionality.
 */

/**
 * Gets a user's member type term object.
 *
 * We fetch like this in order to have the term object, which BP hides in its
 * own API functions.
 *
 * @param int $user_id ID of the user.
 * @return WP_Term
 */
function openlab_get_user_member_type_object( $user_id ) {
	$type = bp_get_member_type( $user_id );
	if ( ! $type ) {
		return null;
	}

	// I don't understand why BP stores and reports member types this way.
	$type_object = bp_get_member_type_object( $type );
	if ( ! $type_object ) {
		return null;
	}

	$term = get_term( $type_object->db_id, 'bp_member_type' );
	if ( ! $term || is_wp_error( $term ) ) {
		return null;
	}

	return $term;
}

/**
 * Gets a user's member type.
 *
 * @param int $user_id ID of the user.
 * @return string
 */
function openlab_get_user_member_type( $user_id ) {
	$term = openlab_get_user_member_type_object( $user_id );

	if ( ! $term ) {
		return '';
	}

	return $term->slug;
}

/**
 * Gets a user's member type label.
 *
 * @param int $user_id ID of the user.
 * @return string
 */
function openlab_get_user_member_type_label( $user_id ) {
	$term = openlab_get_user_member_type_object( $user_id );

	if ( ! $term ) {
		return '';
	}

	return $term->name;
}

/**
 * Sets a user's member type.
 *
 * @param int    $user_id
 * @param string $member_type Slug.
 */
function openlab_set_user_member_type( $user_id, $member_type ) {
	$obj = openlab_get_member_type_object( $member_type );
	if ( ! $obj ) {
		return false;
	}

	return bp_set_member_type( $user_id, $obj->name );
}

/**
 * Gets a list of all member types.
 *
 * @return string
 */
function openlab_get_member_types() {
	$bp_types = bp_get_member_types( [], 'objects' );

	$types = array_map(
		function( $bp_type ) {
			return get_term( $bp_type->db_id, bp_get_member_type_tax_name() );
		},
		bp_get_member_types( [], 'objects' )
	);

	$sort_order = [
		0 => 'student',
		1 => 'faculty',
		2 => 'staff',
		3 => 'alumni',
		4 => 'non-city-tech',
	];

	usort(
		$types,
		function( $a, $b ) use ( $sort_order ) {
			$a_index = array_search( $a->slug, $sort_order, true );
			$b_index = array_search( $b->slug, $sort_order, true );

			if ( $a_index === $b_index ) {
				return 0;
			}

			return $a_index > $b_index ? 1 : -1;
		}
	);

	return $types;
}

/**
 * Gets a member type term object by slug.
 *
 * @param string $slug
 * @return WP_Term
 */
function openlab_get_member_type_object( $slug ) {
	return get_term_by( 'slug', $slug, bp_get_member_type_tax_name() );
}
