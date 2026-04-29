<?php defined( 'ABSPATH' ) || exit();

/**
 * REST API Controller for Content Analysis operations
 *
 * IMPORTANT: This controller supports TWO DIFFERENT workflows:
 *
 * 1. BULK ANALYSIS (Batch Processing System):
 *    - /start-direct-analysis: Initialize batch job for multiple articles
 *    - /content-analysis-process-next: Process one article at a time (called in loop by frontend)
 *    - /content-analysis-progress: Check job status
 *    - /content-analysis-cancel: Cancel ongoing batch job
 *    Used by: Analyze Content tab when selecting multiple articles
 *
 * 2. SINGLE ARTICLE RE-ANALYSIS (Direct Endpoints):
 *    - /content-analysis-details: Get full analysis (runs Tags analysis fresh each time)
 *    - /content-analysis-readability: Run readability analysis for single article
 *    - /content-analysis-gap-analysis: Run gap analysis for single article
 *    Used by: Individual "Re-analyze" buttons in article details view
 *
 * DO NOT mix these workflows! Single article should use direct endpoints, not batch processing.
 */
class EPKB_AI_REST_Content_Analysis_Controller extends EPKB_AI_REST_Base_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function register_routes() {

		// BULK WORKFLOW: Initialize batch job to analyze multiple articles
		register_rest_route( $this->admin_namespace, '/content-analysis-batch-start', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'batch_start_direct_analysis'),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_ids' => array(
						'required' => true,
						'description' => 'Article IDs to analyze or "ALL" or "ALL_STATUS"',
					),
				),
			)
		) );

		// BULK WORKFLOW: Process single article in batch
		register_rest_route( $this->admin_namespace, '/content-analysis-process-article', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'batch_analyze_one_article'),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'analysis_type' => array(
						'required' => false,
						'default' => 'tags',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function( $param ) {
							return in_array( $param, array( 'tags', 'readability', 'gap' ), true );
						},
					),
				),
			)
		) );

		// BULK WORKFLOW: Get current batch job status (read-only, for page reload/resume checks)
		register_rest_route( $this->admin_namespace, '/content-analysis-batch-status', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'batch_get_status'),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			)
		) );

		// BULK WORKFLOW: Cancel the current batch job
		register_rest_route( $this->admin_namespace, '/content-analysis-batch-cancel', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'batch_cancel_analysis'),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			)
		) );


		// RE-ANALYZE: Run fresh Tags analysis for single article (called from "Re-analyze" button)
		register_rest_route( $this->admin_namespace, '/content-analysis-tags', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'run_tags_analysis' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );

		// RE-ANALYZE: Run fresh Readability analysis for single article (called from "Re-analyze" button)
		register_rest_route( $this->admin_namespace, '/content-analysis-readability', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'run_readability_analysis' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );

		// RE-ANALYZE: Run fresh Gap analysis for single article (called from "Re-analyze" button)
		register_rest_route( $this->admin_namespace, '/content-analysis-gap-analysis', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'run_gap_analysis' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );


        // TABLE VIEW: Get paginated list of articles with their analysis scores and status
        register_rest_route( $this->admin_namespace, '/content-analysis-articles-view', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_articles_view_score_status'),
                'permission_callback' => array( $this, 'check_admin_permission' ),
                'args'                => array(
                    'page' => array(
                        'default' => 1,
                        'sanitize_callback' => 'absint',
                    ),
                    'per_page' => array(
                        'default' => 20,
                        'sanitize_callback' => 'absint',
                    ),
                    'status' => array(
                        'default' => 'all',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'search' => array(
                        'default' => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'kb_id' => array(
                        'default' => 1,  // Default to KB #1 for now
                        'sanitize_callback' => 'absint',
                    ),
                ),
            )
        ) );

		// DETAILS VIEW: Get full analysis for single article (runs fresh Tags analysis, uses cached Gap/Readability)
		register_rest_route( $this->admin_namespace, '/content-analysis-details', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_article_analysis_details' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'kb_id' => array(
						'default' => 1,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );

		// PROCESSED CONTENT: Get processed article content for display
		register_rest_route( $this->admin_namespace, '/content-analysis-processed-content', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_article_processed_content' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );


		// ARTICLE STATUS: Toggle article ignored status (affects display_status calculation)
		register_rest_route( $this->admin_namespace, '/content-analysis-toggle-ignored', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'toggle_article_ignored' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'ignored' => array(
						'required' => true,
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
				),
			)
		) );

		// ARTICLE STATUS: Toggle article done status (affects display_status calculation)
		register_rest_route( $this->admin_namespace, '/content-analysis-toggle-done', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'toggle_article_done' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'done' => array(
						'required' => true,
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
				),
			)
		) );

		// TAG MANAGEMENT: Add tag to article (creates tag if it doesn't exist, clears Tags analysis cache)
		register_rest_route( $this->admin_namespace, '/content-analysis-tag-add', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'add_article_tag' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'tag_name' => array(
						'required' => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'kb_id' => array(
						'default' => 1,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );

		// TAG MANAGEMENT: Remove tag from article (clears Tags analysis cache)
		register_rest_route( $this->admin_namespace, '/content-analysis-tag-remove', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'remove_article_tag' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'tag_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'kb_id' => array(
						'default' => 1,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );

		// TAG MANAGEMENT: Edit article tag name (updates tag term, clears Tags analysis cache)
		register_rest_route( $this->admin_namespace, '/content-analysis-tag-edit', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'edit_article_tag' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
				'args'                => array(
					'article_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'tag_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
					),
					'new_name' => array(
						'required' => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'kb_id' => array(
						'default' => 1,
						'sanitize_callback' => 'absint',
					),
				),
			)
		) );
	}

	/**
	 * BULK WORKFLOW: Initialize batch job to analyze multiple articles
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function batch_start_direct_analysis( $request ) {

		// Check if AI is configured (API key set and terms accepted) before proceeding
		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return $this->create_rest_response( array( 'success' => false, 'error' => 'ai_not_configured',
				'message' => __( 'Please add your API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' ) ), 400 );
		}

		// Ensure Content Analysis table exists before starting analysis
		new EPKB_AI_Content_Analysis_DB( true );

		$article_ids = $request->get_param( 'article_ids' );

		// Initialize analysis job using Job Manager
		$result = EPKB_AI_Content_Analysis_Job_Manager::initialize_analysis_job( $article_ids );
		if ( is_wp_error( $result ) ) {
			EPKB_AI_Log::add_log( $result->get_error_message(), array( 'context' => 'Content Analysis Initialization', 'error_code' => $result->get_error_code(),
								'article_ids' => is_array( $article_ids ) ? count( $article_ids ) . ' articles' : $article_ids ) );

			$error_code = $result->get_error_code() == 'job_active' ? 'job_active' : $result->get_error_code();
			$status_code = $result->get_error_code() == 'job_active' ? 400 : 500;
			return $this->create_rest_response( array( 'success' => false, 'error' => $result->get_error_code(), 'message' => $result->get_error_message() ), $status_code );
		}

		return $this->create_rest_response( array( 'success' => true, 'job' => $result, 'total' => $result['total'] ) );
	}

	/**
	 * BULK WORKFLOW:: start single article analysis
	 * Now processes ONE analysis type at a time to avoid timeouts
	 * Returns the actual result directly since processing is synchronous
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function batch_analyze_one_article( $request ) {

		$article_id = $request->get_param( 'article_id' );
		$analysis_type = $request->get_param( 'analysis_type' );

		// Add article to the job
		EPKB_AI_Content_Analysis_Job_Manager::add_article_to_the_job( $article_id );

		// Start timing this analysis
		$analysis_start_time = microtime( true );

		$result = EPKB_AI_Content_Analysis_Job_Manager::process_article_single_analysis( $article_id, $analysis_type );

		// Calculate timing for this analysis
		$analysis_end_time = microtime( true );
		$analysis_elapsed_ms = round( ( $analysis_end_time - $analysis_start_time ) * 1000 );
		EPKB_AI_Content_Analysis_Job_Manager::update_analysis_timing( $analysis_elapsed_ms );

		if ( is_wp_error( $result ) ) {

			if ( EPKB_AI_Utilities::is_retryable_error( $result ) ) {
				return $this->create_rest_response( array( 'can_retry' => true ) );
			}

			// If execution time is too low, cancel the job
			if ( $result->get_error_code() === 'execution_time_too_low' ) {
				EPKB_AI_Content_Analysis_Job_Manager::cancel_all_analysis();
			}

			return $this->create_rest_response( array( 'reason' => $result->get_error_code(), 'success' => false, 'error' => $result->get_error_code(), 'message' => $result->get_error_message() ), 500 );
		}

		// Check for error in result
		if ( isset( $result['error'] ) ) {
			return $this->create_rest_response( array( 'success' => false, 'error' => 'analysis_error', 'message' => $result['error'], 'article_id' => $article_id, 'analysis_type' => $analysis_type ), 500 );
		}

		// update result
		$result['success'] = true;

		// Update job state based on result
		$job = EPKB_AI_Content_Analysis_Job_Manager::get_analysis_job();

		if ( $result['complete'] === true ) {
			// Article fully analyzed - increment processed count and update progress
			$processed_ids = is_array( $job['processed_ids'] ) ? array_map( 'absint', $job['processed_ids'] ) : array();
			$processed_ids[] = absint( $article_id );
			$processed_ids = array_values( array_unique( $processed_ids ) );
			$new_processed = count( $processed_ids );
			$new_percent = $job['total'] > 0 ? round( ( $new_processed / $job['total'] ) * 100 ) : 0;

			EPKB_AI_Content_Analysis_Job_Manager::update_analysis_job( array(
				'processed' => $new_processed,
				'processed_ids' => $processed_ids,
				'percent' => $new_percent,
				'consecutive_errors' => 0,
				'processing_article_id' => null,
				'processing_started_at' => null
			) );

			// Check if all articles are done
			if ( $new_processed >= $job['total'] ) {
				EPKB_AI_Content_Analysis_Job_Manager::update_analysis_job( array(
					'status' => 'completed',
					'percent' => 100
				) );
			}

		} 

		return $this->create_rest_response( $result );
	}

	/**
	 * BULK WORKFLOW: Get current batch job status (read-only, for page reload/resume checks)
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function batch_get_status( $request ) {

		$job = EPKB_AI_Content_Analysis_Job_Manager::get_analysis_job();

		$response = array(
			'success' => true,
			'progress' => array(
				'type' => $job['type'],
				'status' => $job['status'],
				'article_ids' => $job['article_ids'],
				'processed_ids' => $job['processed_ids'],	
				'total' => $job['total'],
				'processed' => $job['processed'],
				'percent' => $job['percent'],
				'errors' => isset( $job['errors'] ) ? $job['errors'] : 0,
				'cancel_requested' => isset( $job['cancel_requested'] ) ? $job['cancel_requested'] : false,
				'avg_analysis_time_ms' => isset( $job['avg_analysis_time_ms'] ) ? $job['avg_analysis_time_ms'] : 0,
				'completed_analyses' => isset( $job['completed_analyses'] ) ? $job['completed_analyses'] : 0,
				'processing_article_id' => isset( $job['processing_article_id'] ) ? $job['processing_article_id'] : null,
				'processing_started_at' => isset( $job['processing_started_at'] ) ? $job['processing_started_at'] : null,

			)
		);

		return $this->create_rest_response( $response );
	}

	/**
	 * BULK WORKFLOW: Cancel the current batch job
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function batch_cancel_analysis( $request ) {

		$result = EPKB_AI_Content_Analysis_Job_Manager::cancel_all_analysis();
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => $result->get_error_message() ) );
		}

		return $this->create_rest_response( array( 'success' => $result, 'message' => __( 'Content analysis canceled successfully', 'echo-knowledge-base' ) ) );
	}

	/*--------------------------------------------------------------------
	 * Individual Analysis Type Endpoints
	 *-------------------------------------------------------------------- */

	/**
	 * RE-ANALYZE: Run fresh Tags analysis for single article
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function run_tags_analysis( $request ) {

		// Check if AI is configured (API key set and terms accepted) before proceeding
		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return $this->create_rest_response( array(
				'success' => false,
				'error' => 'ai_not_configured',
				'message' => __( 'Please add your API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' )
			), 400 );
		}

		$article_id = $request->get_param( 'article_id' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Validate article has valid content for analysis
		$validation_result = EPKB_AI_Content_Analysis_Job_Manager::validate_article_content( $post );
		if ( is_wp_error( $validation_result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'error' => $validation_result->get_error_code(), 'message' => $validation_result->get_error_message() ), 400 );
		}

		// Run tags analysis with force flag (force=true will run fresh analysis)
		$tags_result = EPKB_AI_Tags_Usage::analyze( $post, true );
		if ( is_wp_error( $tags_result ) ) {
			if ( EPKB_AI_Utilities::is_retryable_error( $tags_result ) ) {
				return $this->create_rest_response( array( 'can_retry' => true ) );
			}
			return $this->create_rest_response( array( 'reason' => $tags_result->get_error_code(), 'success' => false, 'message' => $tags_result->get_error_message() ), 500 );
		}

		// If analysis was successful, get the updated scores
		$updated_score = null;
		$updated_components = null;
		if ( isset( $tags_result['score'] ) ) {
			// Get the updated scores to return to frontend
			$scores_data = EPKB_AI_Content_Analysis_Utilities::get_article_scores( $article_id );
			if ( $scores_data ) {
				$updated_score = isset( $scores_data['overall'] ) ? $scores_data['overall'] : null;
				$updated_components = isset( $scores_data['components'] ) ? EPKB_AI_Content_Analysis_Utilities::format_score_components( $scores_data['components'] ) : null;
			}
		}

		// Prepare response with all tags data
		$response_data = array(
			'status' => 'analyzed',
			'score' => isset( $tags_result['score'] ) ? $tags_result['score'] : 0,
			'tag_analysis' => isset( $tags_result['tag_analysis'] ) ? $tags_result['tag_analysis'] : array(),
			'current_tags' => isset( $tags_result['current_tags'] ) ? $tags_result['current_tags'] : array(),
			'suggested_tags' => isset( $tags_result['suggested_tags'] ) ? $tags_result['suggested_tags'] : array(),
			'recommended_tags' => isset( $tags_result['recommended_tags'] ) ? $tags_result['recommended_tags'] : array(),
		);

		// Include updated overall score and components if available
		if ( $updated_score !== null ) {
			$response_data['updated_overall_score'] = $updated_score;
		}
		if ( $updated_components !== null ) {
			$response_data['updated_score_components'] = $updated_components;
		}

		return $this->create_rest_response( array(
			'success' => true,
			'data' => $response_data
		) );
	}

	/**
	 * RE-ANALYZE: Run fresh Readability analysis for single article
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function run_readability_analysis( $request ) {

		// Check if AI is configured (API key set and terms accepted) before proceeding
		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return $this->create_rest_response( array(
				'success' => false,
				'error' => 'ai_not_configured',
				'message' => __( 'Please add your API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' )
			), 400 );
		}

		$article_id = $request->get_param( 'article_id' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Validate article has valid content for analysis
		$validation_result = EPKB_AI_Content_Analysis_Job_Manager::validate_article_content( $post );
		if ( is_wp_error( $validation_result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'error' => $validation_result->get_error_code(), 'message' => $validation_result->get_error_message() ), 400 );
		}

		// Run readability analysis with force flag (force=true will run fresh analysis)
		$readability_result = EPKB_AI_Readability::analyze( $post, true );
		if ( is_wp_error( $readability_result ) ) {
			if ( EPKB_AI_Utilities::is_retryable_error( $readability_result ) ) {
				return $this->create_rest_response( array( 'can_retry' => true ) );
			}
			$status_code = $readability_result->get_error_code() == 'max_retries_exceeded' ? 503 : 500;
			return $this->create_rest_response( array( 'reason' => $readability_result->get_error_code(), 'success' => false, 'message' => $readability_result->get_error_message() ), $status_code );
		}

		// If analysis was successful, get the updated scores (readability score was already saved by analyze method)
		$updated_score = null;
		$updated_components = null;
		if ( isset( $readability_result['status'] ) && $readability_result['status'] === 'analyzed' && isset( $readability_result['score'] ) ) {
			// Get the updated scores to return to frontend
			$scores_data = EPKB_AI_Content_Analysis_Utilities::get_article_scores( $article_id );
			if ( $scores_data ) {
				$updated_score = isset( $scores_data['overall'] ) ? $scores_data['overall'] : null;
				$updated_components = isset( $scores_data['components'] ) ? EPKB_AI_Content_Analysis_Utilities::format_score_components( $scores_data['components'] ) : null;
			}
		}

		// Get processed content for display/highlighting
		$marker_debug = array();

		$processed_content = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $post->post_content, $post->ID );
		if ( is_wp_error( $processed_content ) ) {
			EPKB_AI_Log::add_log( $processed_content->get_error_message() . ' for article ' . $post->ID );
			$processed_content = $post->post_content;

		} else {
			// If we have issues, mark them in the content for highlighting on the frontend
			if ( isset( $readability_result['issues'] ) && is_array( $readability_result['issues'] ) ) {
				$processed_content = $this->mark_readability_issues_in_content( $processed_content, $readability_result['issues'] );
				$marker_debug = array(
					"content_has_markers" => strpos( $processed_content, 'data-epkb-issue="' ) !== false,
					"marker_count" => substr_count( $processed_content, 'data-epkb-issue="' ),
					"content_length" => mb_strlen( $processed_content ),
					"first_200_chars" => mb_substr( $processed_content, 0, 200 )
				);
			}
		}

		$readability_result['updated_overall_score'] = $updated_score !== null ? $updated_score : null;
		$readability_result['updated_score_components'] = $updated_components !== null ? $updated_components : null;
		$readability_result['processed_content'] = $processed_content;
		$readability_result['debug_markers'] = $marker_debug;

		return $this->create_rest_response( array( 'success' => true, 'data' => $readability_result ) );
	}

	/**
	 * RE-ANALYZE: Run fresh Gap analysis for single article
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function run_gap_analysis( $request ) {

		// Check if AI is configured (API key set and terms accepted) before proceeding
		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return $this->create_rest_response( array(
				'success' => false,
				'error' => 'ai_not_configured',
				'message' => __( 'Please add your API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' )
			), 400 );
		}

		$article_id = $request->get_param( 'article_id' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Validate article has valid content for analysis
		$validation_result = EPKB_AI_Content_Analysis_Job_Manager::validate_article_content( $post );
		if ( is_wp_error( $validation_result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'error' => $validation_result->get_error_code(), 'message' => $validation_result->get_error_message() ), 400 );
		}

		// Run gap analysis with force flag (force=true will run fresh analysis)
		try {
			$gap_result = apply_filters( 'epkb_ai_gap_analysis_analyze', $post, [ 'force' => true ] );
			if ( is_wp_error( $gap_result ) ) {
				if ( EPKB_AI_Utilities::is_retryable_error( $gap_result ) ) {
					return $this->create_rest_response( array( 'can_retry' => true ) );
				}
				return $this->create_rest_response( array( 'reason' => $gap_result->get_error_code(), 'success' => false, 'message' => $gap_result->get_error_message() ), 500 );
			}
		} catch ( Exception $e ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => $e->getMessage() ), 500 );
		}

		if ( ! is_array( $gap_result ) ) {
			$gap_result = array(
				'version' => '1.0',
				'score' => 0,
				'status' => 'not_available',
				'message' => __( 'Gap analysis is only available with AI Features add-on.', 'echo-knowledge-base' ),
				'analyzed_at' => null
			);
		}

		// If analysis was successful, get the updated scores (gap analysis score was already saved by analyze method)
		$updated_score = null;
		$updated_components = null;
		if ( isset( $gap_result['score'] ) ) {
			// Get the updated scores to return to frontend
			$scores_data = EPKB_AI_Content_Analysis_Utilities::get_article_scores( $article_id );
			if ( $scores_data ) {
				$updated_score = isset( $scores_data['overall'] ) ? $scores_data['overall'] : null;
				$updated_components = isset( $scores_data['components'] ) ? EPKB_AI_Content_Analysis_Utilities::format_score_components( $scores_data['components'] ) : null;
			}
		}

		// Get processed content for display (similar to readability analysis)
		$processed_content = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $post->post_content, $post->ID );
		if ( is_wp_error( $processed_content ) ) {
			EPKB_AI_Log::add_log( $processed_content->get_error_message() . ' for article ' . $post->ID );
			$processed_content = $post->post_content;
		}

		// Enhance gap analysis data with processed content preview
		$gap_result['processed_content'] = $processed_content;
		$gap_result['updated_overall_score'] = $updated_score !== null ? $updated_score : null;
		$gap_result['updated_score_components'] = $updated_components !== null ? $updated_components : null;

		return $this->create_rest_response( array( 'success' => true, 'data' => $gap_result ) );
	}

	/**
	 * Mark readability issues in content with text markers for easy highlighting
	 *
	 * @param string $content The processed content (markdown)
	 * @param array $issues Array of issues with problematic_text
	 * @return string $content markdown with markers inserted
	 */
	private function mark_readability_issues_in_content( $content, $issues ) {

		if ( empty( $content ) || empty( $issues ) ) {
			return $content;
		}

		$markers_inserted = 0;

		// Process issues in reverse order to maintain correct positions when inserting markers
		$issues_reversed = array_reverse( $issues, true );
		foreach ( $issues_reversed as $index => $issue ) {

			if ( empty( $issue['problematic_text'] ) ) {
				continue;
			}

			$problematic_text = $issue['problematic_text'];

			// Find the position of this text in the content (case-insensitive)
			$pos = stripos( $content, $problematic_text );
			if ( $pos !== false ) {
				// Insert a unique HTML span marker before the problematic text
				// Markdown preserves HTML tags: <span data-epkb-issue="0"></span>
				$marker = '<span data-epkb-issue="' . $index . '"></span>';
				$content = substr( $content, 0, $pos ) . $marker . substr( $content, $pos );
				$markers_inserted++;
			} else {
				EPKB_AI_Log::add_log( 'Readability marker not inserted', array(
					'issue_index' => $index,
					'problematic_text_length' => mb_strlen( $problematic_text ),
					'content_length' => mb_strlen( $content ),
					'first_50_chars_of_text' => mb_substr( $problematic_text, 0, 50 )
				) );
			}
		}

		return $content;
	}

	/*--------------------------------------------------------------------
	 * Table View and Details View Endpoints
	 *-------------------------------------------------------------------- */

	/**
	 * TABLE VIEW: Get paginated list of articles with their analysis scores and status
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_articles_view_score_status( $request ) {

		$page = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$status = $request->get_param( 'status' );
		$search = $request->get_param( 'search' );
		// kb_id=0 means all KBs, otherwise filter by specific KB
		$kb_id = (int) $request->get_param( 'kb_id' );

		// Get KB articles - either for specific KB or all KBs
		if ( $kb_id === 0 ) {
			// Get all KB post types that the user has access to
			$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
			$post_types = array();
			foreach ( $all_kb_configs as $one_kb_config ) {
				$one_kb_id = $one_kb_config['id'];

				// Skip archived KBs
				if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
					continue;
				}

				// Check user has access to this KB
				$required_capability = EPKB_Admin_UI_Access::get_contributor_capability( $one_kb_id );
				if ( ! current_user_can( $required_capability ) ) {
					continue;
				}

				$post_types[] = EPKB_KB_Handler::get_post_type( $one_kb_id );
			}
			$post_type = ! empty( $post_types ) ? $post_types : array( EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) );
		} else {
			$post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		}

		// For 'to_analyse' and 'to_improve', get all articles and filter by display_status
		$get_all_for_filtering = ( $status === 'to_improve' || $status === 'to_analyse' );

		$args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => $get_all_for_filtering ? -1 : $per_page,
			'paged' => $get_all_for_filtering ? 1 : $page,
			'orderby' => 'modified',
			'order' => 'DESC'
		);

		// Add search if provided
		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		// Don't use meta_query for to_analyse and to_improve - we'll filter by display_status instead
		// Keep meta_query only for other status types like 'analyzed', 'error', 'not_analyzed'
		if ( $status !== 'all' && $status !== 'recent' && $status !== 'to_analyse' && $status !== 'to_improve' ) {
			$meta_query = array();

			switch ( $status ) {
				case 'analyzed':
					$meta_query[] = array(
						'key' => EPKB_AI_Content_Analysis_Utilities::META_ANALYSIS_STATUS,
						'value' => 'analyzed',
						'compare' => '='
					);
					break;
				case 'error':
					$meta_query[] = array(
						'key' => EPKB_AI_Content_Analysis_Utilities::META_ANALYSIS_STATUS,
						'value' => 'error',
						'compare' => '='
					);
					break;
				case 'not_analyzed':
					$meta_query[] = array(
						'relation' => 'OR',
						array(
							'key' => EPKB_AI_Content_Analysis_Utilities::META_ANALYSIS_STATUS,
							'compare' => 'NOT EXISTS'
						),
						array(
							'key' => EPKB_AI_Content_Analysis_Utilities::META_ANALYSIS_STATUS,
							'value' => array( 'analyzed', 'error' ),
							'compare' => 'NOT IN'
						)
					);
					break;
			}

			if ( ! empty( $meta_query ) ) {
				$args['meta_query'] = $meta_query;
			}
		}

		$query = new WP_Query( $args );
		$articles = array();
		foreach ( $query->posts as $post ) {

			$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
			if ( ! $is_post_eligible_for_ai_training ) {
				continue;
			}

			// Get analysis data using utility class
			$analysis_data = EPKB_AI_Content_Analysis_Utilities::get_article_analysis_data( $post->ID );
			$scores = $analysis_data['scores'];
			$dates = $analysis_data['dates'];
			$error = $analysis_data['error'];

			// Get display status
			$display_status = EPKB_AI_Content_Analysis_Utilities::get_article_display_status( $post->ID );

			$article_data = array(
				'id' => $post->ID,
				'item_id' => $post->ID,
				'title' => $post->post_title,
				'score' => $scores && isset( $scores['overall'] ) ? $scores['overall'] : '-',
				'importance' => $scores && isset( $scores['importance'] ) ? $scores['importance'] : 'N/A',
				'last_analyzed' => $dates['analyzed'] ? $dates['analyzed'] : 'Not analyzed',
				'updated' => $post->post_modified,
				'status' => $analysis_data['status'],
				'display_status' => $display_status,  // New status for display
				// Include all dates for future use
				'dates' => $dates,
				// Include ignored and done flags
				'is_ignored' => $analysis_data['is_ignored'],
				'is_done' => $analysis_data['is_done']
			);

			// Add score components
			if ( $analysis_data['is_analyzed'] && $scores && isset( $scores['components'] ) ) {
				$article_data['scoreComponents'] = EPKB_AI_Content_Analysis_Utilities::format_score_components( $scores['components'] );
			} else {
				$article_data['scoreComponents'] = array(
					array( 'name' => 'Tags Usage', 'value' => '-' ),
					array( 'name' => 'Gap Analysis', 'value' => '-' ),
					array( 'name' => 'Readability', 'value' => '-' )
				);
			}

			// Add error details if status is error
			if ( $analysis_data['status'] === 'error' && ! empty( $error['message'] ) ) {
				$article_data['error_message'] = $error['message'];
				$article_data['error_code'] = $error['code'] ?: 500;
			}

			$articles[] = $article_data;
		}

		// Filter for 'to_analyse' status - only show articles with display_status 'To Analyze'
		if ( $status === 'to_analyse' ) {
			$articles = array_filter( $articles, function( $article ) {
				// Only include articles with display_status of 'To Analyze'
				return isset( $article['display_status'] ) && $article['display_status'] === 'To Analyze';
			} );

			// Reindex array after filtering
			$articles = array_values( $articles );

			// Calculate proper pagination for filtered results
			$total_filtered = count( $articles );

			// Apply pagination to the filtered results
			$offset = ( $page - 1 ) * $per_page;
			$articles = array_slice( $articles, $offset, $per_page );
		}

		// Filter for 'to_improve' status - only show articles with display_status 'To Improve'
		if ( $status === 'to_improve' ) {
			$articles = array_filter( $articles, function( $article ) {
				// Only include articles with display_status of 'To Improve'
				return isset( $article['display_status'] ) && $article['display_status'] === 'To Improve';
			} );

			// Reindex array after filtering
			$articles = array_values( $articles );

			// Calculate proper pagination for filtered results
			$total_filtered = count( $articles );

			// Apply pagination to the filtered results
			$offset = ( $page - 1 ) * $per_page;
			$articles = array_slice( $articles, $offset, $per_page );
		}

		// Prepare pagination info
		if ( ( $status === 'to_improve' || $status === 'to_analyse' ) && isset( $total_filtered ) ) {
			// For filtered statuses, use the filtered counts
			$pagination = array(
				'total' => $total_filtered,
				'total_pages' => ceil( $total_filtered / $per_page ),
				'page' => $page,
				'per_page' => $per_page
			);
		} else {
			// For other statuses, use query counts
			$pagination = array(
				'total' => $query->found_posts,
				'total_pages' => $query->max_num_pages,
				'page' => $page,
				'per_page' => $per_page
			);
		}

		return $this->create_rest_response( array(
			'success' => true,
			'data' => $articles,
			'pagination' => $pagination
		) );
	}

	/**
	 * DETAILS VIEW: Get full analysis for single article (runs fresh Tags analysis, uses cached Gap/Readability)
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_article_analysis_details( $request ) {

		// Check if AI is configured (API key set and terms accepted) before proceeding
		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return $this->create_rest_response( array(
				'success' => false,
				'error' => 'ai_not_configured',
				'message' => __( 'Please add your API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' )
			), 400 );
		}

		$article_id = $request->get_param( 'article_id' );
		$kb_id = $request->get_param( 'kb_id' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Get analysis data from database
		$analysis_data = EPKB_AI_Content_Analysis_Utilities::get_article_analysis_data( $article_id );

		// Run all three analysis types consistently
		$errors = array();

		// 1. Tags Usage Analysis
		$tags_result = EPKB_AI_Tags_Usage::analyze( $post );
		$tags_score = 0;
		$tag_analysis = array();
		$current_tags = array();
		$suggested_tags = array();
		$recommended_tags = array();

		if ( is_wp_error( $tags_result ) ) {
			$errors['tags_usage'] = $tags_result->get_error_message();
		} elseif ( is_array( $tags_result ) ) {
			$tags_score = isset( $tags_result['score'] ) ? $tags_result['score'] : 0;
			$tag_analysis = isset( $tags_result['tag_analysis'] ) ? $tags_result['tag_analysis'] : array();
			$current_tags = isset( $tags_result['current_tags'] ) ? $tags_result['current_tags'] : array();
			$suggested_tags = isset( $tags_result['suggested_tags'] ) ? $tags_result['suggested_tags'] : array();
			$recommended_tags = isset( $tags_result['recommended_tags'] ) ? $tags_result['recommended_tags'] : array();
		}

		// 2. Gap Analysis
		$gap_score = 0;
		$gap_data = array();
		try {
			$gap_result = apply_filters( 'epkb_ai_gap_analysis_analyze', $post, [ 'force' => false ] );
			if ( is_wp_error( $gap_result ) ) {
				$errors['gap_analysis'] = $gap_result->get_error_message();
			} elseif ( is_array( $gap_result ) ) {
				$gap_score = isset( $gap_result['score'] ) ? $gap_result['score'] : 0;
				$gap_data = $gap_result;
			} else {
				$gap_result = array(
					'version' => '1.0',
					'score' => 0,
					'status' => 'not_available',
					'message' => __( 'Gap analysis is only available with AI Features add-on.', 'echo-knowledge-base' ),
					'analyzed_at' => null
				);
			}

		} catch ( Exception $e ) {
			$errors['gap_analysis'] = $e->getMessage();
		}

		// 3. Readability Analysis
		$readability_score = 0;
		$readability_data = array();
		$readability_result = EPKB_AI_Readability::analyze( $post );
		if ( is_wp_error( $readability_result ) ) {
			$errors['readability'] = $readability_result->get_error_message();
		} elseif ( is_array( $readability_result ) ) {
			$readability_score = isset( $readability_result['score'] ) ? $readability_result['score'] : 0;
			$readability_data = $readability_result;
		}

		// Get processed content for display (matches readability analysis input)
		$processed_content = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $post->post_content, $post->ID );
		if ( is_wp_error( $processed_content ) ) {
			EPKB_AI_Log::add_log( $processed_content->get_error_message() . ' for article ' . $post->ID );
			$processed_content = $post->post_content;
		} else {
			if ( isset( $readability_data['status'] ) && 'analyzed' === $readability_data['status'] && ! empty( $readability_data['issues'] ) && is_array( $readability_data['issues'] ) ) {
				$processed_content = $this->mark_readability_issues_in_content( $processed_content, $readability_data['issues'] );
			}
		}

		// Build score components array consistently
		$score_components = array(
			array( 'name' => 'Tags Usage', 'value' => $tags_score ),
			array( 'name' => 'Gap Analysis', 'value' => $gap_score ),
			array( 'name' => 'Readability', 'value' => $readability_score ),
		);

		// Calculate overall score from only the components that have been analyzed
		// A score of 0 might mean either "not analyzed" or "analyzed with 0 score"
		// Check the status to determine if it's been analyzed
		$analyzed_scores = array();

		// Tags: always analyzed (returns a score immediately)
		if ( $tags_score > 0 || ( is_array( $tags_result ) && isset( $tags_result['status'] ) && $tags_result['status'] === 'analyzed' ) ) {
			$analyzed_scores[] = $tags_score;
		}

		// Gap Analysis: check if it has been analyzed
		if ( $gap_score > 0 || ( is_array( $gap_result ) && isset( $gap_result['status'] ) && $gap_result['status'] === 'analyzed' ) ) {
			$analyzed_scores[] = $gap_score;
		}

		// Readability: check if it has been analyzed
		if ( $readability_score > 0 || ( is_array( $readability_result ) && isset( $readability_result['status'] ) && $readability_result['status'] === 'analyzed' ) ) {
			$analyzed_scores[] = $readability_score;
		}

		// Calculate overall score as average of analyzed components
		if ( count( $analyzed_scores ) > 0 ) {
			$overall_score = round( array_sum( $analyzed_scores ) / count( $analyzed_scores ) );
		} else {
			// If no components have been analyzed, use the stored score or null
			$overall_score = ( $analysis_data && isset( $analysis_data['scores']['overall'] ) ) ? $analysis_data['scores']['overall'] : null;
		}

		// Build detailed response
		$details = array(
			'article_id' => $article_id,
			'title' => $post->post_title,
			'status' => $post->post_status,
			'score' => $overall_score,
			'importance' => ( $analysis_data && isset( $analysis_data['scores']['importance'] ) ) ? $analysis_data['scores']['importance'] : 0,
			'last_analyzed' => ( $analysis_data && isset( $analysis_data['dates']['analyzed'] ) ) ? $analysis_data['dates']['analyzed'] : null,
			'display_status' => EPKB_AI_Content_Analysis_Utilities::get_article_display_status( $article_id ),
			'scoreComponents' => $score_components,
			'current_tags' => $current_tags,
			'suggested_tags' => $suggested_tags,
			'recommended_tags' => $recommended_tags,
			'tag_analysis' => $tag_analysis,
			'gap_analysis' => $gap_data,
			'readability_analysis' => $readability_data,
			'processed_content' => $processed_content,
			'errors' => $errors
		);

		return $this->create_rest_response( array( 'success' => true, 'data' => $details ) );
	}

	/**
	 * Get processed article content for display
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_article_processed_content( $request ) {

		$article_id = $request->get_param( 'article_id' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Validate article has valid content for analysis
		$validation_result = EPKB_AI_Content_Analysis_Job_Manager::validate_article_content( $post );
		if ( is_wp_error( $validation_result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'error' => $validation_result->get_error_code(), 'message' => $validation_result->get_error_message() ), 400 );
		}

		// Get processed content for display
		$processed_content = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $post->post_content, $post->ID );
		if ( is_wp_error( $processed_content ) ) {
			EPKB_AI_Log::add_log( $processed_content->get_error_message() . ' for article ' . $post->ID );
			return $this->create_rest_response( array( 'success' => false, 'message' => $processed_content->get_error_message() ), 500 );
		}

		// Run readability analysis to get issues for marking (use cached results)
		$readability_data = EPKB_AI_Readability::analyze( $post );
		if ( ! is_wp_error( $readability_data ) ) {
			if ( isset( $readability_data['status'] ) && 'analyzed' === $readability_data['status'] && ! empty( $readability_data['issues'] ) && is_array( $readability_data['issues'] ) ) {
				$processed_content = $this->mark_readability_issues_in_content( $processed_content, $readability_data['issues'] );
			}
		}

		return $this->create_rest_response( array( 'success' => true, 'data' => array( 'article_id' => $article_id, 'processed_content' => $processed_content ) ) );
	}

	/*--------------------------------------------------------------------
	 * Article actions: ignore, done, add/remove tag
	 *--------------------------------------------------------------------*/

	/**
	 * Toggle article ignored status
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function toggle_article_ignored( $request ) {

		$article_id = $request->get_param( 'article_id' );
		$ignored = $request->get_param( 'ignored' );

		// Set the ignored status
		$result = EPKB_AI_Content_Analysis_Utilities::set_article_ignored( $article_id, $ignored );

		if ( ! $result ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Failed to update article ignored status', 'echo-knowledge-base' ) ), 500 );
		}

		// Get updated display status
		$display_status = EPKB_AI_Content_Analysis_Utilities::get_article_display_status( $article_id );

		return $this->create_rest_response( array( 'success' => true, 'display_status' => $display_status, 'is_ignored' => $ignored ) );
	}

	/**
	 * Toggle article done status
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function toggle_article_done( $request ) {

		$article_id = $request->get_param( 'article_id' );
		$done = $request->get_param( 'done' );

		// Set the done status
		$result = EPKB_AI_Content_Analysis_Utilities::set_article_done( $article_id, $done );
		if ( ! $result ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Failed to update article done status', 'echo-knowledge-base' ) ), 500 );
		}

		// Get updated display status
		$display_status = EPKB_AI_Content_Analysis_Utilities::get_article_display_status( $article_id );

		return $this->create_rest_response( array( 'success' => true, 'display_status' => $display_status, 'is_done' => $done ) );
	}

	/**
	 * Add tag to article
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function add_article_tag( $request ) {

		$article_id = $request->get_param( 'article_id' );
		$tag_name = $request->get_param( 'tag_name' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Derive KB ID from article's post type
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( empty( $kb_id ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Could not determine KB ID from article', 'echo-knowledge-base' ) ), 400 );
		}

		// Get KB tag taxonomy name
		$kb_tag_taxonomy = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );
		if ( empty( $kb_tag_taxonomy ) ) {
			return $this->create_rest_response( array(
				'success' => false,
				'message' => __( 'Could not determine KB tag taxonomy', 'echo-knowledge-base' )
			), 500 );
		}

		// Check if tag already exists, if not create it
		$term = get_term_by( 'name', $tag_name, $kb_tag_taxonomy );
		if ( ! $term ) {
			$term = wp_insert_term( $tag_name, $kb_tag_taxonomy );
			if ( is_wp_error( $term ) ) {
				return $this->create_rest_response( array( 'success' => false, 'message' => $term->get_error_message() ), 500 );
			}
			$term_id = $term['term_id'];
		} else {
			$term_id = $term->term_id;
		}

		// Add tag to article
		$result = wp_set_object_terms( $article_id, intval( $term_id ), $kb_tag_taxonomy, true );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => $result->get_error_message() ), 500 );
		}

		// Clear analysis cache so fresh data is fetched next time
		EPKB_AI_Tags_Usage::clear_cache( $article_id );

		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Tag added successfully', 'echo-knowledge-base' ), 'tag_id' => $term_id ) );
	}

	/**
	 * Remove tag from article
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function remove_article_tag( $request ) {

		$article_id = $request->get_param( 'article_id' );
		$tag_id = $request->get_param( 'tag_id' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Derive KB ID from article's post type
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( empty( $kb_id ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Could not determine KB ID from article', 'echo-knowledge-base' ) ), 400 );
		}

		// Get KB tag taxonomy name
		$kb_tag_taxonomy = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );
		if ( empty( $kb_tag_taxonomy ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Could not determine KB tag taxonomy', 'echo-knowledge-base' ) ), 500 );
		}

		// Get current tags
		$current_tags = wp_get_object_terms( $article_id, $kb_tag_taxonomy, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $current_tags ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => $current_tags->get_error_message() ), 500 );
		}

		// Remove the specified tag
		$updated_tags = array_diff( $current_tags, array( $tag_id ) );

		// Update article tags
		$result = wp_set_object_terms( $article_id, $updated_tags, $kb_tag_taxonomy );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => $result->get_error_message() ), 500 );
		}

		// Clear analysis cache so fresh data is fetched next time
		EPKB_AI_Tags_Usage::clear_cache( $article_id );

		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Tag removed successfully', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Edit article tag
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function edit_article_tag( $request ) {

		$article_id = $request->get_param( 'article_id' );
		$tag_id = $request->get_param( 'tag_id' );
		$new_name = $request->get_param( 'new_name' );

		// Get article post
		$post = get_post( $article_id );
		if ( ! $post ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article not found', 'echo-knowledge-base' ) ), 404 );
		}

		$is_post_eligible_for_ai_training = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( ! $is_post_eligible_for_ai_training ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Article is not eligible for AI training', 'echo-knowledge-base' ) ), 400 );
		}

		// Derive KB ID from article's post type
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( empty( $kb_id ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Could not determine KB ID from article', 'echo-knowledge-base' ) ), 400 );
		}

		// Get KB tag taxonomy name
		$kb_tag_taxonomy = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );
		if ( empty( $kb_tag_taxonomy ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Could not determine KB tag taxonomy', 'echo-knowledge-base' ) ), 500 );
		}

		// Get the tag term
		$term = get_term( $tag_id, $kb_tag_taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => __( 'Tag not found', 'echo-knowledge-base' ) ), 404 );
		}

		// Update the tag name
		$result = wp_update_term( $tag_id, $kb_tag_taxonomy, array( 'name' => $new_name, 'slug' => sanitize_title( $new_name ) ) );

		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array( 'success' => false, 'message' => $result->get_error_message() ), 500 );
		}

		// Clear analysis cache so fresh data is fetched next time
		EPKB_AI_Tags_Usage::clear_cache( $article_id );

		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Tag updated successfully', 'echo-knowledge-base' ) ) );
	}


	/**
	 * Check if user has permission to access AI admin endpoints
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_admin_permission( $request ) {

		// Check nonce
		$nonce_check = EPKB_AI_Security::check_rest_nonce( $request );
		if ( is_wp_error( $nonce_check ) ) {
			return $nonce_check;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission to perform this action.', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}
		return true;
	}
}
