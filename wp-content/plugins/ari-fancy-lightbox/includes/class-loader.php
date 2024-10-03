<?php
namespace Ari_Fancy_Lightbox;

use Ari_Fancy_Lightbox\Helpers\Settings as Settings;

class Loader {
    private $settings;
    private $deregister_3rd_plugins = false;
    private $load_scripts_in_footer = false;

    public function __construct() {
        $this->settings = Settings::instance();
        $this->deregister_3rd_plugins = $this->settings->get_option( 'advanced.deregister_3rd_plugins' );
        $this->load_scripts_in_footer = $this->settings->get_option( 'advanced.load_scripts_in_footer' );
    }

    public function run() {
        add_action( 'wp_enqueue_scripts', function() { $this->enqueue_scripts(); }, 1000 );
    }

    public function enqueue_scripts() {
        if ( $this->deregister_3rd_plugins ) {
            wp_deregister_script( 'fancybox' );
            wp_deregister_script( 'jquery.fancybox' );
            wp_deregister_script( 'jquery_fancybox' );
            wp_deregister_script( 'jquery-fancybox' );

            wp_deregister_style( 'fancybox' );
        }

        wp_enqueue_style( 'ari-fancybox' );
        wp_enqueue_script( 'ari-fancybox', '', array(), ARIFANCYLIGHTBOX_VERSION, $this->load_scripts_in_footer );

        do_action( 'ari-fancybox-enqueue-scripts' );

        $fancybox_options = $this->settings->get_client_settings();
        $fancybox_options = apply_filters( 'ari-fancybox-options', $fancybox_options );

        wp_localize_script( 'ari-fancybox', 'ARI_FANCYBOX', $fancybox_options );

        $custom_js = trim( $this->settings->get_option( 'advanced.custom_js', '' ) );
        if ( strlen( $custom_js ) > 0 ) {
            wp_add_inline_script(
                'ari-fancybox',
                sprintf(
                    'ARI_FANCYBOX_INIT_FUNC = function($) {%s}',
                    $custom_js
                ),
                'before'
            );
        }

        $custom_styles = $this->settings->get_custom_styles();

        if ( $custom_styles ) {
            wp_add_inline_style(
                'ari-fancybox',
                $custom_styles
            );
        }
    }
}
