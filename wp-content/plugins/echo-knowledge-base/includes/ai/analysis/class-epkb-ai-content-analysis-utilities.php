<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Content Analysis Utilities
 *
 * Utility class for managing content analysis metadata and common operations.
 * Provides centralized methods for score management, date tracking, and metadata operations.
 */
class EPKB_AI_Content_Analysis_Utilities {

	// Legacy metadata keys (no longer used - data now stored in database table)
	const META_SCORES_DATA = '_epkb_content_scores_data';
	const META_ANALYSIS_STATUS = '_epkb_content_analysis_status';
	const META_ANALYSIS_ERROR = '_epkb_content_analysis_error';
	const META_ANALYSIS_ERROR_CODE = '_epkb_content_analysis_error_code';

	/**
	 * Save article analysis scores
	 * Now uses database table instead of post meta
	 *
	 * @param int $article_id Article ID
	 * @param array $scores Array with 'overall' and 'components' keys
	 * @return bool Success
	 */
	public static function save_article_scores( $article_id, $scores ) {

		if ( ! is_array( $scores ) || ! isset( $scores['overall'] ) || ! isset( $scores['components'] ) ) {
			return false;
		}

		$db = new EPKB_AI_Content_Analysis_DB( false );
		$data = array(
			'overall_score' => (int) $scores['overall'],
			'analyzed_at' => gmdate( 'Y-m-d H:i:s' ),
			'status' => 'analyzed'
		);

		$result = $db->save_article_analysis( $article_id, $data );

		return ! is_wp_error( $result );
	}

	/**
	 * Get article scores from database
	 *
	 * @param int $article_id Article ID
	 * @return array|null Array of scores or null if not analyzed
	 */
	public static function get_article_scores( $article_id, $analysis=null ) {

		if ( ! $analysis ) {
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$analysis = $db->get_article_analysis( $article_id );
		}

		if ( ! $analysis ) {
			return null;
		}

		// Build components array from individual score fields
		$components = array(
			'tags_usage' => (int) $analysis->tags_usage_score,
			'readability' => (int) $analysis->readability_score,
			'gap_analysis' => (int) $analysis->gap_analysis_score
		);

		return array(
			'overall' => (int) $analysis->overall_score,
			'components' => $components,
			'importance' => (int) $analysis->importance
		);
	}

	/**
	 * Set article analysis status
	 *
	 * @param int $article_id Article ID
	 * @param string $status
	 * @return bool Success
	 */
	public static function set_analysis_status( $article_id, $status ) {
		$db = new EPKB_AI_Content_Analysis_DB( false );
		$result = $db->save_article_analysis( $article_id, array( 'status' => $status ) );
		return ! is_wp_error( $result );
	}

	/**
	 * Get article analysis status from database
	 *
	 * @param int $article_id Article ID
	 * @return string Status or empty string if not set
	 */
	public static function get_analysis_status( $article_id, $analysis=null ) {

		if ( ! $analysis ) {
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$analysis = $db->get_article_analysis( $article_id );
		}

		return $analysis ? $analysis->status : '';
	}

	/**
	 * Set analysis error - uses database now
	 *
	 * @param $post
	 * @param string $error_message Error message (max 200 characters)
	 * @param int $error_code Error code
	 * @return bool Success
	 */
	public static function set_analysis_error( $post, $error_message, $error_code = 500 ) {

		// Log error to AI error log
		$article_title = $post ? $post->post_title : "Article #{$post->ID}";

		EPKB_AI_Log::add_log( $error_message, array(
			'context' => 'Content Analysis',
			'article_id' => $post->ID,
			'article_title' => $article_title,
			'error_code' => $error_code
		) );

		// Limit error message length
		if ( strlen( $error_message ) > 200 ) {
			$error_message = substr( $error_message, 0, 197 ) . '...';
		}

		$db = new EPKB_AI_Content_Analysis_DB( false );
		$result = $db->save_article_analysis( $post->ID, array( 'error_message' => $error_message, 'status' => 'error' ) );

		return ! is_wp_error( $result );
	}

	/**
	 * Get analysis error from database
	 *
	 * @param int $article_id Article ID
	 * @return array Array with 'message' and 'code' keys
	 */
	public static function get_analysis_error( $article_id, $analysis=null ) {

		if ( ! $analysis ) {
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$analysis = $db->get_article_analysis( $article_id );
		}

		return array(
			'message' => $analysis && $analysis->error_message ? $analysis->error_message : '',
			'code' => 0 // No longer storing error codes separately
		);
	}

