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
 * Get a printable label for a group or group type
 */
function openlab_get_group_type_label( $args = array() ) {
	$r = wp_parse_args( $args, array(
		'group_id' => openlab_fallback_group(),
		'case' => 'lower',
	) );

	// Skip the group type lookup if one has been provided
	if ( empty( $r['group_type'] ) ) {
		$r['group_type'] = openlab_get_group_type( $r['group_id'] );
	}

	if ( 'portfolio' === $r['group_type'] ) {
		$label = openlab_get_portfolio_label( $args );
	} else {
		$label = $r['group_type'];

		if ( 'upper' === $r['case'] ) {
			$label = ucwords( $label );
		}
	}

	return $label;
}

/**
 * Get a group type by group id
 *
 * @param int $group_id
 * @return string
 */
function openlab_get_group_type( $group_id = 0 ) {
	if ( ! bp_is_active( 'groups' ) ) {
		return '';
	}

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
function openlab_get_directory_filter( $filter_type, $label_type ) {
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

			foreach( openlab_get_department_list( '', 'short' ) as $depts ) {
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

		case 'semester' :
			$filter_array['label'] = 'Semester';
			$filter_array['options'] = array();
			foreach ( openlab_get_active_semesters() as $sem ) {
				$filter_array['options'][ $sem['option_value'] ] = $sem['option_label'];
			}
			break;
	}

	return $filter_array;
}

/**
 * Gets the current directory filters, and spits out some markup
 */
function openlab_current_directory_filters() {
	$filters = array();

	if ( is_page( 'people' ) ) {
		$current_view = 'people';
	} else {
		$current_view = openlab_get_current_group_type();
	}

	switch ( $current_view ) {
		case 'portfolio' :
			$filters = array( 'school', 'department', 'usertype' );
			break;

		case 'course' :
		case 'club' :
		case 'project' :
			$filters = array( 'school', 'department', 'semester' );
			break;

		case 'people' :
			$filters = array( 'usertype', 'school', 'department' );
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

	$markup = '';
	if ( !empty( $active_filters ) ) {
		$markup .= '<h3 id="bread-crumb">';

		$filter_words = array();
		foreach( $active_filters as $ftype => $fvalue ) {
			$filter_data = openlab_get_directory_filter( $ftype, 'short' );

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

/**
 * Add group types switcher metabox to group admin in dashboard.
 */
function openlab_group_type_meta_box() {
	add_meta_box(
		'openlab_group_type',
		'Group Type',
		'openlab_group_type_meta_box_cb',
		get_current_screen()->id,
		'side',
		'core'
	);
}
add_action( 'bp_groups_admin_meta_boxes', 'openlab_group_type_meta_box' );

/**
 * Display callback for Group Type meta box.
 */
function openlab_group_type_meta_box_cb( $group ) {
	$group_type = openlab_get_group_type( $group->id );

	wp_nonce_field( 'openlab_group_type_' . $group->id, 'openlab_group_type_nonce' )

	?>

	<ul>
		<li>
			<input type="radio" <?php checked( 'course', $group_type ) ?> value="course" name="openlab-group-type" /> Course
		</li>

		<li>
			<input type="radio" <?php checked( 'club', $group_type ) ?> value="club" name="openlab-group-type" /> Club
		</li>

		<li>
			<input type="radio" <?php checked( 'project', $group_type ) ?> value="project" name="openlab-group-type" /> Project
		</li>

		<li>
			<input type="radio" <?php checked( 'portfolio', $group_type ) ?> value="portfolio" name="openlab-group-type" /> Portfolio
		</li>
	</ul>
	<?php
}

/**
 * Catch group type save in admin.
 */
function openlab_group_type_meta_box_save( $group_id ) {
	check_admin_referer( 'openlab_group_type_' . $group_id, 'openlab_group_type_nonce' );

	$type = isset( $_POST['openlab-group-type'] ) && in_array( $_POST['openlab-group-type'], openlab_group_types() ) ? $_POST['openlab-group-type'] : '';

	if ( ! $type ) {
		return;
	}

	groups_update_groupmeta( $group_id, 'wds_group_type', $type );
}
add_action( 'bp_group_admin_edit_after', 'openlab_group_type_meta_box_save' );

/**
 * Render the "Additional Faculty" field when creating/editing a course.
 */
function openlab_additional_faculty_field() {
	// Courses only.
	if ( bp_is_group() && ! openlab_is_course() ) {
		return;
	}

	if ( bp_is_group_create() && ( ! isset( $_GET['type'] ) || 'course' !== $_GET['type'] ) ) {
		return;
	}

	// Enqueue JS and CSS.
	wp_enqueue_script( 'openlab-additional-faculty', plugins_url() . '/wds-citytech/assets/js/additional-faculty.js', array( 'jquery-ui-autocomplete' ) );
	wp_enqueue_style( 'openlab-additional-faculty', plugins_url() . '/wds-citytech/assets/css/additional-faculty.css' );

	$group_id = 0;
	if ( bp_is_group() ) {
		$group_id = bp_get_current_group_id();
	}

	$addl_faculty = groups_get_groupmeta( $group_id, 'additional_faculty', false );
	$addl_faculty_data = array();
	foreach ( $addl_faculty as $fid ) {
		$f = new WP_User( $fid );
		$addl_faculty_data[] = array(
			'label' => sprintf( '%s (%s)', esc_html( bp_core_get_user_displayname( $fid ) ), esc_html( $f->user_nicename ) ),
			'value' => esc_attr( $f->user_nicename ),
		);
	}

	?>

	<div id="additional-faculty-admin">
		<?php /* Data about existing faculty */ ?>
		<script type="text/javascript">var OL_Addl_Faculty_Existing = '<?php echo json_encode( $addl_faculty_data ) ?>';</script>
		<label for="additional-faculty">Additional Faculty</label>

		<input class="hide-if-no-js" type="textbox" id="additional-faculty-autocomplete" value="" />
		<?php wp_nonce_field( 'openlab_additional_faculty_autocomplete', '_ol_addl_faculty_nonce', false ) ?>

		<ul id="additional-faculty-list"></ul>

		<input class="hide-if-js" type="textbox" name="additional-faculty" id="additional-faculty" value="<?php echo esc_attr( implode( ', ', $addl_faculty ) ) ?>" />
	</div>
	<?php
}
add_action( 'bp_after_group_details_creation_step', 'openlab_additional_faculty_field', 5 );
add_action( 'bp_after_group_details_admin', 'openlab_additional_faculty_field', 5 );

/**
 * AJAX handler for additional faculty autocomplete.
 */
function openlab_additional_faculty_autocomplete_cb() {
	$nonce = $term = '';

	if ( isset( $_GET['nonce'] ) ) {
		$nonce = urldecode( $_GET['nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_additional_faculty_autocomplete' ) ) {
		die( json_encode( -1 ) );
	}

	// @todo Permissions? Faculty only?

	if ( isset( $_GET['term'] ) ) {
		$term = urldecode( $_GET['term'] );
	}

//	add_action( 'bp_user_query_uid_clauses', 'openlab_restrict_user_search_to_name_field', 20, 2 );
	$found = new BP_User_Query( array(
		'search_terms' => $term,
	) );
//	remove_action( 'bp_user_query_uid_clauses', 'openlab_restrict_user_search_to_name_field', 20, 2 );

	$retval = array();
	foreach ( $found->results as $u ) {
		$retval[] = array(
			'label' => sprintf( '%s (%s)', esc_html( $u->display_name ), esc_html( $u->user_nicename ) ),
			'value' => esc_attr( $u->user_nicename ),
		);
	}

	echo json_encode( $retval );
	die();
}
add_action( 'wp_ajax_openlab_additional_faculty_autocomplete', 'openlab_additional_faculty_autocomplete_cb' );

/**
 * Restrict `BP_User_Query` searches to match only against xprofile Name field.
 *
 * We don't need to match all xprofile fields.
 *
 * @param array         $s SQL clause array.
 * @param BP_User_Query $q User query object.
 */
function openlab_restrict_user_search_to_name_field( $s, BP_User_Query $q ) {
	if ( ! isset( $s['where'] ) ) {
		return $s;
	}

	$table = buddypress()->profile->table_name_data;
	$pattern = '/' . $table . ' WHERE ([^)]+)/';
	echo $pattern;
	var_Dump( preg_match( $pattern, $s['where'], $f ) );
	print_r( $f ); die();
	$s['where'] = preg_replace( $table . ' WHERE', $table . ' WHERE field_id = 1 AND ', $s['where'] );

	return $s;
}

/**
 * Process the saving of additional faculty.
 */
function openlab_additional_faculty_save( $group ) {
	$nonce = '';

	if ( isset( $_POST['_ol_addl_faculty_nonce'] ) ) {
		$nonce = urldecode( $_POST['_ol_addl_faculty_nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_additional_faculty_autocomplete' ) ) {
		return;
	}

	// Admins only.
	if ( ! groups_is_user_admin( bp_loggedin_user_id(), $group->id ) ) {
		return;
	}

	// Give preference to JS-saved items.
	$addl_faculty = isset( $_POST['additional-faculty-js'] ) ? $_POST['additional-faculty-js'] : null;
	if ( null === $addl_faculty ) {
		$addl_faculty = $_POST['additional-faculty'];
	}

	// Delete all existing items.
	$existing = groups_get_groupmeta( $group->id, 'additional_faculty', false );
	foreach ( $existing as $e ) {
		groups_delete_groupmeta( $group->id, 'additional_faculty', $e );
	}

	foreach ( (array) $addl_faculty as $nicename ) {
		$f = get_user_by( 'slug', stripslashes( $nicename ) );

		if ( ! $f ) {
			continue;
		}

		// @todo Verify that it's a faculty member?
		groups_add_groupmeta( $group->id, 'additional_faculty', $f->ID );
	}
}
add_action( 'groups_group_after_save', 'openlab_additional_faculty_save' );
