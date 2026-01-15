<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_AI_Advanced_Search_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'ai-advanced-search';

	protected $block_name = 'ai-advanced-search';
	protected $block_var_name = 'ai_advanced_search';
	protected $block_title = 'KB AI Advanced Search';
	protected $icon = 'search';
	protected $keywords = ['ai', 'search', 'advanced', 'knowledge base'];

	public function __construct( $init_hooks = true ) {
		parent::__construct( $init_hooks );

		if ( ! $init_hooks ) {
			return;
		}

		add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) );
	}

	/**
	 * Return the actual specific block content
	 * @param $block_attributes
	 */
	public function render_block_inner( $block_attributes ) {

		// Only render if advanced search mode is enabled
		if ( ! EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			return '';
		}

		// Detect if we're in the block editor (preview mode)
		$is_editor = EPKB_Utilities::get( 'is_editor_preview', null );

		// Only enqueue assets and output script data on frontend
		if ( ! $is_editor ) {
			// Enqueue shortcode assets
			EPKB_AI_Advanced_Search_Shortcode::enqueue_assets();

			// Output inline script data for REST API
			EPKB_AI_Search_Results_Display::output_inline_script_data();
		}

		// Get current KB ID
		$kb_id = isset( $block_attributes['kb_id'] ) ? $block_attributes['kb_id'] : EPKB_KB_Config_DB::DEFAULT_KB_ID;		?>

		<div id="epkb-ml__module-ai-advanced-search" class="epkb-ml__module epkb-ai-sr-shortcode<?php echo $is_editor ? ' epkb-editor-preview' : ''; ?>" data-kb-id="<?php echo esc_attr( $kb_id ); ?>">

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
							<?php echo $is_editor ? 'disabled' : 'required'; ?>
						/>
						<button type="submit" <?php echo $is_editor ? 'disabled' : ''; ?> class="epkb-ai-sr-shortcode__submit">
							<?php echo esc_html__( 'Search', 'echo-knowledge-base' ); ?>
						</button>
					</div>
				</form>
			</div>

			<!-- Results Container -->
			<div class="epkb-ai-sr-shortcode__results" style="display: none;">
				<div id="epkb-ai-sr-dialog__content">				<?php
					echo EPKB_AI_Search_Results_Display::render_columns( EPKB_AI_Search_Results_Display::DESTINATION_BLOCK ); ?>
				</div>
			</div>

		</div>		<?php
	}

	/**
	 * Add required specific attributes to work correctly with KB core functionality
	 * @param $block_attributes
	 * @return array
	 */
	protected function add_this_block_required_kb_attributes( $block_attributes ) {
		$block_attributes['kb_main_page_layout'] = EPKB_Layout::BASIC_LAYOUT;
		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {
		// For now, no custom inline styles - uses shortcode CSS
		return '';
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		// No typography settings for now
		return array();
	}

	/**
	 * Return list attributes with custom specs
	 * @return array[]
	 */
	protected function get_this_block_ui_config() {
		return array(

			// TAB: Settings
			'settings' => array(
				'title' => esc_html__( 'Settings', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-cog',
				'groups' => array(

					// GROUP: Advanced
					'advanced' => array(
						'title' => esc_html__( 'Advanced', 'echo-knowledge-base' ),
						'fields' => array(
							'custom_css_class' => EPKB_Blocks_Settings::get_custom_css_class_setting(),
						)
					),
				),
			),

			// TAB: Style
			'style' => array(
				'title' => esc_html__( 'Style', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-adjust',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => esc_html__( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'block_full_width_toggle' => EPKB_Blocks_Settings::get_block_full_width_setting( array(
								'default' => 'on'
							) ),
							'block_max_width' => EPKB_Blocks_Settings::get_block_max_width_setting(),
						),
					),
				),
			),
		);
	}
}
