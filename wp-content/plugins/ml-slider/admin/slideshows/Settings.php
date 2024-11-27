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
            'cropMultiply' => 1,
            'smoothHeight' => false,
            'carouselMode' => false,
            'infiniteLoop' => false,
            'carouselMargin' => 5,
            'firstSlideFadeIn' => false,
            'easing' => 'linear',
            'autoPlay' => true,
            'thumb_width' => 150,
            'thumb_height' => 100,
            'responsive_thumbs' => true,
            'thumb_min_width' => 100,
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
            'ariaLive' => false,
            'ariaCurrent' => false,
            'tabIndex' => false,
            'pausePlay' => false
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
        $checkboxes = apply_filters("metaslider_checkbox_settings", array('noConflict', 'fullWidth', 'hoverPause', 'reverse', 'random', 'printCss', 'printJs', 'smoothHeight', 'center', 'carouselMode', 'autoPlay', 'firstSlideFadeIn', 'responsive_thumbs', 'keyboard', 'touch', 'infiniteLoop',  'mobileArrows_smartphone', 'mobileArrows_tablet','mobileArrows_laptop', 'mobileArrows_desktop', 'mobileNavigation_smartphone', 'mobileNavigation_tablet', 'mobileNavigation_laptop', 'mobileNavigation_desktop', 'ariaLive', 'tabIndex', 'pausePlay','ariaCurrent'));

        foreach ($checkboxes as $checkbox) {
            $settings[$checkbox] = (isset($settings[$checkbox]) && 'on' == $settings[$checkbox]) ? 'true' : 'false';
        }

        /* Convert submitted dropdown values from 'on' or 'off' to boolean values in string format (e.g. true becomes 'true') 
         * Reason is these settings have true/false + string options, so is better to handle all as strings
         * Keep original value if is different to 'on' and 'off'. 
         * We include actual booleans in $map just in case. */
        $dropdowns = apply_filters("metaslider_dropdown_settings", array('links', 'navigation', 'smartCrop'));

        foreach ($dropdowns as $dropdown) {
            if (isset($settings[$dropdown])) {
                $map = array(
                    'on' => 'true', 
                    'off' => 'false',
                    true => 'true',
                    false => 'false'
                );
                $settings[$dropdown] = $map[$settings[$dropdown]] ?? $settings[$dropdown];
            }            
        }

        return $settings;
    }
}
