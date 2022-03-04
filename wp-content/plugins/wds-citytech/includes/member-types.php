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
 * Gets a list of all member types.
 *
 * @return string
 */
function openlab_get_member_types() {
	return bp_get_member_types( [], 'objects' );
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