	/**
	 * Update article date for specific action - uses database now
	 *
	 * @param int $article_id Article ID
	 * @param string $action Action type: 'analyzed', 'improved', 'ignored', 'done'
	 * @param string|null $date Date string or null for current time
	 * @return bool Success
	 */
	public static function update_article_date( $article_id, $action, $date = null ) {

		$valid_actions = array( 'analyzed', 'improved', 'ignored', 'done' );

		if ( ! in_array( $action, $valid_actions ) ) {
			return false;
		}

		$db = new EPKB_AI_Content_Analysis_DB( false );
		$date_value = $date ?: gmdate( 'Y-m-d H:i:s' );

		// Map action to database field
		$field_map = array(
			'analyzed' => 'analyzed_at',
			'improved' => 'date_improved',
			'ignored' => 'date_ignored',
			'done' => 'date_done'
		);

		$field = $field_map[$action];
		$result = $db->save_article_analysis( $article_id, array( $field => $date_value ) );

		return ! is_wp_error( $result );
	}

	/**
	 * Clear article date for specific action
	 *
	 * @param int $article_id Article ID
	 * @param string $action Action type: 'analyzed', 'improved', 'ignored', 'done'
	 * @return bool Success
	 */
	public static function clear_article_date( $article_id, $action ) {

		$valid_actions = array( 'analyzed', 'improved', 'ignored', 'done' );

		if ( ! in_array( $action, $valid_actions ) ) {
			return false;
		}

		$db = new EPKB_AI_Content_Analysis_DB( false );

		// Map action to database field
		$field_map = array(
			'analyzed' => 'analyzed_at',
			'improved' => 'date_improved',
			'ignored' => 'date_ignored',
			'done' => 'date_done'
		);

		$field = $field_map[$action];
		$result = $db->save_article_analysis( $article_id, array( $field => null ) );

		return ! is_wp_error( $result );
	}

	/**
	 * Get all article dates from database
	 *
	 * @param int $article_id Article ID
	 * @return array Array of dates
	 */
	public static function get_article_dates( $article_id, $analysis=null ) {

		if ( ! $analysis ) {
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$analysis = $db->get_article_analysis( $article_id );
		}

		if ( ! $analysis ) {
			return array(
				'analyzed' => '',
				'improved' => '',
				'ignored' => '',
				'done' => ''
			);
		}

		return array(
			'analyzed' => $analysis->analyzed_at ?: '',
			'improved' => $analysis->date_improved ?: '',
			'ignored' => $analysis->date_ignored ?: '',
			'done' => $analysis->date_done ?: ''
		);
	}

	/**
	 * Get a specific article date from database
	 *
	 * @param int $article_id Article ID
	 * @param string $action Action type: 'analyzed', 'improved', 'ignored', 'done'
	 * @return string Date string or empty string
	 */
	public static function get_article_date( $article_id, $action ) {
		$dates = self::get_article_dates( $article_id );
		return isset( $dates[$action] ) ? $dates[$action] : '';
	}

	/**
	 * Clear all analysis data for an article
	 *
	 * @param int $article_id Article ID
	 * @return bool Success
	 */
	public static function clear_all_analysis_metadata( $article_id ) {
		$db = new EPKB_AI_Content_Analysis_DB( false );
		return $db->delete_article_analysis( $article_id );
	}

