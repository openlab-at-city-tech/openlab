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

	$term = bp_get_term_by( 'id', $type_object->db_id, 'bp_member_type' );
	// @phpstan-ignore-next-line
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
	$switched = false;
	if ( ! bp_is_root_blog() ) {
		switch_to_blog( bp_get_root_blog_id() );
		$switched = true;
	}

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

	if ( $switched ) {
		restore_current_blog();
	}

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

/**
 * Determines whether a user can create courses.
 *
 * @param int $user_id Optional. Defaults to the current user.
 * @return bool
 */
function openlab_user_can_create_courses( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	if ( is_super_admin( $user_id ) ) {
		return true;
	}

	$member_type = cboxol_get_user_member_type( $user_id );

	return $member_type && $member_type->get_can_create_courses();
}

/**
 * Adds singular_name and plural_name properties to the member type object.
 *
 * @param stdClass[] $member_types Member type objects.
 * @return stdClass[]
 */
function openlab_member_types_add_names( $member_types ) {
	foreach ( $member_types as $member_type ) {
		$member_type->labels['singular_name'] = $member_type->name;
		$member_type->labels['name']          = $member_type->name;
	}

	return $member_types;
}
add_filter( 'bp_get_member_types', 'openlab_member_types_add_names' );

/**
 * Manipulate POST values when saving member type metabox.
 *
 * This is a workaround for a limitation in BuddyPress such that BP will only save
 * these changes if the member types are sent in all lowercase.
 */
function openlab_save_member_type_metabox() {
	if ( empty( $_POST['bp-members-profile-member-type'] ) ) {
		return;
	}

	add_filter( 'bp_get_member_types', 'openlab_member_types_convert_keys_to_lowercase', 999 );
}
add_action( 'bp_members_admin_load', 'openlab_save_member_type_metabox', 5 );

/**
 * Convert keys in member types array to lowercase.
 *
 * @param stdClass[] $member_types Member type objects.
 * @return stdClass[]
 */
function openlab_member_types_convert_keys_to_lowercase( $member_types ) {
	$new_member_types = [];

	foreach ( $member_types as $slug => $member_type ) {
		$new_member_types[ sanitize_title( $slug ) ] = $member_type;
	}

	remove_filter( 'bp_get_member_types', 'openlab_member_types_convert_keys_to_lowercase', 999 );

	return $new_member_types;
}
