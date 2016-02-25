<?php

/**
 * Registered report callbacks.
 *
 * @return array
 */
function olur_report_callbacks() {
	$callbacks = array(
		// Users.
		'user_student_counts' => 'olur_user_student_counts',
		'user_faculty_counts' => 'olur_user_faculty_counts',
		'user_staff_counts'   => 'olur_user_staff_counts',
		'user_alumni_counts'  => 'olur_user_alumni_counts',
		'user_other_counts'   => 'olur_user_other_counts',
		'user_total_counts'   => 'olur_user_total_counts',

		// Groups.
		'group_course_public_counts' => 'olur_group_course_public_counts',
		'group_course_private_counts' => 'olur_group_course_private_counts',
		'group_course_hidden_counts' => 'olur_group_course_hidden_counts',

		'group_club_public_counts' => 'olur_group_club_public_counts',
		'group_club_private_counts' => 'olur_group_club_private_counts',
		'group_club_hidden_counts' => 'olur_group_club_hidden_counts',

		'group_project_public_counts' => 'olur_group_project_public_counts',
		'group_project_private_counts' => 'olur_group_project_private_counts',
		'group_project_hidden_counts' => 'olur_group_project_hidden_counts',

		'group_student_eportfolio_public_counts' => 'olur_group_student_eportfolio_public_counts',
		'group_student_eportfolio_private_counts' => 'olur_group_student_eportfolio_private_counts',
		'group_student_eportfolio_hidden_counts' => 'olur_group_student_eportfolio_hidden_counts',

		'group_faculty_portfolio_public_counts' => 'olur_group_faculty_portfolio_public_counts',
		'group_faculty_portfolio_private_counts' => 'olur_group_faculty_portfolio_private_counts',
		'group_faculty_portfolio_hidden_counts' => 'olur_group_faculty_portfolio_hidden_counts',


		'group_staff_portfolio_public_counts' => 'olur_group_staff_portfolio_public_counts',
		'group_staff_portfolio_private_counts' => 'olur_group_staff_portfolio_private_counts',
		'group_staff_portfolio_hidden_counts' => 'olur_group_staff_portfolio_hidden_counts',
	);

	return $callbacks;
}

/**
 * Generate a report and serve as a CSV.
 *
 * @param string $start MySQL-formatted start date.
 * @param string $end MySQL-formatted end date.
 */
