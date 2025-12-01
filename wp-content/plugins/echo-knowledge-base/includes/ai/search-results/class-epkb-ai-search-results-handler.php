<?php

/**
 * Hook Handler for AI Search Results Core Sections
 * Handles core sections (ai-answer, matching-articles) in echo-knowledge-base
 *
 * @copyright   Copyright (C) 2024, Echo Plugins
 */
class EPKB_AI_Search_Results_Handler {

	/**
	 * Get section content via filter hook
	 *
	 * @param mixed $section_data Current section data (null initially)
	 * @param string $section_id Section identifier
	 * @param array $data Data array containing query, kb_id, and optional collection_id
	 * @return array|null Section data or null if section not found
	 */
	public static function get_section_content( $section_data, $section_id, $data ) {

		// If another plugin already provided data, return it
		if ( ! empty( $section_data ) ) {
			return $section_data;
		}

		// Normalize section ID: convert underscores to hyphens for consistency
		$section_id = str_replace( '_', '-', $section_id );

		// Extract parameters from data array - collection_id should always be provided by client
		$query = isset( $data['query'] ) ? $data['query'] : '';
		$kb_id = isset( $data['kb_id'] ) ? $data['kb_id'] : 0;
		$collection_id = isset( $data['collection_id'] ) ? $data['collection_id'] : EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID;

		// Handle core sections
		switch ( $section_id ) {
			case 'ai-answer':
				return self::get_ai_answer_section( $query, $kb_id, $collection_id );
			case 'matching-articles':
				return self::get_matching_articles_section( $query, $kb_id );
			default:
				return null;
		}
	}

	/**
	 * Get AI Answer section data
	 *
	 * @param string $query User's search query
	 * @param int $kb_id Knowledge Base ID
	 * @param int $collection_id AI Training Data Collection ID (should always come from client)
	 * @return array
	 */
	private static function get_ai_answer_section( $query, $kb_id, $collection_id ) {

		// Get custom section name from config
		$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_ai_answer_name' );

		// Call AI Search endpoint using the search handler
		// Note: The epkb_ai_messages record is created in EPKB_AI_Search_Handler::search()
		// collection_id should always come from client
		$search_handler = new EPKB_AI_Search_Handler();
		$result = $search_handler->search( $query, $collection_id );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'AI Answer section error', $result->get_error_message() );
			return self::get_empty_response( $section_name );
		}

		// Check if we have a valid response
		if ( empty( $result['response'] ) ) {
			return self::get_empty_response( $section_name );
		}

		$ai_answer = $result['response'];

		// Treat polite refusal responses as missing content
		if ( EPKB_AI_Utilities::is_search_refusal_answer( $ai_answer ) ) {
			return self::get_empty_response( $section_name );
		}

		// Get chat_id from the result (used by record-feedback and submit-contact-support)
		$chat_id = isset( $result['chat_id'] ) ? $result['chat_id'] : '';

		// Build HTML with markdown-style formatting
		$html = '<div class="epkb-ai-sr-ai-answer-text">' . $ai_answer . '</div>';

		return array(
			'has_content' => true,
			'html' => self::get_section_wrapper( $html, 'ai-answer', $section_name ),
			'data' => array(
				'query' => $query,
				'chat_id' => $chat_id
			)
		);
	}

	/**
	 * Get Matching Articles section data
	 *
	 * @param string $query User's search query
	 * @param int $kb_id Knowledge Base ID
	 * @return array
	 */
	private static function get_matching_articles_section( $query, $kb_id ) {

		// Get custom section name from config
		$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_matching_articles_name' );

		// Perform WordPress search for matching articles
		$articles = EPKB_KB_Search::execute_search( $kb_id, $query, 10 );
		if ( is_wp_error( $articles ) ) {
			return self::get_empty_response( $section_name );
		}

		// If no articles found, return empty response
		if ( empty( $articles ) ) {
			return self::get_empty_response( $section_name );
		}

		// Build articles data array
		$articles_data = array();
		foreach ( $articles as $post ) {
			$article_url = get_permalink( $post->ID );
			if ( empty( $article_url ) || is_wp_error( $article_url ) ) {
				continue;
			}

			$articles_data[] = array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'url' => $article_url,
				'excerpt' => ! empty( $post->post_excerpt ) ? $post->post_excerpt : wp_trim_words( $post->post_content, 25 )
			);
		}

		// Build HTML
		$html = '<ul class="epkb-ai-sr-articles-list">';
		foreach ( $articles_data as $article ) {
			$html .= '<li class="epkb-ai-sr-article-item">';
			$html .= '<a href="' . esc_url( $article['url'] ) . '" class="epkb-ai-sr-article-link" data-kb-article-id="' . esc_attr( $article['id'] ) . '">';
			$html .= '<h4 class="epkb-ai-sr-article-title">' . esc_html( $article['title'] ) . '</h4>';
			$html .= '<p class="epkb-ai-sr-article-excerpt">' . esc_html( $article['excerpt'] ) . '</p>';
			$html .= '</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';

		return array(
			'has_content' => true,
			'html' => self::get_section_wrapper( $html, 'matching-articles', $section_name ),
			'data' => array(
				'articles' => $articles_data,
				'count' => count( $articles_data )
			)
		);
	}

	/**
	 * Get section wrapper HTML
	 *
	 * @param string $inner_html Section content HTML
	 * @param string $section_id Section ID
	 * @param string $section_name Custom section name from config
	 * @return string Complete section HTML
	 */
	public static function get_section_wrapper( $inner_html, $section_id, $section_name = '' ) {
		$section_class = 'epkb-ai-sr-section epkb-ai-sr-section--' . esc_attr( $section_id );

		$output = '<div class="' . $section_class . '" data-section-id="' . esc_attr( $section_id ) . '">';

		if ( ! empty( $section_name ) ) {
			$output .= '<h3 class="epkb-ai-sr-section__title">' . esc_html( $section_name ) . '</h3>';
		}

		$output .= '<div class="epkb-ai-sr-section__content">' . $inner_html . '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get empty response (no content available)
	 *
	 * @param string $section_name Optional section name/title
	 * @return array Empty response
	 */
	private static function get_empty_response( $section_name = '' ) {
		return array(
			'has_content' => false,
			'html' => '',
			'title' => $section_name
		);
	}
}
