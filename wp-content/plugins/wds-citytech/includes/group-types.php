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
		global $bp, $post;

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

		//fix for archive pages, which are pages and don't return an actual group type
		if ($group_type == 'group' && openlab_page_slug_to_grouptype()!= 'not-archive' )
		{
			$group_type = openlab_page_slug_to_grouptype();
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

	switch (openlab_get_current_group_type()) {
		case 'portfolio' :
			$filters = array( 'school', 'department', 'usertype' );
			break;
		case 'course' :
			$filters = array( 'school', 'department', 'semester' );
			break;

		default :

			break;
	}

	$active_filters = array();
	foreach( $filters as $f ) {
		if ( !empty( $_GET[$f] ) && !(strpos($_GET[$f],'_all')) ) {
			$active_filters[$f] = $_GET[$f];
		}
	}

//<h3 id="bread-crumb">'.$school.'<span class="sep">&nbsp;&nbsp;|&nbsp;&nbsp; </span>
	$markup = '';
	if ( !empty( $active_filters ) ) {
		$markup .= '<h3 id="bread-crumb">';

		$filter_words = array();
		foreach( $active_filters as $ftype => $fvalue ) {
			$filter_data = openlab_get_directory_filter( $ftype );

			$word = isset( $filter_data['options'][$fvalue] ) ? $filter_data['options'][$fvalue] : ucwords( $fvalue );

			// Leave out the 'All's
			if ( 'All' != $word ) {
				$filter_words[] = $word;
			}
		}

		$markup .= implode( '<span class="sep">&nbsp;&nbsp;|&nbsp;&nbsp;</span>', $filter_words );

		$markup .= '</h3>';
	}

	echo $markup;
}

/**
 * Get all the groups that a given user is NOT allowed to see, for a given group type
 *
 * Note that this only returns hidden groups. Private groups can be seen by all users, at least
 * in directories.
 *
 * We stash some of the direct queries in the cache so that we can get them later in the page load
 *
 * NOTE: This function is not currently in use. See:
 *   - https://github.com/livinglab/openlab/commit/7525ae11d2550f5c6fc95ce50ec6fda256da239e
 *   - http://openlab.citytech.cuny.edu/redmine/issues/396
 */
function openlab_get_unavailable_groups( $user_id = 0 ) {
	global $bp, $wpdb;

	// Super admins see everything
	if ( is_super_admin() ) {
		return array();
	}

	if ( !isset( $bp->hidden_groups ) ) {
		$bp->hidden_groups = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name} WHERE status = 'hidden' ORDER BY id DESC" ) );
	}

	// Non-logged-in users can't see any hidden groups. For logged-in users, we check to see
	// whether they're members of any
	if ( is_user_logged_in() ) {
		$my_groups_args = array(
			'user_id'         => $user_id,
			'populate_extras' => false,
			'page'            => null,
			'per_page'        => null,
			'type'            => 'newest',
			'show_hidden'     => true
		);

		if ( bp_has_groups( $my_groups_args ) ) {
			while ( bp_groups() ) {
				bp_the_group();

				$key = array_search( bp_get_group_id(), $bp->hidden_groups );
				if ( false !== $key ) {
					unset( $bp->hidden_groups[$key] );
				}
			}
		}
	}

	return $bp->hidden_groups;
}

/**
 * Is this group hidden?
 */
function openlab_group_is_hidden( $group_id = 0 ) {
	$is_hidden = false;

	if ( !$group_id ) {
		if ( bp_is_group() ) {
			$group = groups_get_current_group();
		} else {
			$group_id = openlab_fallback_group();
		}
	}

	if ( empty( $group ) ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );
	}

	if ( empty( $group ) ) {
		return $is_hidden;
	} else {
		return isset( $group->status ) && 'hidden' == $group->status;
	}
}
/**
 * This function is for the group archive pages, which are currently literally pages with specified templates
 * It attaches a group type to a specific page slug
 * At some point these archive pages will be moved to the right location in the BP hierarchy, and this function won't be necessary
 */
function openlab_page_slug_to_grouptype()
{
	global $post;

	$postname = $post->post_name;
	$group_type = explode("-",$postname);

	switch($group_type[count($group_type) - 1]){
		case 'courses':
			$group_type = 'course';
		break;

		case 'projects':
			$group_type = 'project';
		break;

		case 'clubs':
			$group_type = 'club';
		break;

		case 'portfolios':
			$group_type = 'portfolio';
		break;

		default :
			$group_type = 'not-archive';
		break;
	}

	return $group_type;

}
?>
