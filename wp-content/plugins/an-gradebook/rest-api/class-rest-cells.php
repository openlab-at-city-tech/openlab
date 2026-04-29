<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AN_GradeBook_REST_Cells {

	public function register_routes() {
		register_rest_route( 'an-gradebook/v1', '/cells', array(
			'methods'             => 'PUT',
			'callback'            => array( $this, 'update_cell' ),
			'permission_callback' => array( $this, 'admin_permission' ),
		) );
	}

	public function admin_permission() {
		return current_user_can( 'manage_options' );
	}

	public function update_cell( $request ) {
		global $wpdb;
		$table_assignment = an_gradebook_table( 'an_assignment' );

		$uid  = absint( $request['uid'] );
		$amid = absint( $request['amid'] );

		$wpdb->update(
			$table_assignment,
			array(
				'assign_order'         => absint( $request['assign_order'] ),
				'assign_points_earned' => floatval( $request['assign_points_earned'] ),
			),
			array( 'uid' => $uid, 'amid' => $amid )
		);

		$result = $wpdb->get_row( $wpdb->prepare(
			"SELECT assign_points_earned FROM {$table_assignment} WHERE uid = %d AND amid = %d",
			$uid,
			$amid
		), ARRAY_A );

		$result['assign_points_earned'] = floatval( $result['assign_points_earned'] );

		return rest_ensure_response( $result );
	}
}
