<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Featured_Articles_Block extends EPKB_Abstract_Block {
	const EPKB_BLOCK_NAME = 'featured-articles';

	protected $block_name = 'featured-articles';
	protected $block_var_name = 'featured_articles';
	protected $block_title = 'KB Featured Articles';
	protected $icon = 'editor-table';
	protected $keywords = ['knowledge base', 'articles', 'popular articles', 'new articles'];	// is internally wrapped into _x() - see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#internationalization

	public function __construct( $init_hooks = true ) {
		parent::__construct( $init_hooks );

		// when insert blocks programmatically we need to utilize non-static methods of the block classes, but we do not need hooks for this
		if ( ! $init_hooks ) {
			return;
		}

		// must be assigned to hook inside child class to enqueue unique assets for each block type
		add_action( 'enqueue_block_assets', array( $this, 'register_block_assets' ) ); // Frontend / Backend
	}

	/**
	 * Return the actual specific block content
	 * @param $block_attributes
	 */
	public function render_block_inner( $block_attributes ) {	?>
		<div id="epkb-ml__module-articles-list" class="epkb-ml__module">   <?php
			$articles_list_handler = new EPKB_ML_Articles_List( $block_attributes );
			$articles_list_handler->display_articles_list();	?>
		</div>  <?php
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
	 * Inherit article hover colors from the first *-layout block in the post (see parse_block_attributes_from_post( $post, '-layout' )) when KB IDs match. Drill Down: hover always on; others: toggle from attrs or KB config.
	 *
	 * @param array $block_attributes
	 * @return array
	 */
	private function inherit_layout_block_hover_for_featured_block( $block_attributes ) {

		$post = EPKB_Core_Utilities::get_current_post();
		if ( empty( $post ) ) {
			return $block_attributes;
		}

		$layout_attrs = EPKB_Block_Utilities::parse_block_attributes_from_post( $post, '-layout' );
		if ( $layout_attrs === false ) {
			return $block_attributes;
		}

		$featured_kb_id = empty( $block_attributes['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int) $block_attributes['kb_id'];
		$layout_kb_id = empty( $layout_attrs['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int) $layout_attrs['kb_id'];
		if ( $featured_kb_id !== $layout_kb_id ) {
			return $block_attributes;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $layout_kb_id );

		$first_layout_name = EPKB_Block_Utilities::get_kb_block_layout( $post, false );
		$layout_is_drill_down = $first_layout_name === EPKB_Layout::DRILL_DOWN_LAYOUT;

		if ( $layout_is_drill_down ) {
			$hover_toggle = 'on';
		} else {
			$hover_toggle = isset( $layout_attrs['article_list_hover_toggle'] ) && $layout_attrs['article_list_hover_toggle'] !== ''
				? $layout_attrs['article_list_hover_toggle']
				: ( isset( $kb_config['article_list_hover_toggle'] ) ? $kb_config['article_list_hover_toggle'] : 'off' );
		}

		if ( $hover_toggle !== 'on' ) {
			// Do not leave Featured on global KB hover when this layout has hover off (add_internal_kb_settings may have set toggle on).
			$block_attributes['article_list_hover_toggle'] = 'off';
			return $block_attributes;
		}

		$hover_bg = isset( $layout_attrs['article_list_hover_background_color'] ) && $layout_attrs['article_list_hover_background_color'] !== ''
			? $layout_attrs['article_list_hover_background_color']
			: ( isset( $kb_config['article_list_hover_background_color'] ) ? $kb_config['article_list_hover_background_color'] : '#f5f5f5' );

		$hover_text = isset( $layout_attrs['article_list_hover_font_color'] ) && $layout_attrs['article_list_hover_font_color'] !== ''
			? $layout_attrs['article_list_hover_font_color']
			: ( isset( $kb_config['article_list_hover_font_color'] ) ? $kb_config['article_list_hover_font_color'] : '#000000' );

		$block_attributes['article_list_hover_background_color'] = EPKB_Utilities::sanitize_hex_color( $hover_bg );
		$block_attributes['article_list_hover_font_color'] = EPKB_Utilities::sanitize_hex_color( $hover_text );
		$block_attributes['article_list_hover_toggle'] = 'on';

		return $block_attributes;
	}

	/**
	 * Apply section_box_gap from the same source as hover (first *-layout block in the post) when KB IDs match.
	 *
	 * @param array $block_attributes
	 * @return array
	 */
	private function inherit_layout_block_section_gap_for_featured_block( $block_attributes ) {

		$post = EPKB_Core_Utilities::get_current_post();
		if ( empty( $post ) ) {
			return $block_attributes;
		}

		$layout_attrs = EPKB_Block_Utilities::parse_block_attributes_from_post( $post, '-layout' );
		if ( $layout_attrs === false ) {
			return $block_attributes;
		}

		$featured_kb_id = empty( $block_attributes['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int) $block_attributes['kb_id'];
		$layout_kb_id = empty( $layout_attrs['kb_id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : (int) $layout_attrs['kb_id'];
		if ( $featured_kb_id !== $layout_kb_id ) {
			return $block_attributes;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $layout_kb_id );

		if ( ! array_key_exists( 'section_box_gap', $layout_attrs ) ) {
			$section_gap = isset( $kb_config['section_box_gap'] ) ? intval( $kb_config['section_box_gap'] ) : 20;
		} else {
			$section_gap = intval( $layout_attrs['section_box_gap'] );
		}

		$block_attributes['section_box_gap'] = $section_gap;

		return $block_attributes;
	}

	/**
	 * Block dedicated inline styles
	 * @param $block_attributes
	 * @return string
	 */
	protected function get_this_block_inline_styles( $block_attributes ) {

		$block_attributes = $this->inherit_layout_block_hover_for_featured_block( $block_attributes );
		$block_attributes = $this->inherit_layout_block_section_gap_for_featured_block( $block_attributes );

		$block_ui_specs = $this->get_block_ui_specs();

		$output = '';

		$block_selector = '.eckb-kb-block-featured-articles #epkb-ml__module-articles-list';

		$output .=
			/* Title Font */
			$block_selector . ' ' . '.epkb-ml-articles-list__title span {
				font-size: ' . intval( $block_attributes['articles_list_title_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['articles_list_title_typography_controls'], $block_ui_specs['articles_list_title_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['articles_list_title_typography_controls'], $block_ui_specs['articles_list_title_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['articles_list_title_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['articles_list_title_typography_controls']['font_family'] ) ) . ' !important;
				color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['ml_articles_list_title_color'] ) . ' !important;
			}' .
			/* Section Title Font */
			$block_selector . ' ' . '.epkb-ml-article-section__head {
				font-size: ' . intval( $block_attributes['articles_list_head_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['articles_list_head_typography_controls'], $block_ui_specs['articles_list_head_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['articles_list_head_typography_controls'], $block_ui_specs['articles_list_head_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['articles_list_head_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['articles_list_head_typography_controls']['font_family'] ) ) . ' !important;
			}' .
			/* Article Icon Font */
			$block_selector . ' ' . '.epkb-ml-article-container .epkb-article-inner .epkb-article__icon, ' .
			$block_selector . ' ' . '.epkb-ml-article-container .epkb-article-inner .eckb-article-title__icon { 
				font-size: ' . intval( $block_attributes['articles_list_article_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['articles_list_article_typography_controls'], $block_ui_specs['articles_list_article_typography_controls'] ) ) . ' !important;
			}' .
			/* Article Title Font */
			$block_selector . ' ' . '.epkb-ml-article-container .epkb-article-inner .epkb-article__text {
				font-size: ' . intval( $block_attributes['articles_list_article_typography_controls']['font_size'] ) . 'px !important;
				font-weight: ' . intval( EPKB_Blocks_Settings::get_font_appearance_weight( $block_attributes['articles_list_article_typography_controls'], $block_ui_specs['articles_list_article_typography_controls'] ) ) . ' !important;
				font-style: ' . esc_attr( EPKB_Blocks_Settings::get_font_appearance_style( $block_attributes['articles_list_article_typography_controls'], $block_ui_specs['articles_list_article_typography_controls'] ) ) . ' !important;
				font-family: ' . ( empty( $block_attributes['articles_list_article_typography_controls']['font_family'] ) ? 'inherit' : esc_attr( $block_attributes['articles_list_article_typography_controls']['font_family'] ) ) . ' !important;
			}';

		$output .=
			$block_selector . ' ' . '.epkb-ml-article-list-container .epkb-ml-article-section {
				border-color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['section_border_color'] ) . ' !important;
				border-width: ' . intval( $block_attributes['section_border_width'] ) . 'px !important;
				border-radius: ' . intval( $block_attributes['section_border_radius'] ) . 'px !important;
				background-color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['section_body_background_color'] ) . ';' .
			'}';

		$output .=
			$block_selector . ' ' . '.epkb-ml-article-section__head {
				color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['section_head_font_color'] ) . ' !important;
			}';

		$output .=
			$block_selector . ' ' . '.epkb-ml-articles-list li {
			    padding-top: ' . intval( $block_attributes['article_list_spacing'] ) . 'px !important;
			    padding-bottom: ' . intval( $block_attributes['article_list_spacing'] ) . 'px !important;
		    }';

		$output .=
			$block_selector . ' ' . '.epkb-article-inner .epkb-article__text {
				color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['article_font_color'] ) . ';
			}' .
			$block_selector . ' ' . '.epkb-article-inner .epkb-article__icon {
				color: ' . EPKB_Utilities::sanitize_hex_color( $block_attributes['article_icon_color'] ) . ';
			}';

		if ( $block_attributes['ml_articles_list_title_location'] != 'none' ) {
			$output .= $block_selector . ' ' . '.epkb-ml-articles-list__title {
				text-align: ' . esc_attr( $block_attributes['ml_articles_list_title_location'] ) . '!important;
			}';
		}

		// Article hover effect -----------------------------------------/
		$hover_toggle = empty( $block_attributes['article_list_hover_toggle'] ) ? 'off' : $block_attributes['article_list_hover_toggle'];
		if ( $hover_toggle == 'on' ) {
			$spacing = intval( $block_attributes['article_list_spacing'] );
			$hover_bg = EPKB_Utilities::sanitize_hex_color( $block_attributes['article_list_hover_background_color'] );
			$hover_text = EPKB_Utilities::sanitize_hex_color( $block_attributes['article_list_hover_font_color'] );
			$output .= '
				' . $block_selector . ' .epkb-ml-article-container {
					padding: ' . $spacing . 'px !important;
					border-radius: 6px !important;
					transition: background-color 0.2s ease, color 0.2s ease !important;
				}
				' . $block_selector . ' .epkb-ml-article-container:hover {
					background-color: ' . $hover_bg . ' !important;
				}
				' . $block_selector . ' .epkb-ml-article-container:hover .epkb-article-inner {
					color: ' . $hover_text . ' !important;
				}
				' . $block_selector . ' .epkb-ml-article-container:hover .epkb-article__icon {
					color: ' . $hover_text . ' !important;
				}
				' . $block_selector . ' .epkb-ml-article-container:hover .epkb-article__text {
					color: ' . $hover_text . ' !important;
				}
				' . $block_selector . ' .epkb-ml-articles-list li {
					padding-top: 0px !important;
					padding-bottom: 0px !important;
				}';
		}

		// Space between article sections (selector matches featured-articles-block.css .epkb-ml-article-list-container path).
		$section_gap = isset( $block_attributes['section_box_gap'] ) ? intval( $block_attributes['section_box_gap'] ) : 20;
		$output .= '
			' . $block_selector . ' .epkb-ml-article-list-container .epkb-ml-articles-list__row {
				gap: ' . $section_gap . 'px !important;
			}';

		// Section padding -----------------------------------------/
		if ( ! empty( $block_attributes['category_box_padding'] ) ) {
			$output .= '
				' . $block_selector . ' .epkb-ml-article-section {
					padding: ' . intval( $block_attributes['category_box_padding'] ) . 'px !important;
				}';
		}

		return $output;
	}

	/**
	 * Return list of all typography settings for the current block
	 * @return array
	 */
	protected function get_this_block_typography_settings() {
		return array(
			'articles_list_title_typography_controls',
			'articles_list_head_typography_controls',
			'articles_list_article_typography_controls',
		);
	}

	/**
	 * Return list attributes with custom specs - they are not allowed in attributes when registering block, thus need to keep them separately
	 * @return array[]
	 */
	protected function get_this_block_ui_config() {
		return array(

			// TAB: Settings
			'settings' => array(
				'title' => esc_html__( 'Settings', 'echo-knowledge-base' ),
				'icon' => ' ' . 'epkbfa epkbfa-cog',
				'groups' => array(

					// GROUP: General
					'general' => array(
						'title' => esc_html__( 'General', 'echo-knowledge-base' ),
						'fields' => array(
							'kb_id' => EPKB_Blocks_Settings::get_kb_id_setting(),
							'ml_articles_list_title_text' => array(
								'setting_type' => 'text',
							),
							'ml_articles_list_title_location' => array(
								'setting_type' => 'select_buttons_string',
							),
							'ml_articles_list_nof_articles_displayed' => array(
								'setting_type' => 'number',
							),
							'ml_articles_list_column_1' => array(
								'setting_type' => 'dropdown',
							),
							'ml_articles_list_column_2' => array(
								'setting_type' => 'dropdown',
							),
							'ml_articles_list_column_3' => array(
								'setting_type' => 'dropdown',
							),
							'ml_articles_list_popular_articles_msg' => array(
								'setting_type' => 'text',
							),
							'ml_articles_list_newest_articles_msg' => array(
								'setting_type' => 'text',
							),
							'ml_articles_list_recent_articles_msg' => array(
								'setting_type' => 'text',
							),
						),
					),

					// GROUP: Advanced
					'advanced' => array(
						'title' => esc_html__( 'Advanced', 'echo-knowledge-base' ),
						'fields' => array(
							'custom_css_class' => EPKB_Blocks_Settings::get_custom_css_class_setting(),
						)
					),

					// GROUP: Help + Setup Wizard
					'help-resources' => array(
						'title' => esc_html__( 'Help + Setup Wizard', 'echo-knowledge-base' ),
						'fields' => array(
							'help_resources_link' => EPKB_Blocks_Settings::get_help_resources_link(),
							'setup_wizard_link' => EPKB_Blocks_Settings::get_setup_wizard_link(),
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
							'block_full_width_toggle' => EPKB_Blocks_Settings::get_block_full_width_setting(),
							'block_max_width' => EPKB_Blocks_Settings::get_block_max_width_setting(),
							'section_border_width' => array(
								'setting_type' => 'range',
							),
							'section_border_radius' => array(
								'setting_type' => 'range',
							),
							'section_border_color' => array(
								'setting_type' => 'color',
							),
							'section_body_background_color' => array(
								'setting_type' => 'color',
							),
							'section_box_shadow' => array(
								'setting_type' => 'select_buttons_string',
								'options'     => array(
									'no_shadow' => esc_html__( 'No Shadow', 'echo-knowledge-base' ),
									'section_light_shadow' => esc_html__( 'Light', 'echo-knowledge-base' ),
									'section_medium_shadow' => esc_html__( 'Medium', 'echo-knowledge-base' ),
									'section_bottom_shadow' => esc_html__( 'Bottom', 'echo-knowledge-base' )
								),
								'default' => 'no_shadow',
							),
							'articles_list_title_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 24,
										'normal' => 28,
										'big' => 32,
									), 32 ),
								),
							),
						),
					),

					// GROUP: Header
					'featured-articles-title' => array(
						'title' => esc_html__( 'Featured Articles Title', 'echo-knowledge-base' ),
						'fields' => array(
							'ml_articles_list_title_color' => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'Title Color', 'echo-knowledge-base' ),
							),
							'section_head_font_color' => array(
								'setting_type' => 'color',
								'label' => esc_html__( 'List Title', 'echo-knowledge-base' ),
							),
							'articles_list_head_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'List Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 16,
										'normal' => 17,
										'big' => 21,
									), 17 ),
								),
							),
						),
					),

					// GROUP: Featured Articles
					'featured-articles' => array(
						'title' => esc_html__( 'Featured Articles', 'echo-knowledge-base' ),
						'fields' => array(
							'article_icon_toggle' => array(
								'setting_type' => 'toggle',
								'label' => esc_html__( 'Show Article Icon', 'echo-knowledge-base' ),
							),
							'elay_article_icon' => array(
								'setting_type' => EPKB_Utilities::is_elegant_layouts_enabled() ? 'dropdown' : '',
								'hide_on_dependencies' => array(
									'article_icon_toggle' => 'off',
								),
							),
							'article_list_spacing' => array(
								'setting_type' => 'range',
							),
							'article_font_color' => array(
								'setting_type' => 'color',
							),
							'article_icon_color' => array(
								'setting_type' => 'color',
								'hide_on_dependencies' => array(
									'article_icon_toggle' => 'off',
								),
							),
							'articles_list_article_typography_controls' => array(
								'setting_type' => 'typography_controls',
								'label' => esc_html__( 'Article Title Typography', 'echo-knowledge-base' ),
								'controls' => array(
									'font_family' => EPKB_Blocks_Settings::get_typography_control_font_family(),
									'font_appearance' => EPKB_Blocks_Settings::get_typography_control_font_appearance(),
									'font_size' => EPKB_Blocks_Settings::get_typography_control_font_size( array(
										'small' => 14,
										'normal' => 16,
										'big' => 18,
									), 14 ),
								),
							),
						),
					),
				),
			)
		);
	}
}