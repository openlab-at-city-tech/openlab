<?php

class TRP_Gutenberg_Blocks {

    private $settings;

    public function __construct( $settings ) {
        $this->settings = $settings;
        include_once( TRP_PLUGIN_DIR . 'includes/gutenberg-blocks/ls-shortcode/ls-shortcode.php' );

        if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
            add_filter( 'block_categories_all', array( $this, 'register_layout_category' ) );
        } else {
            add_filter( 'block_categories', array( $this, 'register_layout_category' ) );
        }
        add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_enqueue' ) );
    }

    public function block_editor_enqueue() {
        wp_enqueue_style( 'trp-language-switcher-style', TRP_PLUGIN_URL . 'assets/css/trp-language-switcher.css', array(), TRP_PLUGIN_VERSION );
    }

    public function register_layout_category( $categories ) {

        $categories[] = array(
            'slug'  => 'trp-block',
            'title' => 'TranslatePress'
        );

        return $categories;
    }

}
