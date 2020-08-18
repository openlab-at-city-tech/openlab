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

	// We stash in the $bp global for faster subsequent lookups.
	if ( isset( $bp->groups->current_group->group_type ) ) {

		$group_type = $bp->groups->current_group->group_type;

	} else {

		$group_type = 'group';

		if ( bp_is_group() ) {
			$group_type = openlab_get_group_type( bp_get_current_group_id() );
		} elseif ( bp_is_group_create() && isset( $_GET['type'] ) ) {
			$group_type = urldecode( $_GET['type'] );
		} elseif ( bp_is_group_create() ) {
			$group_type = openlab_get_group_type( bp_get_new_group_id() );
		} elseif ( isset( $_COOKIE['wds_bp_group_type'] ) ) {
			$group_type = $_COOKIE['wds_bp_group_type'];
		}

		$group_type = strtolower( $group_type );

		if ( ! in_array( $group_type, openlab_group_types() ) ) {
			$group_type = 'group';
		}

		if ( empty( $bp->groups->current_group ) ) {
			$bp->groups->current_group = new stdClass();
		}
		$bp->groups->current_group->group_type = $group_type;
	}

	// Fix for archive pages, which are pages and don't return an actual group type.
	if ( 'group' === $group_type && 'not-archive' !== openlab_page_slug_to_grouptype() ) {
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
	$r = wp_parse_args(
		$args,
		array(
			'group_id' => openlab_fallback_group(),
			'case'     => 'lower',
		)
	);

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

	if ( ! $group_id ) {
		$group_id = openlab_fallback_group();
	}

	$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

	if ( ! in_array( $group_type, openlab_group_types() ) ) {
		$group_type = 'group';
	}

	return $group_type;
}

///////////////////////////
// CONDITIONAL FUNCTIONS //
///////////////////////////

function openlab_is_group_type( $group_id = 0, $type = 'group' ) {
	return openlab_get_group_type( $group_id ) === $type;
}

function openlab_is_course( $group_id = 0 ) {
	return openlab_is_group_type( $group_id, 'course' ); }

function openlab_is_project( $group_id = 0 ) {
	return openlab_is_group_type( $group_id, 'project' ); }

function openlab_is_portfolio( $group_id = 0 ) {
	return openlab_is_group_type( $group_id, 'portfolio' ); }

