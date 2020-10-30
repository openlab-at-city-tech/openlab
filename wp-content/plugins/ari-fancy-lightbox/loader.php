<?php
defined( 'ABSPATH' ) or die( 'Access forbidden!' );

if ( ! function_exists( 'ari_fancy_lightbox_init' ) ) {
    function ari_fancy_lightbox_init() {
        if ( defined( 'ARIFANCYLIGHTBOX_INITED' ) )
            return ;

        define( 'ARIFANCYLIGHTBOX_INITED', true );

        require_once ARIFANCYLIGHTBOX_PATH . 'includes/defines.php';
        require_once ARIFANCYLIGHTBOX_PATH . 'libraries/arisoft/loader.php';

        Ari_Loader::register_prefix( 'Ari_Fancy_Lightbox', ARIFANCYLIGHTBOX_PATH . 'includes' );

        $plugin = new \Ari_Fancy_Lightbox\Plugin(
            array(
                'class_prefix' => 'Ari_Fancy_Lightbox',

                'version' => ARIFANCYLIGHTBOX_VERSION,

                'path' => ARIFANCYLIGHTBOX_PATH,

                'url' => ARIFANCYLIGHTBOX_URL,

                'assets_url' => ARIFANCYLIGHTBOX_ASSETS_URL,

                'view_path' => ARIFANCYLIGHTBOX_PATH . 'includes/views/',

                'main_file' => __FILE__,
            )
        );
        $plugin->init();
    }
}
