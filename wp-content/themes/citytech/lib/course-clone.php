<?php

/**
 * Course cloning
 */

/**
 * Get the courses that a user is an admin of
 */
function openlab_get_courses_owned_by_user( $user_id ) {
	global $wpdb, $bp;

	// This is pretty hackish, but the alternatives are all hacks too
	// First, get list of all groups a user is in
	$is_admin_of = BP_Groups_Member::get_is_admin_of( $user_id );
	$is_admin_of_ids = wp_list_pluck( $is_admin_of['groups'], 'id' );
	if ( empty( $is_admin_of_ids ) ) {
		$is_admin_of_ids = array( 0 );
	}

	// Next, get list of those that are courses
	$user_course_ids = $wpdb->get_col( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = 'course' AND group_id IN (" . implode( ',', wp_parse_id_list( $is_admin_of_ids ) ) . ")" );
	if ( empty( $user_course_ids ) ) {
		$user_course_ids = array( 0 );
	}

	// Finally, get a pretty list
	$user_courses = groups_get_groups( array(
		'type' => 'alphabetical',
		'include' => $user_course_ids,
		'show_hidden' => true,
	) );

	return $user_courses;
}

/**
 * Catch form submits and save to the new group
 */
function openlab_clone_create_form_catcher() {
	$new_group_id = bp_get_new_group_id();

	switch ( bp_get_groups_current_create_step() ) {
		case 'group-details' :
			if ( isset( $_POST['create-or-clone'] ) && 'clone' === $_POST['create-or-clone'] ) {
				$clone_source_group_id = isset( $_POST['group-to-clone'] ) ? (int) $_POST['group-to-clone'] : 0;

				if ( ! $clone_source_group_id ) {
					return;
				}

				groups_update_groupmeta( $new_group_id, 'clone_source_group_id', $clone_source_group_id );
				openlab_clone_course_group( $new_group_id, $clone_source_group_id );

				if ( isset( $_POST['new_or_old'] ) && ( 'clone' === $_POST['new_or_old'] ) && isset( $_POST['blog-id-to-clone'] ) ) {
					$clone_source_blog_id = (int) $_POST['blog-id-to-clone'];
					groups_update_groupmeta( $new_group_id, 'clone_source_blog_id', $clone_source_blog_id );
					openlab_clone_course_site( $new_group_id, $clone_source_blog_id );
				}
			}
			break;

		case 'group-settings' :
			$clone_source_group_id = intval( groups_get_groupmeta( $new_group_id, 'clone_source_group_id' ) );

			if ( ! $clone_source_group_id ) {
				return;
			}

			break;
	}
}
add_action( 'groups_create_group_step_complete', 'openlab_clone_create_form_catcher' );

/** FILTERS ***********************************************************/

/**
 * Swap out the group privacy status, if available
 */
function openlab_clone_bp_get_new_group_status( $status ) {
	$clone_source_group_id = intval( groups_get_groupmeta( bp_get_new_group_id(), 'clone_source_group_id' ) );

	if ( $clone_source_group_id ) {
		$clone_source_group = groups_get_group( array( 'group_id' => $clone_source_group_id ) );
		$status = $clone_source_group->status;
	}

	return $status;
}
add_filter( 'bp_get_new_group_status', 'openlab_clone_bp_get_new_group_status' );

/**
 * Swap out the new group avatar default with the source avatar
 */
function openlab_clone_bp_new_group_avatar( $avatar ) {

}
add_filter( 'bp_get_new_group_avatar', 'openlab_clone_bp_new_group_avatar' );

/**
 * AJAX handler for fetching group details
 */
function openlab_group_clone_fetch_details() {
	$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
	$retval = openlab_group_clone_details( $group_id );

	die( json_encode( $retval ) );
}
add_action( 'wp_ajax_openlab_group_clone_fetch_details', 'openlab_group_clone_fetch_details' );

function openlab_group_clone_details( $group_id ) {
	$retval = array(
		'group_id'               => $group_id,
		'name'                   => '',
		'description'            => '',
		'schools'                => array(),
		'departments'            => array(),
		'course_code'            => '',
		'section_code'           => '',
		'additional_description' => '',
		'site_id'                => '',
		'site_url'               => '',
	);

	if ( $group_id ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );

		$retval['name'] = $group->name;
		$retval['description'] = $group->description;

		$schools = groups_get_groupmeta( $group_id, 'wds_group_school' );
		if ( ! empty( $schools ) ) {
			$retval['schools'] = explode( ',', $schools );
		}

		$departments = groups_get_groupmeta( $group_id, 'wds_departments' );
		if ( ! empty( $departments ) ) {
			$retval['departments'] = explode( ',', $departments );
		}

		$retval['course_code'] = groups_get_groupmeta( $group_id, 'wds_course_code' );
		$retval['section_code'] = groups_get_groupmeta( $group_id, 'wds_section_code' );
		$retval['additional_description'] = groups_get_groupmeta( $group_id, 'wds_course_html' );

		$retval['site_id'] = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
		$retval['site_url'] = get_blog_option( $retval['site_id'], 'home' );
	}

	return $retval;
}

function openlab_clone_course_group( $group_id, $source_group_id ) {
	$c = new Openlab_Clone_Course_Group( $group_id, $source_group_id );
	$c->go();
}

function openlab_clone_course_site( $group_id, $source_site_id ) {

}

/** CLASSES ******************************************************************/

class Openlab_Clone_Course_Group {
	var $group_id;
	var $source_group_id;

	var $source_group_admins = array();

	public function __construct( $group_id, $source_group_id ) {
		$this->group_id = $group_id;
		$this->source_group_id = $source_group_id;
	}

	/**
	 * Summary:
	 * - Docs posted by admins (but no comments)
	 * - Files posted by admins
	 * - Discussion topics posted by admins (but no replies)
	 */
	public function go() {
		$this->migrate_docs();
		// Docs posted by admins
		// Files posted by admins
		// Discussion topics started by admins
	}

	protected function migrate_docs() {
		$docs = array();
		$docs_args = array(
			'group_id' => $this->source_group_id,
			'posts_per_page' => '-1',
		);
		if ( bp_docs_has_docs( $docs_args ) ) {
			while ( bp_docs_has_docs() ) {
				bp_docs_the_doc();
				global $post;
				var_dump( $post );
			}
		}
	}

	protected function migrate_files() {

	}

	protected function migrate_topics() {

	}
}
