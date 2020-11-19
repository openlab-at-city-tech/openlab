<?php
namespace Ari_Fancy_Lightbox;

use Ari\App\Installer as Ari_Installer;
use Ari_Fancy_Lightbox\Helpers\Settings as Settings;

class Installer extends Ari_Installer {
    function __construct( $options = array() ) {
        if ( ! isset( $options['installed_version'] ) ) {
            $installed_version = get_option( ARIFANCYLIGHTBOX_VERSION_OPTION );

            if ( false !== $installed_version) {
                $options['installed_version'] = $installed_version;
            }
        }

        if ( ! isset( $options['version'] ) ) {
            $options['version'] = ARIFANCYLIGHTBOX_VERSION;
        }

        parent::__construct( $options );
    }

    private function init() {
    }

    public function run() {
        $this->init();
        $this->init_settings();

        if ( ! $this->run_versions_updates() ) {
            return false;
        }

        update_option( ARIFANCYLIGHTBOX_VERSION_OPTION, $this->options->version );

        return true;
    }

    private function init_settings() {
        if ( false !== get_option( ARIFANCYLIGHTBOX_SETTINGS_NAME ) )
            return ;

        add_option( ARIFANCYLIGHTBOX_SETTINGS_NAME, Settings::instance()->get_default_options() );
    }

    protected function update_to_1_2_0() {
        $settings = get_option( ARIFANCYLIGHTBOX_SETTINGS_NAME );

        if ( false === $settings )
            return ;

        if ( isset( $settings['lightbox'] ) && is_array( $settings['lightbox'] ) ) {
            $lightbox_settings =& $settings['lightbox'];

            if ( isset( $lightbox_settings['speed'] ) ) {
                $lightbox_settings['animationDuration'] = $lightbox_settings['transitionDuration'] = $lightbox_settings['speed'];
                unset( $lightbox_settings['speed'] );
            }

            if ( isset( $lightbox_settings['image'] ) && isset( $lightbox_settings['image']['protect'] ) ) {
                $lightbox_settings['protect'] = $lightbox_settings['image']['protect'];

                unset( $lightbox_settings['image']['protect'] );
            }

            if ( isset( $lightbox_settings['focus'] ) ) {
                $lightbox_settings['autoFocus'] = $lightbox_settings['focus'];

                unset( $lightbox_settings['focus'] );
            }

            if ( isset( $lightbox_settings['touch'] ) && is_bool( $lightbox_settings['touch'] ) ) {
                $lightbox_settings['touch_enabled'] = $lightbox_settings['touch'];

                unset( $lightbox_settings['touch'] );
            }

            if ( isset( $lightbox_settings['buttons'] ) && is_bool( $lightbox_settings['buttons'] ) ) {
                $lightbox_settings['toolbar'] = $lightbox_settings['buttons'];

                unset( $lightbox_settings['buttons'] );
            }

            $buttons = array();
            $buttons_mapping = array(
                'slideShow' => 'slideShow',

                'fullScreen' => 'fullScreen',

                'thumbs' => 'thumbs',

                'closeBtn' => 'close',
            );

            foreach ( $buttons_mapping as $old_button => $new_button ) {
                if ( isset( $lightbox_settings[$old_button] ) && is_bool( $lightbox_settings[$old_button] ) ) {
                    if ( $lightbox_settings[$old_button] )
                        $buttons[] = $new_button;

                    unset( $lightbox_settings[$old_button] );
                }
            }

            $lightbox_settings['buttons'] = $buttons;

            if ( isset( $lightbox_settings['gutter'] ) ) {
                unset( $lightbox_settings['gutter'] );
            }
        }

        update_option( ARIFANCYLIGHTBOX_SETTINGS_NAME, $settings );
    }
}
