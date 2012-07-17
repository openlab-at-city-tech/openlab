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
	if ( !$group_id ) {
		$group_id = openlab_fallback_group();
	}

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
	return $type == openlab_get_group_type( $group_id );
}

function openlab_is_course( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'course' ); }

function openlab_is_project( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'project' ); }

function openlab_is_portfolio( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'portfolio' ); }

function openlab_is_club( $group_id = 0 ) { return openlab_is_group_type( $group_id, 'club' ); }

////////////////////////////
//    DIRECTORY FILTERS   //
////////////////////////////

/**
 * Get an array describing some details about filters
 *
 * This is the master function where filter data should be stored
 */
function openlab_get_directory_filter( $filter_type ) {
	$filter_array = array(
		'type' => $filter_type,
		'label' => '',
		'options' => array()
	);

	switch ( $filter_type ) {
		case 'school' :
			$filter_array['label']   = 'School';
			$filter_array['options'] = array(
				'school_all' => 'All',
			);

			foreach( openlab_get_school_list() as $school_key => $school_label ) {
				$filter_array['options'][$school_key] = $school_label;
			}

			break;

		case 'department' :
			$filter_array['label']   = 'Department';
			$filter_array['options'] = array(
				'dept_all' => 'All'
			);

			foreach( openlab_get_department_list() as $depts ) {
				foreach( $depts as $dept_key => $dept_label ) {
					$filter_array['options'][$dept_key] = $dept_label;
				}
			}

			break;

		case 'user_type' :
			$filter_array['label']   = 'User Type';
			$filter_array['options'] = array(
				'user_type_all' => 'All',
				'student'       => 'Student',
				'faculty'       => 'Faculty',
				'staff'         => 'Staff'
			);
			break;
	}

	return $filter_array;
}

/**
 * Gets the current directory filters, and spits out some markup
 */
function openlab_current_directory_filters() {
	$filters = array();

	switch ( openlab_get_current_group_type() ) {
		case 'portfolio' :
			$filters = array( 'school', 'department', 'user_type' );
			break;

		default :

			break;
	}

	$active_filters = array();
	foreach( $filters as $f ) {
		if ( !empty( $_GET[$f] ) ) {
			$active_filters[$f] = $_GET[$f];
		}
	}


	$markup = '';
	if ( !empty( $active_filters ) ) {
		$markup .= '<ul>';
		foreach( $active_filters as $ftype => $fvalue ) {
			$filter_data = openlab_get_directory_filter( $ftype );

			$label = $filter_data['label'];
			$value = isset( $filter_data['options'][$fvalue] ) ? $filter_data['options'][$fvalue] : ucwords( $fvalue );

			$markup .= sprintf( '<li>%s: %s</li>', $label, $value );
		}
		$markup .= '</ul>';
	}

	echo $markup;
}

?>