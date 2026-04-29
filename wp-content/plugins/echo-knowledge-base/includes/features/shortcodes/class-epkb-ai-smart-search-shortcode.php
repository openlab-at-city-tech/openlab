<?php

/**
 * Shortcode - AI Search Results with embedded display
 *
 * @copyright   Copyright (c) 2025, Echo Plugins
 */
class EPKB_AI_Smart_Search_Shortcode {

	public function __construct() {
		add_shortcode( 'ai-smart-search', array('EPKB_AI_Smart_Search_Shortcode', 'output_shortcode' ) );
	}

	/**
	 * Outputs the shortcode content.
	 *
	 * @param array $attributes Shortcode attributes:
	 *   - kb_id: Knowledge Base ID (optional, defaults to global or 1)
	 *   - kb_ai_collection_id: AI Training Data Collection ID (optional, overrides KB default)
	 *   - title: Title text displayed above the search form (optional, defaults to 'AI Search')
	 *   - placeholder: Placeholder text for the search input (optional, defaults to 'Ask a question...')
	 *   - button: Text for the search button (optional, defaults to 'Search')
	 */
	public static function output_shortcode( $attributes ) {

		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() ) {
			return self::render_coming_soon_state();
		}

		// Only render if smart search mode is enabled
		if ( ! EPKB_AI_Utilities::is_ai_search_smart_enabled() ) {
			return '';
		}

		self::enqueue_assets();

		// Parse shortcode attributes
		$attributes = shortcode_atts( array(
			'kb_id'               => '',
			'kb_ai_collection_id' => '',
			'title'               => esc_html__( 'AI Search', 'echo-knowledge-base' ),
			'placeholder'         => esc_attr__( 'Ask a question...', 'echo-knowledge-base' ),
			'button'              => esc_html__( 'Search', 'echo-knowledge-base' ),
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

		// Check for provider mismatch
		if ( ! empty( $collection_id ) ) {
			$mismatch_info = EPKB_AI_Training_Data_Config_Specs::get_active_and_selected_provider_if_mismatched( $collection_id );
			if ( $mismatch_info !== null ) {
				return self::render_disabled_state( $mismatch_info );
			}
		}

		// Prepare data attributes
		$collection_attr_escaped = '';
		if ( ! empty( $collection_id ) ) {
			$collection_attr_escaped = ' data-collection-id="' . esc_attr( $collection_id ) . '"';
			$collection_attr_escaped .= ' data-collection-token="' . esc_attr( EPKB_AI_Security::create_collection_access_token( $collection_id, $kb_id ) ) . '"';
		}

		// Start output buffering
		ob_start();

		// Output inline configuration data
		EPKB_AI_Search_Results_Display::output_inline_script_data();		?>

		<div class="epkb-ai-sr-shortcode" data-kb-id="<?php echo esc_attr( $kb_id ); ?>"<?php echo $collection_attr_escaped; ?>>

			<!-- Search Form -->
			<div class="epkb-ai-sr-shortcode__form">
				<h2 class="epkb-ai-sr-shortcode__title"><?php echo esc_html( $attributes['title'] ); ?></h2>
				<form class="epkb-ai-sr-shortcode__search-form">
					<div class="epkb-ai-sr-shortcode__input-wrapper">
						<input
							type="text"
							class="epkb-ai-sr-shortcode__input"
							placeholder="<?php echo esc_attr( $attributes['placeholder'] ); ?>"
							name="ai_search_query"
							required
						/>
						<button type="submit" class="epkb-ai-sr-shortcode__submit">
							<?php echo esc_html( $attributes['button'] ); ?>
						</button>
					</div>
				</form>
			</div>

			<!-- Results Container -->
			<div class="epkb-ai-sr-shortcode__results" style="display: none;">
				<div id="epkb-ai-sr-dialog__content">				<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_columns returns pre-escaped HTML
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
		EPKB_AI_Utilities::enqueue_ai_search_results_scripts();
		wp_enqueue_style( 'epkb-ai-search-results-shortcode' );
	}

	/**
	 * Render disabled state for shortcode/block when provider mismatch detected
	 *
	 * @param array $mismatch_info Array with collection_provider and active_provider labels
	 * @return string HTML output
	 */
	public static function render_disabled_state( $mismatch_info ) {
		self::enqueue_assets();
		$is_admin = current_user_can( 'manage_options' );

		ob_start(); ?>
		<div class="epkb-ai-sr-shortcode epkb-ai-sr-shortcode--disabled">
			<div class="epkb-ai-sr-shortcode__disabled-notice">
				<?php if ( $is_admin ) :
					$settings_url = admin_url( 'admin.php?page=epkb-ai-features#training-data' ); ?>
				<p><?php
					// translators: %1$s is the collection provider, %2$s is the active provider
					printf(
						esc_html__( 'AI Search unavailable: Collection uses %1$s but active provider is %2$s.', 'echo-knowledge-base' ),
						'<strong>' . esc_html( $mismatch_info['collection_provider'] ) . '</strong>',
						'<strong>' . esc_html( $mismatch_info['active_provider'] ) . '</strong>'
					); ?>
				<a href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Update settings', 'echo-knowledge-base' ); ?></a></p>
				<?php else : ?>
				<p><?php esc_html_e( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ); ?></p>
				<?php endif; ?>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Render placeholder output when AI Features PRO is not active.
	 *
	 * @return string
	 */
	private static function render_coming_soon_state() {
		ob_start(); ?>
		<div class="epkb-ai-sr-shortcode epkb-ai-sr-shortcode--disabled">
			<div class="epkb-ai-sr-shortcode__disabled-notice">
				<p><?php esc_html_e( 'Smart Search comming soon', 'echo-knowledge-base' ); ?></p>
			</div>
		</div>		<?php

		return ob_get_clean();
	}
}
