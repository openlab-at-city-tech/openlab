<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AN_GradeBook_REST_Stats {

	public function register_routes() {
		register_rest_route( 'an-gradebook/v1', '/stats/assignment/(?P<amid>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_pie_chart' ),
			'permission_callback' => array( $this, 'logged_in_permission' ),
		) );

		register_rest_route( 'an-gradebook/v1', '/stats/student', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_line_chart' ),
			'permission_callback' => array( $this, 'admin_permission' ),
		) );
	}

	public function admin_permission() {
		return current_user_can( 'manage_options' );
	}

	public function logged_in_permission() {
		return is_user_logged_in();
	}

	public function get_pie_chart( $request ) {
		global $wpdb;
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_assignments = an_gradebook_table( 'an_assignments' );
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );

		$amid = absint( $request['amid'] );

		// Look up which course this assignment belongs to.
		$gbid = $wpdb->get_var( $wpdb->prepare(
			"SELECT gbid FROM {$table_assignments} WHERE id = %d",
			$amid
		) );

		if ( ! $gbid ) {
			return new WP_Error( 'not_found', 'Assignment not found.', array( 'status' => 404 ) );
		}

		// Non-admin users must be enrolled in the course.
		if ( ! current_user_can( 'manage_options' ) ) {
			$current_user = wp_get_current_user();
			$enrolled     = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$table_gradebook} WHERE uid = %d AND gbid = %d",
				$current_user->ID,
				$gbid
			) );

			if ( ! $enrolled ) {
				return new WP_Error( 'forbidden', 'You are not enrolled in this course.', array( 'status' => 403 ) );
			}
		}

		$pie_chart_data = $wpdb->get_col( $wpdb->prepare(
			"SELECT assign_points_earned FROM {$table_assignment} WHERE amid = %d",
			$amid
		) );

		$is_A = count( array_filter( $pie_chart_data, function ( $n ) { return $n >= 90; } ) );
		$is_B = count( array_filter( $pie_chart_data, function ( $n ) { return $n >= 80 && $n < 90; } ) );
		$is_C = count( array_filter( $pie_chart_data, function ( $n ) { return $n >= 70 && $n < 80; } ) );
		$is_D = count( array_filter( $pie_chart_data, function ( $n ) { return $n >= 60 && $n < 70; } ) );
		$is_F = count( array_filter( $pie_chart_data, function ( $n ) { return $n < 60; } ) );

		return rest_ensure_response( array(
			'grades' => array( intval( $is_A ), intval( $is_B ), intval( $is_C ), intval( $is_D ), intval( $is_F ) ),
		) );
	}

	public function get_line_chart( $request ) {
		global $wpdb;
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_assignments = an_gradebook_table( 'an_assignments' );

		$uid  = absint( $request['uid'] );
		$gbid = absint( $request['gbid'] );

		$student_grades = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_assignment} WHERE uid = %d AND gbid = %d",
			$uid,
			$gbid
		), ARRAY_A );

		$assignments = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_assignments} WHERE gbid = %d",
			$gbid
		), ARRAY_A );

		foreach ( $student_grades as &$grade ) {
			$grade['assign_order']         = intval( $grade['assign_order'] );
			$grade['assign_points_earned'] = intval( $grade['assign_points_earned'] );
			foreach ( $assignments as $assignment ) {
				if ( $assignment['id'] == $grade['amid'] ) {
					$all_scores    = $wpdb->get_col( $wpdb->prepare(
						"SELECT assign_points_earned FROM {$table_assignment} WHERE amid = %d",
						$assignment['id']
					) );
					$count         = count( $all_scores );
					$class_average = $count > 0 ? array_sum( $all_scores ) / $count : 0;
					$grade         = array_merge( $grade, array(
						'assign_name'   => sanitize_text_field( $assignment['assign_name'] ),
						'class_average' => floatval( $class_average ),
					) );
				}
			}
		}

		$result = array( array( 'Assignment', 'Student Score', 'Class Average' ) );
		foreach ( $student_grades as $grade ) {
			$result[] = array(
				sanitize_text_field( $grade['assign_name'] ),
				intval( $grade['assign_points_earned'] ),
				floatval( $grade['class_average'] ),
			);
		}

		return rest_ensure_response( $result );
	}
}
