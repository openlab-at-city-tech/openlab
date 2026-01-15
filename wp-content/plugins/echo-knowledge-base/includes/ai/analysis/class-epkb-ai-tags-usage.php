<?php defined( 'ABSPATH' ) || exit();

/**
 * Tags Usage Score Calculator
 *
 * Analyzes article tags and categories for optimal usage and SEO
 */
class EPKB_AI_Tags_Usage {

	const DATA_VERSION = '1.0';
	const MIN_TAGS = 3;
	const MAX_TAGS = 10;
	const MIN_TAG_LENGTH = 2;
	const MAX_TAG_LENGTH = 30;

	/**
	 * Analyze article tags and categories usage
	 *
	 * @param WP_Post $post
	 * @param bool $force Force fresh analysis, skipping cache
	 * @return array|WP_Error Analysis results with score and details or error
	 */
	public static function analyze( $post, $force = false ) {

		// Validate article ID
		if ( empty( $post->ID ) || ! is_numeric( $post->ID ) ) {
			return new WP_Error( 'invalid_article_id', __( 'Invalid article ID provided', 'echo-knowledge-base' ) );
		}

		// Check if this is a demo article - return demo data if it is
		$article_id = $post->ID;
		if ( EPKB_KB_Demo_Data::is_demo_article( $article_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_tags_usage_data();
		}

		// Check if analysis exists in database (skip if force = true)
		if ( ! $force ) {
			$db = new EPKB_AI_Content_Analysis_DB( false );
			$existing_analysis = $db->get_article_analysis( $article_id );
			if ( $existing_analysis && ! empty( $existing_analysis->tags_data ) ) {
				// Return stored data
				$stored_data = json_decode( $existing_analysis->tags_data, true );
				if ( is_array( $stored_data ) ) {
					return $stored_data;
				}
			}
		}

		// Get article content for relevance checking
		$content = $post->post_content . ' ' . $post->post_title;

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( empty( $kb_id ) ) {
			return new WP_Error( 'article_not_found', __( 'Article post type invalid', 'echo-knowledge-base' ) );
		}

		// Analyze KB tags (not WordPress tags, but KB-specific tags)
		$kb_tag_taxonomy = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );
		if ( empty( $kb_tag_taxonomy ) ) {
			return new WP_Error( 'taxonomy_error', __( 'Could not determine KB tag taxonomy', 'echo-knowledge-base' ) );
		}

		$tags = wp_get_post_terms( $article_id, $kb_tag_taxonomy );
		if ( is_wp_error( $tags ) ) {
			// Log the error but continue with empty tags
			EPKB_Logging::add_log( 'Get post terms failed for KB tag taxonomy', $kb_tag_taxonomy, $tags );
			$tags = array();
		}
		$tag_names = array_map( function($tag) { return $tag->name; }, $tags );
		$tag_count = count( $tags );

		// Analyze KB categories (custom taxonomies like 'epkb_post_type_1_category')
		$kb_category_taxonomy = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		if ( empty( $kb_category_taxonomy ) ) {
			return new WP_Error( 'taxonomy_error', __( 'Could not determine KB category taxonomy', 'echo-knowledge-base' ) );
		}

		$kb_categories = wp_get_post_terms( $article_id, $kb_category_taxonomy );
		if ( is_wp_error( $kb_categories ) ) {
			// Log the error but continue with empty categories
			EPKB_Logging::add_log( 'Get post terms failed for KB category taxonomy', $kb_category_taxonomy, $kb_categories );
			$kb_categories = array();
		}
		$kb_category_count = count( $kb_categories );

		$analyze_ai_result = self::analyze_ai_suggestions( $post, $tag_names );
		if ( is_wp_error( $analyze_ai_result ) ) {
			return $analyze_ai_result;
		}

		// Initialize analysis components
		$tag_analysis = array(
			'count_score' => self::analyze_tag_count( $tag_count ),
			'format_score' => self::analyze_tag_format( $tag_names ),
			'duplicate_score' => self::analyze_duplicates( $tag_names ),
			'category_score' => self::analyze_categories( $kb_category_count ),
			'relevance_score' => self::analyze_tag_relevance( $tag_names, $content ),
			'ai_suggestions' => $analyze_ai_result
		);

		// Calculate overall score
		$score = self::calculate_overall_score( $tag_analysis );

		// Generate recommended_tags
		$recommended_tags = self::generate_recommendations( $tag_count, $tag_names, $tag_analysis, $kb_category_count );

		// Compile results
		$results = array(
			'version' => self::DATA_VERSION,
			'score' => $score,
			'kb_category_count' => $kb_category_count,
			'tag_count' => $tag_count,
			'tags' => $tag_names,
			'current_tags' => $tags,  // Full tag objects for detailed info
			'suggested_tags' => isset( $tag_analysis['ai_suggestions']['suggestions'] ) ? $tag_analysis['ai_suggestions']['suggestions'] : array(),  // AI suggested tags
			'recommended_tags' => $recommended_tags,
			'tag_analysis' => $tag_analysis,
			'analyzed_at' => current_time( 'mysql' )
		);

		// Add AI error if it occurred (non-fatal)
		if ( ! empty( $tag_analysis['ai_suggestions']['ai_error'] ) ) {
			$results['ai_error'] = $tag_analysis['ai_suggestions']['ai_error'];
		}

		// Save to database
		$db = new EPKB_AI_Content_Analysis_DB( false );
		$db->update_tags_usage( $article_id, $score, $results );

		return $results;
	}

