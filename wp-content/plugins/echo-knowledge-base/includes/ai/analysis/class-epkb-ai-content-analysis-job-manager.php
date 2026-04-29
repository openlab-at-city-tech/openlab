<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Content Analysis Job Manager
 *
 * Manages content analysis jobs with unified approach for both direct and cron modes.
 * Processes articles one at a time for consistent behavior, following the same pattern as sync.
 * Stores analysis state in WordPress option for persistence and single-job enforcement.
 */
class EPKB_AI_Content_Analysis_Job_Manager {

	private static $cached_job = null;

	const ANALYSIS_JOB_OPTION_NAME = 'epkb_ai_content_analysis_job_status';
	const ANALYSIS_ARTICLE_PROGRESS_META_KEY = '_epkb_content_analysis_progress';

	const CRON_HOOK = 'epkb_do_content_analysis_cron_event';
	const OLD_RUNNING_JOB_THRESHOLD_HOURS = 24; // Jobs older than 1 day are auto-canceled

	/**
	 * Process a single analysis type for an article
	 *
	 * @param int $article_id Article ID to analyze
	 * @param string $analysis_type Type of analysis: 'tags', 'readability', or 'gap'
	 */
	public static function process_article_single_analysis( $article_id, $analysis_type ) {

		// Check if job was canceled
		if ( self::is_job_canceled() ) {
			return new WP_Error( 'analysis_canceled', __( 'Analysis was canceled', 'echo-knowledge-base' ), array( 'error' => __( 'Analysis was canceled', 'echo-knowledge-base' ) ) );
		}

		// Verify this article is still the one being processed (only on first analysis)
		if ( self::get_analysis_job()['processing_article_id'] != $article_id ) {
			return new WP_Error( 'article_mismatch', __( 'Article mismatch', 'echo-knowledge-base' ), array( 'error' => __( 'Article mismatch', 'echo-knowledge-base' ) ) );
		}

		try {
			// Perform the specific analysis type (with no-retry flag)
			$result = self::analyze_article_single_type( $article_id, $analysis_type );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

		} catch ( Exception $e ) {
			// translators: %1$s is the analysis type, %2$s is the error message
			$error_message = sprintf( __( 'Unexpected error in %1$s analysis: %2$s', 'echo-knowledge-base' ), $analysis_type, $e->getMessage() );

			return new WP_Error( 'unexpected_error', $error_message, array( 'error' => $error_message ) );
		}

		return $result;
	}

	/**
	 * Analyze a single type for an article
	 * Does NOT retry on errors - client handles retries
	 *
	 * @param int $article_id Article ID
	 * @param string $analysis_type Type of analysis: 'tags', 'readability', or 'gap'
	 * @return array|WP_Error Analysis result for this specific type
	 */
	private static function analyze_article_single_type( $article_id, $analysis_type ) {

		$post = get_post( $article_id );
		if ( ! $post ) {
			return new WP_Error( 'invalid_article', __( 'Article not found', 'echo-knowledge-base' ) );
		}

		// check this is article
		if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return new WP_Error( 'invalid_article', __( 'Article is not a knowledge base article', 'echo-knowledge-base' ) );
		}

		// Validate article has valid content for analysis
		$validation_result = self::validate_article_content( $post );
		if ( is_wp_error( $validation_result ) ) {
			return $validation_result;
		}
		
		// Get current analysis metadata (scores accumulated across calls)
		$progress = get_post_meta( $article_id, self::ANALYSIS_ARTICLE_PROGRESS_META_KEY, true );
		if ( ! is_array( $progress ) ) {
			$progress = array(
				'tags_score' => null,
				'readability_score' => null,
				'gap_score' => null,
				'tags_analysis' => null,
				'readability_analysis' => null,
				'gap_analysis' => null
			);
		}

