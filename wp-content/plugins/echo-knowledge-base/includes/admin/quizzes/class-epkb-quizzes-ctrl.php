<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AJAX controller for quizzes admin page.
 */
class EPKB_Quizzes_Ctrl {

	const SOURCE_LOCK_PREFIX = 'epkb_quiz_source_lock_';
	const SOURCE_LOCK_TIMEOUT = 10 * MINUTE_IN_SECONDS;

	public function __construct() {
		add_action( 'wp_ajax_epkb_save_quiz', array( $this, 'save_quiz' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_quiz', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_get_quiz', array( $this, 'get_quiz' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_quiz', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_get_quiz_by_article', array( $this, 'get_quiz_by_article' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_quiz_by_article', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_delete_quiz', array( $this, 'delete_quiz' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_quiz', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_generate_quiz', array( $this, 'generate_quiz' ) );
		add_action( 'wp_ajax_nopriv_epkb_generate_quiz', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_submit_quiz_interest', array( $this, 'submit_quiz_interest' ) );
		add_action( 'wp_ajax_nopriv_epkb_submit_quiz_interest', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Save or update quiz.
	 */
	public function save_quiz() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_quizzes_write' );

		$quiz_id = absint( EPKB_Utilities::post( 'quiz_id', 0 ) );
		$existing_quiz = $this->get_existing_quiz_or_error( $quiz_id );
		if ( is_wp_error( $existing_quiz ) ) {
			wp_send_json_error( array( 'message' => $existing_quiz->get_error_message() ) );
		}

		$title = sanitize_text_field( EPKB_Utilities::post( 'quiz_title' ) );
		$intro = EPKB_Utilities::post( 'quiz_intro', '', 'wp_editor' );
		$source_article_id = absint( EPKB_Utilities::post( 'source_article_id', 0 ) );
		$post_status = EPKB_Utilities::post( 'post_status' ) === 'publish' ? 'publish' : 'draft';
		$question_count_mode = EPKB_Quizzes_Utilities::sanitize_question_count_mode( EPKB_Utilities::post( 'question_count_mode', 'auto' ) );
		$questions = $this->decode_questions_from_request();

		if ( is_wp_error( $questions ) ) {
			wp_send_json_error( array( 'message' => $questions->get_error_message() ) );
		}

		if ( $title === '' ) {
			wp_send_json_error( array( 'message' => __( 'Quiz title is required.', 'echo-knowledge-base' ) ) );
		}

		if ( $post_status === 'publish' && empty( $questions ) ) {
			wp_send_json_error( array( 'message' => __( 'Add at least one question before publishing.', 'echo-knowledge-base' ) ) );
		}

		if ( count( $questions ) > 10 ) {
			wp_send_json_error( array( 'message' => __( 'A quiz can have at most 10 questions in this MVP.', 'echo-knowledge-base' ) ) );
		}

		$result = $this->with_source_article_lock( $source_article_id, function() use ( $existing_quiz, $intro, $post_status, $question_count_mode, $questions, $source_article_id, $title ) {
			$source_validation = $this->validate_source_assignment( $source_article_id, $existing_quiz );
			if ( is_wp_error( $source_validation ) ) {
				return $source_validation;
			}

			$is_new_quiz = empty( $existing_quiz );
			$generation_meta = empty( $existing_quiz ) ? array() : get_post_meta( $existing_quiz->ID, EPKB_Quizzes_Utilities::META_GENERATION_META, true );
			$generation_meta = EPKB_Quizzes_Utilities::sanitize_generation_meta( is_array( $generation_meta ) ? $generation_meta : array() );
			$generation_meta['question_count_mode'] = $question_count_mode;

			$saved_quiz_id = wp_insert_post( array(
				'ID'             => empty( $existing_quiz ) ? 0 : $existing_quiz->ID,
				'post_type'      => EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE,
				'post_title'     => $title,
				'post_content'   => $intro,
				'post_status'    => $post_status,
				'comment_status' => 'closed',
			), true );

			if ( empty( $saved_quiz_id ) || is_wp_error( $saved_quiz_id ) ) {
				return new WP_Error( 'quiz_save_failed', EPKB_Utilities::report_generic_error( 901, $saved_quiz_id ) );
			}

			update_post_meta( $saved_quiz_id, EPKB_Quizzes_Utilities::META_SOURCE_ARTICLE_ID, $source_article_id );
			update_post_meta( $saved_quiz_id, EPKB_Quizzes_Utilities::META_QUESTIONS, $questions );
			update_post_meta( $saved_quiz_id, EPKB_Quizzes_Utilities::META_GENERATION_META, $generation_meta );

			$saved_quiz = EPKB_Quizzes_Utilities::get_quiz( $saved_quiz_id );

			return array(
				'message'             => $post_status === 'publish' ? __( 'Quiz published.', 'echo-knowledge-base' ) : __( 'Quiz saved as draft.', 'echo-knowledge-base' ),
				'quiz'                => EPKB_Quizzes_Utilities::get_quiz_payload( $saved_quiz ),
				'quiz_row_html'       => EPKB_Quizzes_Page::display_quiz_list_row( $saved_quiz, true ),
				'is_new_quiz'         => $is_new_quiz,
				'show_interest_modal' => EPKB_Quizzes_Utilities::should_show_interest_modal(),
			);
		} );

		if ( is_wp_error( $result ) ) {
			$this->send_duplicate_or_error( $result );
		}

		wp_send_json_success( $result );
	}

	/**
	 * Get quiz by ID.
	 */
	public function get_quiz() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_quizzes_write' );

		$quiz = $this->get_existing_quiz_or_error( absint( EPKB_Utilities::post( 'quiz_id', 0 ) ) );
		if ( is_wp_error( $quiz ) ) {
			wp_send_json_error( array( 'message' => $quiz->get_error_message() ) );
		}

		wp_send_json_success( array(
			'quiz' => EPKB_Quizzes_Utilities::get_quiz_payload( $quiz ),
		) );
	}

	/**
	 * Get quiz by selected article.
	 */
	public function get_quiz_by_article() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_quizzes_write' );

		$article_id = absint( EPKB_Utilities::post( 'source_article_id', 0 ) );
		if ( empty( $article_id ) ) {
			wp_send_json_success( array( 'quiz' => null ) );
		}

		$quiz = EPKB_Quizzes_Utilities::get_quiz_by_source_article( $article_id );
		wp_send_json_success( array(
			'quiz' => empty( $quiz ) ? null : EPKB_Quizzes_Utilities::get_quiz_payload( $quiz ),
		) );
	}

	/**
	 * Delete quiz.
	 */
	public function delete_quiz() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_quizzes_write' );

		$quiz = $this->get_existing_quiz_or_error( absint( EPKB_Utilities::post( 'quiz_id', 0 ) ) );
		if ( is_wp_error( $quiz ) ) {
			wp_send_json_error( array( 'message' => $quiz->get_error_message() ) );
		}

		$result = wp_delete_post( $quiz->ID, true );
		if ( empty( $result ) || is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => EPKB_Utilities::report_generic_error( 902, $result ) ) );
		}

		wp_send_json_success( array(
			'message' => __( 'Quiz deleted.', 'echo-knowledge-base' ),
			'quiz_id' => (int) $quiz->ID,
		) );
	}

	/**
	 * Generate quiz from source article and save draft.
	 */
	public function generate_quiz() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_quizzes_write' );

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to use AI quiz generation.', 'echo-knowledge-base' ) ) );
		}

		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() ) {
			wp_send_json_error( array( 'message' => __( 'AI quiz generation requires AI Features Pro.', 'echo-knowledge-base' ) ) );
		}

		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			wp_send_json_error( array( 'message' => __( 'Configure AI before generating quizzes.', 'echo-knowledge-base' ) ) );
		}

		$quiz_id = absint( EPKB_Utilities::post( 'quiz_id', 0 ) );
		$existing_quiz = $this->get_existing_quiz_or_error( $quiz_id );
		if ( is_wp_error( $existing_quiz ) ) {
			wp_send_json_error( array( 'message' => $existing_quiz->get_error_message() ) );
		}

		$title = sanitize_text_field( EPKB_Utilities::post( 'quiz_title' ) );
		$intro = EPKB_Utilities::post( 'quiz_intro', '', 'wp_editor' );
		$source_article_id = absint( EPKB_Utilities::post( 'source_article_id', 0 ) );
		$question_count_mode = EPKB_Quizzes_Utilities::sanitize_question_count_mode( EPKB_Utilities::post( 'question_count_mode', 'auto' ) );

		$result = $this->with_source_article_lock( $source_article_id, function() use ( $existing_quiz, $intro, $question_count_mode, $source_article_id, $title ) {
			$source_validation = $this->validate_source_assignment( $source_article_id, $existing_quiz, true );
			if ( is_wp_error( $source_validation ) ) {
				return $source_validation;
			}

			$article = get_post( $source_article_id );
			$generated_quiz = EPKB_Quizzes_Utilities::generate_quiz_from_article( $article, $question_count_mode );
			if ( is_wp_error( $generated_quiz ) ) {
				return $generated_quiz;
			}

			$is_new_quiz = empty( $existing_quiz );
			$stored_title = $title === '' ? $generated_quiz['title'] : $title;
			$stored_intro = wp_strip_all_tags( $intro ) === '' ? wpautop( $generated_quiz['intro'] ) : $intro;

			$saved_quiz_id = wp_insert_post( array(
				'ID'             => empty( $existing_quiz ) ? 0 : $existing_quiz->ID,
				'post_type'      => EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE,
				'post_title'     => $stored_title,
				'post_content'   => $stored_intro,
				'post_status'    => 'draft',
				'comment_status' => 'closed',
			), true );

			if ( empty( $saved_quiz_id ) || is_wp_error( $saved_quiz_id ) ) {
				return new WP_Error( 'quiz_generate_save_failed', EPKB_Utilities::report_generic_error( 903, $saved_quiz_id ) );
			}

			update_post_meta( $saved_quiz_id, EPKB_Quizzes_Utilities::META_SOURCE_ARTICLE_ID, $source_article_id );
			update_post_meta( $saved_quiz_id, EPKB_Quizzes_Utilities::META_QUESTIONS, $generated_quiz['questions'] );
			update_post_meta( $saved_quiz_id, EPKB_Quizzes_Utilities::META_GENERATION_META, EPKB_Quizzes_Utilities::sanitize_generation_meta( $generated_quiz['generation_meta'] ) );

			$saved_quiz = EPKB_Quizzes_Utilities::get_quiz( $saved_quiz_id );

			return array(
				'message'             => __( 'Quiz generated and saved as draft.', 'echo-knowledge-base' ),
				'quiz'                => EPKB_Quizzes_Utilities::get_quiz_payload( $saved_quiz ),
				'quiz_row_html'       => EPKB_Quizzes_Page::display_quiz_list_row( $saved_quiz, true ),
				'is_new_quiz'         => $is_new_quiz,
				'show_interest_modal' => EPKB_Quizzes_Utilities::should_show_interest_modal(),
			);
		} );

		if ( is_wp_error( $result ) ) {
			$this->send_duplicate_or_error( $result );
		}

		wp_send_json_success( $result );
	}

	/**
	 * Submit quiz interest feedback.
	 */
	public function submit_quiz_interest() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_quizzes_write' );

		$first_name = sanitize_text_field( EPKB_Utilities::post( 'first_name' ) );
		$email = sanitize_email( EPKB_Utilities::post( 'email', '', 'email' ) );
		$feedback = sanitize_textarea_field( EPKB_Utilities::post( 'feedback', '', 'text-area' ) );

		if ( ! empty( $email ) && ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please provide a valid email address.', 'echo-knowledge-base' ) ) );
		}

		$feedback_message = "Quiz Interest Form\n";
		$feedback_message .= 'Site URL: ' . get_site_url() . "\n";
		$feedback_message .= 'Feedback: ' . ( $feedback === '' ? __( 'No additional feedback provided.', 'echo-knowledge-base' ) : $feedback );

		$feedback_data = array(
			'epkb_action'    => 'epkb_process_user_feedback',
			'feedback_type'  => 'quiz_interest',
			'feedback_input' => $feedback_message,
			'plugin_name'    => 'KB',
			'plugin_version' => class_exists( 'Echo_Knowledge_Base' ) ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version'  => '',
			'wp_version'     => '',
			'theme_info'     => '',
			'contact_user'   => trim( $email . ' - ' . $first_name, ' -' ),
			'first_name'     => $first_name,
			'email_subject'  => 'Quiz Interest Form',
		);

		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $feedback_data, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $feedback_data,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to submit feedback. Please try again.', 'echo-knowledge-base' ) ) );
		}

		update_option( EPKB_Quizzes_Utilities::OPTION_INTEREST_SUBMITTED, 'on', false );
		update_option( EPKB_Quizzes_Utilities::OPTION_INTEREST_LAST_SUBMITTED_AT, current_time( 'mysql' ), false );

		wp_send_json_success( array(
			'message' => __( 'Thank you. Your feedback has been submitted.', 'echo-knowledge-base' ),
		) );
	}

	/**
	 * Decode questions JSON from request.
	 *
	 * @return array|WP_Error
	 */
	private function decode_questions_from_request() {

		$questions_json = isset( $_POST['questions_json'] ) ? wp_unslash( $_POST['questions_json'] ) : '[]'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$decoded_questions = json_decode( $questions_json, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'invalid_questions_json', __( 'Quiz questions could not be read.', 'echo-knowledge-base' ) );
		}

		$questions = EPKB_Quizzes_Utilities::normalize_questions( $decoded_questions );
		return is_wp_error( $questions ) ? $questions : $questions;
	}

	/**
	 * Validate source article for save or generation.
	 *
	 * @param int $source_article_id
	 * @param WP_Post|null $existing_quiz
	 * @param bool $require_renderable_source
	 * @return true|WP_Error
	 */
	private function validate_source_assignment( $source_article_id, $existing_quiz = null, $require_renderable_source = false ) {

		if ( empty( $source_article_id ) ) {
			return new WP_Error( 'source_required', __( 'Select a source article.', 'echo-knowledge-base' ) );
		}

		$current_source_article_id = empty( $existing_quiz ) ? 0 : absint( get_post_meta( $existing_quiz->ID, EPKB_Quizzes_Utilities::META_SOURCE_ARTICLE_ID, true ) );
		$is_same_source = ! empty( $existing_quiz ) && $current_source_article_id === $source_article_id;
		$source_state = EPKB_Quizzes_Utilities::get_source_article_state( $source_article_id );

		if ( ( ! $is_same_source || $require_renderable_source ) && ! $source_state['is_assignable'] ) {
			return new WP_Error( 'invalid_source', empty( $source_state['warning_message'] ) ? __( 'The selected source article is not eligible for quizzes.', 'echo-knowledge-base' ) : $source_state['warning_message'] );
		}

		if ( $require_renderable_source && ! $source_state['is_renderable'] ) {
			return new WP_Error( 'invalid_render_source', empty( $source_state['warning_message'] ) ? __( 'The selected source article cannot be used to generate a quiz.', 'echo-knowledge-base' ) : $source_state['warning_message'] );
		}

		$duplicate_quiz = EPKB_Quizzes_Utilities::get_quiz_by_source_article( $source_article_id );
		if ( ! empty( $duplicate_quiz ) && ( empty( $existing_quiz ) || (int) $duplicate_quiz->ID !== (int) $existing_quiz->ID ) ) {
			return new WP_Error( 'quiz_exists', __( 'A quiz already exists for this article.', 'echo-knowledge-base' ), array(
				'quiz' => EPKB_Quizzes_Utilities::get_quiz_payload( $duplicate_quiz ),
			) );
		}

		return true;
	}

	/**
	 * Return existing quiz or validation error.
	 *
	 * @param int $quiz_id
	 * @return WP_Post|null|WP_Error
	 */
	private function get_existing_quiz_or_error( $quiz_id ) {

		if ( empty( $quiz_id ) ) {
			return null;
		}

		$quiz = EPKB_Quizzes_Utilities::get_quiz( $quiz_id );
		if ( empty( $quiz ) ) {
			return new WP_Error( 'quiz_not_found', __( 'Quiz not found.', 'echo-knowledge-base' ) );
		}

		return $quiz;
	}

	/**
	 * Run a callback while holding a per-source quiz lock.
	 *
	 * @param int $source_article_id
	 * @param callable $callback
	 * @return mixed
	 */
	private function with_source_article_lock( $source_article_id, $callback ) {

		$lock_name = $this->acquire_source_article_lock( $source_article_id );
		if ( is_wp_error( $lock_name ) ) {
			return $lock_name;
		}

		try {
			return call_user_func( $callback );
		} finally {
			$this->release_source_article_lock( $lock_name );
		}
	}

	/**
	 * Acquire a short-lived write lock for a source article.
	 *
	 * @param int $source_article_id
	 * @return string|WP_Error
	 */
	private function acquire_source_article_lock( $source_article_id ) {

		$source_article_id = absint( $source_article_id );
		if ( empty( $source_article_id ) ) {
			return new WP_Error( 'source_required', __( 'Select a source article.', 'echo-knowledge-base' ) );
		}

		$lock_name = self::SOURCE_LOCK_PREFIX . $source_article_id;
		$lock_time = time();

		if ( add_option( $lock_name, $lock_time, '', false ) ) {
			return $lock_name;
		}

		$existing_lock_time = absint( get_option( $lock_name, 0 ) );
		if ( ! empty( $existing_lock_time ) && ( $lock_time - $existing_lock_time ) > self::SOURCE_LOCK_TIMEOUT ) {
			delete_option( $lock_name );
			if ( add_option( $lock_name, $lock_time, '', false ) ) {
				return $lock_name;
			}
		}

		return new WP_Error( 'quiz_source_locked', __( 'Another quiz update is already in progress for that article. Please try again in a moment.', 'echo-knowledge-base' ) );
	}

	/**
	 * Release a source-article lock.
	 *
	 * @param string $lock_name
	 */
	private function release_source_article_lock( $lock_name ) {

		if ( ! empty( $lock_name ) ) {
			delete_option( $lock_name );
		}
	}

	/**
	 * Send duplicate-quiz or regular validation error.
	 *
	 * @param WP_Error $error
	 */
	private function send_duplicate_or_error( $error ) {

		if ( $error->get_error_code() === 'quiz_exists' ) {
			$error_data = $error->get_error_data();
			wp_send_json_error( array(
				'message' => __( 'That article already has a quiz. Opening the existing quiz instead.', 'echo-knowledge-base' ),
				'code'    => 'quiz_exists',
				'quiz'    => empty( $error_data['quiz'] ) ? null : $error_data['quiz'],
			) );
		}

		wp_send_json_error( array( 'message' => $error->get_error_message() ) );
	}
}
