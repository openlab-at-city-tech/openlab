<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Quiz utilities.
 */
class EPKB_Quizzes_Utilities {

	const META_SOURCE_ARTICLE_ID = 'source_article_id';
	const META_QUESTIONS = 'epkb_quiz_questions';
	const META_GENERATION_META = 'epkb_quiz_generation_meta';
	const DEMO_QUIZ_URL = 'https://www.echoknowledgebase.com/documentation/quizzes/';

	const OPTION_INTEREST_SUBMITTED = 'epkb_quizzes_interest_form_submitted';
	const OPTION_INTEREST_LAST_SUBMITTED_AT = 'epkb_quizzes_interest_last_submitted_at';

	/**
	 * Get default KB config used by shared quiz feature.
	 *
	 * @return array
	 */
	public static function get_shared_config() {
		return epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
	}

	/**
	 * Get demo quiz URL.
	 *
	 * @return string
	 */
	public static function get_demo_quiz_url() {
		return self::DEMO_QUIZ_URL;
	}

	/**
	 * Get the internal quiz generation runtime profile.
	 *
	 * @param string|null $provider
	 * @return array
	 */
	public static function get_quiz_runtime_profile( $provider = null ) {
		return EPKB_AI_Provider::get_runtime_profile( 'quiz', $provider );
	}

	/**
	 * Get the quiz generation model for a provider.
	 *
	 * @param string|null $provider
	 * @return string
	 */
	public static function get_quiz_model( $provider = null ) {
		$runtime_profile = self::get_quiz_runtime_profile( $provider );
		return $runtime_profile['model'];
	}

	/**
	 * Check if quizzes feature is enabled.
	 *
	 * @return bool
	 */
	public static function is_feature_enabled() {
		$config = self::get_shared_config();
		return ! empty( $config['quizzes_enable'] ) && $config['quizzes_enable'] === 'on';
	}

	/**
	 * Get shared frontend settings used by quiz rendering.
	 *
	 * @return array
	 */
	public static function get_frontend_settings() {

		$config = self::get_shared_config();

		return array(
			'eyebrow_text'               => $config['quizzes_eyebrow_text'],
			'start_button_text'          => $config['quizzes_start_button_text'],
			'question_label_text'        => $config['quizzes_question_label_text'],
			'true_text'                  => $config['quizzes_true_text'],
			'false_text'                 => $config['quizzes_false_text'],
			'summary_title_text'         => $config['quizzes_summary_title_text'],
			'correct_text'               => $config['quizzes_correct_text'],
			'incorrect_text'             => $config['quizzes_incorrect_text'],
			'score_prefix_text'          => $config['quizzes_score_prefix_text'],
			'accent_color'               => $config['quizzes_accent_color'],
			'button_text_color'          => $config['quizzes_button_text_color'],
			'card_border_color'          => $config['quizzes_card_border_color'],
			'card_background_color'      => $config['quizzes_card_background_color'],
			'heading_text_color'         => $config['quizzes_heading_text_color'],
			'body_text_color'            => $config['quizzes_body_text_color'],
			'intro_background_color'     => $config['quizzes_intro_background_color'],
			'correct_background_color'   => $config['quizzes_correct_background_color'],
			'correct_border_color'       => $config['quizzes_correct_border_color'],
			'incorrect_background_color' => $config['quizzes_incorrect_background_color'],
			'incorrect_border_color'     => $config['quizzes_incorrect_border_color'],
			'summary_background_color'   => $config['quizzes_summary_background_color'],
			'summary_text_color'         => $config['quizzes_summary_text_color'],
		);
	}

	/**
	 * Get shared True / False labels.
	 *
	 * @return array
	 */
	public static function get_true_false_choices() {

		$config = self::get_shared_config();

		return array(
			$config['quizzes_true_text'],
			$config['quizzes_false_text'],
		);
	}