	/**
	 * Clear tags usage analysis for article
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
	 * Analyze tag count
	 *
	 * @param int $count
	 * @return array
	 */
	private static function analyze_tag_count( $count ) {
		$score = 100;
		$issues = array();

		if ( $count < self::MIN_TAGS ) {
			$score = max( 0, 50 - ( self::MIN_TAGS - $count ) * 15 );
			$issues[] = sprintf( __( 'Too few tags (%d). Recommended: %d-%d tags', 'echo-knowledge-base' ), $count, self::MIN_TAGS, self::MAX_TAGS
			);
		} elseif ( $count > self::MAX_TAGS ) {
			$score = max( 0, 100 - ( $count - self::MAX_TAGS ) * 10 );
			$issues[] = sprintf( __( 'Too many tags (%d). Recommended: %d-%d tags', 'echo-knowledge-base' ), $count, self::MIN_TAGS, self::MAX_TAGS
			);
		}

		return array(
			'score' => $score,
			'issues' => $issues
		);
	}

	/**
	 * Analyze tag format and consistency
	 *
	 * @param array $tags
	 * @return array
	 */
	private static function analyze_tag_format( $tags ) {
		if ( empty( $tags ) || ! is_array( $tags ) ) {
			return array( 'score' => 0, 'issues' => array() );
		}

		$issues = array();
		$score = 100;
		$deductions = 0;

		foreach ( $tags as $tag ) {
			// Check length
			$length = strlen( $tag );
			if ( $length < self::MIN_TAG_LENGTH ) {
				$issues[] = sprintf( __( 'Tag too short: "%s" (%d chars)', 'echo-knowledge-base' ), $tag, $length );
				$deductions += 10;
			} elseif ( $length > self::MAX_TAG_LENGTH ) {
				$issues[] = sprintf( __( 'Tag too long: "%s" (%d chars)', 'echo-knowledge-base' ), $tag, $length );
				$deductions += 10;
			}

			// Check word count - tags should be concise (max 4 words)
			$word_count = $tag === '' ? 0 : count( preg_split( '/\s+/u', trim( $tag ) ) );
			if ( $word_count > 4 ) {
				$issues[] = sprintf( __( 'Tag has too many words: "%s" (%d words, max 4)', 'echo-knowledge-base' ), $tag, $word_count );
				$deductions += 15;
			}

			// Check for special characters (allow Unicode letters, numbers, spaces, hyphens)
			if ( ! preg_match( '/^[\p{L}\p{N}\s\-]+$/u', $tag ) ) {
				$issues[] = sprintf( __( 'Special characters in tag: %s', 'echo-knowledge-base' ), $tag );
				$deductions += 15;
			}

			// Check for all caps (allow short acronyms 2-4 chars)
			if ( preg_match( '/^\p{Lu}{5,}$/u', $tag ) ) {
				$issues[] = sprintf( __( 'All caps tag: "%s"', 'echo-knowledge-base' ), $tag );
				$deductions += 5;
			}
		}

		$score = max( 0, $score - $deductions );

		return array(
			'score' => $score,
			'issues' => $issues
		);
	}

