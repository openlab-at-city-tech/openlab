<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AJAX controller for Glossary admin page
 */
class EPKB_Glossary_Ctrl {

	public function __construct() {
		add_action( 'wp_ajax_epkb_glossary_save_term', array( $this, 'save_term' ) );
		add_action( 'wp_ajax_nopriv_epkb_glossary_save_term', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_glossary_delete_term', array( $this, 'delete_term' ) );
		add_action( 'wp_ajax_nopriv_epkb_glossary_delete_term', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_glossary_bulk_publish', array( $this, 'bulk_publish' ) );
		add_action( 'wp_ajax_nopriv_epkb_glossary_bulk_publish', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_glossary_bulk_delete', array( $this, 'bulk_delete' ) );
		add_action( 'wp_ajax_nopriv_epkb_glossary_bulk_delete', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Save (create or update) a glossary term
	 */
	public function save_term() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_glossary_write' );

		$term_id    = (int) EPKB_Utilities::post( 'term_id', 0 );
		$term_name  = sanitize_text_field( EPKB_Utilities::post( 'term_name' ) );
		$definition = sanitize_textarea_field( EPKB_Utilities::post( 'definition' ) );
		$status     = EPKB_Utilities::post( 'status' ) === 'draft' ? 'draft' : 'publish';

		if ( empty( $term_name ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Term name is required.', 'echo-knowledge-base' ) );
		}

		if ( $status === 'publish' && empty( $definition ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Definition is required for published terms.', 'echo-knowledge-base' ) );
		}

		// Enforce max lengths
		$term_name  = mb_substr( $term_name, 0, 100 );
		$definition = mb_substr( $definition, 0, 500 );

		// Create new term
		if ( empty( $term_id ) ) {

			// Check uniqueness
			$existing = term_exists( $term_name, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY );
			if ( $existing ) {
				EPKB_Utilities::ajax_show_error_die( esc_html__( 'A term with this name already exists.', 'echo-knowledge-base' ) );
			}

			$result = wp_insert_term( $term_name, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY, array( 'description' => $definition ) );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 760, $result ) );
			}

			$term_id = $result['term_id'];

		// Update existing term
		} else {

			// Check uniqueness for different term
			$existing = term_exists( $term_name, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY );
			if ( $existing && (int) $existing['term_id'] !== $term_id ) {
				EPKB_Utilities::ajax_show_error_die( esc_html__( 'A term with this name already exists.', 'echo-knowledge-base' ) );
			}

			$result = wp_update_term( $term_id, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY, array(
				'name'        => $term_name,
				'description' => $definition,
			) );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 761, $result ) );
			}
		}

		update_term_meta( $term_id, 'epkb_glossary_status', $status );

		$sort_key = sanitize_text_field( EPKB_Utilities::post( 'sort_key' ) );
		$sort_key = mb_substr( $sort_key, 0, 100 );
		update_term_meta( $term_id, 'epkb_glossary_sort_key', $sort_key );

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => esc_html__( 'Term Saved', 'echo-knowledge-base' ),
			'data'    => array(
				'term_id'    => esc_attr( $term_id ),
				'name'       => esc_html( $term_name ),
				'definition' => esc_html( $definition ),
				'status'     => esc_attr( $status ),
				'sort_key'   => esc_html( $sort_key ),
			),
		) ) );
	}

	/**
	 * Delete a glossary term
	 */
	public function delete_term() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_glossary_write' );

		$term_id = (int) EPKB_Utilities::post( 'term_id', 0 );
		if ( empty( $term_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 762 ) );
		}

		$result = wp_delete_term( $term_id, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY );
		if ( empty( $result ) || is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 763, $result ) );
		}

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => esc_html__( 'Term Deleted', 'echo-knowledge-base' ),
		) ) );
	}

	/**
	 * Bulk publish glossary terms
	 */
	public function bulk_publish() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_glossary_write' );

		$term_ids = EPKB_Utilities::post( 'term_ids' );
		if ( empty( $term_ids ) || ! is_array( $term_ids ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 766 ) );
		}

		$count = 0;
		$skipped = 0;
		foreach ( $term_ids as $term_id ) {
			$term_id = (int) $term_id;
			$term = get_term( $term_id, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY );
			if ( empty( $term ) || is_wp_error( $term ) ) {
				continue;
			}
			if ( empty( $term->description ) ) {
				$skipped++;
				continue;
			}
			update_term_meta( $term_id, 'epkb_glossary_status', 'publish' );
			$count++;
		}

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => esc_html__( 'Term(s) published', 'echo-knowledge-base' ),
			'data'    => array( 'count' => $count ),
		) ) );
	}

	/**
	 * Bulk delete glossary terms
	 */
	public function bulk_delete() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_glossary_write' );

		$term_ids = EPKB_Utilities::post( 'term_ids' );
		if ( empty( $term_ids ) || ! is_array( $term_ids ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 767 ) );
		}

		$deleted_term_ids = array();
		foreach ( $term_ids as $term_id ) {
			$term_id = (int) $term_id;
			if ( empty( $term_id ) ) {
				continue;
			}

			$result = wp_delete_term( $term_id, EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY );
			if ( empty( $result ) || is_wp_error( $result ) ) {
				continue;
			}

			$deleted_term_ids[] = $term_id;
		}

		if ( empty( $deleted_term_ids ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 768 ) );
		}

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => esc_html__( 'Terms deleted', 'echo-knowledge-base' ),
			'data'    => array(
				'count'    => count( $deleted_term_ids ),
				'term_ids' => $deleted_term_ids,
			),
		) ) );
	}
}
