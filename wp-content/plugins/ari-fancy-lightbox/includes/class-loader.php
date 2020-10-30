<?php
namespace Ari_Fancy_Lightbox;

use Ari_Fancy_Lightbox\Helpers\Settings as Settings;

class Loader {
    private $settings;
    private $deregister_3rd_plugins = false;

    public function __construct() {
        $this->settings = Settings::instance();
        $this->deregister_3rd_plugins = $this->settings->get_option( 'advanced.deregister_3rd_plugins' );
        $this->load_scripts_in_footer = $this->settings->get_option( 'advanced.load_scripts_in_footer' );
    }

    public function run() {
        add_action( 'wp_enqueue_scripts', function() { $this->enqueue_scripts(); }, 1000 );
        add_action( 'wp_head', function() { $this->header_includes(); } );
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
        wp_enqueue_script( 'ari-fancybox', '', array(), false, $this->load_scripts_in_footer );

        do_action( 'ari-fancybox-enqueue-scripts' );

        $fancybox_options = $this->settings->get_client_settings();
        $fancybox_options = apply_filters( 'ari-fancybox-options', $fancybox_options );

        wp_localize_script( 'ari-fancybox', 'ARI_FANCYBOX', $fancybox_options );
    }

    public function header_includes() {
        $custom_js = trim( $this->settings->get_option( 'advanced.custom_js', '' ) );
        if ( strlen( $custom_js ) > 0 )
            printf(
                '<script>ARI_FANCYBOX_INIT_FUNC = function($) {%s}</script>',
                $custom_js
            );

        $custom_styles = $this->settings->get_custom_styles();

        if ( $custom_styles ) {
            printf(
                '<style type="text/css">%s</style>',
                $custom_styles
            );
        }
    }
}