	/**
	 * Get quiz frontend script strings.
	 *
	 * @return array
	 */
	public static function get_frontend_script_data() {

		$settings = self::get_frontend_settings();

		return array(
			'correct'       => $settings['correct_text'],
			'incorrect'     => $settings['incorrect_text'],
			'summaryPrefix' => $settings['score_prefix_text'],
		);
	}

	/**
	 * Get inline CSS overrides for frontend quiz colors.
	 *
	 * @return string
	 */
	public static function get_frontend_color_css() {

		$settings = self::get_frontend_settings();
		$selector = '#eckb-article-page-container-v2 #eckb-article-body #eckb-article-content #eckb-article-content-footer .epkb-article-quiz';

		return EPKB_Utilities::minify_css(
			$selector . ' {' .
				'--epkb-quiz-accent-color: ' . sanitize_hex_color( $settings['accent_color'] ) . ';' .
				'--epkb-quiz-button-text-color: ' . sanitize_hex_color( $settings['button_text_color'] ) . ';' .
				'--epkb-quiz-card-border-color: ' . sanitize_hex_color( $settings['card_border_color'] ) . ';' .
				'--epkb-quiz-card-background-color: ' . sanitize_hex_color( $settings['card_background_color'] ) . ';' .
				'--epkb-quiz-heading-text-color: ' . sanitize_hex_color( $settings['heading_text_color'] ) . ';' .
				'--epkb-quiz-body-text-color: ' . sanitize_hex_color( $settings['body_text_color'] ) . ';' .
				'--epkb-quiz-intro-background-color: ' . sanitize_hex_color( $settings['intro_background_color'] ) . ';' .
				'--epkb-quiz-correct-background-color: ' . sanitize_hex_color( $settings['correct_background_color'] ) . ';' .
				'--epkb-quiz-correct-border-color: ' . sanitize_hex_color( $settings['correct_border_color'] ) . ';' .
				'--epkb-quiz-incorrect-background-color: ' . sanitize_hex_color( $settings['incorrect_background_color'] ) . ';' .
				'--epkb-quiz-incorrect-border-color: ' . sanitize_hex_color( $settings['incorrect_border_color'] ) . ';' .
				'--epkb-quiz-summary-background-color: ' . sanitize_hex_color( $settings['summary_background_color'] ) . ';' .
				'--epkb-quiz-summary-text-color: ' . sanitize_hex_color( $settings['summary_text_color'] ) . ';' .
			'}'
		);
	}

	/**
	 * Get article post types for all KBs.
	 *
	 * @return array
	 */
	public static function get_kb_article_post_types() {
		$kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids( true );
		$post_types = array();

		foreach ( $kb_ids as $kb_id ) {
			$post_types[] = EPKB_KB_Handler::get_post_type( $kb_id );
		}

		return array_values( array_unique( array_filter( $post_types ) ) );
	}

	/**
	 * Get quizzes list.
	 *
	 * @param array|string $post_status
	 * @return array
	 */
	public static function get_quizzes( $post_status = array( 'draft', 'publish' ) ) {
		return get_posts( array(
			'post_type'      => EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE,
			'post_status'    => $post_status,
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		) );
	}

	/**
	 * Get a single quiz post.
	 *
	 * @param int|WP_Post $quiz
	 * @return WP_Post|null
	 */
	public static function get_quiz( $quiz ) {

		if ( $quiz instanceof WP_Post ) {
			return $quiz->post_type === EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE ? $quiz : null;
		}

		$quiz_id = absint( $quiz );
		if ( empty( $quiz_id ) ) {
			return null;
		}

		$quiz = get_post( $quiz_id );
		if ( empty( $quiz ) || $quiz->post_type !== EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE ) {
			return null;
		}

		return $quiz;
	}

