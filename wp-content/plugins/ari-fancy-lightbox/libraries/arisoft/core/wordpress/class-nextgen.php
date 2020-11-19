<?php
namespace Ari\Wordpress;

class Nextgen {
    static public function is_installed_v2() {
        if ( defined( 'NEXTGEN_GALLERY_PLUGIN_VERSION' ) )
            return version_compare( NEXTGEN_GALLERY_PLUGIN_VERSION, '2.0.0', '>=' );

        return false;
    }

    static public function install_lightbox_v2( $lightbox, $lightbox_title, $code ) {
        if ( class_exists( 'C_Lightbox_Installer' ) ) {
            $installer = new \C_Lightbox_Installer();
            $is_installed = $installer->mapper->find_by_name( $lightbox );

            if ( ! $is_installed ) {
                $installer->install_lightbox(
                    $lightbox,
                    $lightbox_title,
                    $code,
                    array( '', '' ),
                    array( '', '' )
                );
            }
        } else if ( class_exists( 'C_Lightbox_Library_Manager' ) ) {
            $ngg_lightbox_manager = \C_Lightbox_Library_Manager::get_instance();

            $lightbox_options = new \stdClass();
            $lightbox_options->title = $lightbox_title;
            $lightbox_options->code = $code;
            $lightbox_options->styles = array();
            $lightbox_options->scripts = array();
            $ngg_lightbox_manager->register( $lightbox, $lightbox_options);
        }
    }
}