	/**
	 * Analyze duplicate or similar tags
	 *
	 * @param array $tags
	 * @return array
	 */
	private static function analyze_duplicates( $tags ) {
		if ( empty( $tags ) || ! is_array( $tags ) ) {
			return array( 'score' => 100, 'issues' => array() );
		}

		$issues = array();
		$score = 100;
		$duplicates = array();

		// Check for case-insensitive duplicates and similar tags
		$processed = array();
		foreach ( $tags as $tag ) {
			$tag_lower = strtolower( $tag );
			$tag_clean = str_replace( array( '-', '_', ' ' ), '', $tag_lower );

			// Check exact duplicates (case-insensitive)
			if ( in_array( $tag_lower, $processed ) ) {
				$duplicates[] = $tag;
				continue;
			}

			// Check similar tags (removing separators)
			foreach ( $processed as $existing ) {
				$existing_clean = str_replace( array( '-', '_', ' ' ), '', $existing );
				if ( $tag_clean === $existing_clean ) {
					$duplicates[] = $tag . ' ~ ' . $existing;
				}

				// Check if one tag contains another
				if ( strlen( $tag_lower ) > 3 && strlen( $existing ) > 3 ) {
						if ( strpos( $tag_lower, $existing ) !== false || strpos( $existing, $tag_lower ) !== false ) {
							$duplicates[] = $tag . ' ~ ' . $existing;
						}
				}
			}

			$processed[] = $tag_lower;
		}

		if ( ! empty( $duplicates ) ) {
			$score = max( 0, 100 - count( $duplicates ) * 20 );
			$issues[] = sprintf( __( 'Similar or duplicate tags found: %s', 'echo-knowledge-base' ), implode( ', ', array_unique( $duplicates ) ) );
		}

		return array(
			'score' => $score,
			'issues' => $issues,
			'duplicates' => $duplicates
		);
	}

	/**
	 * Analyze category usage
	 *
	 * KB Categories are custom taxonomies created by Echo Knowledge Base plugin specifically
	 * for organizing KB articles. Each KB has its own taxonomy like 'epkb_post_type_1_category'.
	 *
	 * @param int $kb_category_count Echo KB custom taxonomy count
	 * @return array
	 */
	private static function analyze_categories( $kb_category_count ) {
		$issues = array();
		$score = 100;

		// Check KB categories
		if ( $kb_category_count == 0 ) {
			$score -= 50;
			$issues[] = __( 'No KB categories assigned', 'echo-knowledge-base' );
		} elseif ( $kb_category_count > 3 ) {
			$score -= 20;
			$issues[] = sprintf( __( 'Too many KB categories (%d). Consider 1-3 categories', 'echo-knowledge-base' ), $kb_category_count );
		}

		return array(
			'score' => max( 0, $score ),
			'issues' => $issues
		);
	}