	/**
	 * Get complete analysis data for an article
	 *
	 * @param int $article_id Article ID
	 * @return array Complete analysis data
	 */
	public static function get_article_analysis_data( $article_id ) {

		// Check if this is a demo article
		$is_demo_article = EPKB_KB_Demo_Data::is_demo_article( $article_id );
		if ( $is_demo_article ) {
			$demo_tags_data = EPKB_KB_Demo_Data::get_demo_tags_usage_data();
			return array(
				'status' => 'analyzed',
				'scores' => array(
					'overall' => $demo_tags_data['score'],
					'components' => array(
						'tags_usage' => $demo_tags_data['score'],
						'readability' => 0,
						'gap_analysis' => 0
					),
					'importance' => 50
				),
				'dates' => array(
					'analyzed' => current_time( 'mysql' ),
					'improved' => '',
					'ignored' => '',
					'done' => ''
				),
				'error' => array( 'message' => '', 'code' => 0 ),
				'is_analyzed' => true,
				'is_improved' => false,
				'is_ignored' => false,
				'is_done' => false,
				'is_demo' => true
			);
		}

		$db = new EPKB_AI_Content_Analysis_DB( false );
		$analysis = $db->get_article_analysis( $article_id );

		$scores = self::get_article_scores( $article_id, $analysis );
		$dates = self::get_article_dates( $article_id, $analysis );
		$status = self::get_analysis_status( $article_id, $analysis );
		$error = self::get_analysis_error( $article_id, $analysis );

		return array(
			'status' => $status ?: 'not_analyzed',
			'scores' => $scores,
			'dates' => $dates,
			'error' => $error,
			'is_analyzed' => ! empty( $dates['analyzed'] ) && $status === 'analyzed',
			'is_improved' => ! empty( $dates['improved'] ),
			'is_ignored' => ! empty( $dates['ignored'] ),
			'is_done' => ! empty( $dates['done'] ),
			'is_demo' => false
		);
	}

	/**
	 * Set article as ignored
	 *
	 * @param int $article_id Article ID
	 * @return bool Success
	 */
	public static function set_article_ignored( $article_id, $ignored = true ) {
		if ( $ignored ) {
			return self::update_article_date( $article_id, 'ignored' );
		} else {
			return self::clear_article_date( $article_id, 'ignored' );
		}
	}

	/**
	 * Set article as done
	 *
	 * @param int $article_id Article ID
	 * @return bool Success
	 */
	public static function set_article_done( $article_id, $done = true ) {
		if ( $done ) {
			return self::update_article_date( $article_id, 'done' );
		} else {
			return self::clear_article_date( $article_id, 'done' );
		}
	}

	/**
	 * Get article display status for the Content Analysis table
	 *
	 * @param int $article_id Article ID
	 * @return string Status: 'To Analyze', 'Ignored', 'Done', 'To Improve'
	 */
	public static function get_article_display_status( $article_id ) {

		$data = self::get_article_analysis_data( $article_id );
		$post = get_post( $article_id );

		// Check if ignored
		if ( $data['is_ignored'] ) {
			return 'Ignored';
		}

		// Check if done
		if ( $data['is_done'] ) {
			return 'Done';
		}

		// Check if needs analysis (never analyzed or analyzed date is older than last update)
		if ( ! $data['is_analyzed'] || empty( $data['scores'] ) ) {
			return 'To Analyze';
		}

		// Check if analyzed date is older than article update date
		if ( ! empty( $data['dates']['analyzed'] ) && $post ) {
			$analyzed_time = strtotime( $data['dates']['analyzed'] );
			$updated_time = strtotime( $post->post_modified );

			if ( $updated_time > $analyzed_time ) {
				return 'To Analyze';
			}
		}

		// Default to 'To Improve' if none of the above conditions are met
		return 'To Improve';
	}

	/**
	 * Format score components for display
	 *
	 * @param array $components Score components array
	 * @return array Formatted components
	 */
	public static function format_score_components( $components ) {

		$formatted = array();

		// Tags Usage
		if ( isset( $components['tags_usage'] ) ) {
			$formatted[] = array(
				'name' => __( 'Tags Usage', 'echo-knowledge-base' ),
				'value' => $components['tags_usage']
			);
		} else {
			$formatted[] = array(
				'name' => __( 'Tags Usage', 'echo-knowledge-base' ),
				'value' => '-'
			);
		}

		// Gap Analysis
		if ( isset( $components['gap_analysis'] ) ) {
			$formatted[] = array(
				'name' => __( 'Gap Analysis', 'echo-knowledge-base' ),
				'value' => $components['gap_analysis']
			);
		} else {
			$formatted[] = array(
				'name' => __( 'Gap Analysis', 'echo-knowledge-base' ),
				'value' => '-'
			);
		}

		// Readability
		if ( isset( $components['readability'] ) ) {
			$formatted[] = array(
				'name' => __( 'Readability', 'echo-knowledge-base' ),
				'value' => $components['readability']
			);
		} else {
			$formatted[] = array(
				'name' => __( 'Readability', 'echo-knowledge-base' ),
				'value' => '-'
			);
		}

		return $formatted;
	}

