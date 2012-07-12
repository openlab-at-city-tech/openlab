<?php

/**
 * Group types
 *
 * OpenLab makes extensive use of BP Groups, dividing them into (at the moment) four types:
 *   - Courses
 *   - Clubs
 *   - Projects
 *   - Portfolios
 * This file contains utility functions for the group type functionality.
 */

/**
 * Returns a list of allowed group types. Used for validation
 *
 * @return array
 */
function openlab_group_types() {
	return array(
		'project',
		'club',
		'course',
		'portfolio',
		'school' // Legacy. Not sure what this is used for
	);
}

/**
 * Echoes the current group type
 */
function openlab_current_group_type( $case = 'lower' ) {
	echo openlab_get_current_group_type( $case );
}
	/**
	 * Get the current group type
	 *
	 * Does some generous logic to account for new group creation
	 *
	 * @bool $case 'lower' for all lowercase, otherwise Title Case
	 * @return string
	 */
	function openlab_get_current_group_type( $case = 'lower' ) {
		global $bp;

		// We stash in the $bp global for faster subsequent lookups
		if ( isset( $bp->groups->current_group->group_type ) ) {

			$group_type = $bp->groups->current_group->group_type;

		} else {

			$group_type = 'group';

			if ( bp_is_group() ) {
				$group_type = openlab_get_group_type( bp_get_current_group_id() );
			} else if ( bp_is_group_create() && isset( $_GET['type'] ) ) {
				$group_type = urldecode( $_GET['type'] );
			} else if ( bp_is_group_create() ) {
				$group_type = openlab_get_group_type( bp_get_new_group_id() );
			} else if ( isset( $_COOKIE['wds_bp_group_type'] ) ) {
				$group_type = $_COOKIE['wds_bp_group_type'];
			}

			$group_type = strtolower( $group_type );

			if ( !in_array( $group_type, openlab_group_types() ) ) {
				$group_type = 'group';
			}

			if ( empty( $bp->groups->current_group ) ) {
				$bp->groups->current_group = new stdClass;
			}
			$bp->groups->current_group->group_type = $group_type;
		}

		if ( 'lower' !== $case ) {
			$group_type = ucwords( $group_type );
		}

		return $group_type;
	}

/**
 * Get a group type by group id
 *
 * @param int $group_id
 * @return string
 */
function openlab_get_group_type( $group_id = 0 ) {
	$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

	if ( !in_array( $group_type, openlab_group_types() ) ) {
		$group_type = 'group';
	}

	return $group_type;
}

///////////////////////////
// CONDITIONAL FUNCTIONS //
///////////////////////////

function openlab_is_group_type( $group_id = 0, $type = 'group' ) {
	if ( !$group_id ) {
		$group_id = openlab_fallback_group();
	}

	return $type == openlab_get_group_type( $group_id );
}

function openlab_is_course( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'course' ); }

function openlab_is_project( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'project' ); }

function openlab_is_portfolio( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'portfolio' ); }

function openlab_is_club( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'club' ); }

?>