	/**
	 * Analyze tag relevance to content
	 * Checks for whole word matches and counts occurrences
	 *
	 * @param array $tags
	 * @param string $content Full article content (HTML stripped not required)
	 * @return array
	 */
	private static function analyze_tag_relevance( $tags, $content ) {
		if ( empty( $tags ) || ! is_array( $tags ) ) {
			return array( 'score' => 0, 'issues' => array() );
		}

		if ( empty( $content ) ) {
			return array( 'score' => 0, 'issues' => array( __( 'No content to analyze', 'echo-knowledge-base' ) ) );
		}

		$strong_matches = 0;
		$weak_matches = 0;
		$irrelevant_tags = array();
		$weak_tags = array();
		$issues = array();

		foreach ( $tags as $tag ) {
			$tag_variations = array(
				$tag,
				str_replace( '-', ' ', $tag ),
				str_replace( '_', ' ', $tag )
			);

			$occurrence_count = 0;
			$found_as_word = false;

			// Check each variation
			foreach ( $tag_variations as $variation ) {
				// Count occurrences using word boundaries for whole word matching (Unicode-aware)
				$pattern = '/\b' . preg_quote( $variation, '/' ) . '\b/iu';
				if ( preg_match_all( $pattern, $content, $matches ) ) {
					$count = count( $matches[0] );
					$occurrence_count += $count;
					$found_as_word = true;
				}
			}

			// If not found as whole word, check for substring matches
			if ( ! $found_as_word ) {
				foreach ( $tag_variations as $variation ) {
					$substr_pattern = '/' . preg_quote( $variation, '/' ) . '/iu';
					if ( preg_match( $substr_pattern, $content ) ) {
						$occurrence_count = 1; // Count as weak match
						break;
					}
				}
			}

			// Categorize matches
			if ( $occurrence_count == 0 ) {
				$irrelevant_tags[] = $tag;
			} elseif ( $occurrence_count < 2 || ! $found_as_word ) {
				$weak_matches++;
				$weak_tags[] = sprintf( '%s (%dx)', $tag, $occurrence_count );
			} else {
				$strong_matches++;
			}
		}

		// Calculate score with weighted relevance
		$total_tags = count( $tags );
		$score = ( ( $strong_matches * 100 ) + ( $weak_matches * 50 ) ) / $total_tags;

		// Generate issues
		if ( ! empty( $irrelevant_tags ) ) {
			$issues[] = sprintf( __( 'Tags not found in content: %s', 'echo-knowledge-base' ), implode( ', ', $irrelevant_tags ) );
		}

		if ( ! empty( $weak_tags ) ) {
			$issues[] = sprintf( __( 'Tags with weak relevance (low occurrences or partial matches): %s', 'echo-knowledge-base' ), implode( ', ', $weak_tags )	 );
		}

		return array(
			'score' => round( $score ),
			'issues' => $issues,
			'irrelevant_tags' => $irrelevant_tags,
			'weak_tags' => $weak_tags,
			'strong_matches' => $strong_matches,
			'weak_matches' => $weak_matches
		);
	}

	/**
	 * Analyze AI tag suggestions
	 *
	 * @param WP_Post $post
	 * @param array $tag_names
	 * @return array
	 */
	private static function analyze_ai_suggestions( $post, $tag_names ) {
		$ai_suggestions = array();
		$ai_error = null;

		// Use filter to get AI tag suggestions
		if ( has_filter( 'epkb_ai_get_tag_suggestions' ) ) {
			$ai_suggestions = apply_filters( 'epkb_ai_get_tag_suggestions', $post, $tag_names, [] );
			if ( is_wp_error( $ai_suggestions ) ) {

				if ( $ai_suggestions->get_error_code() === 'execution_time_too_low' ) {
					return $ai_suggestions;
				}

				// Store the error but don't fail the entire analysis and log it
				$ai_error = $ai_suggestions->get_error_message();
				$ai_suggestions = array();
			}
		}

		return array(
			'score' => 100,  // Neutral score - doesn't affect overall score
			'issues' => array(),
			'suggestions' => $ai_suggestions,
			'ai_feature_unavailable' => EPKB_Utilities::is_ai_features_pro_enabled(),
			'ai_error' => $ai_error
		);
	}

	/**
	 * Calculate overall score from components
	 *
	 * @param array $analysis
	 * @return int
	 */
	private static function calculate_overall_score( $analysis ) {
		if ( ! is_array( $analysis ) ) {
			return 0;
		}

		// Weight the different components
		$weights = array(
			'count_score' => 0.25,
			'relevance_score' => 0.30,
			'format_score' => 0.15,
			'duplicate_score' => 0.15,
			'category_score' => 0.15
		);

		$weighted_score = 0;
		foreach ( $weights as $component => $weight ) {
			if ( isset( $analysis[$component]['score'] ) && is_numeric( $analysis[$component]['score'] ) ) {
				$weighted_score += $analysis[$component]['score'] * $weight;
			}
		}

		return round( $weighted_score );
	}