function openlab_is_club( $group_id = 0 ) {
	return openlab_is_group_type( $group_id, 'club' ); }

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

	if ( ! isset( $bp->hidden_groups ) ) {
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
			'show_hidden'     => true,
		);

		if ( bp_has_groups( $my_groups_args ) ) {
			while ( bp_groups() ) {
				bp_the_group();

				$key = array_search( bp_get_group_id(), $bp->hidden_groups );
				if ( false !== $key ) {
					unset( $bp->hidden_groups[ $key ] );
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

	if ( ! $group_id ) {
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
 * Determines whether this is a "my-{grouptype}" directory.
 */
function openlab_is_my_groups_directory() {
	return is_page( 'my-courses' ) || is_page( 'my-clubs' ) || is_page( 'my-projects' ) || is_page( 'my-sites' );
}

/**
 * This function is for the group archive pages, which are currently literally pages with specified templates
 * It attaches a group type to a specific page slug
 * At some point these archive pages will be moved to the right location in the BP hierarchy, and this function won't be necessary
 */
function openlab_page_slug_to_grouptype() {
	global $post;

	$postname   = $post->post_name;
	$group_type = explode( '-', $postname );

	switch ( $group_type[ count( $group_type ) - 1 ] ) {
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

		default:
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
			<input type="radio" <?php checked( 'course', $group_type ); ?> value="course" name="openlab-group-type" /> Course
		</li>

		<li>
			<input type="radio" <?php checked( 'club', $group_type ); ?> value="club" name="openlab-group-type" /> Club
		</li>

		<li>
			<input type="radio" <?php checked( 'project', $group_type ); ?> value="project" name="openlab-group-type" /> Project
		</li>

		<li>
			<input type="radio" <?php checked( 'portfolio', $group_type ); ?> value="portfolio" name="openlab-group-type" /> Portfolio
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
 * Render the "Faculty" field when creating/editing a course.
 */
function openlab_course_faculty_metabox() {
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

	if ( bp_is_group_create() ) {
		$primary_faculty = bp_loggedin_user_id();
	} else {
		$primary_faculty = openlab_get_primary_faculty( $group_id );
	}

	$primary_faculty_data  = array();
	$primary_faculty_first = null;
	if ( $primary_faculty ) {
		$primary_faculty_first = $primary_faculty[0];
		foreach ( $primary_faculty as $primary_faculty_id ) {
			$primary_faculty_user   = new WP_User( $primary_faculty_id );
			$primary_faculty_data[] = array(
				'label' => sprintf( '%s (%s)', esc_html( bp_core_get_user_displayname( $primary_faculty_user->ID ) ), esc_html( $primary_faculty_user->user_nicename ) ),
				'value' => esc_attr( $primary_faculty_user->user_nicename ),
			);
		}
	}

	$addl_faculty      = groups_get_groupmeta( $group_id, 'additional_faculty', false );
	$addl_faculty_data = array();

	if ( ! empty( $addl_faculty ) ) {
		foreach ( $addl_faculty as $fid ) {
			$f                   = new WP_User( $fid );
			$addl_faculty_data[] = array(
				'label' => sprintf( '%s (%s)', esc_html( bp_core_get_user_displayname( $fid ) ), esc_html( $f->user_nicename ) ),
				'value' => esc_attr( $f->user_nicename ),
			);
		}
	} else {
		//if no additional faculty, provide to view as empty array
		$addl_faculty = array();
	}

	?>

	<div id="additional-faculty-admin" class="panel panel-default">
		<fieldset>
			<legend class="panel-heading">Faculty</legend>
			<div class="panel-body">
				<?php /* Data about existing faculty */ ?>
				<script type="text/javascript">var OL_Primary_Faculty_Existing = '<?php echo json_encode( $primary_faculty_data ); ?>';</script>
				<script type="text/javascript">var OL_Addl_Faculty_Existing = '<?php echo json_encode( $addl_faculty_data ); ?>';</script>

				<div class="subpanel">
					<?php wp_nonce_field( 'openlab_faculty_autocomplete', '_ol_faculty_autocomplete_nonce', false ); ?>

					<label for="primary-faculty-autocomplete">Primary Faculty</label>

					<p>This is usually the person creating the course.</p>

					<input class="hide-if-no-js" type="textbox" id="primary-faculty-autocomplete" value="" />

					<ul id="primary-faculty-list" class="inline-element-list"></ul>

					<label class="sr-only hide-if-js" for="primary-faculty">Primary Faculty</label>
					<input class="hide-if-js" type="textbox" name="primary-faculty" id="primary-faculty" value="<?php echo esc_attr( $primary_faculty_first ); ?>" />
				</div>

				<div class="subpanel">
					<label for="additional-faculty-autocomplete">Additional Faculty</label>

					<p>If your course is taught by multiple instructors, list additional names on the Course Profile by typing the name in the box below, and select from the dropdown list. To become admins for this Course, these additional instructors must also join the Course and be promoted to admin.</p>

					<input class="hide-if-no-js" type="textbox" id="additional-faculty-autocomplete" value="" />

					<ul id="additional-faculty-list" class="inline-element-list"></ul>

					<label class="sr-only hide-if-js" for="additional-faculty">Additional Faculty</label>
					<input class="hide-if-js" type="textbox" name="additional-faculty" id="additional-faculty" value="<?php echo esc_attr( implode( ', ', $addl_faculty ) ); ?>" />
				</div>
		</fieldset>
	</div>
	<?php
}
add_action( 'bp_after_group_details_creation_step', 'openlab_course_faculty_metabox', 5 );
add_action( 'bp_after_group_details_admin', 'openlab_course_faculty_metabox', 5 );

/**
 * AJAX handler for additional faculty autocomplete.
 */
function openlab_additional_faculty_autocomplete_cb() {
	global $wpdb;

	$nonce = '';
	$term  = '';

	if ( isset( $_GET['nonce'] ) ) {
		$nonce = urldecode( $_GET['nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_faculty_autocomplete' ) ) {
		die( json_encode( -1 ) );
	}

	// @todo Permissions? Faculty only?

	if ( isset( $_GET['term'] ) ) {
		$term = urldecode( $_GET['term'] );
	}

	// Direct query for speed.
	$bp          = buddypress();
	$at_field_id = xprofile_get_field_id_from_name( 'Account Type' );
	$like        = $wpdb->esc_like( $term );
	$found       = $wpdb->get_results( $wpdb->prepare( "SELECT u.display_name, u.user_nicename FROM $wpdb->users u LEFT JOIN {$bp->profile->table_name_data} x ON (u.ID = x.user_id) WHERE ( u.display_name LIKE '%%{$like}%%' OR u.user_nicename LIKE '%%{$like}%%' ) AND x.field_id = %d AND x.value = 'Faculty'", $at_field_id ) );

	$retval = array();
	foreach ( (array) $found as $u ) {
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
 * Process the saving of additional and primary faculty.
 */
function openlab_group_faculty_save( $group ) {
	$nonce = '';

	if ( isset( $_POST['_ol_faculty_autocomplete_nonce'] ) ) {
		$nonce = urldecode( $_POST['_ol_faculty_autocomplete_nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_faculty_autocomplete' ) ) {
		return;
	}

	// Admins only.
	if ( ! current_user_can( 'bp_moderate' ) && ! groups_is_user_admin( bp_loggedin_user_id(), $group->id ) ) {
		return;
	}

	// Give preference to JS-saved items.
	$primary_faculty = isset( $_POST['primary-faculty-js'] ) ? $_POST['primary-faculty-js'] : null;
	if ( null === $primary_faculty ) {
		$primary_faculty = $_POST['primary-faculty'];
	}

	$addl_faculty = isset( $_POST['additional-faculty-js'] ) ? $_POST['additional-faculty-js'] : null;
	if ( null === $addl_faculty ) {
		$addl_faculty = $_POST['additional-faculty'];
	}

	// Delete all existing items.
	groups_delete_groupmeta( $group->id, 'primary_faculty' );
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

	foreach ( (array) $primary_faculty as $nicename ) {
		$f = get_user_by( 'slug', stripslashes( $nicename ) );

		if ( ! $f ) {
			continue;
		}

		// @todo Verify that it's a faculty member?
		groups_add_groupmeta( $group->id, 'primary_faculty', $f->ID );
	}
}
add_action( 'groups_group_after_save', 'openlab_group_faculty_save' );

/**
 * Render the "Group Contact" field when creating/editing a project or club.
 */
function openlab_group_contact_field() {
	$group_type = '';

	// Projects and clubs only.
	if ( bp_is_group() ) {
		$group_type = openlab_get_group_type( bp_get_current_group_id() );
	} elseif ( bp_is_group_create() && isset( $_GET['type'] ) ) {
		$group_type = urldecode( $_GET['type'] );
	}

	if ( ! in_array( $group_type, array( 'club', 'project' ), true ) ) {
		return;
	}

	// Enqueue JS and CSS.
	wp_enqueue_script( 'openlab-group-contact', plugins_url() . '/wds-citytech/assets/js/group-contact.js', array( 'jquery-ui-autocomplete' ) );
	wp_enqueue_style( 'openlab-group-contact', plugins_url() . '/wds-citytech/assets/css/group-contact.css' );

	$existing_contacts = array();
	if ( bp_is_group_create() ) {
		$group_id            = 0;
		$existing_contacts[] = bp_loggedin_user_id();
	} else {
		$group_id          = bp_get_current_group_id();
		$existing_contacts = openlab_get_group_contacts();
	}

	$existing_contacts_data = array();
	foreach ( $existing_contacts as $uid ) {
		$u                        = new WP_User( $uid );
		$existing_contacts_data[] = array(
			'label' => sprintf( '%s (%s)', esc_html( bp_core_get_user_displayname( $uid ) ), esc_html( $u->user_nicename ) ),
			'value' => esc_attr( $u->user_nicename ),
		);
	}

	wp_localize_script( 'openlab-group-contact', 'OL_Group_Contact_Existing', $existing_contacts_data );

	?>

	<div id="group-contact-admin" class="panel panel-default">
			<div class="panel-heading"><label for="group-contact-autocomplete"><?php echo ucwords( $group_type ); ?> Contact</label></div>
			<div class="panel-body">
		<p>By default, you are the <?php echo ucwords( $group_type ); ?> Contact. You may add or remove Contacts once your <?php echo $group_type; ?> has more members.</p>

		<label for="group-contact-autocomplete"><?php echo ucwords( $group_type ); ?> Contact</label>
		<input class="hide-if-no-js form-control" type="textbox" id="group-contact-autocomplete" value="" <?php disabled( bp_is_group_create() ); ?> />
		<?php wp_nonce_field( 'openlab_group_contact_autocomplete', '_ol_group_contact_nonce', false ); ?>
		<input type="hidden" name="group-contact-group-id" id="group-contact-group-id" value="<?php echo intval( $group_id ); ?>" />

		<ul id="group-contact-list" class="inline-element-list"></ul>

				<label class="sr-only hide-if-js" for="group-contacts">Group Contacts</label>
		<input class="hide-if-js" type="textbox" name="group-contacts" id="group-contacts" value="<?php echo esc_attr( implode( ', ', $existing_contacts ) ); ?>" />

			</div>
	</div>

	<?php
}
add_action( 'bp_after_group_details_creation_step', 'openlab_group_contact_field', 5 );
add_action( 'bp_after_group_details_admin', 'openlab_group_contact_field', 5 );

/**
 * AJAX handler for group contact autocomplete.
 */
function openlab_group_contact_autocomplete_cb() {
	global $wpdb;

	$nonce = '';
	$term  = '';

	if ( isset( $_GET['nonce'] ) ) {
		$nonce = urldecode( $_GET['nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_group_contact_autocomplete' ) ) {
		die( json_encode( -1 ) );
	}

	$group_id = isset( $_GET['group_id'] ) ? (int) $_GET['group_id'] : 0;
	if ( ! $group_id ) {
		die( json_encode( -1 ) );
	}

	if ( isset( $_GET['term'] ) ) {
		$term = urldecode( $_GET['term'] );
	}

	$q = new BP_Group_Member_Query(
		array(
			'group_id'     => $group_id,
			'search_terms' => $term,
			'type'         => 'alphabetical',
			'group_role'   => array( 'member', 'mod', 'admin' ),
		)
	);

	$retval = array();
	foreach ( $q->results as $u ) {
		$retval[] = array(
			'label' => sprintf( '%s (%s)', esc_html( $u->fullname ), esc_html( $u->user_nicename ) ),
			'value' => esc_attr( $u->user_nicename ),
		);
	}

	echo json_encode( $retval );
	die();
}
add_action( 'wp_ajax_openlab_group_contact_autocomplete', 'openlab_group_contact_autocomplete_cb' );

/**
 * Process the saving of group contacts.
 */
function openlab_group_contact_save( $group ) {
	$nonce = '';

	if ( isset( $_POST['_ol_group_contact_nonce'] ) ) {
		$nonce = urldecode( $_POST['_ol_group_contact_nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_group_contact_autocomplete' ) ) {
		return;
	}

	// Admins only.
	if ( ! groups_is_user_admin( bp_loggedin_user_id(), $group->id ) && ! current_user_can( 'bp_moderate' ) ) {
		return;
	}

	// Give preference to JS-saved items.
	$group_contact = isset( $_POST['group-contact-js'] ) ? $_POST['group-contact-js'] : null;
	if ( null === $group_contact ) {
		$group_contact = $_POST['group-contact'];
	}

	// Delete all existing items.
	$existing = openlab_get_group_contacts( $group->id );
	foreach ( $existing as $e ) {
		groups_delete_groupmeta( $group->id, 'group_contact', $e );
	}

	foreach ( (array) $group_contact as $nicename ) {
		$f = get_user_by( 'slug', stripslashes( $nicename ) );

		if ( ! $f ) {
			continue;
		}

		if ( ! groups_is_user_member( $f->ID, $group->id ) ) {
			continue;
		}

		groups_add_groupmeta( $group->id, 'group_contact', $f->ID );
	}
}
add_action( 'groups_group_after_save', 'openlab_group_contact_save' );

/**
 * Gets a list of group contact IDs.
 *
 * @param int $group_id ID of the group.
 * @return array
 */
function openlab_get_group_contacts( $group_id ) {
	$contact_ids = groups_get_groupmeta( bp_get_current_group_id(), 'group_contact', false );
	if ( ! $contact_ids ) {
		$contact_ids = [];
	}
	return array_map( 'intval', $contact_ids );
}

/**
 * Gets a list of group primary faculty IDs.
 *
 * @param int $group_id ID of the group.
 * @return array
 */
function openlab_get_primary_faculty( $group_id ) {
	$primary_faculty_ids = [];

	$primary_faculty_id = groups_get_groupmeta( bp_get_current_group_id(), 'primary_faculty', true );
	if ( $primary_faculty_id ) {
		$primary_faculty_ids[] = (int) $primary_faculty_id;
	}

	return $primary_faculty_ids;
}
