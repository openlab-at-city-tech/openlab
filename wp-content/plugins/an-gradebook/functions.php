<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns prefixed table name for gradebook tables.
 */
function an_gradebook_table( $table ) {
	global $wpdb;
	return $wpdb->prefix . $table;
}

/**
 * Builds a comparison function for usort by a given array key.
 */
function an_gradebook_build_sorter( $key ) {
	return function ( $a, $b ) use ( $key ) {
		return strnatcmp( $a[ $key ], $b[ $key ] );
	};
}

/**
 * Checks if current or specified user has a given role.
 */
function an_gradebook_check_user_role( $role, $user_id = null ) {
	if ( is_numeric( $user_id ) ) {
		$user = get_userdata( $user_id );
	} else {
		$user = wp_get_current_user();
	}
	if ( empty( $user ) ) {
		return false;
	}
	return in_array( $role, (array) $user->roles );
}
