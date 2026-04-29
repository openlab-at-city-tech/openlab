<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Main theme file
 */
class MetaSlider_Theme_Cascade extends MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id = 'cascade';

    /**
     * Theme Version
     *
     * @var string
     */
    public $version = '1.0.0';

    public function __construct()
    {
        parent::__construct( $this->id, $this->version );
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
            METASLIDER_VERSION 
        );
    }
}

if ( ! isset( MetaSlider_Theme_Base::$themes['cascade'] ) ) {
    new MetaSlider_Theme_Cascade();
}
