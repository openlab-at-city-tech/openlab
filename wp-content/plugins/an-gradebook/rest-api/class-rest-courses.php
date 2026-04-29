<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AN_GradeBook_REST_Courses {

	public function register_routes() {
		register_rest_route( 'an-gradebook/v1', '/courses', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_courses' ),
				'permission_callback' => array( $this, 'admin_permission' ),
			),
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_course' ),
				'permission_callback' => array( $this, 'admin_permission' ),
			),
		) );

		register_rest_route( 'an-gradebook/v1', '/courses/(?P<id>\d+)', array(
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_course' ),
				'permission_callback' => array( $this, 'admin_permission' ),
			),
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_course' ),
				'permission_callback' => array( $this, 'admin_permission' ),
			),
		) );

		register_rest_route( 'an-gradebook/v1', '/courses/(?P<id>\d+)/gradebook', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_gradebook' ),
			'permission_callback' => array( $this, 'admin_permission' ),
		) );

		register_rest_route( 'an-gradebook/v1', '/courses/(?P<id>\d+)/export', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'export_csv' ),
			'permission_callback' => array( $this, 'admin_permission' ),
		) );
	}

	public function admin_permission() {
		return current_user_can( 'manage_options' );
	}

	public function get_courses() {
		global $wpdb;
		$table   = an_gradebook_table( 'an_gradebooks' );
		$results = $wpdb->get_results( "SELECT * FROM {$table}", ARRAY_A );

		$results = array_map( array( $this, 'prepare_course' ), $results );

		return rest_ensure_response( $results );
	}

	public function create_course( $request ) {
		global $wpdb;
		$table = an_gradebook_table( 'an_gradebooks' );

		$wpdb->insert( $table, array(
			'name'     => sanitize_text_field( $request['name'] ),
			'school'   => sanitize_text_field( $request['school'] ),
			'semester' => sanitize_text_field( $request['semester'] ),
			'year'     => absint( $request['year'] ),
		), array( '%s', '%s', '%s', '%d' ) );

		if ( ! $wpdb->insert_id ) {
			return new WP_Error( 'create_failed', 'Failed to create course.', array( 'status' => 500 ) );
		}

		$course = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $wpdb->insert_id ), ARRAY_A );
		return rest_ensure_response( $this->prepare_course( $course ) );
	}

	public function update_course( $request ) {
		global $wpdb;
		$table = an_gradebook_table( 'an_gradebooks' );
		$id    = absint( $request['id'] );

		$wpdb->update( $table, array(
			'name'     => sanitize_text_field( $request['name'] ),
			'school'   => sanitize_text_field( $request['school'] ),
			'semester' => sanitize_text_field( $request['semester'] ),
			'year'     => absint( $request['year'] ),
		), array( 'id' => $id ) );

		$course = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
		return rest_ensure_response( $this->prepare_course( $course ) );
	}

	public function delete_course( $request ) {
		global $wpdb;
		$id = absint( $request['id'] );

		$wpdb->delete( an_gradebook_table( 'an_gradebooks' ), array( 'id' => $id ) );
		$wpdb->delete( an_gradebook_table( 'an_gradebook' ), array( 'gbid' => $id ) );
		$wpdb->delete( an_gradebook_table( 'an_assignments' ), array( 'gbid' => $id ) );
		$wpdb->delete( an_gradebook_table( 'an_assignment' ), array( 'gbid' => $id ) );

		return rest_ensure_response( array( 'deleted' => true ) );
	}

	public function get_gradebook( $request ) {
		global $wpdb;
		$gbid              = absint( $request['id'] );
		$table_assignments = an_gradebook_table( 'an_assignments' );
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );

		$assignments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignments} WHERE gbid = %d", $gbid ), ARRAY_A );
		$assignments = array_map( array( $this, 'prepare_assignment' ), $assignments );

		$cells = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignment} WHERE gbid = %d", $gbid ), ARRAY_A );
		usort( $cells, an_gradebook_build_sorter( 'assign_order' ) );
		$cells = array_map( array( $this, 'prepare_cell' ), $cells );

		$student_rows = $wpdb->get_results( $wpdb->prepare( "SELECT uid FROM {$table_gradebook} WHERE gbid = %d", $gbid ), ARRAY_N );
		$students     = array();
		foreach ( $student_rows as $row ) {
			$u          = get_userdata( $row[0] );
			$students[] = $this->prepare_student( $u, $gbid );
		}

		return rest_ensure_response( array(
			'assignments' => $assignments,
			'cells'       => $cells,
			'students'    => $students,
		) );
	}

	public function export_csv( $request ) {
		global $wpdb;
		$gbid              = absint( $request['id'] );
		$table_gradebooks  = an_gradebook_table( 'an_gradebooks' );
		$table_assignments = an_gradebook_table( 'an_assignments' );
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );

		$gradebook   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_gradebooks} WHERE id = %d", $gbid ), ARRAY_A );
		$assignments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignments} WHERE gbid = %d", $gbid ), ARRAY_A );
		foreach ( $assignments as &$a ) {
			$a['id']           = intval( $a['id'] );
			$a['gbid']         = intval( $a['gbid'] );
			$a['assign_order'] = intval( $a['assign_order'] );
		}
		usort( $assignments, an_gradebook_build_sorter( 'assign_order' ) );

		$column_headers = array( 'firstname', 'lastname', 'user_login', 'id', 'gbid' );
		foreach ( $assignments as $a ) {
			$column_headers[] = sanitize_text_field( $a['assign_name'] );
		}

		$student_assignments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignment} WHERE gbid = %d", $gbid ), ARRAY_A );
		usort( $student_assignments, an_gradebook_build_sorter( 'assign_order' ) );
		foreach ( $student_assignments as &$sa ) {
			$sa['amid']                 = intval( $sa['amid'] );
			$sa['uid']                  = intval( $sa['uid'] );
			$sa['assign_points_earned'] = floatval( $sa['assign_points_earned'] );
		}

		$student_rows = $wpdb->get_results( $wpdb->prepare( "SELECT uid FROM {$table_gradebook} WHERE gbid = %d", $gbid ), ARRAY_N );
		$student_records = array();
		foreach ( $student_rows as $row ) {
			$u    = get_userdata( $row[0] );
			$base = array( sanitize_text_field( $u->first_name ), sanitize_text_field( $u->last_name ), sanitize_text_field( $u->user_login ), intval( $u->ID ), $gbid );
			$scores = array_values( array_map(
				function ( $sa ) { return $sa['assign_points_earned']; },
				array_filter( $student_assignments, function ( $sa ) use ( $u ) { return $sa['uid'] === intval( $u->ID ); } )
			) );
			$student_records[] = array_merge( $base, $scores );
		}

		$filename = sanitize_file_name( $gradebook['name'] . '_' . $gbid );

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '.csv"' );

		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, array_map( array( $this, 'sanitize_csv_value' ), $column_headers ) );
		foreach ( $student_records as $row ) {
			fputcsv( $output, array_map( array( $this, 'sanitize_csv_value' ), $row ) );
		}
		fclose( $output );
		exit;
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

	private function sanitize_csv_value( $value ) {
		if ( is_string( $value ) && isset( $value[0] ) && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			return "'" . $value;
		}
		return $value;
	}
}