	/**
	 * Calculate article importance based on view count
	 *
	 * Uses a logarithmic scale to convert article views into an importance score (0-100).
	 * The logarithmic approach means that early views have more impact than later views,
	 * which makes sense because the difference between 0 and 10 views is more significant
	 * than the difference between 990 and 1000 views.
	 *
	 * Scale examples:
	 * - 0 views = 0 importance
	 * - 10 views = 30 importance
	 * - 100 views = 60 importance
	 * - 1000 views = 90 importance
	 * - 10000+ views = 100 importance
	 *
	 * @param int $article_id Article ID
	 * @return int Importance score (0-100)
	 */
	public static function calculate_article_importance( $article_id ) {

		// Get article view count from post meta
		$views = (int) EPKB_Utilities::get_postmeta( $article_id, 'epkb-article-views', 0 );
		if ( $views <= 0 ) {
			return 0;
		}

		// Use logarithmic scale: importance = 30 * log10(views + 1)
		// This gives a nice curve where early views matter more
		$importance = round( 30 * log10( $views + 1 ) );

		// Cap at 100 and ensure non-negative
		$importance = max( 0, min( 100, $importance ) );

		return $importance;
	}

	// ===== AI ANALYSIS HELPER METHODS =====

	/**
	 * Remove markdown code fences from text
	 *
	 * @param string $text Text that may contain markdown code blocks
	 * @return string Cleaned text
	 */
	public static function remove_markdown_code_fences( $text ) {
		$text = trim( $text );

		// Remove markdown code blocks if present (e.g., ```json ... ``` or ``` ... ```)
		if ( preg_match( '/^```(?:json)?\s*\n?(.*?)\n?```$/s', $text, $matches ) ) {
			return trim( $matches[1] );
		}

		return $text;
	}

	/**
	 * Extract content from OpenAI Responses API response structure
	 *
	 * @param array $response OpenAI API response
	 * @return string|WP_Error Response content text or error
	 */
	public static function extract_openai_response_content( $response ) {

		// Validate response structure
		if ( empty( $response['output'] ) || ! is_array( $response['output'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response from AI service', 'echo-knowledge-base' ) );
		}

		// Get the last output item
		$last_output = end( $response['output'] );
		if ( empty( $last_output['content'] ) || ! is_array( $last_output['content'] ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response content from AI service', 'echo-knowledge-base' ) );
		}

		// Get the first content item
		$response_content = $last_output['content'][0];

		// If content is an object with a 'text' property, extract it
		if ( is_array( $response_content ) && isset( $response_content['text'] ) ) {
			$response_content = $response_content['text'];
		}

		return trim( $response_content );
	}

	/**
	 * Parse JSON response from AI with error handling
	 *
	 * @param string $json_text JSON text to parse
	 * @param string $context Context for logging (e.g., 'readability', 'gap_analysis')
	 * @return array|WP_Error Parsed data or error
	 */
	public static function parse_json_response( $json_text, $context = 'ai_analysis' ) {

		// Parse JSON response
		$parsed_response = json_decode( $json_text, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			// Log the raw response for debugging
			EPKB_Logging::add_log( 'Failed to parse AI response', $json_text, array( 'context' => $context ) );
			return new WP_Error( 'json_parse_error', __( 'Failed to parse AI response as JSON', 'echo-knowledge-base' ), $json_text );
		}

		return $parsed_response;
	}

	/**
	 * Process article content for AI analysis
	 * Uses EPKB_AI_Content_Processor for consistent content cleaning
	 *
	 * @param string $content Raw article content
	 * @param int $post_id Article ID
	 * @param int $max_length Maximum length for AI analysis (default: 10000)
	 * @return string|WP_Error Processed content or error
	 */
	public static function process_article_content_for_ai( $content, $post_id = 0, $max_length = 10000 ) {

		// Use the same content processing that we use for Training Data sync
		$content_processor = new EPKB_AI_Content_Processor();
		$processed_content = $content_processor->clean_content( $content, $post_id );
		if ( is_wp_error( $processed_content ) ) {
			return $processed_content;
		}

		// Apply length limit for AI analysis
		return mb_substr( $processed_content, 0, $max_length, 'UTF-8' );
	}
}