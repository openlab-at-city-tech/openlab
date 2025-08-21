<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Main theme file
 */
class MetaSlider_Theme_Simply_Dark extends MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id = 'simply-dark';

    /**
     * Theme Version
     *
     * @var string
     */
    public $version = '1.0.0';

    public function __construct()
    {
        parent::__construct($this->id, $this->version);
        add_filter('metaslider_flex_slider_parameters', array($this, 'update_parameters_arrows'), 10, 2);
        add_filter('metaslider_responsive_slider_parameters', array($this, 'update_parameters_arrows'), 10, 2);
        add_filter('metaslider_nivo_slider_parameters', array($this, 'update_parameters_arrows'), 10, 2);
        add_filter('metaslider_coin_slider_parameters', array($this, 'update_parameters_arrows'), 10, 2);
    }

    /**
     * Enqueues theme specific styles and scripts
     */
    public function enqueue_assets()
    {
        wp_enqueue_style( 
            "metaslider_{$this->id}_theme_styles", 
            METASLIDER_THEMES_URL. "{$this->id}/v{$this->version}/style.css", 
            array('metaslider-public'), 
            $this->version 
        );
    }

    /**
     * Parameters
     *
     * @param array  $options - The flexslider options
     * @param string $id      - the id of the slideshow
     *
     * @return array
     */
    public function update_parameters_arrows($options, $id)
    {
        $options['prevText'] = "'<svg aria-labelledBy=\'simply-dark-prev-title\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 256 512\' data-fa-i2svg=\'\'><title id=\'simply-dark-prev-title\'>" . __('Previous Slide', 'ml-slider') . "</title><path fill=\'currentColor\' d=\'M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z\'></path></svg>'";
        $options['nextText'] = "'<svg aria-labelledBy=\'simply-dark-next-title\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 256 512\' data-fa-i2svg=\'\'><title id=\'simply-dark-next-title\'>" . __('Next Slide', 'ml-slider') . "</title><path fill=\'currentColor\' d=\'M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z\'></path></svg>'";
    
        return $options;
    }
}

if (! isset(MetaSlider_Theme_Simply_Dark::$themes['simply-dark'])) {
    new MetaSlider_Theme_Simply_Dark();
}
