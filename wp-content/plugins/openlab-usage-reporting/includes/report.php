<?php

/**
 * Registered report callbacks.
 *
 * @return array
 */
function olur_report_callbacks() {
	$callbacks = array(
		// Users.
		'User' => array(
			'Students' => array( 'label' => 'Students', 'type' => 'student' ),
			'Faculty'  => array( 'label' => 'Faculty', 'type' => 'faculty' ),
			'Staff'    => array( 'label' => 'Staff', 'type' => 'staff' ),
			'Alumni'   => array( 'label' => 'Alumni', 'type' => 'alumni' ),
			'Other'    => array( 'label' => 'Other', 'type' => 'other' ),
			'Total'    => array( 'label' => 'Total', 'type' => 'total' ),
		),

		// Groups.
		'Group' => array(
			array( 'label' => 'Courses (Public)', 'type' => 'course', 'status' => 'public' ),
			array( 'label' => 'Courses (Private)', 'type' => 'course', 'status' => 'private' ),
			array( 'label' => 'Courses (Hidden)', 'type' => 'course', 'status' => 'hidden' ),
			array( 'label' => 'Courses (Total)', 'type' => 'course', 'status' => 'any' ),
			'',

			array( 'label' => 'Clubs (Public)', 'type' => 'club', 'status' => 'public' ),
			array( 'label' => 'Clubs (Private)', 'type' => 'club', 'status' => 'private' ),
			array( 'label' => 'Clubs (Hidden)', 'type' => 'club', 'status' => 'hidden' ),
			array( 'label' => 'Clubs (Total)', 'type' => 'club', 'status' => 'any' ),

			'',

			array( 'label' => 'Projects (Public)', 'type' => 'project', 'status' => 'public' ),
			array( 'label' => 'Projects (Private)', 'type' => 'project', 'status' => 'private' ),
			array( 'label' => 'Projects (Hidden)', 'type' => 'project', 'status' => 'hidden' ),
			array( 'label' => 'Projects (Total)', 'type' => 'project', 'status' => 'any' ),
		),

		// Portfolios.
		'Portfolio' => array(
			array( 'label' => 'Student ePortfolios (Public)', 'type' => 'student', 'status' => 'public' ),
			array( 'label' => 'Student ePortfolios (Private)', 'type' => 'student', 'status' => 'private' ),
			array( 'label' => 'Student ePortfolios (Hidden)', 'type' => 'student', 'status' => 'hidden' ),
			array( 'label' => 'Student ePortfolios (Total)', 'type' => 'student', 'status' => 'any' ),

			'',

			array( 'label' => 'Faculty Portfolios (Public)', 'type' => 'faculty', 'status' => 'public' ),
			array( 'label' => 'Faculty Portfolios (Private)', 'type' => 'faculty', 'status' => 'private' ),
			array( 'label' => 'Faculty Portfolios (Hidden)', 'type' => 'faculty', 'status' => 'hidden' ),
			array( 'label' => 'Faculty Portfolios (Total)', 'type' => 'faculty', 'status' => 'any' ),

			'',

			array( 'label' => 'Staff Portfolios (Public)', 'type' => 'staff', 'status' => 'public' ),
			array( 'label' => 'Staff Portfolios (Private)', 'type' => 'staff', 'status' => 'private' ),
			array( 'label' => 'Staff Portfolios (Hidden)', 'type' => 'staff', 'status' => 'hidden' ),
			array( 'label' => 'Staff Portfolios (Total)', 'type' => 'staff', 'status' => 'any' ),
		),

		// Activity.
		'Activity' => array(

			array( 'PROFILES', 'Total Instances', 'Total Unique Users', 'Students', 'Faculty', 'Staff', 'Alumni', 'Other Users' ),
			array( 'label' => 'New Avatar', 'component' => 'profile', 'type' => 'new_avatar' ),
			array( 'label' => 'Profile Update', 'component' => 'xprofile', 'type' => 'updated_profile' ),

			// @todo These are probably not accurate because of 'site_public'.
			'',
			array( 'SITES', 'Total Instances', 'Total Unique Users', 'Students', 'Faculty', 'Staff', 'Alumni', 'Other Users', 'Groups', 'Courses', 'Clubs', 'Projects', 'ePortfolios', 'Portfolios' ),
			array( 'label' => 'New Site', 'component' => 'groups', 'type' => 'new_blog' ),
			array( 'label' => 'New Site Posts', 'component' => 'groups', 'type' => 'new_blog_post' ),
			array( 'label' => 'New Site Comments', 'component' => 'groups', 'type' => 'new_blog_comment' ),

			'',
			array( 'GROUP FILES', 'Total Instances', 'Total Unique Users', 'Students', 'Faculty', 'Staff', 'Alumni', 'Other Users', 'Groups', 'Courses', 'Clubs', 'Projects', 'ePortfolios', 'Portfolios' ),
			array( 'label' => 'Group File Created', 'component' => 'groups', 'type' => 'added_group_document' ),
			array( 'label' => 'Group File Edited', 'component' => 'groups', 'type' => 'edited_group_document' ),
			array( 'label' => 'Group File Deleted', 'component' => 'groups', 'type' => 'deleted_group_document' ),

			'',
			array( 'DISCUSSION FORUMS (since 2014)', 'Total Instances', 'Total Unique Users', 'Students', 'Faculty', 'Staff', 'Alumni', 'Other Users', 'Groups', 'Courses', 'Clubs', 'Projects', 'ePortfolios', 'Portfolios' ),
			array( 'label' => 'New Topics', 'component' => 'groups', 'type' => 'bbp_topic_create' ),
			array( 'label' => 'Replies', 'component' => 'groups', 'type' => 'bbp_reply_create' ),

			'',
			array( 'DOCS', 'Total Instances', 'Total Unique Users', 'Students', 'Faculty', 'Staff', 'Alumni', 'Other Users', 'Groups', 'Courses', 'Clubs', 'Projects', 'ePortfolios', 'Portfolios' ),
			array( 'label' => 'New Doc', 'component' => 'groups', 'type' => 'bp_doc_created' ),
			array( 'label' => 'Edit Doc', 'component' => 'groups', 'type' => 'bp_doc_edited' ),
			array( 'label' => 'New Doc Comment', 'component' => 'groups', 'type' => 'bp_doc_comment' ),

			'',
			array( 'GROUP JOINS', 'Total Instances', 'Total Unique Users', 'Students', 'Faculty', 'Staff', 'Alumni', 'Other Users', 'Groups', 'Courses', 'Clubs', 'Projects', 'ePortfolios', 'Portfolios' ),
			array( 'label' => 'Joined Group', 'component' => 'groups', 'type' => 'joined_group' ),

		),

		// Friendships.
		'Friend' => array(
			array( 'FRIENDS (Confirmed/Pending)', 'Student', 'Faculty', 'Staff', 'Alumni', 'Other', 'Total' ),
			array( 'label' => 'Student', 'type' => 'student' ),
			array( 'label' => 'Faculty', 'type' => 'faculty' ),
			array( 'label' => 'Staff', 'type' => 'staff' ),
			array( 'label' => 'Alumni', 'type' => 'alumni' ),
			array( 'label' => 'Other', 'type' => 'other' ),
			array( 'label' => 'Total', 'type' => 'total' ),
		),
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
	$filename = sprintf( 'openlab-usage-%s-through-%s.csv', $start_formatted, $end_formatted );

	$title_row = array(
		sprintf( 'OpenLab usage for the dates %s through %s', $start_formatted, $end_formatted ),
	);

	$header_row = array(
		0 => '',
		1 => 'Start #',
		2 => '# Created',
		4 => 'End #',
		5 => 'Actively Active',
		6 => 'Passively Active',
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

	foreach ( $data as $data_row ) {
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

	foreach ( $callbacks as $class_name => $queries ) {
		$class_name = '\OLUR\\' . $class_name;
		$counter    = new $class_name;

		$counter->set_start( $start );
		$counter->set_end( $end );

		foreach ( $queries as $query ) {
			// If the query doesn't have a label, it's a literal.
			// Used for blank rows and other labels.
			if ( ! isset( $query['label'] ) ) {
				$data[] = (array) $query;
			} else {
				$counter->set_label( $query['label'] );
				unset( $query['label'] );

				$counter->query( $query );

				$data[] = $counter->format_results_for_csv();
			}
		}

		// Insert an empty row after each section.
		$data[] = array();
	}

	return $data;
}

/**
 * Generate report data.
 *
 * @param string $start MySQL-formatted start date.
 * @param string $end MySQL-formatted end date.
 * @return array
 */
function olur_generate_report_data_row( $class, $callback, $start, $end ) {
	$class_name = '\OLUR\\' . $class;
	$counter    = new $class_name;

	$counter->set_start( $start );
	$counter->set_end( $end );

	// If the query doesn't have a label, it's a literal.
	// Used for blank rows and other labels.
	if ( ! isset( $callback['label'] ) ) {
		$data = $callback;
	} else {
		$counter->set_label( $callback['label'] );
		unset( $callback['label'] );

		$counter->query( $callback );

		$data = $counter->format_results_for_csv();
	}

	return $data;
}
