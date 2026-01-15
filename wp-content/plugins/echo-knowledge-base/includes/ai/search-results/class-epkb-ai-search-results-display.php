<?php

/**
 * Handles display of AI Search Results in dialog or page context
 *
 * @copyright   Copyright (C) 2024, Echo Plugins
 */
class EPKB_AI_Search_Results_Display {

	// Destination constants for rendering context
	const DESTINATION_DIALOG = 'dialog';
	const DESTINATION_SHORTCODE = 'shortcode';
	const DESTINATION_BLOCK = 'block';

	private static $search_box_rendered = false;

	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'output_dialog_via_footer' ), 999 );
		add_filter( 'epkb_ai_search_results_get_section', array( 'EPKB_AI_Search_Results_Handler', 'get_section_content' ), 5, 3 );
	}

	/**
	 * Output dialog template via wp_footer hook for KB search boxes
	 */
	public static function output_dialog_via_footer() {

		// Only output if search box was rendered on this page
		if ( ! self::$search_box_rendered ) {
			return;
		}

		// Don't output on admin pages
		if ( is_admin() ) {
			return;
		}

		// Check if AI search results feature is enabled
		if ( ! EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			return;
		}

		// Get current KB ID
		global $eckb_kb_id;
		$kb_id = empty( $eckb_kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $eckb_kb_id;

		// Render and output dialog (hidden by default via CSS)
		$display = new self();

		echo $display->render_dialog( $kb_id );
	}

	/**
	 * Mark that search box with AI results has been rendered on this page
	 */
	public static function mark_search_box_rendered() {
		self::$search_box_rendered = true;
	}

	/**
	 * Get common script data (i18n strings) used by dialog, shortcode, and block
	 * @return array
	 */
	public static function get_script_data() {
		return array(
			'loading_text'           => __( 'Loading...', 'echo-knowledge-base' ),
			'loading'                => __( 'Loading...', 'echo-knowledge-base' ),
			'error'                  => __( 'Unable to load content. Please try again.', 'echo-knowledge-base' ),
			'error_admin'            => __( 'Error loading section:', 'echo-knowledge-base' ),
			'try_again'              => __( 'Try Again', 'echo-knowledge-base' ),
			'feedback_thanks'        => __( 'Thank you for your feedback!', 'echo-knowledge-base' ),
			'feedback_submitting'    => __( 'Submitting...', 'echo-knowledge-base' ),
			'clarify_prompt'         => __( 'We could not find an answer. Could you clarify or rephrase your question?', 'echo-knowledge-base' ),
			'submit'                 => __( 'Submit', 'echo-knowledge-base' ),
			'submitting'             => __( 'Submitting...', 'echo-knowledge-base' ),
			'contact_name_required'  => __( 'Please enter your name', 'echo-knowledge-base' ),
			'contact_email_required' => __( 'Please enter your email', 'echo-knowledge-base' ),
			'contact_success'        => __( 'Thank you! We will get back to you soon.', 'echo-knowledge-base' ),
			'contact_error'          => __( 'Failed to submit. Please try again.', 'echo-knowledge-base' ),
			'contact_required'       => __( 'Please fill in all fields', 'echo-knowledge-base' ),
			'contact_invalid_email'  => __( 'Please enter a valid email address', 'echo-knowledge-base' ),
		);
	}

	/**
	 * Output inline script data for shortcode and block contexts
	 * Sets up window.epkbAISearchResults and window.epkbAISearchResultsShortcode
	 */
	public static function output_inline_script_data() {
		$script_data = self::get_script_data();		?>
		<script type="text/javascript">
			window.epkbAISearchResults = window.epkbAISearchResults || {};
			window.epkbAISearchResults.rest_url = <?php echo wp_json_encode( esc_url_raw( rest_url() ) ); ?>;
			window.epkbAISearchResults.rest_nonce = <?php echo wp_json_encode( epkb_get_instance()->security_obj->get_nonce() ); ?>;
			window.epkbAISearchResults.i18n = <?php echo wp_json_encode( $script_data ); ?>;

			window.epkbAISearchResultsShortcode = window.epkbAISearchResultsShortcode || {};
			window.epkbAISearchResultsShortcode.rest_url = window.epkbAISearchResults.rest_url;
			window.epkbAISearchResultsShortcode.rest_nonce = window.epkbAISearchResults.rest_nonce;
			window.epkbAISearchResultsShortcode.i18n = window.epkbAISearchResults.i18n;
		</script>		<?php
	}

	/**
	 * Render search results dialog
	 * @param int $kb_id
	 * @return string HTML
	 */
	public function render_dialog( $kb_id ) {

		if ( ! EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			return '';
		}

		// Get KB config for font family
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		$width = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_width', '60%' );
		$font_family = ! empty( $kb_config['general_typography']['font-family'] ) ? $kb_config['general_typography']['font-family'] : '';

		$inline_styles = 'max-width: ' . esc_attr( $width ) . ';';
		if ( ! empty( $font_family ) ) {
			$inline_styles .= ' font-family: ' . esc_attr( $font_family ) . ';';
		}

		$output = '<div id="epkb-ai-sr-dialog" class="epkb-ai-sr-dialog" style="' . $inline_styles . '" data-kb-id="' . esc_attr( $kb_id ) . '">';
		$output .=      '<button class="epkb-ai-sr-dialog__close" aria-label="' . esc_attr__( 'Close', 'echo-knowledge-base' ) . '">';
		$output .=          '<span class="epkbfa epkbfa-times"></span>';
		$output .=      '</button>';
		$output .=      '<div id="epkb-ai-sr-dialog__content">';
		$output .=          self::render_columns( self::DESTINATION_DIALOG );
		$output .=      '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Render contact-us section directly (static HTML, no AJAX needed)
	 * @param string $destination Rendering context: 'dialog', 'shortcode', or 'block'
	 * @return string HTML
	 */
	public static function render_contact_us_section_static( $destination = self::DESTINATION_DIALOG ) {
		$button_text = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_contact_support_button_text', 'Contact Support' );
		$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_contact_us_name', 'Contact Us' );

		$output = '<div class="epkb-ai-sr-section-wrapper epkb-ai-sr-static-section" data-section-id="contact-us" data-destination="' . esc_attr( $destination ) . '">';
		$output .= '<div class="epkb-ai-sr-section epkb-ai-sr-section--contact-us">';

		if ( ! empty( $section_name ) ) {
			$output .= '<h3 class="epkb-ai-sr-section__title">' . esc_html( $section_name ) . '</h3>';
		}

		$output .= '<div class="epkb-ai-sr-contact-box">';
		$output .= '<p class="epkb-ai-sr-contact-message">' . esc_html__( 'Couldn\'t find what you were looking for?', 'echo-knowledge-base' ) . '</p>';
		$output .= '<p class="epkb-ai-sr-contact-description">' . esc_html__( 'Our support team is here to help. Reach out and we\'ll respond as soon as possible.', 'echo-knowledge-base' ) . '</p>';

		// Hidden form fields
		$output .= '<div class="epkb-ai-sr-contact-form" style="display: none;">';
		$output .= '<div class="epkb-ai-sr-contact-field">';
		$output .= '<label for="epkb-ai-sr-contact-name">' . esc_html__( 'Name', 'echo-knowledge-base' ) . '</label>';
		$output .= '<input type="text" id="epkb-ai-sr-contact-name" class="epkb-ai-sr-contact-input" required />';
		$output .= '</div>';
		$output .= '<div class="epkb-ai-sr-contact-field">';
		$output .= '<label for="epkb-ai-sr-contact-email">' . esc_html__( 'Email', 'echo-knowledge-base' ) . '</label>';
		$output .= '<input type="email" id="epkb-ai-sr-contact-email" class="epkb-ai-sr-contact-input" required />';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<button class="epkb-ai-sr-contact-button">' . esc_html( $button_text ) . '</button>';
		$output .= '</div>'; // .epkb-ai-sr-contact-box

		$output .= '</div>'; // .epkb-ai-sr-section
		$output .= '</div>'; // .epkb-ai-sr-section-wrapper

		return $output;
	}

	/**
	 * Render feedback section directly (static HTML, no AJAX needed)
	 * @param string $destination Rendering context: 'dialog', 'shortcode', or 'block'
	 * @return string HTML
	 */
	public static function render_feedback_section_static( $destination = self::DESTINATION_DIALOG ) {
		$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_feedback_name' );

		$output = '<div class="epkb-ai-sr-section-wrapper epkb-ai-sr-static-section" data-section-id="feedback" data-destination="' . esc_attr( $destination ) . '">';
		$output .= '<div class="epkb-ai-sr-section epkb-ai-sr-section--feedback">';

		if ( ! empty( $section_name ) ) {
			$output .= '<h3 class="epkb-ai-sr-section__title">' . esc_html( $section_name ) . '</h3>';
		}

		$output .= '<div class="epkb-ai-sr-section__content">';
		$output .= '<div class="epkb-ai-sr-feedback-widget">';
		$output .= '<p class="epkb-ai-sr-feedback-question">' . esc_html__( 'Was this answer helpful?', 'echo-knowledge-base' ) . '</p>';
		$output .= '<div class="epkb-ai-sr-feedback-buttons">';
		$output .= '<button class="epkb-ai-sr-feedback-btn epkb-ai-sr-feedback-btn--up" data-vote="up">';
		$output .= '<span class="epkbfa epkbfa-thumbs-up"></span> ' . esc_html__( 'Yes', 'echo-knowledge-base' );
		$output .= '</button>';
		$output .= '<button class="epkb-ai-sr-feedback-btn epkb-ai-sr-feedback-btn--down" data-vote="down">';
		$output .= '<span class="epkbfa epkbfa-thumbs-down"></span> ' . esc_html__( 'No', 'echo-knowledge-base' );
		$output .= '</button>';
		$output .= '</div>';
		$output .= '</div>'; // .epkb-ai-sr-feedback-widget
		$output .= '</div>'; // .epkb-ai-sr-section__content

		$output .= '</div>'; // .epkb-ai-sr-section
		$output .= '</div>'; // .epkb-ai-sr-section-wrapper

		return $output;
	}

	/**
	 * Render columns with sections
	 * @param string $destination Rendering context: 'dialog', 'shortcode', or 'block'
	 * @return string HTML
	 */
	public static function render_columns( $destination = self::DESTINATION_DIALOG ) {

		$num_columns = (int) EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_num_columns', 2 );
		$separator = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_separator', 'line' );
		$gap_px = 30; // Keep in sync with CSS 'gap'

		// Parse column widths
		$column_widths_string = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_column_widths', '30-70' );
		$column_widths = self::parse_column_widths( $column_widths_string, $num_columns );

		// Add destination class for context-specific styling
		$destination_class = ' epkb-ai-sr-columns--' . esc_attr( $destination );
		$output = '<div class="epkb-ai-sr-columns epkb-ai-sr-columns--' . esc_attr( $num_columns ) . ' epkb-ai-sr-separator--' . esc_attr( $separator ) . $destination_class . '">';

		// Render each column
		for ( $i = 1; $i <= $num_columns; $i++ ) {
			$sections = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_column_' . $i . '_sections', array() );
			$width = isset( $column_widths[$i - 1] ) ? (float) $column_widths[$i - 1] : ( 100 / $num_columns );

			// Dialog uses calc() for proper gap handling, page contexts use simple percentage
			if ( $destination === self::DESTINATION_DIALOG ) {
				$ratio = $width / 100.0;
				$total_gap_expr = $gap_px . 'px * ' . max( 0, $num_columns - 1 );
				$flex_basis = 'calc( (100% - (' . $total_gap_expr . ')) * ' . $ratio . ' )';
			} else {
				$flex_basis = $width . '%';
			}

			$output .= '<div class="epkb-ai-sr-column epkb-ai-sr-column--' . $i . '" style="flex: 0 0 ' . esc_attr( $flex_basis ) . ';" data-column="' . $i . '">';
			$output .= self::render_sections( $sections, $destination );
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Render sections within a column
	 * @param array $sections Section IDs
	 * @param string $destination Rendering context: 'dialog', 'shortcode', or 'block'
	 * @return string HTML
	 */
	private static function render_sections( $sections, $destination = self::DESTINATION_DIALOG ) {
		if ( empty( $sections ) || ! is_array( $sections ) ) {
			return '';
		}

		$output = '';
		foreach ( $sections as $section_id ) {
			$output .= self::render_section_placeholder( $section_id, $destination );
		}

		return $output;
	}

	/**
	 * Render section placeholder with loading state
	 * @param string $section_id
	 * @param string $destination Rendering context: 'dialog', 'shortcode', or 'block'
	 * @return string HTML
	 */
	private static function render_section_placeholder( $section_id, $destination = self::DESTINATION_DIALOG ) {
		// Contact-us section is static - render it directly instead of loading via AJAX
		if ( $section_id === 'contact_us' || $section_id === 'contact-us' ) {
			return self::render_contact_us_section_static( $destination );
		}

		// Feedback section is static - render it directly instead of loading via AJAX
		if ( $section_id === 'feedback' ) {
			return self::render_feedback_section_static( $destination );
		}

		$output = '<div class="epkb-ai-sr-section-wrapper" data-section-id="' . esc_attr( $section_id ) . '" data-destination="' . esc_attr( $destination ) . '">';
		$output .= '<div class="epkb-ai-sr-loading">';
		$output .= '<span class="epkbfa epkbfa-spinner epkbfa-pulse"></span>';
		$output .= '<span class="epkb-ai-sr-loading__text">' . esc_html__( 'Loading...', 'echo-knowledge-base' ) . '</span>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Parse column widths from config string
	 * @param string $widths_string Format: "30-70" or "25-50-25"
	 * @param int $num_columns
	 * @return array Array of width percentages
	 */
	private static function parse_column_widths( $widths_string, $num_columns ) {
		if ( $num_columns == 1 ) {
			return array( 100 );
		}

		// Parse string like "30-70" or "25-50-25"
		$widths = explode( '-', $widths_string );

		// Validate and sanitize
		$widths = array_map( 'intval', $widths );
		$widths = array_filter( $widths, function( $w ) {
			return $w > 0 && $w <= 100;
		});

		// If we don't have the right number of widths, use equal distribution
		if ( count( $widths ) != $num_columns ) {
			$equal_width = floor( 100 / $num_columns );
			return array_fill( 0, $num_columns, $equal_width );
		}

		return array_values( $widths );
	}
}