	/**
	 * Get quiz by source article.
	 *
	 * @param int $article_id
	 * @param array|string $post_status
	 * @return WP_Post|null
	 */
	public static function get_quiz_by_source_article( $article_id, $post_status = array( 'draft', 'publish' ) ) {

		$article_id = absint( $article_id );
		if ( empty( $article_id ) ) {
			return null;
		}

		$quizzes = get_posts( array(
			'post_type'      => EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE,
			'post_status'    => $post_status,
			'posts_per_page' => 1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'   => self::META_SOURCE_ARTICLE_ID,
					'value' => $article_id,
				),
			),
		) );

		return empty( $quizzes[0] ) ? null : $quizzes[0];
	}

	/**
	 * Get publishable quiz for frontend.
	 *
	 * @param int $article_id
	 * @return array|null
	 */
	public static function get_frontend_quiz_payload( $article_id ) {

		if ( ! self::is_feature_enabled() ) {
			return null;
		}

		$source_state = self::get_source_article_state( $article_id );
		if ( ! $source_state['is_renderable'] ) {
			return null;
		}

		$quiz = self::get_quiz_by_source_article( $article_id, 'publish' );
		if ( empty( $quiz ) ) {
			return null;
		}

		$payload = self::get_quiz_payload( $quiz );
		if ( empty( $payload['questions'] ) ) {
			return null;
		}

		return $payload;
	}

	/**
	 * Get selectable source articles.
	 *
	 * @return array
	 */
	public static function get_selectable_articles() {

		$post_types = self::get_kb_article_post_types();
		if ( empty( $post_types ) ) {
			return array();
		}

		$kb_names = array();
		foreach ( epkb_get_instance()->kb_config_obj->get_kb_ids( true ) as $kb_id ) {
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
			$kb_names[ EPKB_KB_Handler::get_post_type( $kb_id ) ] = empty( $kb_config['kb_name'] ) ? sprintf( __( 'KB %d', 'echo-knowledge-base' ), $kb_id ) : $kb_config['kb_name'];
		}

		$articles = get_posts( array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		$options = array();
		foreach ( $articles as $article ) {
			$state = self::get_source_article_state( $article->ID );
			if ( ! $state['is_assignable'] ) {
				continue;
			}

			$title = empty( $article->post_title ) ? __( '(no title)', 'echo-knowledge-base' ) : $article->post_title;
			$kb_name = empty( $kb_names[ $article->post_type ] ) ? '' : $kb_names[ $article->post_type ];
			$options[] = array(
				'id'    => (int) $article->ID,
				'label' => empty( $kb_name ) ? $title : $title . ' [' . $kb_name . ']',
			);
		}

		return $options;
	}

	/**
	 * Get source article state.
	 *
	 * @param int $article_id
	 * @return array
	 */
	public static function get_source_article_state( $article_id ) {

		$article_id = absint( $article_id );
		$default = array(
			'article_id'       => $article_id,
			'label'            => '',
			'code'             => '',
			'is_assignable'    => false,
			'is_renderable'    => false,
			'warning_message'  => '',
		);

		if ( empty( $article_id ) ) {
			return $default;
		}

		$article = get_post( $article_id );
		if ( empty( $article ) ) {
			$default['code'] = 'missing';
			$default['label'] = sprintf( __( 'Deleted Article (#%d)', 'echo-knowledge-base' ), $article_id );
			$default['warning_message'] = __( 'The source article no longer exists. This quiz is kept as a record, but it will not render on the frontend.', 'echo-knowledge-base' );
			return $default;
		}

		$title = empty( $article->post_title ) ? __( '(no title)', 'echo-knowledge-base' ) : $article->post_title;
		$default['label'] = $title;

		if ( ! EPKB_KB_Handler::is_kb_post_type( $article->post_type ) ) {
			$default['code'] = 'invalid_post_type';
			$default['warning_message'] = __( 'The selected source article is no longer a KB article, so this quiz cannot render on the frontend.', 'echo-knowledge-base' );
			return $default;
		}

		if ( $article->post_status !== 'publish' ) {
			$default['code'] = 'not_published';
			$default['warning_message'] = sprintf( __( 'The source article is not published (current status: %s). The quiz record is kept, but frontend rendering is suppressed.', 'echo-knowledge-base' ), $article->post_status );
			return $default;
		}

		if ( ! empty( $article->post_password ) ) {
			$default['code'] = 'password_protected';
			$default['warning_message'] = __( 'The source article is password protected. The quiz record is kept, but frontend rendering is suppressed.', 'echo-knowledge-base' );
			return $default;
		}

		if ( $article->post_mime_type === 'kb_link' ) {
			$default['code'] = 'linked_article';
			$default['warning_message'] = __( 'Linked articles cannot be used as quiz sources. The quiz record is kept, but frontend rendering is suppressed.', 'echo-knowledge-base' );
			return $default;
		}

		$default['is_assignable'] = true;
		$default['is_renderable'] = true;

		return $default;
	}

	/**
	 * Normalize quiz payload for admin JS.
	 *
	 * @param int|WP_Post $quiz
	 * @return array|null
	 */
	public static function get_quiz_payload( $quiz ) {

		$quiz = self::get_quiz( $quiz );
		if ( empty( $quiz ) ) {
			return null;
		}

		$source_article_id = absint( get_post_meta( $quiz->ID, self::META_SOURCE_ARTICLE_ID, true ) );
		$source_state = self::get_source_article_state( $source_article_id );
		$generation_meta = get_post_meta( $quiz->ID, self::META_GENERATION_META, true );
		$generation_meta = self::sanitize_generation_meta( is_array( $generation_meta ) ? $generation_meta : array() );

		$source_article_url = '';
		if ( $quiz->post_status === 'publish' && $source_state['is_renderable'] && ! empty( $source_article_id ) ) {
			$source_article_url = get_permalink( $source_article_id );
		}

		return array(
			'quiz_id'                => (int) $quiz->ID,
			'title'                  => $quiz->post_title,
			'intro'                  => $quiz->post_content,
			'status'                 => $quiz->post_status === 'publish' ? 'publish' : 'draft',
			'source_article_id'      => $source_article_id,
			'source_article_label'   => $source_state['label'],
			'source_article_url'     => $source_article_url,
			'source_warning_message' => $source_state['warning_message'],
			'question_count_mode'    => self::sanitize_question_count_mode( empty( $generation_meta['question_count_mode'] ) ? 'auto' : $generation_meta['question_count_mode'] ),
			'questions'              => self::get_quiz_questions( $quiz->ID ),
			'generation_meta'        => $generation_meta,
		);
	}

	/**
	 * Get sanitized questions from stored meta.
	 *
	 * @param int $quiz_id
	 * @return array
	 */
	public static function get_quiz_questions( $quiz_id ) {

		$questions = get_post_meta( $quiz_id, self::META_QUESTIONS, true );
		if ( ! is_array( $questions ) ) {
			return array();
		}

		$questions = self::normalize_questions( $questions );
		return is_wp_error( $questions ) ? array() : $questions;
	}

	/**
	 * Normalize questions to the MVP schema.
	 *
	 * @param array $questions
	 * @return array|WP_Error
	 */
	public static function normalize_questions( $questions ) {

		if ( ! is_array( $questions ) ) {
			return new WP_Error( 'invalid_questions', __( 'Quiz questions are invalid.', 'echo-knowledge-base' ) );
		}

		$normalized_questions = array();
		$used_ids = array();

		foreach ( $questions as $index => $question ) {
			if ( ! is_array( $question ) ) {
				return new WP_Error( 'invalid_question', __( 'One or more quiz questions are invalid.', 'echo-knowledge-base' ) );
			}

			if ( self::is_empty_question_row( $question ) ) {
				continue;
			}

			$type = empty( $question['type'] ) ? 'multiple_choice' : sanitize_key( $question['type'] );
			if ( ! in_array( $type, array( 'multiple_choice', 'true_false' ), true ) ) {
				return new WP_Error( 'invalid_question_type', __( 'Quiz questions must use either multiple choice or true/false.', 'echo-knowledge-base' ) );
			}

			$question_text = sanitize_text_field( wp_unslash( empty( $question['question'] ) ? '' : $question['question'] ) );
			if ( $question_text === '' ) {
				return new WP_Error( 'question_required', sprintf( __( 'Question %d is missing the question text.', 'echo-knowledge-base' ), $index + 1 ) );
			}

			$question_id = empty( $question['id'] ) ? self::generate_question_id( $index ) : sanitize_key( $question['id'] );
			if ( $question_id === '' || isset( $used_ids[ $question_id ] ) ) {
				$question_id = self::generate_question_id( $index );
			}
			$used_ids[ $question_id ] = true;

			$correct_choice = isset( $question['correct_choice'] ) ? absint( $question['correct_choice'] ) : null;
			$explanation = sanitize_textarea_field( wp_unslash( empty( $question['explanation'] ) ? '' : $question['explanation'] ) );

			if ( $type === 'true_false' ) {
				if ( ! in_array( $correct_choice, array( 0, 1 ), true ) ) {
					return new WP_Error( 'invalid_true_false_answer', sprintf( __( 'Question %d must use True or False as the correct answer.', 'echo-knowledge-base' ), $index + 1 ) );
				}

				$choices = self::get_true_false_choices();
			} else {
				$choices = array();
				$raw_choices = empty( $question['choices'] ) || ! is_array( $question['choices'] ) ? array() : $question['choices'];

				foreach ( array_slice( $raw_choices, 0, 4 ) as $choice ) {
					$choices[] = sanitize_text_field( wp_unslash( $choice ) );
				}

				if ( count( $choices ) !== 4 || in_array( '', $choices, true ) ) {
					return new WP_Error( 'invalid_choices', sprintf( __( 'Question %d must include exactly 4 answer choices.', 'echo-knowledge-base' ), $index + 1 ) );
				}

				if ( ! in_array( $correct_choice, array( 0, 1, 2, 3 ), true ) ) {
					return new WP_Error( 'invalid_correct_choice', sprintf( __( 'Question %d must have a valid correct answer selected.', 'echo-knowledge-base' ), $index + 1 ) );
				}
			}

			$normalized_questions[] = array(
				'id'             => $question_id,
				'type'           => $type,
				'question'       => $question_text,
				'choices'        => $choices,
				'correct_choice' => $correct_choice,
				'explanation'    => $explanation,
			);
		}

		return $normalized_questions;
	}

	/**
	 * Get generation state for admin UI.
	 *
	 * @return array
	 */
	public static function get_generation_state() {

		$ai_admin_url = admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=epkb-kb-ai-features' );
		$ai_pro_features_admin_url = admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=pro-features' );
		$state = array(
			'is_available' => false,
			'reason'       => 'upgrade',
			'message'      => __( 'Upgrade to AI Features Pro to generate quizzes from articles.', 'echo-knowledge-base' ),
			'link_url'     => $ai_pro_features_admin_url,
			'link_label'   => __( 'Upgrade to PRO', 'echo-knowledge-base' ),
		);

		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() ) {
			return $state;
		}

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			return array(
				'is_available' => false,
				'reason'       => 'permission',
				'message'      => __( 'You do not have permission to use AI quiz generation.', 'echo-knowledge-base' ),
				'link_url'     => '',
				'link_label'   => '',
			);
		}

		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return array(
				'is_available' => false,
				'reason'       => 'configure',
				'message'      => __( 'Configure your AI provider and accept the data privacy agreement before generating quizzes.', 'echo-knowledge-base' ),
				'link_url'     => $ai_admin_url,
				'link_label'   => __( 'Configure AI', 'echo-knowledge-base' ),
			);
		}

		return array(
			'is_available' => true,
			'reason'       => '',
			'message'      => '',
			'link_url'     => '',
			'link_label'   => '',
		);
	}

	/**
	 * Check whether interest modal should appear.
	 *
	 * @return bool
	 */
	public static function should_show_interest_modal() {
		return get_option( self::OPTION_INTEREST_SUBMITTED, 'off' ) !== 'on';
	}

	/**
	 * Sanitize generation meta.
	 *
	 * @param array $generation_meta
	 * @return array
	 */
	public static function sanitize_generation_meta( $generation_meta ) {
		$sanitized = array(
			'question_count_mode' => self::sanitize_question_count_mode( empty( $generation_meta['question_count_mode'] ) ? 'auto' : $generation_meta['question_count_mode'] ),
		);

		if ( ! empty( $generation_meta['source_checksum'] ) ) {
			$sanitized['source_checksum'] = sanitize_text_field( $generation_meta['source_checksum'] );
		}

		if ( ! empty( $generation_meta['generated_at'] ) ) {
			$sanitized['generated_at'] = sanitize_text_field( $generation_meta['generated_at'] );
		}

		if ( isset( $generation_meta['question_count'] ) ) {
			$sanitized['question_count'] = absint( $generation_meta['question_count'] );
		}

		return $sanitized;
	}

	/**
	 * Sanitize question count mode.
	 *
	 * @param string|int $question_count_mode
	 * @return string
	 */
	public static function sanitize_question_count_mode( $question_count_mode ) {

		if ( is_numeric( $question_count_mode ) ) {
			$question_count_mode = (string) absint( $question_count_mode );
		}

		$allowed = array( 'auto', '3', '4', '5', '6', '7', '8', '9', '10' );
		return in_array( $question_count_mode, $allowed, true ) ? $question_count_mode : 'auto';
	}

	/**
	 * Generate quiz content from AI.
	 *
	 * @param WP_Post $article
	 * @param string $question_count_mode
	 * @return array|WP_Error
	 */
	public static function generate_quiz_from_article( $article, $question_count_mode ) {

		$question_count_mode = self::sanitize_question_count_mode( $question_count_mode );
		$eligibility = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $article );
		if ( is_wp_error( $eligibility ) ) {
			return $eligibility;
		}

		$content_for_analysis = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $article->post_content, $article->ID, 12000 );
		if ( is_wp_error( $content_for_analysis ) ) {
			return $content_for_analysis;
		}

		$count_instruction = $question_count_mode === 'auto'
			? 'Choose the best number of questions between 3 and 10 based on the article depth.'
			: 'Create exactly ' . absint( $question_count_mode ) . ' questions.';

		$prompt = 'Create a learner quiz from the knowledge base article below.

