<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Main theme file
 */
class MetaSlider_Theme_Nexus extends MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id = 'nexus';

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
        wp_enqueue_style('metaslider_{$this->id}_theme_styles', METASLIDER_THEMES_URL . $this->id . '/v1.0.0/style.css', array('metaslider-public'), '1.0.0');
        wp_enqueue_script('metaslider_{$this->id}_theme_script', METASLIDER_THEMES_URL . $this->id . '/v1.0.0/script.js', array('jquery'), '1.0.0', true);

        wp_localize_script('metaslider_{$this->id}_theme_script', 'nexusText', array(
            'buttonText' => __( 'Click Here', 'ml-slider' ),
        ));
    }
}

if ( ! isset( MetaSlider_Theme_Base::$themes['nexus'] ) ) {
    new MetaSlider_Theme_Nexus();
}