function olur_generate_report( $start, $end ) {
	$data = olur_generate_report_data( $start, $end );

	$start_formatted = date( 'Y-m-d', strtotime( $start ) );
	$end_formatted   = date( 'Y-m-d', strtotime( $end ) );
	$filename = 'openlab-usage-' . date( 'Y-m-d', $start_formatted ) . '-through-' . $end_formatted . '.csv';

	$title_row = array(
		sprintf( 'OpenLab usage for the dates %s through %s', $start_formatted, $end_formatted ),
	);

	$header_row = array(
		0 => '',
		1 => 'Start #',
		2 => '# Created',
		3 => '# Deleted',
		4 => 'End #',
	);

	$data_rows = array();

	$data_rows[] = array_merge(
		array( 'Students' ),
		array_values( $data['user_student_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Faculty' ),
		array_values( $data['user_faculty_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Staff' ),
		array_values( $data['user_staff_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Alumni' ),
		array_values( $data['user_alumni_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Other' ),
		array_values( $data['user_other_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Total' ),
		array_values( $data['user_total_counts'] )
	);

	// Blank
	$data_rows[] = array();

	// Courses
	$data_rows[] = array_merge(
		array( 'Courses (Public)' ),
		array_values( $data['group_course_public_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Courses (Private)' ),
		array_values( $data['group_course_private_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Courses (Hidden)' ),
		array_values( $data['group_course_hidden_counts'] )
	);

	// Blank
	$data_rows[] = array();

	// Clubs.
	$data_rows[] = array_merge(
		array( 'Clubs (Public)' ),
		array_values( $data['group_club_public_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Clubs (Private)' ),
		array_values( $data['group_club_private_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Clubs (Hidden)' ),
		array_values( $data['group_club_hidden_counts'] )
	);

	// Blank
	$data_rows[] = array();

	// Projects.
	$data_rows[] = array_merge(
		array( 'Projects (Public)' ),
		array_values( $data['group_project_public_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Projects (Private)' ),
		array_values( $data['group_project_private_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Projects (Hidden)' ),
		array_values( $data['group_project_hidden_counts'] )
	);

	// Blank
	$data_rows[] = array();

	// Student ePortfolios.
	$data_rows[] = array_merge(
		array( 'Student ePortfolios (Public)' ),
		array_values( $data['group_student_eportfolio_public_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Student ePortfolios (Private)' ),
		array_values( $data['group_student_eportfolio_private_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Student ePortfolios (Hidden)' ),
		array_values( $data['group_student_eportfolio_hidden_counts'] )
	);

	// Blank
	$data_rows[] = array();

	// Faculty Portfolio.
	$data_rows[] = array_merge(
		array( 'Faculty Portfolios (Public)' ),
		array_values( $data['group_faculty_portfolio_public_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Faculty Portfolios (Private)' ),
		array_values( $data['group_faculty_portfolio_private_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Faculty Portfolios (Hidden)' ),
		array_values( $data['group_faculty_portfolio_hidden_counts'] )
	);

	// Blank
	$data_rows[] = array();

	// Staff Portfolio.
	$data_rows[] = array_merge(
		array( 'Staff Portfolios (Public)' ),
		array_values( $data['group_staff_portfolio_public_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Staff Portfolios (Private)' ),
		array_values( $data['group_staff_portfolio_private_counts'] )
	);

	$data_rows[] = array_merge(
		array( 'Staff Portfolios (Hidden)' ),
		array_values( $data['group_staff_portfolio_hidden_counts'] )
	);

	$fh = @fopen( 'php://output', 'w' );

	//fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );

	fputcsv( $fh, $title_row );
	fputcsv( $fh, $header_row );

	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}

	fclose( $fh );
	die();
}

/**
 * Generate report data.
 *
 * @param string $start MySQL-formatted start date.
 * @param string $end MySQL-formatted end date.
 * @return array
 */
function olur_generate_report_data( $start, $end ) {
	$data = array();
	$callbacks = olur_report_callbacks();

	foreach ( $callbacks as $cb_label => $cb ) {
		$data[ $cb_label ] = call_user_func( $cb, $start, $end );
	}

	return $data;
}

/** Callbacks ****************************************************************/

function olur_user_counts( $start, $end, $user_type ) {
	global $wpdb;

	$counts = array(
		'start'   => '',
		'created' => '',
		'deleted' => '',
		'end'     => '',
	);

	$bp = buddypress();
	$ut_subquery = $wpdb->prepare( "SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = 7 AND value = %s", $user_type );

	// Start
	$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID IN ({$ut_subquery}) AND user_registered < %s", $start ) );

	// End
	$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID IN ({$ut_subquery}) AND user_registered < %s", $end ) );

	// Created
	$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID IN ({$ut_subquery}) AND user_registered >= %s AND user_registered < %s", $start, $end ) );

	return array_map( 'intval', $counts );
}

function olur_user_student_counts( $start, $end ) {
	return olur_user_counts( $start, $end, 'Student' );
}

function olur_user_faculty_counts( $start, $end ) {
	return olur_user_counts( $start, $end, 'Faculty' );
}

function olur_user_staff_counts( $start, $end ) {
	return olur_user_counts( $start, $end, 'Staff' );
}

function olur_user_alumni_counts( $start, $end ) {
	return olur_user_counts( $start, $end, 'Alumni' );
}

function olur_user_other_counts( $start, $end ) {
	global $wpdb;

	$counts = array(
		'start'   => '',
		'created' => '',
		'deleted' => '',
		'end'     => '',
	);

	$bp = buddypress();

	// Note that this is for a NOT IN.
	$student_subquery = "SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = 7 AND value IN ('Student','Faculty','Staff','Alumni')";

	// Start
	$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID NOT IN ({$student_subquery}) AND user_registered < %s", $start ) );

	// End
	$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID NOT IN ({$student_subquery}) AND user_registered < %s", $end ) );

	// Created
	$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND ID NOT IN ({$student_subquery}) AND user_registered >= %s AND user_registered < %s", $start, $end ) );

	return array_map( 'intval', $counts );
}

function olur_user_total_counts( $start, $end ) {
	global $wpdb;

	$counts = array(
		'start'   => '',
		'created' => '',
		'deleted' => '',
		'end'     => '',
	);

	$bp = buddypress();

	// Start
	$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND user_registered < %s", $start ) );

	// End
	$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND user_registered < %s", $end ) );

	// Created
	$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE deleted != 1 AND spam != 1 AND user_registered >= %s AND user_registered < %s", $start, $end ) );

	return array_map( 'intval', $counts );
}

function olur_group_course_public_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'course', 'public' );
}

function olur_group_course_private_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'course', 'private' );
}

function olur_group_course_hidden_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'course', 'hidden' );
}

function olur_group_club_public_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'club', 'public' );
}

function olur_group_club_private_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'club', 'private' );
}

function olur_group_club_hidden_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'club', 'hidden' );
}

function olur_group_project_public_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'project', 'public' );
}

function olur_group_project_private_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'project', 'private' );
}

