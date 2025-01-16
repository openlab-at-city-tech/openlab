<?php

/**
 * Control for FAQs admin page
 */
class EPKB_FAQs_Ctrl {

	public function __construct() {

		add_action( 'wp_ajax_epkb_save_faq', array( $this, 'save_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_faq', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_get_faq', array( $this, 'get_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_faq', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_delete_faq', array( $this, 'delete_faq' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_faq', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_save_faq_group', array( $this, 'save_faq_group' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_faq_group', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_delete_faq_group', array( $this, 'delete_faq_group' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_faq_group', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Edit Question dialog: user added a new question or updated existing one
	 */
	public function save_faq() {

		// wp_die if nonce invalid or user does not have admin permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_faqs_write' );

		$faq_status = 'publish';
		$faq_id = (int)EPKB_Utilities::post( 'faq_id' );
		$faq_question = stripslashes( EPKB_Utilities::post( 'faq_title' ) );
		$faq_answer = stripslashes( wpautop( EPKB_Utilities::post( 'faq_content', '', 'wp_editor' ) ) );

		// create new or update existing FAQ
		$faq_args = array(
			'ID'                => $faq_id,
			'post_title'        => $faq_question,
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'post_content'      => $faq_answer,
			'post_status'       => $faq_status,
			'comment_status'    => 'closed'
		);
		$faq_id = wp_insert_post( $faq_args, true );
		if ( empty( $faq_id ) || is_wp_error( $faq_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 701, $faq_id ) );
		}

		$faq = get_post( $faq_id );
		if ( empty( $faq ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 702 ) );
		}

		$faq_html = EPKB_FAQs_Page::display_question( [
			'faq_id'        => $faq->ID,
			'title'         => $faq->post_title,
			'add_icon'      => true,
			'order_icon'    => true,
			'include_icon'  => true,
			'edit_icon'     => true,
			'return_html'   => true,
		] );

		$faq_esc = [
			'faq_id'    => esc_attr( $faq->ID ),
			'title'     => esc_attr( $faq->post_title ),
			'content'   => wp_kses_post( $faq->post_content ),
			'faq_html'  => wp_kses( $faq_html, EPKB_Utilities::get_admin_ui_extended_html_tags() ),
		];

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => esc_html__( 'Question Saved', 'echo-knowledge-base' ) ,
			'data'    => $faq_esc,
		) ) );
	}

	/**
	 * Retrieve FAQ to show to user for edit
	 */
	public function get_faq() {

		// wp_die if nonce invalid or user does not have admin permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_faqs_write' );

		$faq_id = (int)EPKB_Utilities::post( 'faq_id', 0 );
		if ( empty( $faq_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 730 ) );
		}

		$faq = get_post( $faq_id );
		if ( empty( $faq ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 731 ) );
		}

		$faq_esc = [
			'faq_id'    => esc_attr( $faq->ID ),
			'title'     => esc_attr( $faq->post_title ),
			'content'   => wp_kses_post( $faq->post_content ),
		];