		// Run the requested analysis type
		switch ( $analysis_type ) {
			case 'tags':
				$tags_analysis = EPKB_AI_Tags_Usage::analyze( $post, true );
				if ( is_wp_error( $tags_analysis ) ) {
					EPKB_AI_Content_Analysis_Utilities::set_analysis_error( $post, $tags_analysis->get_error_message() );
					return $tags_analysis;
				}
				$progress['tags_score'] = isset( $tags_analysis['score'] ) ? $tags_analysis['score'] : 0;
				$progress['tags_analysis'] = $tags_analysis;
				break;

			case 'readability':
				$readability_analysis = EPKB_AI_Readability::analyze( $post, true );
				if ( is_wp_error( $readability_analysis ) ) {
					EPKB_AI_Content_Analysis_Utilities::set_analysis_error( $post, $readability_analysis->get_error_message() );
					return $readability_analysis;
				}
				$progress['readability_score'] = isset( $readability_analysis['score'] ) ? $readability_analysis['score'] : 0;
				$progress['readability_analysis'] = $readability_analysis;
				break;

			case 'gap':
				try {
					$gap_analysis = apply_filters( 'epkb_ai_gap_analysis_analyze', $post, [ 'force' => true ] );
				} catch ( Exception $e ) {
					// translators: %s is the exception error message
					return new WP_Error( 'gap_analysis_exception', sprintf( __( 'Gap analysis exception: %s', 'echo-knowledge-base' ), $e->getMessage() ) );
				}

				if ( is_wp_error( $gap_analysis ) ) {
					EPKB_AI_Content_Analysis_Utilities::set_analysis_error( $post, $gap_analysis->get_error_message() );
					return $gap_analysis;
				}

				// if AI Features add-on is not active, return not_available
				if ( ! is_array( $gap_analysis ) ) {
					$gap_analysis = array(
						'version' => '1.0',
						'score' => 0,
						'status' => 'not_available',
						'message' => __( 'Gap analysis is only available with AI Features add-on.', 'echo-knowledge-base' ),
						'analyzed_at' => null
					);
				}

				$progress['gap_score'] = isset( $gap_analysis['score'] ) ? $gap_analysis['score'] : 0;
				$progress['gap_analysis'] = $gap_analysis;
				break;

			default:
				return new WP_Error( 'invalid_analysis_type', __( 'Invalid analysis type', 'echo-knowledge-base' ) );
		}

		// Save progress metadata
		update_post_meta( $article_id, self::ANALYSIS_ARTICLE_PROGRESS_META_KEY, $progress );

		// if we completed only one analysis, return partial result
		$all_complete = ( $progress['tags_score'] !== null && $progress['readability_score'] !== null && $progress['gap_score'] !== null );
		if ( ! $all_complete ) {
			// Return partial result
			return array(
				'id' => $article_id,
				'title' => $post->post_title,
				'analysis_type' => $analysis_type,
				'status' => 'partial',
				'complete' => false,
			);
		}

		// Calculate final scores
		$tags_score = $progress['tags_score'];
		$readability_score = $progress['readability_score'];
		$gap_score = $progress['gap_score'];

		// Calculate overall score based on analyzed scores
		$analyzed_scores = array();
		if ( $tags_score > 0 || ( is_array( $progress['tags_analysis'] ) && isset( $progress['tags_analysis']['status'] ) && $progress['tags_analysis']['status'] === 'analyzed' ) ) {
			$analyzed_scores[] = $tags_score;
		}
		if ( $readability_score > 0 || ( is_array( $progress['readability_analysis'] ) && isset( $progress['readability_analysis']['status'] ) && $progress['readability_analysis']['status'] === 'analyzed' ) ) {
			$analyzed_scores[] = $readability_score;
		}
		if ( $gap_score > 0 || ( is_array( $progress['gap_analysis'] ) && isset( $progress['gap_analysis']['status'] ) && $progress['gap_analysis']['status'] === 'analyzed' ) ) {
			$analyzed_scores[] = $gap_score;
		}

		// Calculate overall score based on analyzed scores
		$overall_score = 0;
		if ( count( $analyzed_scores ) > 0 ) {
			$overall_score = round( array_sum( $analyzed_scores ) / count( $analyzed_scores ) );
		}

		// Save final scores
		$scores = array(
			'overall' => $overall_score,
			'components' => array(
				'gap_analysis' => $gap_score,
				'tags_usage' => $tags_score,
				'readability' => $readability_score
			)
		);
		EPKB_AI_Content_Analysis_Utilities::save_article_scores( $article_id, $scores );
		EPKB_AI_Content_Analysis_Utilities::set_analysis_status( $article_id, 'analyzed' );
		EPKB_AI_Content_Analysis_Utilities::update_article_date( $article_id, 'analyzed' );

		// Clear progress metadata
		delete_post_meta( $article_id, self::ANALYSIS_ARTICLE_PROGRESS_META_KEY );

		// Return complete result
		$current_date = EPKB_AI_Content_Analysis_Utilities::get_article_date( $article_id, 'analyzed' );