function olur_group_project_hidden_counts( $start, $end ) {
	return olur_group_counts( $start, $end, 'project', 'hidden' );
}

function olur_group_student_eportfolio_public_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'public', 'student' );
}

function olur_group_student_eportfolio_private_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'private', 'student' );
}

function olur_group_student_eportfolio_hidden_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'hidden', 'student' );
}

function olur_group_faculty_portfolio_public_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'public', 'faculty' );
}

function olur_group_faculty_portfolio_private_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'private', 'faculty' );
}

function olur_group_faculty_portfolio_hidden_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'hidden', 'faculty' );
}

function olur_group_staff_portfolio_public_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'public', 'staff' );
}

function olur_group_staff_portfolio_private_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'private', 'staff' );
}

function olur_group_staff_portfolio_hidden_counts( $start, $end ) {
	return olur_portfolio_counts( $start, $end, 'hidden', 'staff' );
}

function olur_group_counts( $start, $end, $group_type, $group_status ) {
	global $wpdb;

	$counts = array(
		'start'   => '',
		'created' => '',
		'deleted' => '',
		'end'     => '',
	);

	$bp = buddypress();
	$gt_subquery = $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = %s", $group_type );

	$statuses = array();
	foreach ( (array) $group_status as $status ) {
		$statuses[] = $wpdb->prepare( '%s', $status );
	}
	$status_sql = implode( ', ', $statuses );

	// Start
	$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) AND status IN ({$status_sql}) AND date_created < %s", $start ) );

	// End
	$counts['end'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) AND status IN ({$status_sql}) AND date_created < %s", $end ) );

	// Created
	$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} WHERE id IN ({$gt_subquery}) AND status IN ({$status_sql}) AND date_created >= %s AND date_created < %s", $start, $end ) );

	return array_map( 'intval', $counts );
}

function olur_portfolio_counts( $start, $end, $group_status, $user_type ) {
	global $wpdb;

	$counts = array(
		'start'   => '',
		'created' => '',
		'deleted' => '',
		'end'     => '',
	);

	$bp = buddypress();
	$gt_subquery = "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = 'portfolio'";
	$ut_subquery = $wpdb->prepare( "SELECT p.user_id FROM {$bp->profile->table_name_data} p WHERE p.field_id = 7 AND p.value = %s", ucwords( $user_type ) );

	$statuses = array();
	foreach ( (array) $group_status as $status ) {
		$statuses[] = $wpdb->prepare( '%s', $status );
	}
	$status_sql = implode( ', ', $statuses );

	// Start
	$counts['start'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g INNER JOIN {$wpdb->usermeta} um ON (g.id = um.meta_value AND um.meta_key = 'portfolio_group_id') WHERE g.id IN ({$gt_subquery}) AND um.user_id IN ({$ut_subquery}) AND g.status IN ({$status_sql}) AND g.date_created < %s", $start ) );

	// End
	$counts['end']   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g INNER JOIN {$wpdb->usermeta} um ON (g.id = um.meta_value AND um.meta_key = 'portfolio_group_id') WHERE g.id IN ({$gt_subquery}) AND um.user_id IN ({$ut_subquery}) AND g.status IN ({$status_sql}) AND g.date_created < %s", $end ) );

	// Created
	$counts['created'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name} g INNER JOIN {$wpdb->usermeta} um ON (g.id = um.user_id AND um.meta_key = 'portfolio_group_id') WHERE g.id IN ({$gt_subquery}) AND um.user_id IN ({$ut_subquery}) AND g.status IN ({$status_sql}) AND g.date_created >= %s AND g.date_created < %s", $start, $end ) );

	return array_map( 'intval', $counts );
}
