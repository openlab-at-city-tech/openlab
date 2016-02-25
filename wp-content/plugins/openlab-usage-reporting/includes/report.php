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
			array( 'type' => 'student' ),
			array( 'type' => 'faculty' ),
			array( 'type' => 'staff' ),
			array( 'type' => 'alumni' ),
			array( 'type' => 'other' ),
			array( 'type' => 'total' ),
		),

		// Groups.
		'Group' => array(
			array( 'type' => 'course', 'status' => 'public' ),
			array( 'type' => 'course', 'status' => 'private' ),
			array( 'type' => 'course', 'status' => 'hidden' ),

			array( 'type' => 'club', 'status' => 'public' ),
			array( 'type' => 'club', 'status' => 'private' ),
			array( 'type' => 'club', 'status' => 'hidden' ),

			array( 'type' => 'project', 'status' => 'public' ),
			array( 'type' => 'project', 'status' => 'private' ),
			array( 'type' => 'project', 'status' => 'hidden' ),
		),

		// Portfolios.
		'Portfolio' => array(
			array( 'type' => 'student', 'status' => 'public' ),
			array( 'type' => 'student', 'status' => 'private' ),
			array( 'type' => 'student', 'status' => 'hidden' ),

			array( 'type' => 'faculty', 'status' => 'public' ),
			array( 'type' => 'faculty', 'status' => 'private' ),
			array( 'type' => 'faculty', 'status' => 'hidden' ),

			array( 'type' => 'staff', 'status' => 'public' ),
			array( 'type' => 'staff', 'status' => 'private' ),
			array( 'type' => 'staff', 'status' => 'hidden' ),
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

	foreach ( $callbacks as $class_name => $queries ) {
		$class_name = '\OLUR\\' . $class_name;
		$counter = new $class_name;
		$counter->set_start( $start );
		$counter->set_end( $end );
		foreach ( $queries as $query ) {
			$results = $counter->query( $query );
			$data[ $results['label'] ] = $results['results'];
		}
	}

	return $data;
}
