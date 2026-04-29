<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AJAX controller for PDF import.
 */
class EPKB_PDF_Import_Ctrl {

	public function __construct() {
		add_action( 'wp_ajax_epkb_import_pdf_article', array( $this, 'import_pdf_article' ) );
		add_action( 'wp_ajax_nopriv_epkb_import_pdf_article', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_prepare_pdf_content', array( $this, 'prepare_pdf_content' ) );
		add_action( 'wp_ajax_nopriv_epkb_prepare_pdf_content', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_ai_extract_pdf_text', array( $this, 'ai_extract_pdf_text' ) );
		add_action( 'wp_ajax_nopriv_epkb_ai_extract_pdf_text', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Handle PDF article import AJAX request.
	 */
	public function import_pdf_article() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();
		EPKB_PDF_Utilities::setup_timeout_error_handling( 'ajax' );

		$kb_id = EPKB_Utilities::post( 'kb_id' );
		$kb_id = empty( $kb_id ) ? '' : EPKB_Utilities::sanitize_get_id( $kb_id );
		if ( empty( $kb_id ) || is_wp_error( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 500 ), '', 500 );
		}

		$title = sanitize_text_field( EPKB_Utilities::post( 'title' ) );
		if ( empty( $title ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Article title is missing.', 'echo-knowledge-base' ), '', 501 );
		}

		$category_id = (int) EPKB_Utilities::post( 'category_id', 0 );
		$post_status = EPKB_Utilities::post( 'post_status', 'draft' );
		$post_status = in_array( $post_status, array( 'draft', 'publish' ), true ) ? $post_status : 'draft';
		$use_ai = EPKB_Utilities::post( 'use_ai', 'no' ) === 'yes' ? 'yes' : 'no';
		$raw_text = EPKB_Utilities::post( 'raw_text', '', 'text-area' );
		$html_content = EPKB_Utilities::post( 'html_content', '', 'wp_editor' );

		if ( empty( $html_content ) ) {
			$format_mode = $use_ai === 'yes' ? 'ai' : 'basic';
			if ( $format_mode === 'ai' ) {
				$pdf_data = self::get_posted_pdf_data();
				if ( is_wp_error( $pdf_data ) ) {
					EPKB_Utilities::ajax_show_error_die( $pdf_data->get_error_message(), '', 502 );
				}

				$html_content = EPKB_PDF_Utilities::format_pdf_for_display( $pdf_data['binary'], $pdf_data['file_name'], 'ai' );
			} else {
				if ( empty( $raw_text ) ) {
					EPKB_Utilities::ajax_show_error_die( esc_html__( 'PDF text content is empty.', 'echo-knowledge-base' ), '', 502 );
				}

				$html_content = EPKB_PDF_Utilities::format_text_for_display( $raw_text, $format_mode );
			}

			if ( is_wp_error( $html_content ) ) {
				EPKB_Utilities::ajax_show_error_die( $html_content->get_error_message(), '', 502 );
			}
		}

		// Create the article
		$post_id = self::create_article( $kb_id, $title, $html_content, $category_id, $post_status );
		if ( is_wp_error( $post_id ) ) {
			EPKB_Utilities::ajax_show_error_die( $post_id->get_error_message(), '', 503 );
		}

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => sprintf( esc_html__( 'Article "%s" created successfully.', 'echo-knowledge-base' ), $title ),
			'post_id' => $post_id,
		) ) );
	}

