<?php
namespace Ari_Fancy_Lightbox\Helpers;

use Ari\Wordpress\Settings as Settings_Base;
use Ari\Utils\Array_Helper as Array_Helper;
use Ari\Wordpress\Nextgen as Nextgen_Helper;

class Settings extends Settings_Base {
    protected $settings_group = ARIFANCYLIGHTBOX_SETTINGS_GROUP;

    protected $settings_name = ARIFANCYLIGHTBOX_SETTINGS_NAME;

    protected $default_settings = array(
        'convert' => array(
            'wp_gallery' => array(
                'convert' => true,

                'grouping' => true,
            ),

            'images' => array(
                'convert' => false,

                'post_grouping' => false,

                'grouping_selector' => '',

                'titleFromExif' => false,

                'filenameToTitle' => false,

                'convertNameSmart' => false,
            ),

            'woocommerce' => array(
                'convert' => false,
            ),

            'nextgen' => array(
                'convert' => false,
            ),

            'youtube' => array(
                'convert' => false,
            ),

            'vimeo' => array(
                'convert' => false,
            ),

            'metacafe' => array(
                'convert' => false,
            ),

            'dailymotion' => array(
                'convert' => false,
            ),

            'vine' => array(
                'convert' => false,
            ),

            'instagram' => array(
                'convert' => false,
            ),

            'google_maps' => array(
                'convert' => false,

                'showMarker' => false,
            ),

            'links' => array(
                'convert' => false,
            ),

            'pdf' => array(
                'convert' => false,

                'internal' => array(
                    'convert' => true,

                    'viewer' => 'pdfjs',
                ),

                'external' => array(
                    'convert' => false,

                    'viewer' => 'iframe',
                ),
            ),
        ),

        'lightbox' => array(
            'animationEffect' => 'zoom', // zoom, fade, zoom-in-out, false

            'animationDuration' => 330,

            'transitionEffect' => 'fade', // false, fade, slide, circular, tube, zoom-in-out, rotate

            'transitionDuration' => 330,

            'zoomOpacity' => 'auto',

            'idleTime' => 4,

            'slideClass' => '',

            'baseClass' => '',

            'loop' => false,

            'arrows' => true,

            'infobar' => true,

            'toolbar' => true,

            'buttons' => array(
                'slideShow',

                'fullScreen',

                'thumbs',

                'close',
            ),

            //'smallBtn' => 'auto',

            'keyboard' => true,

            'autoFocus' => true,

            'backFocus' => true,

            'trapFocus' => true,

            'closeClickOutside' => true,

            'protect' => false,

            'modal' => false,

            'image' => array(
                'preload' => 'auto',
            ),

            'thumbs' => array(
                'autoStart' => false,

                'hideOnClose' => true,
            ),

            'touch_enabled' => true,

            'touch' => array(
                'vertical' => true,

                'momentum' => true,
            ),

            'fullScreen' => array(
                'autoStart' => false,
            ),

            'slideShow' => array(
                'autoStart' => false,

                'speed' => 4000,
            ),
        ),

        'style' => array(
            'custom' => '',

            'zIndex' => 200000,

            'overlay_opacity' => 0.87,

            'overlay_bgcolor' => '#0f0f11',

            'thumbs_bgcolor' => '#ffffff',
        ),

        'advanced' => array(
            'deregister_3rd_plugins' => false,

            'clean_uninstall' => false,

            'custom_js' => '',

            'load_scripts_in_footer' => false,
        )
    );

    protected function __construct() {
        $this->default_settings = apply_filters( 'ari-fancybox-default-settings', $this->default_settings );

        parent::__construct();
    }

    public function sanitize( $input, $defaults = false ) {
        $default_settings = $this->get_default_options();

        $defaults = Array_Helper::to_flat_array( $default_settings );
        $input = Array_Helper::to_complex_array( parent::sanitize( $input, $defaults ) );

        return $input;
    }

