<?php

if ( !defined('ABSPATH' ) )
    exit();

class TRP_Gutenberg_Blocks {
    private $settings;

    public function __construct( $settings ) {
        $this->settings = $settings;

        include_once( TRP_PLUGIN_DIR . 'includes/gutenberg-blocks/ls-shortcode/ls-shortcode.php' );
        include_once( TRP_PLUGIN_DIR . 'includes/gutenberg-blocks/block-language-restriction/block-language-restriction.php' );

        if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
            add_filter( 'block_categories_all', array( $this, 'register_layout_category' ) );
        } else {
            add_filter( 'block_categories', array( $this, 'register_layout_category' ) );
        }
        add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_enqueue' ) );
    }

    public function block_editor_enqueue() {
        global $pagenow;

        wp_enqueue_style( 'trp-language-switcher-style', TRP_PLUGIN_URL . 'assets/css/trp-language-switcher.css', array(), TRP_PLUGIN_VERSION );

        if ( $pagenow === 'widgets.php' ) {
            $arrDeps = [ 'wp-blocks', 'wp-dom', 'wp-dom-ready', 'wp-edit-widgets', 'lodash' ];
        } elseif ( $pagenow === 'customize.php' ) {
            $arrDeps = [ 'wp-blocks', 'wp-dom', 'wp-dom-ready', 'lodash' ];
        } else {
            $arrDeps = [ 'wp-blocks', 'wp-dom', 'wp-dom-ready', 'wp-edit-post', 'lodash' ];
        }

        $languagesObject = ( TRP_Translate_Press::get_trp_instance() )->get_component( 'languages' );

        $published_languages = $languagesObject->get_language_names( $this->settings['publish-languages'] );

        wp_enqueue_script( 'trp-block-language-restriction', TRP_PLUGIN_URL . 'includes/gutenberg-blocks/block-language-restriction/build/index.js', $arrDeps, TRP_PLUGIN_VERSION );
        wp_localize_script('trp-block-language-restriction', 'trpBlockEditorData',
            [
              'all_languages' => $published_languages,
              'plugin_url'    => TRP_PLUGIN_URL
            ]
        );
    }

    public function register_layout_category( $categories ) {
        $categories[] = array(
            'slug'  => 'trp-block',
            'title' => 'TranslatePress'
        );

        return $categories;
    }

}
