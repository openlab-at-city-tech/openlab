<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class EPKB_Articles_Index_Directory_Block extends EPKB_Abstract_Block {
    const EPKB_BLOCK_NAME = 'articles-index-directory';

    protected $block_name      = self::EPKB_BLOCK_NAME;
    protected $block_var_name  = 'articles_index_directory';
    protected $block_title     = 'KB Articles Index Directory';
    protected $icon            = 'list-view';
    protected $keywords        = [ 'knowledge base', 'articles', 'index', 'directory' ];

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
		echo EPKB_Articles_Index_Shortcode::render_directory(
			array(
				'title'    => $block_attributes['title'],
				'kb_id'    => $block_attributes['kb_id'],
				'is_block' => true,
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
            'settings' => array(
                'title'  => esc_html__( 'Settings', 'echo-knowledge-base' ),
                'icon'   => ' epkbfa epkbfa-cog',
                'groups' => array(
                    'general' => array(
                        'title'  => esc_html__( 'General', 'echo-knowledge-base' ),
                        'fields' => array(
                            'kb_id'                   => EPKB_Blocks_Settings::get_kb_id_setting(),
                            'title'                   => array(
                                'label'        => esc_html__( 'Title', 'echo-knowledge-base' ),
                                'setting_type' => 'text',
                            ),
                            'block_full_width_toggle' => EPKB_Blocks_Settings::get_block_full_width_setting(),
                            'block_max_width'         => EPKB_Blocks_Settings::get_block_max_width_setting(),
                        ),
                    ),
                    'advanced' => array(
                        'title'  => esc_html__( 'Advanced', 'echo-knowledge-base' ),
                        'fields' => array(
                            'custom_css_class' => EPKB_Blocks_Settings::get_custom_css_class_setting(),
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