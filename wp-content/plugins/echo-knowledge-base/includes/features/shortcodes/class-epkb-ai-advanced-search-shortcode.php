<?php

/**
 * Shortcode - AI Search Results with embedded display
 *
 * @copyright   Copyright (c) 2025, Echo Plugins
 */
class EPKB_AI_Advanced_Search_Shortcode {

	public function __construct() {
		add_shortcode( 'ai-advanced-search', array('EPKB_AI_Advanced_Search_Shortcode', 'output_shortcode' ) );
	}

	/**
	 * Outputs the shortcode content.
	 *
	 * @param array $attributes Shortcode attributes:
	 *   - kb_id: Knowledge Base ID (optional, defaults to global or 1)
	 *   - kb_ai_collection_id: AI Training Data Collection ID (optional, overrides KB default)
	 */
	public static function output_shortcode( $attributes ) {

		// Only render if advanced search mode is enabled
		if ( ! EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			return '';
		}

		self::enqueue_assets();

		// Parse shortcode attributes
		$attributes = shortcode_atts( array(
			'kb_id' => '',
			'kb_ai_collection_id' => '',
		), $attributes );

		// Get KB ID from attribute, global, or default
		$kb_id = empty( $attributes['kb_id'] ) ? ( empty( $GLOBALS['eckb_kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $GLOBALS['eckb_kb_id'] ) : absint( $attributes['kb_id'] );

		// Get collection ID from attribute or KB config
		$collection_id = '';
		if ( ! empty( $attributes['kb_ai_collection_id'] ) ) {
			$collection_id = absint( $attributes['kb_ai_collection_id'] );
		} else {
			// Get from KB configuration
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
			if ( ! is_wp_error( $kb_config ) && ! empty( $kb_config['kb_ai_collection_id'] ) ) {
				$collection_id = $kb_config['kb_ai_collection_id'];
			}
		}

		// Prepare data attributes
		$collection_attr = ! empty( $collection_id ) ? ' data-collection-id="' . esc_attr( $collection_id ) . '"' : '';

		// Start output buffering
		ob_start();

		// Output inline configuration data
		EPKB_AI_Search_Results_Display::output_inline_script_data();		?>

		<div class="epkb-ai-sr-shortcode" data-kb-id="<?php echo esc_attr( $kb_id ); ?>"<?php echo $collection_attr; ?>>

			<!-- Search Form -->
			<div class="epkb-ai-sr-shortcode__form">
				<h2 class="epkb-ai-sr-shortcode__title"><?php echo esc_html__( 'AI Search', 'echo-knowledge-base' ); ?></h2>
				<form class="epkb-ai-sr-shortcode__search-form">
					<div class="epkb-ai-sr-shortcode__input-wrapper">
						<input
							type="text"
							class="epkb-ai-sr-shortcode__input"
							placeholder="<?php echo esc_attr__( 'Ask a question...', 'echo-knowledge-base' ); ?>"
							name="ai_search_query"
							required
						/>
						<button type="submit" class="epkb-ai-sr-shortcode__submit">
							<?php echo esc_html__( 'Search', 'echo-knowledge-base' ); ?>
						</button>
					</div>
				</form>
			</div>

			<!-- Results Container -->
			<div class="epkb-ai-sr-shortcode__results" style="display: none;">
				<div id="epkb-ai-sr-dialog__content">				<?php
					echo EPKB_AI_Search_Results_Display::render_columns( EPKB_AI_Search_Results_Display::DESTINATION_SHORTCODE ); ?>
				</div>
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Ensure shortcode assets are loaded when needed.
	 */
	public static function enqueue_assets() {
		wp_enqueue_style( 'epkb-ai-search-results-shortcode' );
		wp_enqueue_script( 'epkb-ai-search-results' ); // Shortcode adapter is included in main file
	}
}
