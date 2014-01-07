<?php //ajax based functions

/**
* This function process the department dropdown on the Courses archive page
*
*/
function openlab_ajax_return_course_list() {
	if ( ! wp_verify_nonce( $_GET['nonce'], 'dept_select_nonce' ) ) {
		exit( 'exit' );
	}

	$school = $_GET['school'];

	$depts = openlab_get_department_list( $school, 'short' );

	$options = '<option value="dept_all">All</option>';

	foreach ( $depts as $dept_name => $dept_label ) {
		$options .= '<option value="' . esc_attr( $dept_name ) . '">' . esc_attr( $dept_label ) . '</option>';
	}

	die( $options );
}

add_action( 'wp_ajax_nopriv_openlab_ajax_return_course_list', 'openlab_ajax_return_course_list' );
add_action( 'wp_ajax_openlab_ajax_return_course_list', 'openlab_ajax_return_course_list' );