	/**
	 * Generate recommendations based on analysis
	 *
	 * @param int $tag_count
	 * @param array $tags
	 * @param array $analysis
	 * @param int $kb_category_count
	 * @return array
	 */
	private static function generate_recommendations( $tag_count, $tags, $analysis, $kb_category_count ) {
		$recommendations = array();

		// Validate inputs
		if ( ! is_array( $analysis ) ) {
			return $recommendations;
		}

		// Tag count recommendations
		if ( $tag_count < self::MIN_TAGS ) {
			$recommendations[] = array(
				'priority' => 'high',
				'type' => 'tag_count',
				'message' => sprintf(
					__( 'Add %d more tags for better discoverability', 'echo-knowledge-base' ),
					self::MIN_TAGS - $tag_count
				)
			);
		} elseif ( $tag_count > self::MAX_TAGS ) {
			$recommendations[] = array(
				'priority' => 'medium',
				'type' => 'tag_count',
				'message' => sprintf(
					__( 'Remove %d tags to focus on core topics', 'echo-knowledge-base' ),
					$tag_count - self::MAX_TAGS
				)
			);
		}

		// Tag relevance recommendations
		if ( ! empty( $analysis['relevance_score']['irrelevant_tags'] ) ) {
			$recommendations[] = array(
				'priority' => 'high',
				'type' => 'relevance',
				'message' => __( 'Remove or replace tags that don\'t appear in the content', 'echo-knowledge-base' ),
				'tags' => $analysis['relevance_score']['irrelevant_tags']
			);
		}

		// Format recommendations
		if ( isset( $analysis['format_score']['issues'] ) && is_array( $analysis['format_score']['issues'] ) ) {
			foreach ( $analysis['format_score']['issues'] as $issue ) {
				$recommendations[] = array(
					'priority' => 'low',
					'type' => 'format',
					'message' => $issue
				);
			}
		}

		// Duplicate recommendations
		if ( ! empty( $analysis['duplicate_score']['duplicates'] ) ) {
			$recommendations[] = array(
				'priority' => 'medium',
				'type' => 'duplicates',
				'message' => __( 'Consolidate similar tags to avoid redundancy', 'echo-knowledge-base' ),
				'duplicates' => $analysis['duplicate_score']['duplicates']
			);
		}

		// Category recommendations
		if ( $kb_category_count == 0 ) {
			$recommendations[] = array(
				'priority' => 'high',
				'type' => 'kb_category',
				'message' => __( 'Assign at least one KB category for proper organization', 'echo-knowledge-base' )
			);
		}

		// Handle AI suggestions - mutually exclusive conditions
		if ( ! empty( $analysis['ai_suggestions']['suggestions'] ) ) {
			// Scenario 1: PRO is active and has suggestions
			$recommendations[] = array(
				'priority' => 'medium',
				'type' => 'ai_suggestions',
				'message' => __( 'AI-powered tag suggestions based on content analysis:', 'echo-knowledge-base' ),
				'suggested_tags' => $analysis['ai_suggestions']['suggestions']
			);
		} elseif ( EPKB_Utilities::is_ai_features_pro_enabled() ) {
			// Scenario 2: PRO is active but no suggestions were generated
			$recommendations[] = array(
				'priority' => 'low',
				'type' => 'ai_no_suggestions',
				'message' => __( 'AI analysis did not generate tag suggestions for this article. This may be because the article already has well-optimized tags or the content is too short.', 'echo-knowledge-base' )
			);
		} elseif ( ! empty( $analysis['ai_suggestions']['ai_feature_unavailable'] ) ) {
			// Scenario 3: PRO is NOT active - show activation message
			$recommendations[] = array(
				'priority' => 'medium',
				'type' => 'ai_feature_required',
				'message' => sprintf(
					__( 'Get the %sAI Features add-on%s to unlock AI-powered tag suggestions based on your article content.', 'echo-knowledge-base' ),
					'<a href="https://www.echoknowledgebase.com/wordpress-plugin/ai-features/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				)
			);
		}

		return $recommendations;
	}
}