Requirements:
- Return only valid JSON. No markdown, no comments, no code fences.
- The response must be one JSON object with exactly these top-level keys: "title", "intro", "questions".
- "title" must be a concise learner-facing quiz title.
- "intro" must be 1-2 short learner-facing sentences.
- "questions" must be an array. ' . $count_instruction . '
- Every question object must contain exactly these keys: "id", "type", "question", "choices", "correct_choice", "explanation".
- "type" must be either "multiple_choice" or "true_false".
- For "multiple_choice", "choices" must contain exactly 4 short answer strings.
- For "true_false", "choices" must be exactly ["True","False"] as the canonical storage order.
- "correct_choice" must always be a zero-based index into the "choices" array.
- "explanation" must briefly explain why the correct answer is correct.
- Make every question answerable from the article alone.
- Avoid trick questions and avoid duplicate questions.
- Keep wording clear for learners.

Article Title: ' . $article->post_title . '
Article Content:
' . $content_for_analysis;

		$instructions = 'You create concise learner quizzes for knowledge base articles and must return strict JSON only.';
		$response_format = array(
			'type'   => 'json_schema',
			'name'   => 'quiz_generation',
			'schema' => array(
				'type'                 => 'object',
				'additionalProperties' => false,
				'properties'           => array(
					'title'     => array( 'type' => 'string' ),
					'intro'     => array( 'type' => 'string' ),
					'questions' => array(
						'type'  => 'array',
						'items' => array(
							'type'                 => 'object',
							'additionalProperties' => false,
							'properties'           => array(
								'id'             => array( 'type' => 'string' ),
								'type'           => array( 'type' => 'string', 'enum' => array( 'multiple_choice', 'true_false' ) ),
								'question'       => array( 'type' => 'string' ),
								'choices'        => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
								'correct_choice' => array( 'type' => 'integer' ),
								'explanation'    => array( 'type' => 'string' ),
							),
							'required'             => array( 'id', 'type', 'question', 'choices', 'correct_choice', 'explanation' ),
						),
					),
				),
				'required'             => array( 'title', 'intro', 'questions' ),
			),
		);
		$response_text = EPKB_AI_Provider::send_prompt_request( $prompt, $instructions, 'quiz_generation', 'quiz', null, array(), array(), $response_format );
		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		$parsed_response = EPKB_AI_Content_Analysis_Utilities::parse_json_response( $response_text, 'quiz_generation' );
		if ( is_wp_error( $parsed_response ) ) {
			return $parsed_response;
		}

		if ( empty( $parsed_response['title'] ) || empty( $parsed_response['intro'] ) || ! isset( $parsed_response['questions'] ) || ! is_array( $parsed_response['questions'] ) ) {
			return new WP_Error( 'invalid_quiz_response', __( 'AI quiz generation returned an invalid quiz payload.', 'echo-knowledge-base' ) );
		}

		$questions = self::normalize_questions( $parsed_response['questions'] );
		if ( is_wp_error( $questions ) ) {
			return $questions;
		}

		$question_count = count( $questions );
		if ( $question_count < 3 || $question_count > 10 ) {
			return new WP_Error( 'invalid_question_count', __( 'AI quiz generation must return between 3 and 10 questions.', 'echo-knowledge-base' ) );
		}

		if ( $question_count_mode !== 'auto' && $question_count !== absint( $question_count_mode ) ) {
			return new WP_Error( 'unexpected_question_count', __( 'AI quiz generation returned the wrong number of questions.', 'echo-knowledge-base' ) );
		}

		return array(
			'title' => sanitize_text_field( $parsed_response['title'] ),
			'intro' => sanitize_textarea_field( $parsed_response['intro'] ),
			'questions' => $questions,
			'generation_meta' => array(
				'source_checksum'     => self::get_source_checksum( $article ),
				'generated_at'        => current_time( 'mysql' ),
				'question_count'      => $question_count,
				'question_count_mode' => $question_count_mode,
			),
		);
	}

	/**
	 * Build source checksum.
	 *
	 * @param WP_Post $article
	 * @return string
	 */
	public static function get_source_checksum( $article ) {
		$content = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $article->post_content, $article->ID, 12000 );
		if ( is_wp_error( $content ) ) {
			$content = wp_strip_all_tags( $article->post_content );
		}

		return md5( $article->post_title . "\n" . $content );
	}

	/**
	 * Generate fallback question ID.
	 *
	 * @param int $index
	 * @return string
	 */
	private static function generate_question_id( $index ) {
		return 'question_' . ( $index + 1 );
	}

	/**
	 * Detect blank question row.
	 *
	 * @param array $question
	 * @return bool
	 */
	private static function is_empty_question_row( $question ) {

		$question_text = empty( $question['question'] ) ? '' : trim( wp_unslash( $question['question'] ) );
		$explanation = empty( $question['explanation'] ) ? '' : trim( wp_unslash( $question['explanation'] ) );
		$choices = empty( $question['choices'] ) || ! is_array( $question['choices'] ) ? array() : $question['choices'];
		$choices = array_filter( array_map( 'trim', array_map( 'wp_unslash', $choices ) ) );

		return $question_text === '' && $explanation === '' && empty( $choices );
	}
}
