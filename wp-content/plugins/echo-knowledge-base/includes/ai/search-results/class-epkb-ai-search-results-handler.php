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

		// Extract parameters from data array - collection_id should always be provided by client (0 means not configured)
		$query = isset( $data['query'] ) ? $data['query'] : '';
		$kb_id = isset( $data['kb_id'] ) ? $data['kb_id'] : 0;
		$collection_id = isset( $data['collection_id'] ) ? $data['collection_id'] : 0;

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
			if ( $result->get_error_code() !== 'empty_response' ) {
				EPKB_AI_Log::add_log( 'AI Answer section error', array(
					'error_code' => $result->get_error_code(),
					'details'    => $result->get_error_message(),
				) );
			}
			return self::get_ai_error_response( $section_name, $result );
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

		// Store the raw answer as text so frontend formatting can be applied safely after insertion.
		$html = '<div class="epkb-ai-sr-ai-answer-text">' . esc_html( $ai_answer ) . '</div>';

		return array(
			'has_content' => true,
			'html' => self::get_section_wrapper( $html, 'ai-answer', $section_name, true, $query ),
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

		// Get number of articles to display from AI config setting (default 5)
		$results_page_size = (int) EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_articles_count' );

		// Perform WordPress search for matching articles
		$articles = EPKB_KB_Search::execute_search( $kb_id, $query, $results_page_size );
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
				'excerpt' => EPKB_Utilities::is_link_editor( $post ) ? '' : ( ! empty( $post->post_excerpt ) ? $post->post_excerpt : wp_trim_words( $post->post_content, 25 ) )
			);
		}

		// Build HTML
		$html = '<ul class="epkb-ai-sr-articles-list">';
		foreach ( $articles_data as $article ) {
			$html .= '<li class="epkb-ai-sr-article-item">';
			$html .= '<a href="' . esc_url( $article['url'] ) . '" class="epkb-ai-sr-article-link" data-kb-article-id="' . esc_attr( $article['id'] ) . '">';
			$html .= '<h4 class="epkb-ai-sr-article-title">' . esc_html( $article['title'] ) . '</h4>';
			if ( ! empty( $article['excerpt'] ) ) {
				$html .= '<p class="epkb-ai-sr-article-excerpt">' . esc_html( $article['excerpt'] ) . '</p>';
			}
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
	 * @param bool $show_copy_button Whether to show copy to clipboard button
	 * @param string $continue_chat_query When non-empty and AI Chat is enabled, render an "Open in AI Chat" button carrying this query
	 * @return string Complete section HTML
	 */
	public static function get_section_wrapper( $inner_html, $section_id, $section_name = '', $show_copy_button = false, $continue_chat_query = '' ) {
		$section_class = 'epkb-ai-sr-section epkb-ai-sr-section--' . esc_attr( $section_id );

		$show_continue_chat_button = ! empty( $continue_chat_query )
			&& EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_continue_in_chat', 'on' ) === 'on'
			&& EPKB_AI_Chat_Frontend::can_display_chat_widget();

		$output = '<div class="' . $section_class . '" data-section-id="' . esc_attr( $section_id ) . '">';

		// Section header with title and optional action buttons
		if ( ! empty( $section_name ) || $show_copy_button || $show_continue_chat_button ) {
			$output .= '<div class="epkb-ai-sr-section__header">';
			if ( ! empty( $section_name ) ) {
				$output .= '<h3 class="epkb-ai-sr-section__title">' . esc_html( $section_name ) . '</h3>';
			}
			if ( $show_continue_chat_button ) {
				$output .= self::get_continue_chat_button_html( $continue_chat_query );
			}
			if ( $show_copy_button ) {
				$output .= self::get_copy_button_html();
			}
			$output .= '</div>';
		}

		$output .= '<div class="epkb-ai-sr-section__content">' . $inner_html . '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get copy to clipboard button HTML
	 * @return string
	 */
	public static function get_copy_button_html() {
		return '<button class="epkb-ai-sr-copy-btn" type="button" title="' . esc_attr__( 'Copy to clipboard', 'echo-knowledge-base' ) . '">' .
			'<span class="epkb-ai-sr-copy-btn__icon epkbfa epkbfa-copy"></span>' .
			'<span class="epkb-ai-sr-copy-btn__text">' . esc_html__( 'Copy', 'echo-knowledge-base' ) . '</span>' .
			'<span class="epkb-ai-sr-copy-btn__copied">' . esc_html__( 'Copied!', 'echo-knowledge-base' ) . '</span>' .
		'</button>';
	}

	/**
	 * Get "Open in AI Chat" button HTML
	 *
	 * @param string $query User's original search query, passed via data attribute to pre-fill the chat input
	 * @return string
	 */
	public static function get_continue_chat_button_html( $query ) {
		return '<button class="epkb-ai-sr-continue-chat-btn" type="button" title="' . esc_attr__( 'Open in AI Chat', 'echo-knowledge-base' ) . '" data-query="' . esc_attr( $query ) . '">' .
			'<span class="epkb-ai-sr-continue-chat-btn__icon epkbfa epkbfa-comments"></span>' .
			'<span class="epkb-ai-sr-continue-chat-btn__text">' . esc_html__( 'Open in AI Chat', 'echo-knowledge-base' ) . '</span>' .
		'</button>';
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

	/**
	 * Get section response for an AI error.
	 *
	 * Admins should see the full AI error in Smart Search results, matching AI Chat behavior.
	 * Regular users keep the existing empty-state flow.
	 *
	 * @param string   $section_name Optional section name/title.
	 * @param WP_Error $error        AI request error.
	 * @return array
	 */
	private static function get_ai_error_response( $section_name, $error ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return self::get_empty_response( $section_name );
		}

		$processed_error = EPKB_AI_Log::rest_process_wp_error( $error, EPKB_AI_Log::get_error_status_code( $error->get_error_code() ) );
		$error_data = empty( $processed_error['data'] ) || ! is_array( $processed_error['data'] ) ? array() : $processed_error['data'];
		$error_message = empty( $error_data['admin_message'] ) ? $error->get_error_message() : $error_data['admin_message'];

		return array(
			'has_content'      => false,
			'html'             => '',
			'title'            => $section_name,
			'error'            => $error_message,
			'error_type'       => empty( $error_data['error_type'] ) ? 'unknown' : $error_data['error_type'],
			'display_as_error' => true,
		);
	}
}
