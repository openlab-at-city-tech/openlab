<?php defined( 'ABSPATH' ) || exit();

/**
 * Readability Score Calculator
 *
 * Analyzes article readability using AI
 */
class EPKB_AI_Readability {

	const DATA_VERSION = '1.0';

	/**
	 * Analyze article readability
	 *
	 * @param WP_Post $post
	 * @param bool $force_analysis Whether to force a new analysis (default: false, will return stored or not_analyzed status)
	 * @return array|WP_Error Analysis results with score and details or error
	 */
	public static function analyze( $post, $force_analysis = false ) {

		// Validate article ID
		if ( empty( $post->ID ) || ! is_numeric( $post->ID ) ) {
			return new WP_Error( 'invalid_article_id', __( 'Invalid article ID provided', 'echo-knowledge-base' ) );
		}

		// Check if this is a demo article - return demo data if it is
		$article_id = $post->ID;
		if ( EPKB_KB_Demo_Data::is_demo_article( $article_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_readability_data();
		}

		// Check if analysis exists in database
		$db = new EPKB_AI_Content_Analysis_DB( false );
		$existing_analysis = $force_analysis ? null : $db->get_article_analysis( $article_id );
		if ( ! $force_analysis && $existing_analysis && ! empty( $existing_analysis->readability_data ) ) {
			// Return stored data
			$stored_data = json_decode( $existing_analysis->readability_data, true );
			if ( is_array( $stored_data ) ) {
				return $stored_data;
			}
			$existing_analysis = null;
		}

		// If not forcing analysis and no data exists, return not_analyzed status
		if ( ! $force_analysis && ! $existing_analysis ) {
			return array(
				'version' => self::DATA_VERSION,
				'score' => 0,
				'status' => 'not_analyzed',
				'message' => __( 'Readability analysis has not been run yet.', 'echo-knowledge-base' ),
				'analyzed_at' => null
			);
		}

		// Get article content
		$content = $post->post_content;
		$title = $post->post_title;

		// Get AI readability analysis
		$ai_readability = self::get_ai_readability_analysis( $title, $content, $article_id );
		if ( is_wp_error( $ai_readability ) ) {
			// Store the error but don't fail the entire analysis
			EPKB_Logging::add_log( 'AI readability analysis error', $ai_readability, array( 'context' => 'readability_analysis', 'article_id' => $article_id ) );

			$error_result = array(
				'version' => self::DATA_VERSION,
				'score' => 0,
				'status' => 'error',
				'error' => $ai_readability->get_error_message(),
				'ai_feature_unavailable' => false,
				'analyzed_at' => current_time( 'mysql' )
			);

			// Save error to database
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$db->update_readability( $article_id, 0, $error_result );

			return $ai_readability;
		}

		// Check if AI returned empty result
		if ( empty( $ai_readability ) ) {
			$empty_result = array(
				'version' => self::DATA_VERSION,
				'score' => 0,
				'status' => 'error',
				'error' => __( 'AI analysis returned no data. Please try again.', 'echo-knowledge-base' ),
				'ai_feature_unavailable' => false,
				'analyzed_at' => current_time( 'mysql' )
			);

			// Save error to database
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$db->update_readability( $article_id, 0, $empty_result );

			return $empty_result;
		}

		// Parse and validate AI response
		$result = array(
			'version' => self::DATA_VERSION,
			'score' => isset( $ai_readability['score'] ) ? (int) $ai_readability['score'] : 0,
			'status' => 'analyzed',
			'issues' => isset( $ai_readability['issues'] ) ? $ai_readability['issues'] : array(),
			'ai_feature_unavailable' => false,
			'analyzed_at' => current_time( 'mysql' )
		);

		// Save to database
		$db = new EPKB_AI_Content_Analysis_DB( false );
		$db->update_readability( $article_id, $result['score'], $result );

		return $result;
	}

	/**
	 * Clear readability analysis for article
	 *
	 * @param int $article_id
	 * @return bool
	 */
	public static function clear_cache( $article_id ) {
		// Clear from database table
		$db = new EPKB_AI_Content_Analysis_DB( false );
		return $db->delete_article_analysis( $article_id );
	}

	/**
	 * Batch analyze multiple articles
	 *
	 * @param array $article_ids
	 * @param bool $force_analysis Whether to force analysis (default: true for batch operations)
	 * @return array
	 */
	public static function batch_analyze( $article_ids, $force_analysis = true ) {
		$results = array();

		foreach ( $article_ids as $article_id ) {
			$results[ $article_id ] = self::analyze( $article_id, $force_analysis );
		}

		return $results;
	}

	/**
	 * Get AI-powered readability analysis using OpenAI
	 *
	 * @param string $title Article title
	 * @param string $content Article content
	 * @param int    $post_id   Article ID for consistent content processing
	 * @return array|WP_Error Analysis with score, analysis text, and suggestions or error
	 */
	private static function get_ai_readability_analysis( $title, $content, $post_id = 0 ) {

		// Validate input parameters
		if ( empty( $title ) && empty( $content ) ) {
			return new WP_Error( 'invalid_input', __( 'Title and content cannot both be empty', 'echo-knowledge-base' ) );
		}

		// Check if OpenAI client is available
		if ( ! class_exists( 'EPKB_OpenAI_Client' ) ) {
			return new WP_Error( 'openai_unavailable', __( 'OpenAI client is not available', 'echo-knowledge-base' ) );
		}

		// Process article content for AI analysis
		$content_for_analysis = EPKB_AI_Content_Analysis_Utilities::process_article_content_for_ai( $content, $post_id );
		if ( is_wp_error( $content_for_analysis ) ) {
			return $content_for_analysis;
		}

		// Build the prompt for readability analysis
		$prompt = 'Analyze the readability of this knowledge base article and provide a detailed assessment.

You are a professional documentation editor. Analyze the provided content for readability, correctness, clarity, and professionalism. Identify specific passages that contain issues and report them only as JSON.

Scope of review (what to look for):
- Complexity & Jargon: Overly complex, run‑on, or jargon‑heavy sentences; opportunities to split long sentences/paragraphs.
- Grammar & Mechanics: Grammar, spelling, punctuation errors; awkward or unnatural phrasing; tense/person inconsistencies.
- Visual Aid Opportunities: Places where images/diagrams/screenshots would help (e.g., software settings, step sequences, system overview).
- Clarity, Accuracy, Bias: Ambiguous or unclear statements, likely inaccuracies, unsupported claims, or biased/subjective language.

Output requirements (strict):

Return one JSON object with a single top‑level key "issues" whose value is an array.

Each array item is an object with exactly these keys (no others):

"issue_type": Title of the issue type (e.g., "Complexity", "Jargon", "Grammar", "Awkward phrasing", "Visual aid opportunity", "Clarity", "Accuracy", "Bias").

"problematic_text": The full, exact sentence or paragraph from the input that exhibits the problem (do not paraphrase or truncate; preserve original line breaks as \n).

"explanation": A clear, detailed explanation that (a) names the problem, (b) explains its impact on readability/credibility, and (c) offers a concrete improvement (e.g., a suggested rewrite, where to split, or what visual to add).

No additional commentary, markdown, or code fences. Output must be valid JSON, UTF‑8, double‑quoted strings, and no trailing commas.

If no issues are found, return: {"issues":[]}.

Order items by first appearance of the problematic text in the document.

If one passage has multiple distinct problems, create separate entries (reusing the same "problematic_text" with different "issue_type" values).

Allowed values for "issue_type" (choose the best fit):
"Complexity", "Jargon", "Run-on sentence", "Grammar", "Spelling", "Punctuation", "Awkward phrasing", "Paragraph structure", "List needed", "Visual aid opportunity", "Clarity", "Accuracy", "Bias", "Tone inconsistency", "Ambiguous reference", "Missing context".

Content to analyze (verbatim): ' . "
Article Title: {$title}
Content:
{$content_for_analysis}";

		// Get the fastest model preset for content analysis
		$fastest_preset = EPKB_OpenAI_Client::get_preset_parameters( 'fastest' );
		$model = $fastest_preset['model'];
		$model_params = array(
			'verbosity' => $fastest_preset['verbosity'],
			'reasoning' => $fastest_preset['reasoning'],
			'max_output_tokens' => $fastest_preset['max_output_tokens']
		);

		// Prepare the request for Responses API
		$request = array(
			'model' => $model,
			'instructions' => 'You are a knowledge base content expert specializing in readability analysis and content optimization.',
			'input' => array(
				array(
					'role' => 'user',
					'content' => $prompt
				)
			)
		);

		// Apply model parameters
		$request = EPKB_OpenAI_Client::apply_model_parameters( $request, $model, $model_params );

		// Make the API request (pass disable_retry flag and content_analysis purpose for timeout determination)
		$client = new EPKB_OpenAI_Client();
		$response = $client->request( '/responses', $request, 'POST', 'content_analysis_readability' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Extract content from OpenAI response
		$response_text = EPKB_AI_Content_Analysis_Utilities::extract_openai_response_content( $response );
		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		// Parse JSON response
		$parsed_response = EPKB_AI_Content_Analysis_Utilities::parse_json_response( $response_text, 'readability_analysis' );
		if ( is_wp_error( $parsed_response ) ) {
			return $parsed_response;
		}

		// Validate required fields
		if ( ! isset( $parsed_response['issues'] ) || ! is_array( $parsed_response['issues'] ) ) {
			return new WP_Error( 'invalid_response_format', __( 'AI response missing required fields', 'echo-knowledge-base' ) );
		}

		// Remove markdown code fences from individual text fields within issues
		foreach ( $parsed_response['issues'] as &$issue ) {
			if ( isset( $issue['problematic_text'] ) ) {
				$issue['problematic_text'] = EPKB_AI_Content_Analysis_Utilities::remove_markdown_code_fences( $issue['problematic_text'] );
			}
			if ( isset( $issue['explanation'] ) ) {
				$issue['explanation'] = EPKB_AI_Content_Analysis_Utilities::remove_markdown_code_fences( $issue['explanation'] );
			}
			if ( isset( $issue['issue_type'] ) ) {
				$issue['issue_type'] = EPKB_AI_Content_Analysis_Utilities::remove_markdown_code_fences( $issue['issue_type'] );
			}
		}
		unset( $issue ); // Break reference

		// Calculate score based on number of issues (fewer issues = higher score)
		// 0 issues = 100%, scale down from there
		$issue_count = count( $parsed_response['issues'] );
		$score = max( 0, min( 100, 100 - ( $issue_count * 5 ) ) ); // Each issue reduces score by 5%

		return array(
			'score' => $score,
			'issues' => $parsed_response['issues']
		);
	}
}