    public function get_client_settings( $unique = true ) {
        $settings = null;
        $default_settings = $this->get_default_options();

        if ( $unique ) {
            $settings = array(
                'lightbox' => (object) Array_Helper::get_unique_override_parameters( $default_settings['lightbox'], $this->get_option( 'lightbox' ) ),

                'convert' => (object) Array_Helper::get_unique_override_parameters( $default_settings['convert'], $this->get_option( 'convert' ) ),
            );
        } else {
            $settings = array(
                'lightbox' => $this->get_option( 'lightbox' ),

                'convert' => $this->get_option( 'convert' ),
            );
        }

        $settings['viewers'] = array(
            'pdfjs' => array(
                'url' => ARIFANCYLIGHTBOX_URL . 'assets/pdfjs/web/viewer.html',
            )
        );

        $lightbox_settings = $settings['lightbox'];

        if (isset($lightbox_settings->closeClickOutside)) {
            $lightbox_settings->clickSlide = $lightbox_settings->clickOutside = $lightbox_settings->closeClickOutside ? 'close' : false;
            unset($lightbox_settings->closeClickOutside);
        }

        $touch_enabled = $this->get_option( 'lightbox.touch_enabled' );
        $lightbox_settings->touch = array(
            'vertical' => $touch_enabled,

            'momentum' => $touch_enabled,
        );

        if ( isset( $lightbox_settings->touch_enabled ) )
            unset( $lightbox_settings->touch_enabled );

        $lightbox_settings->buttons = $this->get_option( 'lightbox.buttons' );

        if ( Nextgen_Helper::is_installed_v2() ) {
            if ( ! isset( $settings['convert']->nextgen ) )
                $settings['convert']->nextgen = new \stdClass();

            $settings['convert']->nextgen->convert = true;
        }

        $lightbox_settings->lang = 'custom';
        $lightbox_settings->i18n = array(
            'custom' => array(
                'PREV' => __( 'Previous', 'ari-fancy-lightbox' ),

                'NEXT' => __( 'Next', 'ari-fancy-lightbox' ),

                'PLAY_START' => __( 'Start slideshow (P)', 'ari-fancy-lightbox' ),

                'PLAY_STOP' => __( 'Stop slideshow (P)', 'ari-fancy-lightbox' ),

                'FULL_SCREEN' => __( 'Full screen (F)', 'ari-fancy-lightbox' ),

                'THUMBS' => __( 'Thumbnails (G)', 'ari-fancy-lightbox' ),

                'CLOSE' => __( 'Close (Esc)', 'ari-fancy-lightbox' ),

                'ERROR' => __( 'The requested content cannot be loaded. <br/> Please try again later.', 'ari-fancy-lightbox' ),
            ),
        );

        return $settings;
    }

    public function get_custom_styles() {
        $styles = '';

        $custom_style = trim( $this->get_option( 'style.custom', '' ) );

        if ( $custom_style )
            $styles .= $custom_style;

        $zIndex = intval( $this->get_option( 'style.zIndex' ), 10 );
        $styles .= 'BODY .fancybox-container{z-index:' . $zIndex . '}';

        $overlay_opacity = floatval( $this->get_option( 'style.overlay_opacity' ) );
        $styles .= 'BODY .fancybox-is-open .fancybox-bg{opacity:' . $overlay_opacity . '}';

        $overlay_bgcolor = $this->get_option( 'style.overlay_bgcolor' );
        if ( empty( $overlay_bgcolor ) )
            $overlay_bgcolor = 'transparent';

        $styles .= 'BODY .fancybox-bg {background-color:' . $overlay_bgcolor . '}';

        $thumbs_bgcolor = $this->get_option( 'style.thumbs_bgcolor' );
        if ( empty( $thumbs_bgcolor ) )
            $thumbs_bgcolor = 'transparent';

        $styles .= 'BODY .fancybox-thumbs {background-color:' . $thumbs_bgcolor . '}';

        return $styles;
    }
}