		return array(
			'id' => $article_id,
			'title' => $post->post_title,
			'status' => 'analyzed',
			'complete' => true,
			'score' => $overall_score,
			'scoreComponents' => EPKB_AI_Content_Analysis_Utilities::format_score_components( $scores['components'] ),
			'analyzed_at' => $current_date,
			'details' => array(
				'gap_analysis' => $progress['gap_analysis'],
				'tags_analysis' => $progress['tags_analysis'],
				'readability_analysis' => $progress['readability_analysis']
			),
		);
	}


	/*****************************************************************************************
	 * Article Metadata Utilities
	 *****************************************************************************************/

	/**
	 * Validate article has valid content for analysis
	 * Checks for empty content, processes it, and validates the markdown output
	 *
	 * @param WP_Post $post Article post object
	 * @return string|WP_Error Processed markdown content or WP_Error on failure
	 */
	public static function validate_article_content( $post ) {

		// Validate article has content before processing
		if ( empty( $post->post_content ) ) {
			return new WP_Error( 'empty_content', __( 'Article has empty content', 'echo-knowledge-base' ) );
		}

		// Process content to check it's valid after filtering
		$content_processor = new EPKB_AI_Content_Processor();
		$markdown = $content_processor->clean_content( $post->post_content, $post->ID );
		if ( is_wp_error( $markdown ) ) {
			// translators: %s is the error message
			return new WP_Error( 'content_processing_failed', sprintf( __( 'Failed to process article content: %s', 'echo-knowledge-base' ), $markdown->get_error_message() ) );
		}

		// Verify markdown is not empty after processing
		if ( empty( trim( $markdown ) ) ) {
			return new WP_Error( 'empty_markdown', __( 'Article content is empty after markdown processing', 'echo-knowledge-base' ) );
		}

		return $markdown;
	}

	/**
	 * Add article to the job
	 * Called from REST endpoint when frontend starts analyzing an article
	 *
	 * @param int $article_id Article ID
	 */
	public static function add_article_to_the_job( $article_id ) {
		self::update_analysis_job( array(
			'processing_article_id' => $article_id,
			'processing_started_at' => gmdate( 'Y-m-d H:i:s' )
		) );
	}

	/**
	 * Get job timing data with completed analysis timing
	 *
	 * @param int $elapsed_ms Time in milliseconds for this analysis
	 */
	public static function update_analysis_timing( $elapsed_ms ) {
		
		$job = self::get_analysis_job();

		$total_time = ( isset( $job['total_analysis_time_ms'] ) ? $job['total_analysis_time_ms'] : 0 ) + $elapsed_ms;
		$completed = ( isset( $job['completed_analyses'] ) ? $job['completed_analyses'] : 0 ) + 1;
		$avg_time = $completed > 0 ? round( $total_time / $completed ) : 0;

		self::update_analysis_job( array(
			'total_analysis_time_ms' => $total_time,
			'completed_analyses' => $completed,
			'avg_analysis_time_ms' => $avg_time
		) );
	}


	/*****************************************************************************************
	 * Analysis Job Data Management
	 *****************************************************************************************/

	private static function get_default_job_data() {
		return array(
			'status' => 'idle',					// ACTIVE: pending (initializing)/scheduled (cron)/running, idle, completed, failed (error occurred)
			'type' => '',						// 'direct' or 'cron'
			'items' => array(),					// array of articles to analyze
			'article_ids' => array(),			// list of article IDs for resume support
			'processed_ids' => array(),			// list of article IDs already processed (for resume support)
			'processed' => 0,					// number of articles processed
			'total' => 0,						// total number of articles to analyze
			'percent' => 0,						// percentage of articles processed
			'errors' => 0,						// number of errors
			'consecutive_errors' => 0,
			'cancel_requested' => false,		// flag to cancel the job
			'start_time' => gmdate( 'Y-m-d H:i:s' ),
			'last_update' => gmdate( 'Y-m-d H:i:s' ),
			'processing_article_id' => null,  // Track currently processing article for async flow
			'processing_started_at' => null,  // Track when processing started
			'total_analysis_time_ms' => 0,     // Total time spent on all analyses (milliseconds)
			'completed_analyses' => 0,         // Number of completed analyses (for calculating average)
			'avg_analysis_time_ms' => 0        // Running average time per analysis
		);
	}

	/**
	 * Get current analysis job status
	 *
	 * @return array Analysis job data or default values
	 */
	public static function get_analysis_job() {

		if ( self::$cached_job !== null ) {
			return self::$cached_job;
		}

		$default = self::get_default_job_data();
		$job = get_option( self::ANALYSIS_JOB_OPTION_NAME, $default );
		self::$cached_job = wp_parse_args( $job, $default );

		return self::$cached_job;
	}

	/**
	 * Check if a job is active
	 *
	 * @return bool
	 */
	private static function is_job_active() {
		$job = self::get_analysis_job();
		return in_array( $job['status'], array( 'pending', 'scheduled', 'running' ) );
	}

	/**
	 * Check if an active job is older than OLD_JOB_THRESHOLD_HOURS (1 day)
	 * @return bool
	 */
	private static function is_job_old() {	
		$job = self::get_analysis_job();

		// No last_update means it's old
		if ( empty( $job['last_update'] ) ) {
			return true;
		}

		// Parse timestamp as UTC since gmdate stores in UTC
		$last_update = strtotime( $job['last_update'] . ' UTC' );
		$threshold_seconds = self::OLD_RUNNING_JOB_THRESHOLD_HOURS * 3600;

		// Check if last_update is older than 1 day
		return ( time() - $last_update ) > $threshold_seconds;
	}

	private static function is_job_canceled() {
		$job = self::get_analysis_job();
		return ! empty( $job['cancel_requested'] );
	}

	/**
	 * Update analysis job status
	 *
	 * @param array $data Data to update
	 * @return array Array with 'success' (bool) and 'reason' (string: 'job_canceled', 'no_change', 'updated', 'update_failed')
	 */
	public static function update_analysis_job( $data=array(), $force=false ) {

		if ( ! $force ) {
			$job = self::get_analysis_job();
			$updated_job = array_merge( $job, $data );
		}

		$updated_job['last_update'] = gmdate( 'Y-m-d H:i:s' );

		// Get current option value to detect if it's the same
		$current_option = get_option( self::ANALYSIS_JOB_OPTION_NAME );

		// If values are identical, treat as success (no change needed)
		if ( $current_option === $updated_job ) {
			return array( 'success' => true, 'reason' => 'no_change' );
		}

		$result = update_option( self::ANALYSIS_JOB_OPTION_NAME, $updated_job, false );
		if ( $result === false ) {
			return array( 'success' => false, 'reason' => 'update_failed' );
		}

		self::$cached_job = null;

		return array( 'success' => true, 'reason' => 'updated' );
	}

	/**
	 * Initialize a new analysis job
	 *
	 * @param array|string $article_ids Article IDs or 'ALL' or 'ALL_STATUS'
	 * @param string $mode 'direct' or 'cron'
	 * @return array|WP_Error
	 */
	public static function initialize_analysis_job( $article_ids, $mode = 'direct' ) {

		// Check if there's an active recent job for user to confirm canceling
		/* if ( self::is_job_active() && ! self::is_job_old() ) {
			return new WP_Error( 'job_active', __( 'An analysis job is already running. Cancel the current job or wait for it to finish.', 'echo-knowledge-base' ) );
		} */

		// Cancel any existing job
		self::cancel_all_analysis();

		// Get articles to analyze
		$articles = array();
		if ( $article_ids === 'ALL' ) {

			// Get all KB articles
			$args = array(
				'post_type' => EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ),
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'orderby' => 'modified',
				'order' => 'DESC'
			);
			$all_articles = get_posts( $args );
			foreach ( $all_articles as $article_id ) {
				$articles[] = array( 'id' => $article_id, 'type' => 'article' );
			}

		} elseif ( is_array( $article_ids ) ) {
			foreach ( $article_ids as $article_id ) {
				$articles[] = array( 'id' => $article_id, 'type' => 'article' );
			}

		} else {
			return new WP_Error( 'invalid_article_ids', __( 'Invalid article IDs provided:', 'echo-knowledge-base' ) . ' ' . ( is_string( $article_ids ) ? $article_ids : gettype( $article_ids ) ) );
		}

		// Check if we have articles to analyze
		if ( empty( $articles ) ) {
			return new WP_Error( 'no_articles', __( 'No articles found to analyze', 'echo-knowledge-base' ) );
		}

		// Create initial job data
		$job_data = array(
			'status' => $mode === 'cron' ? 'scheduled' : 'running',
			'type' => $mode,
			'items' => $articles,
			'article_ids' => wp_list_pluck( $articles, 'id' ),
			'total' => count( $articles )
		);

		// Save job via helper to ensure consistent metadata (e.g., last_update)
		$update_result = self::update_analysis_job( $job_data );
		if ( ! $update_result['success'] ) {
			return new WP_Error( 'save_failed', __( 'Failed to save analysis job:', 'echo-knowledge-base' ) . ' ' . $update_result['reason'] );
		}

		return $job_data;
	}

	/*****************************************************************************************
	 * Utility methods for managing analysis job data
	 *****************************************************************************************/

	/**
	 * Cancel all analysis operations
	 * @return true|WP_Error
	 */
	public static function cancel_all_analysis() {

		// Mark cancel requested and set to idle (align with sync semantics)
		$job = self::get_default_job_data();
		$job['cancel_requested'] = true;
		$job['status'] = 'completed';
		self::$cached_job = $job;
		$update_result = self::update_analysis_job( $job, true );
		if ( ! $update_result['success'] ) {
			return new WP_Error( 'update_failed', __( 'Failed to update job status:', 'echo-knowledge-base' ) . ' ' . $update_result['reason'] );
		}

		// Clear scheduled cron event if exists
		// FUTURE wp_clear_scheduled_hook( self::CRON_HOOK );

		return true;
	}
}
