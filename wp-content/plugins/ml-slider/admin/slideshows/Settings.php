<?php

if (!defined('ABSPATH')) {
die('No direct access.');
}

/**
 * Class to handle individual slideshow settings
 */
class MetaSlider_Slideshow_Settings
{
    /**
     * Themes class
     *
     * @var object | bool
     */
    private $settings;

    /**
     * Constructor
     *
     * @param string|null $slideshow_id The settings object
     */
    public function __construct($slideshow_id = null)
    {
        $this->settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);
    }

    /**
     * Returns settings
     *
     * @return object
     */
    public function get_settings()
    {
        return $this->settings ? $this->settings : self::defaults();
    }

    /**
     * Returns a single setting
     *
     * @param string $setting A single setting name
     *
     * @return mixed|WP_error The setting result or an error object
     */
    public function get_single($setting)
    {
        return isset($this->settings[$setting]) ? $this->settings[$setting] : new WP_Error('invalid_setting', 'The setting was not found', array('status' => 404));
    }

    /**
     * Returns the default settings
     *
     * @return array
     */
    public static function defaults()
    {
        $defaults = array(
            'title' => __('New Slideshow', 'ml-slider'),
            'type' => 'flex',
            'random' => false,
            'cssClass' => '',
            'printCss' => true,
            'printJs' => true,
            'width' => 700,
            'height' => 300,
            'spw' => 7,
            'sph' => 5,
            'delay' => 3000,
            'sDelay' => 30,
            'opacity' => 0.7,
            'titleSpeed' => 500,
            'effect' => 'slide',
            'extra_effect' => 'none',
            'navigation' => true,
            'filmstrip_delay' => 7000,
            'filmstrip_animationSpeed' => 600,
            'links' => true,
            'hoverPause' => true,
            'theme' => 'none',
            'direction' => 'horizontal',
            'reverse' => false,
            'keyboard' => true,
            'touch' => true,
            'animationSpeed' => 600,
            'prevText' => __('Previous', 'ml-slider'),
            'nextText' => __('Next', 'ml-slider'),
            'slices' => 15,
            'center' => false,
            'smartCrop' => true,
            'smartCropSource' => 'slideshow',
            'imageWidth' => 400,
            'imageHeight' => 400,
            'cropMultiply' => 1,
            'smoothHeight' => false,
            'carouselMode' => false,
            'infiniteLoop' => false,
            'carouselMargin' => 5,
            'minItems' => 2,
            'forceHeight' => false,
            'firstSlideFadeIn' => false,
            'easing' => 'linear',
            'autoPlay' => true,
            'loop' => 'continuously',
            'thumb_width' => 150,
            'thumb_height' => 100,
            'responsive_thumbs' => true,
            'thumb_min_width' => 100,
            'thumb_layout' => 'grid',
            'fullWidth' => true,
            'noConflict' => true,
            'mobileArrows_smartphone' => false,
            'mobileArrows_tablet' => false,
            'mobileArrows_laptop' => false,
            'mobileArrows_desktop' => false,
            'mobileNavigation_smartphone' => false,
            'mobileNavigation_tablet' => false,
            'mobileNavigation_laptop' => false,
            'mobileNavigation_desktop' => false,
            'mobileSlideshow_smartphone' => false,
            'mobileSlideshow_tablet' => false,
            'mobileSlideshow_laptop' => false,
            'mobileSlideshow_desktop' => false,
            'ariaLive' => true,
            'ariaCurrent' => true,
            'tabIndex' => true,
            'pausePlay' => false,
            'progressBar' => false,
            'loading' => false,
            'lazyLoad' => false,
            'playText' => '',
            'pauseText' => '',
            'container' => false,
            'container_background' => 'rgba(255,255,255,0)',
            'containerPadding_top' => 10,
            'containerPadding_right' => 10,
            'containerPadding_bottom' => 10,
            'containerPadding_left' => 10,
            'containerMargin_top' => 10,
            'containerMargin_bottom' => 30
        );
        $defaults = apply_filters('metaslider_default_parameters', $defaults);
        $overrides = get_option('metaslider_default_settings');
        return is_array($overrides) ? array_merge($defaults, $overrides) : $defaults;
    }

    /**
     * Convert 'on' or 'off' to boolean values
     * 
     * @since 3.92
     * 
     * @param array $settings Slideshow settings
     * 
     * @return array
     */
    public static function adjust_settings($settings)
    {
        // Convert submitted checkbox values from 'on' or 'off' to boolean values in string format (e.g. true becomes 'true')
        $checkboxes = array('noConflict', 'fullWidth', 'hoverPause', 'reverse', 'printCss', 'printJs', 'smoothHeight', 'center', 'carouselMode', 'autoPlay', 'firstSlideFadeIn', 'responsive_thumbs', 'keyboard', 'touch', 'infiniteLoop',  'mobileArrows_smartphone', 'mobileArrows_tablet','mobileArrows_laptop', 'mobileArrows_desktop', 'mobileNavigation_smartphone', 'mobileNavigation_tablet', 'mobileNavigation_laptop', 'mobileNavigation_desktop', 'mobileSlideshow_smartphone', 'mobileSlideshow_tablet', 'mobileSlideshow_laptop', 'mobileSlideshow_desktop', 'ariaLive', 'tabIndex', 'pausePlay', 'showPlayText', 'ariaCurrent', 'progressBar', 'loading', 'lazyLoad', 'forceHeight', 'lightbox', 'container');

        foreach ($checkboxes as $checkbox) {
            $settings[$checkbox] = (isset($settings[$checkbox]) && 'on' == $settings[$checkbox]) ? 'true' : 'false';
        }

        /* Convert submitted dropdown values from 'on' or 'off' to boolean values in string format (e.g. true becomes 'true') and sanitize the rest
         * Reason is these settings have true/false + string options, so is better to handle all as strings
         * Keep original value if is different to 'on' and 'off'. 
         * We include actual booleans in $map just in case. */
        $dropdowns = array('effect', 'cropMultiply', 'direction', 'easing', 'links', 'navigation', 'smartCrop', 'random', 'loop', 'layer_scaling');

        foreach ($dropdowns as $dropdown) {
            if (isset($settings[$dropdown])) {
                $map = array(
                    'on' => 'true', 
                    'off' => 'false',
                    true => 'true',
                    false => 'false'
                );
                $settings[$dropdown] = $map[$settings[$dropdown]] ?? sanitize_text_field($settings[$dropdown]);
            }            
        }

        // Sanitize text fields
        $texts = array('cssClass', 'nextText', 'prevText', 'playText', 'pauseText');

        foreach ($texts as $text) {
            if (isset($settings[$text])) {
                $settings[$text] = sanitize_text_field($settings[$text]);
            }
        }

        return $settings;
    }
}
