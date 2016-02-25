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
			'',

			array( 'label' => 'Clubs (Public)', 'type' => 'club', 'status' => 'public' ),
			array( 'label' => 'Clubs (Private)', 'type' => 'club', 'status' => 'private' ),
			array( 'label' => 'Clubs (Hidden)', 'type' => 'club', 'status' => 'hidden' ),

			'',

			array( 'label' => 'Projects (Public)', 'type' => 'project', 'status' => 'public' ),
			array( 'label' => 'Projects (Private)', 'type' => 'project', 'status' => 'private' ),
			array( 'label' => 'Projects (Hidden)', 'type' => 'project', 'status' => 'hidden' ),
		),

		// Portfolios.
		'Portfolio' => array(
			array( 'label' => 'Student ePortfolios (Public)', 'type' => 'student', 'status' => 'public' ),
			array( 'label' => 'Student ePortfolios (Private)', 'type' => 'student', 'status' => 'private' ),
			array( 'label' => 'Student ePortfolios (Hidden)', 'type' => 'student', 'status' => 'hidden' ),

			'',

			array( 'label' => 'Faculty Portfolios (Public)', 'type' => 'faculty', 'status' => 'public' ),
			array( 'label' => 'Faculty Portfolios (Private)', 'type' => 'faculty', 'status' => 'private' ),
			array( 'label' => 'Faculty Portfolios (Hidden)', 'type' => 'faculty', 'status' => 'hidden' ),

			'',

			array( 'label' => 'Staff Portfolios (Public)', 'type' => 'staff', 'status' => 'public' ),
			array( 'label' => 'Staff Portfolios (Private)', 'type' => 'staff', 'status' => 'private' ),
			array( 'label' => 'Staff Portfolios (Hidden)', 'type' => 'staff', 'status' => 'hidden' ),
		),

		// User activity.
		'UserActivity' => array(
			'New Avatar',
			array( 'label' => 'Students', 'member_type' => 'student', 'component' => 'profile', 'type' => 'new_avatar' ),
			array( 'label' => 'Faculty', 'member_type' => 'faculty', 'component' => 'profile', 'type' => 'new_avatar' ),
			array( 'label' => 'Staff', 'member_type' => 'staff', 'component' => 'profile', 'type' => 'new_avatar' ),
			array( 'label' => 'Alumni', 'member_type' => 'alumni', 'component' => 'profile', 'type' => 'new_avatar' ),
			array( 'label' => 'Other', 'member_type' => 'other', 'component' => 'profile', 'type' => 'new_avatar' ),

			'',
			'Created New Site',
			array( 'label' => 'Students', 'member_type' => 'student', 'component' => 'blogs', 'type' => 'new_blog' ),
			array( 'label' => 'Faculty', 'member_type' => 'faculty', 'component' => 'blogs', 'type' => 'new_blog' ),
			array( 'label' => 'Staff', 'member_type' => 'staff', 'component' => 'blogs', 'type' => 'new_blog' ),
			array( 'label' => 'Alumni', 'member_type' => 'alumni', 'component' => 'blogs', 'type' => 'new_blog' ),
			array( 'label' => 'Other', 'member_type' => 'other', 'component' => 'blogs', 'type' => 'new_blog' ),

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
			// If the query is a string, use it to populate a full row.
			// Used for blank rows and other labels.
			if ( ! is_array( $query ) ) {
				$data[] = array( $query );
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