		wp_die( wp_json_encode( array(
			'status'   => 'success',
			'message'  => '',
			'data'     => $faq_esc,
		) ) );
	}

	/**
	 * Delete Question
	 */
	public function delete_faq() {

		// wp_die if nonce invalid or user does not have admin permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_faqs_write' );

		$faq_id = (int) EPKB_Utilities::post( 'faq_id', 0 );
		if ( empty( $faq_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 740 ) );
		}

		$result = wp_delete_post( $faq_id, true );
		if ( empty( $result ) || is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 741, $result ) );
		}

		wp_die( wp_json_encode( array(
			'status'    => 'success',
			'message'   => esc_html__( 'Question Deleted', 'echo-knowledge-base' ),
		) ) );
	}

	public function save_faq_group() {

		// wp_die if nonce invalid or user does not have admin permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_faqs_write' );

		$faq_group_id = (int)EPKB_Utilities::post( 'faq_group_id', 0 );
		$faq_group_name = stripslashes( EPKB_Utilities::post( 'faq_group_name' ) );
		//$faq_group_status = EPKB_Utilities::post( 'faq_group_status' ) == 'publish' ? 'publish' : 'draft';
		$faqs_order_sequence = EPKB_Utilities::post( 'faqs_order_sequence', [] );

		// create the FAQ Group if it does not exist yet
		if ( empty( $faq_group_id ) ) {
			$faq_group = wp_create_term( $faq_group_name, EPKB_FAQs_CPT_Setup::FAQ_CATEGORY );
			if ( is_wp_error( $faq_group ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 710, $faq_group ) );
			}

		// update FAQ Group
		} else {
			$faq_group = wp_update_term( $faq_group_id, EPKB_FAQs_CPT_Setup::FAQ_CATEGORY, [ 'name' => $faq_group_name ] );
			if ( is_wp_error( $faq_group ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 711, $faq_group ) );
			}
		}

		// update FAQ Group id
		$faq_group_id = $faq_group['term_id'];

		// update FAQ Group status
		/* $result = update_term_meta( $faq_group_id, 'faq_group_status', $faq_group_status );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 712 ) );
		} */

		// get current Group FAQs
		$current_faqs_ids = get_posts( [
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'fields'            => 'ids',
			'posts_per_page'    => -1,
			'orderby'           => 'post_title',
			'order'             => 'ASC',
			'tax_query'         => array(
				array(
					'taxonomy'  => EPKB_FAQs_CPT_Setup::FAQ_CATEGORY,
					'field'     => 'term_id',
					'terms'     => $faq_group_id,
				)
			),
		] );

		// include new FAQs
		$include_faqs_ids = array_diff( $faqs_order_sequence, $current_faqs_ids );
		foreach ( $include_faqs_ids as $faq_id ) {
			wp_set_object_terms( $faq_id, $faq_group_id, EPKB_FAQs_CPT_Setup::FAQ_CATEGORY, true );
		}

		// exclude FAQs
		$exclude_faqs_ids = array_diff( $current_faqs_ids, $faqs_order_sequence );
		foreach ( $exclude_faqs_ids as $faq_id ) {
			wp_remove_object_terms( $faq_id, $faq_group_id, EPKB_FAQs_CPT_Setup::FAQ_CATEGORY );
		}

		// update FAQs sequence
		$result = update_term_meta( $faq_group_id, 'faqs_order_sequence', $faqs_order_sequence );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 713, $result ) );
		}

		$faq_group_html = EPKB_FAQs_Page::display_group_container( $faq_group_id, $faq_group_name, true );
		$shortcode_group_html = EPKB_FAQs_Page::display_shortcode_group( $faq_group_id, $faq_group_name, true );

		wp_die( wp_json_encode( array(
			'status'                => 'success',
			'message'               => esc_html__( 'FAQ Group Saved', 'echo-knowledge-base' ),
			'faq_group_id'          => esc_attr( $faq_group_id ),
			'faq_group_name'        => esc_attr( $faq_group_name ),
			'faq_group_html'        => wp_kses( $faq_group_html, EPKB_Utilities::get_admin_ui_extended_html_tags() ),
			'shortcode_group_html'  => wp_kses( $shortcode_group_html, EPKB_Utilities::get_admin_ui_extended_html_tags() )
		) ) );
	}

	public function delete_faq_group() {

		// wp_die if nonce invalid or user does not have admin permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_faqs_write' );

		$faq_group_id = (int)EPKB_Utilities::post( 'faq_group_id', 0 );
		if ( empty( $faq_group_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 721 ) );
		}

		$result = wp_delete_term( $faq_group_id, EPKB_FAQs_CPT_Setup::FAQ_CATEGORY );
		if ( empty( $result ) || is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 720, $result ) );
		}

		wp_die( wp_json_encode( array(
			'status'    => 'success',
			'message'   => esc_html__( 'FAQ Group Deleted', 'echo-knowledge-base' ),
		) ) );
	}
}