<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AN_GradeBook_REST_Students {

	public function register_routes() {
		register_rest_route( 'an-gradebook/v1', '/students', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'create_student' ),
			'permission_callback' => array( $this, 'admin_permission' ),
		) );

		register_rest_route( 'an-gradebook/v1', '/students/(?P<id>\d+)', array(
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_student' ),
				'permission_callback' => array( $this, 'admin_permission' ),
			),
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_student' ),
				'permission_callback' => array( $this, 'admin_permission' ),
			),
		) );
	}

	public function admin_permission() {
		return current_user_can( 'manage_options' );
	}

	public function create_student( $request ) {
		global $wpdb;
		$table_gradebook   = an_gradebook_table( 'an_gradebook' );
		$table_assignment  = an_gradebook_table( 'an_assignment' );
		$table_assignments = an_gradebook_table( 'an_assignments' );

		$gbid              = absint( $request['gbid'] );
		$existing_login    = isset( $request['existing_user_login'] ) ? sanitize_user( $request['existing_user_login'] ) : '';

		if ( $existing_login ) {
			// Enroll existing user
			$studentDetails = get_user_by( 'login', $existing_login );
			if ( ! $studentDetails ) {
				return new WP_Error( 'user_not_found', 'User not found.', array( 'status' => 404 ) );
			}

			$wpdb->insert( $table_gradebook, array( 'uid' => $studentDetails->ID, 'gbid' => $gbid ), array( '%d', '%d' ) );
			$enrollmentId = $wpdb->insert_id;

			$assignmentDetails = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignments} WHERE gbid = %d", $gbid ), ARRAY_A );
			foreach ( $assignmentDetails as $assignment ) {
				$wpdb->insert( $table_assignment, array(
					'gbid'         => $gbid,
					'amid'         => $assignment['id'],
					'uid'          => $studentDetails->ID,
					'assign_order' => $assignment['assign_order'],
				), array( '%d', '%d', '%d', '%d' ) );
			}

			$anGradebook = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_gradebook} WHERE id = %d", $enrollmentId ), ARRAY_A );
			foreach ( $anGradebook as &$value ) {
				$value['gbid'] = intval( $value['gbid'] );
			}

			$assignments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignment} WHERE uid = %d AND gbid = %d", $studentDetails->ID, $gbid ), ARRAY_A );
			usort( $assignments, an_gradebook_build_sorter( 'assign_order' ) );

			return rest_ensure_response( array(
				'student'     => $this->prepare_student( $studentDetails, $gbid ),
				'assignment'  => array_map( array( $this, 'prepare_cell' ), $assignments ),
				'anGradebook' => $anGradebook,
			) );
		}

		// Create new user
		$firstname  = sanitize_text_field( $request['firstname'] );
		$lastname   = sanitize_text_field( $request['lastname'] );
		$user_login = strtolower( $firstname[0] . $lastname );

		$result = wp_insert_user( array(
			'user_login' => $user_login,
			'first_name' => $firstname,
			'last_name'  => $lastname,
			'user_pass'  => wp_generate_password(),
		) );

		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'user_create_failed', $result->get_error_message(), array( 'status' => 400 ) );
		}

		// Append user ID to login to ensure uniqueness
		$wpdb->update(
			$wpdb->users,
			array( 'user_login' => $user_login . $result ),
			array( 'ID' => $result ),
			array( '%s' ),
			array( '%d' )
		);

		$assignmentDetails = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignments} WHERE gbid = %d", $gbid ), ARRAY_A );
		foreach ( $assignmentDetails as $assignment ) {
			$wpdb->insert( $table_assignment, array(
				'gbid'         => $gbid,
				'amid'         => $assignment['id'],
				'uid'          => $result,
				'assign_order' => $assignment['assign_order'],
			), array( '%d', '%d', '%d', '%d' ) );
		}

		$studentDetails = get_user_by( 'id', $result );
		$wpdb->insert( $table_gradebook, array( 'uid' => $studentDetails->ID, 'gbid' => $gbid ), array( '%d', '%d' ) );

		$assignments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_assignment} WHERE uid = %d", $result ), ARRAY_A );
		$anGradebook = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_gradebook} WHERE id = %d", $wpdb->insert_id ), ARRAY_A );
		usort( $assignments, an_gradebook_build_sorter( 'assign_order' ) );

		return rest_ensure_response( array(
			'student'     => $this->prepare_student( $studentDetails, $gbid ),
			'assignment'  => array_map( array( $this, 'prepare_cell' ), $assignments ),
			'anGradebook' => $anGradebook,
		) );
	}

	public function update_student( $request ) {
		$id     = absint( $request['id'] );
		$result = wp_update_user( array(
			'ID'         => $id,
			'first_name' => sanitize_text_field( $request['firstname'] ),
			'last_name'  => sanitize_text_field( $request['lastname'] ),
		) );

		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'user_update_failed', $result->get_error_message(), array( 'status' => 400 ) );
		}

		$studentDetails = get_user_by( 'id', $result );

		return rest_ensure_response( array(
			'student' => array(
				'firstname' => sanitize_text_field( $studentDetails->first_name ),
				'lastname'  => sanitize_text_field( $studentDetails->last_name ),
				'id'        => intval( $result ),
			),
		) );
	}

	public function delete_student( $request ) {
		global $wpdb;
		$table_gradebook  = an_gradebook_table( 'an_gradebook' );
		$table_assignment = an_gradebook_table( 'an_assignment' );

		$id             = absint( $request['id'] );
		$gbid           = absint( $request['gbid'] );
		$delete_options = sanitize_text_field( $request['delete_options'] );

		switch ( $delete_options ) {
			case 'gradebook':
				$wpdb->delete( $table_gradebook, array( 'uid' => $id, 'gbid' => $gbid ) );
				$wpdb->delete( $table_assignment, array( 'uid' => $id, 'gbid' => $gbid ) );
				break;

			case 'all_gradebooks':
				$wpdb->delete( $table_gradebook, array( 'uid' => $id ) );
				$wpdb->delete( $table_assignment, array( 'uid' => $id ) );
				break;

			case 'database':
				$wpdb->delete( $table_gradebook, array( 'uid' => $id ) );
				$wpdb->delete( $table_assignment, array( 'uid' => $id ) );
				require_once ABSPATH . 'wp-admin/includes/user.php';
				wp_delete_user( $id );
				break;

			default:
				return new WP_Error( 'invalid_option', 'Invalid delete option.', array( 'status' => 400 ) );
		}

		return rest_ensure_response( array( 'id' => $id ) );
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
}
