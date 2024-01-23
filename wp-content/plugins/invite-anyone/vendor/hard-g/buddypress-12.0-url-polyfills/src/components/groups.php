<?php

/**
 * BuddyPress 12.0 URL polyfills - Groups component.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'bp_group_url' ) ) :
/**
 * Output the URL for the group.
 *
 * @param int|BP_Groups_Group $group       The group ID or the group object.
 * @param array			   $path_chunks {
 *    An array of path chunks to append to the URL. Optional.
 *
 *    @type string $single_item_action           The slug of the action (the first chunk of
 *    									   the URL following the group URL).
 *    @type array  $single_item_action_variables An array of additional URL chunks.
 * }
 * @return void
 */
function bp_group_url( $group = 0, $path_chunks = array() ) {
	echo esc_url( bp_get_group_url( $group, $path_chunks ) );
}
endif;

if ( ! function_exists( 'bp_get_group_url' ) ) :
/**
 * Gets the URL of a group.
 *
 * @param int|BP_Groups_Group $group       The group ID or the group object.
 * @param array               $path_chunks {
 *     An array of path chunks to append to the URL. Optional.
 *
 *     @type string $single_item_action           The slug of the action (the first chunk of
 *                                                the URL following the group URL).
 *     @type array  $single_item_action_variables An array of additional URL chunks.
 * }
 * @return string The URL of the group.
 */
function bp_get_group_url( $group = 0, $path_chunks = array() ) {
	$group_url = bp_get_group_permalink( $group );

	if ( ! empty( $path_chunks['single_item_action'] ) ) {
		$group_url = trailingslashit( $group_url . $path_chunks['single_item_action'] );

		if ( ! empty( $path_chunks['single_item_action_variables'] ) ) {
			$group_url .= implode( '/', $path_chunks['single_item_action_variables'] );
		}
	}

	return trailingslashit( $group_url );
}
endif;

if ( ! function_exists( 'bp_group_manage_url' ) ) :
/**
 * Outputs the requested group's manage URL.
 *
 * @param int|BP_Groups_Group $group  The group ID or the group object.
 * @param array               $chunks A list of default BP Slugs to append.
 * @return void
 */
function bp_group_manage_url( $group = 0, $chunks = array() ) {
	$path_chunks = bp_groups_get_path_chunks( $chunks, 'manage' );

	echo esc_url( bp_get_group_manage_url( $group, $path_chunks ) );
}
endif;

if ( ! function_exists( 'bp_get_group_manage_url' ) ) :
/**
 * Gets the URL of the manage screen for a group.
 *
 * @param int|BP_Groups_Group $group       The group ID or the group object.
 * @param array               $path_chunks {
 *     An array of path chunks to append to the URL. Optional.
 *
 *     @type array $single_item_action_variables An array of additional URL chunks.
 * }
 * @return string The URL of the manage screen for the group.
 */
function bp_get_group_manage_url( $group = 0, $path_chunks = array() ) {
	$full_path_chunks = array(
		'single_item_action' => 'admin',
	);

	if ( ! empty( $path_chunks['single_item_action_variables'] ) ) {
		$full_path_chunks['single_item_action_variables'] = $path_chunks['single_item_action_variables'];
	}

	return bp_get_group_url( $group, $full_path_chunks );
}
endif;

if ( ! function_exists( 'bp_groups_get_path_chunks' ) ) :
/**
 * Builds an array of path chunks in the format expected by bp_members_get_user_url().
 *
 * The BP 12.0 version checks rewrite slugs in this function. This polyfill simply
 * trusts the values passed to it.
 *
 * @param array $chunks Array of URL path chunks.
 * @param string $context The context of the URL. Optional. Default: 'read'.
 * @return array Array of path chunks in the format expected by bp_members_get_user_url().
 */
function bp_groups_get_path_chunks( $chunks = array(), $context = 'read' ) {
	$path_chunks = array();

	if ( 'manage' === $context ) {
		$path_chunks['single_item_action_variables'] = $chunks;

	} elseif ( 'create' === $context && $chunks ) {
		$path_chunks['create_single_item_variables'] = array( 'step' );
		$path_chunks['create_single_item_variables'] = array_merge( $path_chunks['create_single_item_variables'], $chunks );

	} else {
		$single_item_action = array_shift( $chunks );
		if ( $single_item_action ) {
			$path_chunks['single_item_action'] = $single_item_action;
		}

		// If action variables were added as an array, reset chunks to it.
		if ( isset( $chunks[0] ) && is_array( $chunks[0] ) ) {
			$chunks = reset( $chunks );
		}

		if ( $chunks ) {
			foreach ( $chunks as $chunk ) {
				$path_chunks['single_item_action_variables'][] = $chunk;
			}
		}
	}

	return $path_chunks;
}
endif;

if ( ! function_exists( 'bp_groups_directory_url' ) ) :
/**
 * Output Groups directory's URL.
 *
 * @return void
 */
function bp_groups_directory_url() {
	echo esc_url( bp_get_groups_directory_url() );
}
endif;

if ( ! function_exists( 'bp_get_groups_directory_url' ) ) :
/**
 * Returns the Groups directory's URL.
 *
 * @param array $path_chunks {
 *     An array of arguments. Optional.
 *
 *     @type int   $create_single_item `1` to get the create a group URL.
 *     @type array $directory_type     The group type slug.
 * }
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_get_groups_directory_url( $path_chunks = array() ) {
	if ( ! empty( $path_chunks['directory_type'] ) ) {
		return bp_get_group_type_directory_permalink( $path_chunks['directory_type'] );
	}

	$directory_url = bp_get_groups_directory_permalink();

	if ( ! empty( $path_chunks['create_single_item'] ) ) {
		$directory_url = trailingslashit( $directory_url . 'create' );

		if ( ! empty( $path_chunks['create_single_item_variables'] ) && is_array( $path_chunks['create_single_item_variables'] ) ) {
			$directory_url .= join( '/', $path_chunks['create_single_item_variables'] ) . '/';
		}
	}

	return $directory_url;
}
endif;

if ( ! function_exists( 'bp_groups_get_create_url' ) ) :
/**
 * Returns a group create URL accoding to requested path chunks.
 *
 * @param array $chunks array A list of create action variables.
 * @return string The group create URL.
 */
function bp_groups_get_create_url( $action_variables = array() ) {
	$path_chunks = array(
		'create_single_item' => 1,
	);

	if ( is_array( $action_variables ) && $action_variables ) {
		$path_chunks = array_merge( $path_chunks, bp_groups_get_path_chunks( $action_variables, 'create' ) );
	}

	return bp_get_groups_directory_url( $path_chunks );
}
endif;
