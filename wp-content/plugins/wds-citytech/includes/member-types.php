<?php

/**
 * Member Types functionality.
 */

/**
 * Gets a user's member type.
 *
 * @param int $user_id ID of the user.
 * @return string
 */
function openlab_get_user_member_type( $user_id ) {
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

	return $term->slug;
}

/**
 * Gets a list of all member types.
 *
 * @return string
 */
function openlab_get_member_types() {
	return bp_get_member_types( [], 'objects' );
}
