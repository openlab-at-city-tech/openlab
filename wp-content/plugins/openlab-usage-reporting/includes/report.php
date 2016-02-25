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
			'Students' => array( 'type' => 'student' ),
			'Faculty'  => array( 'type' => 'faculty' ),
			'Staff'    => array( 'type' => 'staff' ),
			'Alumni'   => array( 'type' => 'alumni' ),
			'Other'    => array( 'type' => 'other' ),
			'Total'    => array( 'type' => 'total' ),
		),

		// Groups.
		'Group' => array(
			'Courses (Public)'  => array( 'type' => 'course', 'status' => 'public' ),
			'Courses (Private)' => array( 'type' => 'course', 'status' => 'private' ),
			'Courses (Hidden)'  => array( 'type' => 'course', 'status' => 'hidden' ),
			'',

			'Clubs (Public)'  => array( 'type' => 'club', 'status' => 'public' ),
			'Clubs (Private)' => array( 'type' => 'club', 'status' => 'private' ),
			'Clubs (Hidden)'  => array( 'type' => 'club', 'status' => 'hidden' ),

			'',

			'Projects (Public)'  => array( 'type' => 'project', 'status' => 'public' ),
			'Projects (Private)' => array( 'type' => 'project', 'status' => 'private' ),
			'Projects (Hidden)'  => array( 'type' => 'project', 'status' => 'hidden' ),
		),

		// Portfolios.
		'Portfolio' => array(
			'Student ePortfolios (Public)'  => array( 'type' => 'student', 'status' => 'public' ),
			'Student ePortfolios (Private)' => array( 'type' => 'student', 'status' => 'private' ),
			'Student ePortfolios (Hidden)'  => array( 'type' => 'student', 'status' => 'hidden' ),

			'',

			'Faculty Portfolios (Public)'  => array( 'type' => 'faculty', 'status' => 'public' ),
			'Faculty Portfolios (Private)' => array( 'type' => 'faculty', 'status' => 'private' ),
			'Faculty Portfolios (Hidden)'  => array( 'type' => 'faculty', 'status' => 'hidden' ),

			'',

			'Staff Portfolios (Public)'  => array( 'type' => 'staff', 'status' => 'public' ),
			'Staff Portfolios (Private)' => array( 'type' => 'staff', 'status' => 'private' ),
			'Staff Portfolios (Hidden)'  => array( 'type' => 'staff', 'status' => 'hidden' ),
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

		foreach ( $queries as $query_label => $query ) {
			// If the query label is a string, use it to populate a full row.
			// Used for blank rows and other labels.
			if ( ! is_array( $query ) ) {
				$data[] = array( $query );
			} else {
				$counter->set_label( $query_label );
				$counter->query( $query );

				$data[] = $counter->format_results_for_csv();
			}
		}

		// Insert an empty row after each section.
		$data[] = array();
	}

	return $data;
}
