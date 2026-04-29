<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AN_GradeBook_REST_Student_View {

	public function register_routes() {
		register_rest_route( 'an-gradebook/v1', '/student/courses', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_courses' ),
			'permission_callback' => array( $this, 'logged_in_permission' ),
		) );

		register_rest_route( 'an-gradebook/v1', '/student/courses/(?P<id>\d+)/gradebook', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_gradebook' ),
			'permission_callback' => array( $this, 'logged_in_permission' ),
		) );

		register_rest_route( 'an-gradebook/v1', '/stats/student/me', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_line_chart' ),
			'permission_callback' => array( $this, 'logged_in_permission' ),
		) );
	}

	public function logged_in_permission() {
		return is_user_logged_in();
	}

	public function get_courses() {
		global $wpdb;
		$table_gradebook  = an_gradebook_table( 'an_gradebook' );
		$table_gradebooks = an_gradebook_table( 'an_gradebooks' );

		$current_user = wp_get_current_user();
		$gbids        = $wpdb->get_col( $wpdb->prepare(
			"SELECT gbid FROM {$table_gradebook} WHERE uid = %d",
			$current_user->ID
		) );

		if ( empty( $gbids ) ) {
			return rest_ensure_response( array() );
		}

		$gbids        = array_map( 'absint', $gbids );
		$placeholders = implode( ',', array_fill( 0, count( $gbids ), '%d' ) );
		$courses      = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_gradebooks} WHERE id IN ({$placeholders})",
			$gbids
		), ARRAY_A );

		return rest_ensure_response( array_map( array( $this, 'prepare_course' ), $courses ) );
	}

	public function get_gradebook( $request ) {
		global $wpdb;
		$table_assignments = an_gradebook_table( 'an_assignments' );
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );

		$gbid         = absint( $request['id'] );
		$current_user = wp_get_current_user();

		// Verify student is enrolled in this course.
		$enrolled = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_gradebook} WHERE uid = %d AND gbid = %d",
			$current_user->ID,
			$gbid
		) );

		if ( ! $enrolled ) {
			return new WP_Error( 'forbidden', 'You are not enrolled in this course.', array( 'status' => 403 ) );
		}

		// Only visible assignments
		$assignments = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_assignments} WHERE assign_visibility = 'Students' AND gbid = %d",
			$gbid
		), ARRAY_A );

		if ( empty( $assignments ) ) {
			$student = get_userdata( $current_user->ID );
			return rest_ensure_response( array(
				'assignments' => array(),
				'cells'       => array(),
				'students'    => $this->prepare_student( $student, $gbid ),
			) );
		}

		// Only cells for visible assignments and current user
		$assignment_ids = array_map( function ( $a ) { return absint( $a['id'] ); }, $assignments );
		$placeholders   = implode( ',', array_fill( 0, count( $assignment_ids ), '%d' ) );
		$query_args     = array_merge( $assignment_ids, array( $current_user->ID ) );

		$student_assignments = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_assignment} WHERE amid IN ({$placeholders}) AND uid = %d",
			$query_args
		), ARRAY_A );

		$student = get_userdata( $current_user->ID );

		usort( $student_assignments, an_gradebook_build_sorter( 'assign_order' ) );

		return rest_ensure_response( array(
			'assignments' => array_map( array( $this, 'prepare_assignment' ), $assignments ),
			'cells'       => array_map( array( $this, 'prepare_cell' ), $student_assignments ),
			'students'    => $this->prepare_student( $student, $gbid ),
		) );
	}

	public function get_line_chart( $request ) {
		global $wpdb;
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_assignments = an_gradebook_table( 'an_assignments' );
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );

		$uid  = get_current_user_id();
		$gbid = absint( $request['gbid'] );

		// Verify student is enrolled in this course.
		$enrolled = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_gradebook} WHERE uid = %d AND gbid = %d",
			$uid,
			$gbid
		) );

		if ( ! $enrolled ) {
			return new WP_Error( 'forbidden', 'You are not enrolled in this course.', array( 'status' => 403 ) );
		}

		// Only visible assignments
		$assignments = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_assignments} WHERE assign_visibility = 'Students' AND gbid = %d",
			$gbid
		), ARRAY_A );

		if ( empty( $assignments ) ) {
			return rest_ensure_response( array( array( 'Assignment', 'Student Score', 'Class Average' ) ) );
		}

		$assignment_ids = array_map( function ( $a ) { return absint( $a['id'] ); }, $assignments );
		$placeholders   = implode( ',', array_fill( 0, count( $assignment_ids ), '%d' ) );
		$query_args     = array_merge( $assignment_ids, array( $uid ) );

		$student_grades = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_assignment} WHERE amid IN ({$placeholders}) AND uid = %d",
			$query_args
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

	private function prepare_course( $row ) {
		return array(
			'id'       => intval( $row['id'] ),
			'name'     => sanitize_text_field( $row['name'] ),
			'school'   => sanitize_text_field( $row['school'] ),
			'semester' => sanitize_text_field( $row['semester'] ),
			'year'     => intval( $row['year'] ),
		);
	}

	private function prepare_assignment( $row ) {
		return array(
			'id'                => intval( $row['id'] ),
			'gbid'              => intval( $row['gbid'] ),
			'assign_order'      => intval( $row['assign_order'] ),
			'assign_name'       => sanitize_text_field( $row['assign_name'] ),
			'assign_category'   => sanitize_text_field( $row['assign_category'] ),
			'assign_visibility' => sanitize_text_field( $row['assign_visibility'] ),
			'assign_date'       => sanitize_text_field( $row['assign_date'] ),
			'assign_due'        => sanitize_text_field( $row['assign_due'] ),
		);
	}

	private function prepare_cell( $row ) {
		return array(
			'id'                   => intval( $row['id'] ),
			'uid'                  => intval( $row['uid'] ),
			'gbid'                 => intval( $row['gbid'] ),
			'amid'                 => intval( $row['amid'] ),
			'assign_order'         => intval( $row['assign_order'] ),
			'assign_points_earned' => floatval( $row['assign_points_earned'] ),
		);
	}

	private function prepare_student( $user, $gbid ) {
		return array(
			'firstname'  => sanitize_text_field( $user->first_name ),
			'lastname'   => sanitize_text_field( $user->last_name ),
			'user_login' => sanitize_text_field( $user->user_login ),
			'id'         => intval( $user->ID ),
			'gbid'       => intval( $gbid ),
		);
	}
}