	/**
	 * Create a KB article from HTML content.
	 *
	 * @param int $kb_id
	 * @param string $title
	 * @param string $html_content
	 * @param int $category_id
	 * @param string $post_status
	 * @return int|WP_Error Post ID on success
	 */
	private static function create_article( $kb_id, $title, $html_content, $category_id, $post_status ) {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $kb_config ) ) {
			EPKB_Logging::add_log( 'PDF import could not load KB configuration.', array( 'kb_id' => $kb_id ), $kb_config );
			return new WP_Error( 'invalid_kb', esc_html__( 'Unable to load knowledge base configuration.', 'echo-knowledge-base' ) );
		}

		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		if ( empty( $post_type ) || ! post_type_exists( $post_type ) ) {
			EPKB_Logging::add_log( 'PDF import target post type is not registered.', array(
				'kb_id'     => $kb_id,
				'post_type' => $post_type,
			) );
			return new WP_Error( 'invalid_kb', esc_html__( 'Invalid knowledge base.', 'echo-knowledge-base' ) );
		}

		$taxonomy = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
			EPKB_Logging::add_log( 'PDF import target taxonomy is not registered.', array(
				'kb_id'    => $kb_id,
				'taxonomy' => $taxonomy,
			) );
			return new WP_Error( 'invalid_kb', esc_html__( 'Invalid knowledge base.', 'echo-knowledge-base' ) );
		}

		if ( ! empty( $category_id ) ) {
			$category = get_term( (int) $category_id, $taxonomy );
			if ( ! $category || is_wp_error( $category ) ) {
				EPKB_Logging::add_log( 'PDF import category is invalid.', array(
					'kb_id'       => $kb_id,
					'category_id' => $category_id,
					'taxonomy'    => $taxonomy,
				), is_wp_error( $category ) ? $category : null );
				return new WP_Error( 'invalid_category', esc_html__( 'Selected category is invalid.', 'echo-knowledge-base' ) );
			}
		}

		$sanitized_content = wp_kses_post( $html_content );
		if ( $sanitized_content === '' || EPKB_PDF_Utilities::strip_html_for_ai( $sanitized_content ) === '' ) {
			return new WP_Error( 'empty_content', esc_html__( 'PDF text content is empty.', 'echo-knowledge-base' ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'   => sanitize_text_field( $title ),
			'post_content' => $sanitized_content,
			'post_type'    => $post_type,
			'post_status'  => $post_status,
		), true );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Assign category if provided
		if ( ! empty( $category_id ) ) {
			$term_result = wp_set_post_terms( $post_id, array( (int) $category_id ), $taxonomy );
			if ( is_wp_error( $term_result ) ) {
				self::rollback_article( $post_id, 'PDF import could not assign the selected category.', array(
					'kb_id'       => $kb_id,
					'category_id' => $category_id,
					'taxonomy'    => $taxonomy,
				), $term_result );
				return new WP_Error( 'article_category_assign_failed', esc_html__( 'Unable to assign the selected category.', 'echo-knowledge-base' ) );
			}
		}

		// Update article and category sequences
		if ( ! EPKB_Reset::update_articles_and_categories_sequence( $kb_id ) ) {
			self::rollback_article( $post_id, 'PDF import could not rebuild article and category sequences.', array(
				'kb_id'   => $kb_id,
				'post_id' => $post_id,
			) );
			return new WP_Error( 'article_sequence_update_failed', esc_html__( 'Unable to finalize the imported article. Please try again.', 'echo-knowledge-base' ) );
		}

		return $post_id;
	}

	/**
	 * Remove a newly-created article when PDF import finalization fails.
	 *
	 * @param int            $post_id
	 * @param string         $message
	 * @param array          $context
	 * @param WP_Error|null  $wp_error
	 */
	private static function rollback_article( $post_id, $message, $context = array(), $wp_error = null ) {

		$context['post_id'] = (int) $post_id;
		EPKB_Logging::add_log( $message, $context, $wp_error );

		$deleted_post = wp_delete_post( $post_id, true );
		if ( empty( $deleted_post ) ) {
			EPKB_Logging::add_log( 'PDF import rollback failed to delete the incomplete article.', $context );
		}
	}

	/**
	 * Prepare PDF content for article/note review.
	 */
	public function prepare_pdf_content() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();
		EPKB_PDF_Utilities::setup_timeout_error_handling( 'ajax' );

		$format_mode = EPKB_Utilities::post( 'format_mode', 'basic' );
		$format_mode = in_array( $format_mode, array( 'none', 'basic', 'ai' ), true ) ? $format_mode : 'basic';

		$raw_text = '';
		if ( $format_mode === 'ai' ) {
			$pdf_data = self::get_posted_pdf_data();
			if ( is_wp_error( $pdf_data ) ) {
				EPKB_Utilities::ajax_show_error_die( $pdf_data->get_error_message() );
			}

			$content = EPKB_PDF_Utilities::format_pdf_for_display( $pdf_data['binary'], $pdf_data['file_name'], 'ai' );
		} else {
			$raw_text = EPKB_Utilities::post( 'raw_text', '', 'text-area' );
			if ( empty( $raw_text ) ) {
				EPKB_Utilities::ajax_show_error_die( esc_html__( 'PDF text content is empty.', 'echo-knowledge-base' ) );
			}

			$content = EPKB_PDF_Utilities::format_text_for_display( $raw_text, $format_mode );
		}

		if ( is_wp_error( $content ) ) {
			EPKB_Utilities::ajax_show_error_die( $content->get_error_message() );
		}

		$response = array(
			'status'       => 'success',
			'content'      => $content,
			'plain_text'   => $raw_text === '' ? '' : EPKB_PDF_Utilities::normalize_extracted_text( $raw_text ),
			'format_mode'  => $format_mode,
		);

		$ai_debug = EPKB_PDF_Utilities::get_last_ai_debug();
		if ( $ai_debug ) {
			$response['ai_debug'] = $ai_debug;
		}

		wp_die( wp_json_encode( $response ) );
	}

	/**
	 * Handle AI-based PDF text extraction AJAX request.
	 * Used when client-side PDF.js extraction returns empty text (scanned/image PDFs).
	 */
	public function ai_extract_pdf_text() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();
		EPKB_PDF_Utilities::setup_timeout_error_handling( 'ajax' );

		$pdf_data = self::get_posted_pdf_data();
		if ( is_wp_error( $pdf_data ) ) {
			EPKB_Utilities::ajax_show_error_die( $pdf_data->get_error_message() );
		}

		$result = EPKB_AI_PDF_Extractor::extract_text_from_pdf( $pdf_data['binary'], $pdf_data['file_name'] );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( $result->get_error_message() );
		}

		wp_die( wp_json_encode( array( 'status' => 'success', 'text' => $result['text'] ) ) );
	}

	/**
	 * Decode a posted PDF payload shared by PDF preview and extraction endpoints.
	 *
	 * @return array|WP_Error
	 */
	private static function get_posted_pdf_data() {

		$pdf_base64 = EPKB_Utilities::post( 'pdf_base64', '', 'text-area' );
		if ( empty( $pdf_base64 ) ) {
			return new WP_Error( 'empty_pdf', __( 'No PDF data received.', 'echo-knowledge-base' ) );
		}

		$file_name = sanitize_file_name( EPKB_Utilities::post( 'file_name', 'document.pdf' ) );

		return EPKB_PDF_Utilities::decode_base64_pdf( $pdf_base64, $file_name );
	}
}
