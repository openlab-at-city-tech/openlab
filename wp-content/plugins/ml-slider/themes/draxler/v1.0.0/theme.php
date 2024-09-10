<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Main theme file
 */
class MetaSlider_Theme_Draxler extends MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id = 'draxler';

    /**
     * Theme Version
     *
     * @var string
     */
    public $version = '1.0.0';

    public function __construct()
    {
        parent::__construct( $this->id, $this->version );

        // Customize theme design
        add_filter( 'metaslider_css', array( $this, 'theme_customize' ), 10, 3  );
    }

    /**
     * Parameters
     *
     * @var string
     */
    public $slider_parameters = array();

    /**
     * Enqueues theme specific styles and scripts
     */
    public function enqueue_assets()
    {
        wp_enqueue_style( 
            "metaslider_{$this->id}_theme_styles", 
            METASLIDER_THEMES_URL. "{$this->id}/v{$this->version}/style.css", 
            array( 'metaslider-public' ), 
            $this->version 
        );
    }

    /**
     * Add inline CSS to customize theme design
     * 
     * @since 3.91.0
     */
    public function theme_customize( $css, $settings, $slideshow_id )
    {
        // This CSS only works with Flexslider
        if ($settings['type'] !== 'flex') {
            return $css;
        }

        $new_css = "";

        if ( isset( $settings['theme_customize'] ) ) {
            $customize = $settings['theme_customize'];

            // Arrows color
            if ( isset( $customize['arrows_color'] ) ) {
                $new_css .= "
                #metaslider-id-{$slideshow_id} .flexslider .flex-direction-nav li a {
                    background-color: " . esc_html( $customize['arrows_color'] ) . "; 
                }";
            }

            // Navigation color
            if ( isset( $customize['navigation_color'] ) ) {
                $new_css .= "
                #metaslider-id-{$slideshow_id} .flexslider .flex-control-nav li a:not(.flex-active) { 
                    background: " . esc_html( $customize['navigation_color'] ) . "; 
                }";
            }

            // Caption background color
            if ( isset( $customize['caption_background'] ) ) {
                $new_css .= "
                #metaslider-id-{$slideshow_id} .flexslider .caption-wrap { 
                    background: linear-gradient(to top, rgba(0,0,0,0), " . esc_html( $customize['caption_background'] ) . "); 
                }";
            }

            // Caption text color
            if ( isset( $customize['caption_text_color'] ) ) {
                $new_css .= "
                #metaslider-id-{$slideshow_id} .flexslider .caption-wrap { 
                    color: " . esc_html( $customize['caption_text_color'] ) . "; 
                }";
            }

            // Caption link color
            if ( isset( $customize['caption_links_color'] ) ) {
                $new_css .= "
                #metaslider-id-{$slideshow_id} .flexslider .caption-wrap a { 
                    color: " . esc_html( $customize['caption_links_color'] ) . "; 
                }";
            }
        }

        return $css . $new_css;
    }
}

if ( ! isset( MetaSlider_Theme_Base::$themes['draxler'] ) ) {
    new MetaSlider_Theme_Draxler();
}
