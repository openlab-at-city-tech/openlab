<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Glossary_Index_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'glossary-index';

	protected $block_name      = self::EPKB_BLOCK_NAME;
	protected $block_var_name  = 'glossary_index';
	protected $block_title     = 'Glossary Index';
	protected $icon            = 'editor-table';
	protected $keywords        = [ 'knowledge base', 'glossary', 'index', 'terms' ];

	public function __construct( $init_hooks = true ) {
		parent::__construct( $init_hooks );

		if ( ! $init_hooks ) {
			return;
		}

		add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) );
	}

	/**
	 * Render block content
	 *
	 * @param array $block_attributes
	 */
	public function render_block_inner( $block_attributes ) {
		echo EPKB_Glossary_Index_Shortcode::render_glossary_index(
			array(
				'glossary_index_accent_color'     => $block_attributes['glossary_index_accent_color'],
				'glossary_index_back_to_top_text' => $block_attributes['glossary_index_back_to_top_text'],
				'is_block'                        => true,
			)
		);
	}

	/**
	 * No specific KB attributes required for this block
	 *
	 * @param array $block_attributes
	 * @return array
	 */
	protected function add_this_block_required_kb_attributes( $block_attributes ) {
		return $block_attributes;
	}

	/**
	 * Block inline styles (none)
	 *
	 * @param array $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {
		return '';
	}

	/**
	 * Block editor UI configuration
	 *
	 * @return array
	 */
	protected function get_this_block_ui_config() {
		return array(

			// TAB: Settings
			'settings' => array(
				'title'  => esc_html__( 'Settings', 'echo-knowledge-base' ),
				'icon'   => ' epkbfa epkbfa-cog',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title'  => esc_html__( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'glossary_index_back_to_top_text' => array(
								'setting_type' => 'text',
								'label'        => esc_html__( 'Back to Top Text', 'echo-knowledge-base' ),
								'default'      => esc_html__( 'Back to top', 'echo-knowledge-base' ),
							),
							'block_full_width_toggle'         => EPKB_Blocks_Settings::get_block_full_width_setting(),
							'block_max_width'                 => EPKB_Blocks_Settings::get_block_max_width_setting(),
						),
					),

					// GROUP: Advanced
					'advanced' => array(
						'title'  => esc_html__( 'Advanced', 'echo-knowledge-base' ),
						'fields' => array(
							'custom_css_class' => EPKB_Blocks_Settings::get_custom_css_class_setting(),
						),
					),
				),
			),

			// TAB: Style
			'style' => array(
				'title'  => esc_html__( 'Style', 'echo-knowledge-base' ),
				'icon'   => ' epkbfa epkbfa-adjust',
				'groups' => array(

					// GROUP: Colors
					'colors' => array(
						'title'  => esc_html__( 'Colors', 'echo-knowledge-base' ),
						'fields' => array(
							'glossary_index_accent_color' => array(
								'setting_type' => 'color',
								'label'        => esc_html__( 'Accent Color', 'echo-knowledge-base' ),
								'default'      => '#1e73be',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * No typography settings for this block
	 *
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array();
	}
}
