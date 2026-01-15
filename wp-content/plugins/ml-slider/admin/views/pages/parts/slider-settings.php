<?php
if (!defined('ABSPATH')) {
    die('No direct access.');
}

// Slider libraries
$aFields = array(
    'type' => array(
        'priority' => 0,
        'type' => 'slider-lib',
        'value' => $this->slider->get_setting('type'),
        'options' => array(
            'flex' => array('label' => "FlexSlider"),
            'responsive' => array('label' => "R. Slides"),
            'nivo' => array('label' => "Nivo Slider"),
            'coin' => array('label' => "Coin Slider")
        ),
        'is_legacy' => true
    )
);

$aFields = apply_filters('metaslider_slider_libraries_settings', $aFields, $this->slider);

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $this->build_settings_rows($aFields);
?>
<div class="ms-loading-settings flex mb-3" style="min-height:500px;">
    <span style="background-image: url(<?php echo esc_url(admin_url( '/images/loading.gif' )); ?>);">
        <?php _e( 'Loading...', 'ml-slider' ); ?>
    </span>
</div>
<div class="ms-settings-table" style="display:none;">
    <div class="ms-settings-box mainOptions ms-on">
        <div class="ms-highlight border-t-0">
            <?php esc_html_e( 'Main Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            $aFields = array(
                'width' => array(
                    'priority' => 10,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 9999,
                    'step' => 1,
                    'value' => $this->slider->get_setting('width'),
                    'label' => __("Width", "ml-slider"),
                    'class' => 'coin flex responsive nivo',
                    'helptext' => __("Slideshow width", "ml-slider"),
                    'after' => __("px", "ml-slider")
                ),
                'height' => array(
                    'priority' => 20,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 9999,
                    'step' => 1,
                    'value' => $this->slider->get_setting('height'),
                    'label' => __("Height", "ml-slider"),
                    'class' => 'coin flex responsive nivo',
                    'helptext' => __("Slideshow height", "ml-slider"),
                    'after' => __("px", "ml-slider")
                ),
                'links' => array(
                    'priority' => 50,
                    'type' => 'select',
                    'label' => __("Arrows", "ml-slider"),
                    'class' => 'option coin flex nivo responsive',
                    'value' => $this->slider->get_setting('links'),
                    'helptext' => __(
                        "Show the Previous / Next arrows.",
                        "ml-slider"
                    ),
                    'options' => array(
                        'false' => array(
                            'label' => __("Hidden", "ml-slider")
                        ),
                        'true' => array(
                            'label' => __("Visible", "ml-slider")
                        ),
                        'onhover' => array(
                            'label' => __("Visible On Hover", "ml-slider")
                        )
                    ),
                    'dependencies' => array(
                        array(
                            'show' => 'mobileArrows_smartphone',
                            'when' => array(
                                'true',
                                'onhover'
                            )
                        )
                    )
                ),
                'navigation' => array(
                    'priority' => 60,
                    'type' => 'navigation',
                    'label' => __("Navigation", "ml-slider"),
                    'class' => 'option coin flex nivo responsive inline-block',
                    'value' => $this->slider->get_setting('navigation'),
                    'helptext' => __(
                        "Show navigation options so that users can browse the slides.",
                        "ml-slider"
                    ),
                    'options' => array(
                        'false' => array(
                            'label' => __("Hidden", "ml-slider")
                        ),
                        'true' => array(
                            'label' => __("Dots", "ml-slider")
                        ),
                        'dots_onhover' => array(
                            'label' => __("Dots - Visible On Hover", "ml-slider")
                        ),
                        'thumbs' => array(
                            'label' => __("Thumbnails (Pro)", "ml-slider"),
                            'addon_required' => true
                        ),
                        'thumbs_onhover' => array(
                            'label' => __("Thumbnails - Visible On Hover (Pro)", "ml-slider"),
                            'addon_required' => true
                        ),
                        'filmstrip' => array(
                            'label' => __("Filmstrip (Pro)", "ml-slider"),
                            'addon_required' => true
                        ),
                        'filmstrip_onhover' => array(
                            'label' => __("Filmstrip - Visible On Hover (Pro)", "ml-slider"),
                            'addon_required' => true
                        ),
                    ),
                    'dependencies' => array(
                        array(
                            'show' => 'mobileNavigation_smartphone',
                            'when' => array(
                                'true',
                                'dots_onhover',
                                'thumbs', 
                                'filmstrip'
                            )
                        ),
                        array(
                            'show' => 'ariaCurrent',
                            'when' => array(
                                'true',
                                'thumbs', 
                                'filmstrip'
                            )
                        ),
                    ),
                    'after' => metaslider_upgrade_pro_small_btn()
                ),
                'fullWidth' => array(
                    'priority' => 70,
                    'type' => 'checkbox',
                    'label' => esc_html__("100% Width", "ml-slider"),
                    'class' => 'option flex nivo responsive',
                    'checked' => $this->slider->get_setting(
                        'fullWidth'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        'If the space for the slideshow is larger than the "Width" setting, the slideshow output will expand to fill all of that space.',
                        "ml-slider"
                    )
                ),
            );

            $aFields = apply_filters('metaslider_basic_settings', $aFields, $this->slider);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>                                    
    </div>
    <div class="ms-settings-box themeOptions ms-on">
        <div class="ms-highlight">
            <?php esc_html_e( 'Theme', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <tr>
                <td>
                    <metaslider-theme-viewer
                        theme-directory-url="<?php
                        echo esc_url(METASLIDER_THEMES_URL); ?>"
                    ></metaslider-theme-viewer>
                </td>
            </tr>
        </table>
    </div>
    <div class="ms-settings-box transitionOptions ms-on">
        <div class="ms-highlight">
            <?php esc_html_e( 'Transition Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            $aFields = array(
                'effect' => array(
                    'priority' => 10,
                    'type' => 'select',
                    'value' => $this->slider->get_setting('effect'),
                    'label' => __("Transition Effect", "ml-slider"),
                    'class' => 'effect coin flex responsive nivo width w-40',
                    'helptext' => __("This animation is used when changing slides.", "ml-slider"),
                    'dependencies' => array(
                        array(
                            'show' => 'easing', // Show Easing setting
                            'when' => 'slide' // When Effect is 'slide'
                        ),
                        array(
                            'show' => 'firstSlideFadeIn',
                            'when' => 'fade'
                        )
                    ),
                    'options' => array(
                        'fade' => array(
                            'class' => 'option nivo flex responsive',
                            'label' => __("Fade", "ml-slider")
                        ),
                        'slide' => array(
                            'class' => 'option flex',
                            'label' => __("Slide", "ml-slider")
                        ),
                        'zooming' => array(
                            'class' => 'option flex',
                            'label' => __("Zooming", "ml-slider")
                        ),
                        'flip' => array(
                            'class' => 'option flex',
                            'label' => __("Flip", "ml-slider")
                        ),
                        'random' => array(
                            'class' => 'option coin nivo',
                            'label' => __("Random", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'swirl' => array(
                            'class' => 'option coin',
                            'label' => __("Swirl", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'rain' => array(
                            'class' => 'option coin',
                            'label' => __("Rain", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'straight' => array(
                            'class' => 'option coin',
                            'label' => __("Straight", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'sliceDown' => array(
                            'class' => 'option nivo',
                            'label' => __("Slice Down", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'sliceUp' => array(
                            'class' => 'option nivo',
                            'label' => __("Slice Up", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'sliceUpLeft' => array(
                            'class' => 'option nivo',
                            'label' => __("Slice Up Left", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'sliceUpDown' => array(
                            'class' => 'option nivo',
                            'label' => __("Slide Up Down", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'sliceUpDownLeft' => array(
                            'class' => 'option nivo',
                            'label' => __("Slice Up Down Left", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'fold' => array(
                            'class' => 'option nivo',
                            'label' => __("Fold", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'slideInRight' => array(
                            'class' => 'option nivo',
                            'label' => __("Slide in Right", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'slideInLeft' => array(
                            'class' => 'option nivo',
                            'label' => __("Slide in Left", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'boxRandom' => array(
                            'class' => 'option nivo',
                            'label' => __("Box Random", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'boxRain' => array(
                            'class' => 'option nivo',
                            'label' => __("Box Rain", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'boxRainReverse' => array(
                            'class' => 'option nivo',
                            'label' => __("Box Rain Reverse", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'boxRainGrow' => array(
                            'class' => 'option nivo',
                            'label' => __("Box Rain Grow", "ml-slider"),
                            'is_legacy' => true
                        ),
                        'boxRainGrowReverse' => array(
                            'class' => 'option nivo',
                            'label' => __("Box Rain Grow Reverse", "ml-slider"),
                            'is_legacy' => true
                        )
                    ),
                ),
                'delay' => array(
                    'priority' => 20,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 500,
                    'max' => 10000,
                    'step' => 100,
                    'value' => $this->slider->get_setting('delay'),
                    'label' => esc_html__("Slide Delay", "ml-slider"),
                    'class' => 'option coin flex responsive nivo',
                    'helptext' => esc_html__(
                        "How long to display each slide, in milliseconds.",
                        "ml-slider"
                    ),
                    'after' => esc_html_x(
                        "ms",
                        "Short for milliseconds",
                        "ml-slider"
                    )
                ),
                'animationSpeed' => array(
                    'priority' => 30,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 2000,
                    'step' => 100,
                    'value' => $this->slider->get_setting('animationSpeed'),
                    'label' => esc_html__("Transition Speed", "ml-slider"),
                    'class' => 'option flex responsive nivo',
                    'helptext' => esc_html__(
                        'Choose the speed of the animation in milliseconds. You can select the animation in the "Transition Effect" field.',
                        'ml-slider'
                    ),
                    'after' => esc_html_x(
                        "ms",
                        "Short for milliseconds",
                        "ml-slider"
                    )
                ),
                'slices' => array(
                    'priority' => 40,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 20,
                    'step' => 1,
                    'value' => $this->slider->get_setting('slices'),
                    'label' => esc_html__("Number of Slices", "ml-slider"),
                    'class' => 'option nivo',
                    'helptext' => esc_html__("Number of Slices", "ml-slider"),
                    'is_legacy' => true
                ),
                'spw' => array(
                    'priority' => 50,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 20,
                    'step' => 1,
                    'value' => $this->slider->get_setting('spw'),
                    'label' => esc_html__(
                            "Number of Squares",
                            "ml-slider"
                        ) . " (" . esc_html__("Width", "ml-slider") . ")",
                    'class' => 'option nivo',
                    'helptext' => esc_html__("Number of squares", "ml-slider"),
                    'after' => '',
                    'is_legacy' => true
                ),
                'sph' => array(
                    'priority' => 60,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 20,
                    'step' => 1,
                    'value' => $this->slider->get_setting('sph'),
                    'label' => esc_html__(
                            "Number of Squares",
                            "ml-slider"
                        ) . " (" . esc_html__("Height", "ml-slider") . ")",
                    'class' => 'option nivo',
                    'helptext' => esc_html__("Number of squares", "ml-slider"),
                    'after' => '',
                    'is_legacy' => true
                ),
                'direction' => array(
                    'priority' => 70,
                    'type' => 'select',
                    'label' => esc_html__("Slide Direction", "ml-slider"),
                    'class' => 'option flex',
                    'helptext' => esc_html__(
                        'Select the direction that slides will move. Vertical will not work if "Carousel mode" is enabled or "Transition Effect" is set to "Fade".',
                        'ml-slider'
                    ),
                    'value' => $this->slider->get_setting('direction'),
                    'options' => array(
                        'horizontal' => array(
                            'label' => esc_html__(
                                "Horizontal",
                                "ml-slider"
                            ),
                            'class' => ''
                        ),
                        'vertical' => array(
                            'label' => esc_html__(
                                "Vertical",
                                "ml-slider"
                            ),
                            'class' => ''
                        ),
                    )
                ),
                'reverse' => array(
                    'priority' => 70,
                    'type' => 'checkbox',
                    'label' => esc_html__("Reverse", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'reverse'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Reverse the slide direction.",
                        "ml-slider"
                    )
                ),
                'easing' => array(
                    'priority' => 80,
                    'type' => 'select',
                    'label' => esc_html__("Image Animation", "ml-slider"),
                    'class' => 'option flex',
                    'helptext' => esc_html__(
                        'This feature adds gradual acceleration and deceleration to slide transitions, rather than abrupt starts and stops. This feature only works with the "Slide" Transition Effect.',
                        "ml-slider"
                    ),
                    'value' => $this->slider->get_setting('easing'),
                    'options' => $this->get_easing_options()
                ),
                'extra_effect' => array( // Don't target 'extra_effect' to show/hide with 'dependencies' array key
                    'priority' => 81,
                    'type' => 'select',
                    'label' => __( 'Extra Effect', 'ml-slider' ),
                    'class' => 'option flex inline-block',
                    'helptext' => __( 'Extra effect for slides.', 'ml-slider' ),
                    'value' => $this->slider->get_setting( 'extra_effect' ),
                    'options' => array(
                        'none' => array( 
                            'label' => __( 'None', 'ml-slider' )
                        ),
                        'kenburns' => array( 
                            'label' => __( 'Ken Burns (Pro)', 'ml-slider' ),
                            'addon_required' => true
                        )
                    ),
                    'after' => metaslider_upgrade_pro_small_btn()
                ),
                'firstSlideFadeIn' => array(
                    'priority' => 90,
                    'type' => 'checkbox',
                    'label' => esc_html__("Fade In", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => 'true' == $this->slider->get_setting(
                        'firstSlideFadeIn'
                    ) ? 'checked' : '',
                    'helptext' => esc_html__(
                        'This adds an animation when the slideshow loads. It only uses the "Fade" transition effect.',
                        "ml-slider"
                    ),
                )
            );

            // Transition effects options
            $aFields = apply_filters(
                'metaslider_transition_settings',
                $aFields,
                $this->slider
            );
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>
    </div>
    <div class="ms-settings-box carouselOptions ms-on">
        <div class="ms-highlight">
            <?php esc_html_e( 'Carousel Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            $aFields = array(
                'carouselMode' => array(
                    'priority' => 10,
                    'type' => 'checkbox',
                    'label' => esc_html__("Carousel Mode", "ml-slider"),
                    'class' => 'option flex showNextWhenChecked',
                    'checked' => $this->slider->get_setting(
                        'carouselMode'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Display multiple slides at once. The slideshow output will default to using 100% width and the 'Slide' Transition Effect.",
                        "ml-slider"
                    ),
                    'dependencies' => array(
                        array(
                            'show' => 'infiniteLoop', // Show Infinite loop
                            'when' => true // When carouselMode is true
                        ),
                        array(
                            'show' => 'loop', // Show Loop
                            'when' => false // When carouselMode is false
                        ),
                        array(
                            'show' => 'carouselMargin',
                            'when' => true
                        ),
                        array(
                            'show' => 'minItems', // Show Carousel items
                            'when' => true // When carouselMode is true
                        ),
                        array(
                            'show' => 'forceHeight', // Show Force height
                            'when' => true // When carouselMode is true
                        )
                    )
                ),
                'infiniteLoop' => array(
                    'priority' => 20,
                    'type' => 'checkbox',
                    'label' => esc_html__("Loop Carousel Continuously", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'infiniteLoop'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Infinite loop of slides when Carousel Mode is enabled. This option disables arrows and navigation. The slideshow width must be less than or equal to the width of the container where it is inserted.",
                        "ml-slider"
                    )
                ),
                'carouselMargin' => array(
                    'priority' => 30,
                    'min' => 0,
                    'max' => 9999,
                    'step' => 1,
                    'type' => 'number',
                    'label' => esc_html__("Carousel Margin", "ml-slider"),
                    'class' => 'option flex',
                    'value' => $this->slider->get_setting('carouselMargin'),
                    'helptext' => esc_html__(
                        "Pixel margin between slides in carousel.",
                        "ml-slider"
                    ),
                    'after' => esc_html__("px", "ml-slider")
                ),
                'minItems' => array(
                    'priority' => 40,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 1,
                    'max' => 99,
                    'step' => 1,
                    'value' => $this->slider->get_setting('minItems'),
                    'label' => esc_html__("Carousel Items", "ml-slider"),
                    'class' => 'flex',
                    'helptext' => esc_html__(
                        "Minimum number of slides to be displayed at once in the carousel.",
                        "ml-slider"
                    ),
                    'after' => ''
                ),
                'forceHeight' => array(
                    'priority' => 50,
                    'type' => 'checkbox',
                    'label' => esc_html__("Force Height", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'forceHeight'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "If the slideshow looks small, force slideshow height when using Carousel mode. Please note when is enabled slides may look cropped.",
                        "ml-slider"
                    )
                )
            );

            $aFields = apply_filters(
                'metaslider_carousel_settings',
                $aFields,
                $this->slider
            );

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>
    </div>
    <?php
    // Device options
    if ( !isset( $global_settings['mobileSettings'] ) 
        || ( isset( $global_settings['mobileSettings'] ) && true == $global_settings['mobileSettings'] )  
    ) {
        $default_settings = get_site_option( 'metaslider_default_settings' );
        $breakpoints      = array(
            'smartphone' => isset( $default_settings['smartphone'] ) ? (int) $default_settings['smartphone'] : 320,
            'tablet'     => isset( $default_settings['tablet'] ) ? (int) $default_settings['tablet'] : 768,
            'laptop'     => isset( $default_settings['laptop'] ) ? (int) $default_settings['laptop'] : 1024,
            'desktop'    => isset( $default_settings['desktop'] ) ? (int) $default_settings['desktop'] : 1440
        );
        ?>
        <div class="ms-settings-box mobileOptions ms-off">
            <div class="ms-highlight">
                <?php esc_html_e( 'Device Options', 'ml-slider' ) ?>
                <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
            </div>
            <table class="ms-settings-box-inner">
                <?php
                $aFields = array(
                    'mobileArrows' => array(
                        'priority' => 1,
                        'type' => 'mobile',
                        'label' => __("Hide Arrows On", "ml-slider"),
                        'options' => array(
                            'smartphone' => array(
                                'checked' => $this->slider->get_setting('mobileArrows_smartphone') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the arrows on screen widths less than %spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['tablet'] 
                                )
                            ),
                            'tablet' => array(
                                'checked' => $this->slider->get_setting('mobileArrows_tablet') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the arrows on screen widths of %1$spx to %2$spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['tablet'],
                                    $breakpoints['laptop'] - 1
                                )
                            ),
                            'laptop' => array(
                                'checked' => $this->slider->get_setting('mobileArrows_laptop') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the arrows on screen widths of %1$spx to %2$spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['laptop'],
                                    $breakpoints['desktop'] - 1
                                )
                            ),
                            'desktop' => array(
                                'checked' => $this->slider->get_setting('mobileArrows_desktop') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the arrows on screen widths equal to or greater than %spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['desktop'] 
                                )
                            ),
                        )
                    ),
                    'mobileNavigation' => array(
                        'priority' => 2,
                        'type' => 'mobile',
                        'label' => __("Hide Navigation On", "ml-slider"),
                        'options' => array(
                            'smartphone' => array(
                                'checked' => $this->slider->get_setting('mobileNavigation_smartphone') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the navigation on screen widths less than %spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['tablet'] 
                                )
                            ),
                            'tablet' => array(
                                'checked' => $this->slider->get_setting('mobileNavigation_tablet') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the navigation on screen widths of %1$spx to %2$spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['tablet'],
                                    $breakpoints['laptop'] - 1
                                )
                            ),
                            'laptop' => array(
                                'checked' => $this->slider->get_setting('mobileNavigation_laptop') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the navigation on screen widths of %1$spx to %2$spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['laptop'],
                                    $breakpoints['desktop'] - 1
                                )
                            ),
                            'desktop' => array(
                                'checked' => $this->slider->get_setting('mobileNavigation_desktop') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the navigation on screen widths equal to or greater than %spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['desktop'] 
                                )
                            ),
                        )
                    ),
                    'mobileSlideshow' => array(
                        'priority' => 3,
                        'type' => 'mobile',
                        'label' => __("Hide Slideshow On", "ml-slider"),
                        'options' => array(
                            'smartphone' => array(
                                'checked' => $this->slider->get_setting('mobileSlideshow_smartphone') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the slideshow on screen widths less than %spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['tablet'] 
                                )
                            ),
                            'tablet' => array(
                                'checked' => $this->slider->get_setting('mobileSlideshow_tablet') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the slideshow on screen widths of %1$spx to %2$spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['tablet'],
                                    $breakpoints['laptop'] - 1
                                )
                            ),
                            'laptop' => array(
                                'checked' => $this->slider->get_setting('mobileSlideshow_laptop') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the slideshow on screen widths of %1$spx to %2$spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['laptop'],
                                    $breakpoints['desktop'] - 1
                                )
                            ),
                            'desktop' => array(
                                'checked' => $this->slider->get_setting('mobileSlideshow_desktop') == 'true' ? 'checked' : '',
                                'helptext' => sprintf( 
                                    __( 
                                        'When enabled this setting will hide the slideshow on screen widths equal to or greater than %spx.', 
                                        'ml-slider'
                                    ), 
                                    $breakpoints['desktop'] 
                                )
                            ),
                        )
                    ),
                );

                $aFields = apply_filters('metaslider_mobile_settings', $aFields, $this->slider);
                
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $this->build_settings_rows($aFields);
                ?>
            </table>
        </div>
    <?php 
    }
    ?>
    <div class="ms-settings-box advancedOptions ms-off">
        <div class="ms-highlight highlight">
            <?php esc_html_e( 'Advanced Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            // Advanced options
            $aFields = array(
                'center' => array(
                    'priority' => 10,
                    'type' => 'checkbox',
                    'label' => esc_html__("Center Align", "ml-slider"),
                    'class' => 'option coin flex nivo responsive',
                    'checked' => $this->slider->get_setting(
                        'center'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Center align the slideshow in the available space on your website.",
                        "ml-slider"
                    )
                ),
                'autoPlay' => array( // Don't target 'autoPlay' to show/hide with 'dependencies' array key
                    'priority' => 20,
                    'type' => 'checkbox',
                    'label' => esc_html__("Auto Play", "ml-slider"),
                    'class' => 'option flex nivo responsive coin',
                    'checked' => 'true' == $this->slider->get_setting(
                        'autoPlay'
                    ) ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Transition between slides automatically.",
                        "ml-slider"
                    )
                ),
                'pausePlay' => array( // Don't target 'pausePlay' to show/hide with 'dependencies' array key
                    'priority' => 21,
                    'type' => 'checkbox',
                    'label' => esc_html__("Play / Pause Button", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'pausePlay'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "This allows user to pause or resume Auto Play on the slideshow.",
                        "ml-slider"
                    )
                ),
                'showPlayText' => array(
                    'priority' => 22,
                    'type' => 'checkbox',
                    'label' => esc_html__("Show Play / Pause Button Text", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'showPlayText'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Show text options for Play / Pause Button",
                        "ml-slider"
                    )
                ),
                'playText' => array(
                    'priority' => 23,
                    'type' => 'text',
                    'label' => esc_html__("Play Text", "ml-slider"),
                    'class' => 'option flex',
                    'helptext' => esc_html__(
                        "Enter text for the Play/Pause Button. Leave the field empty to use the icon only.",
                        "ml-slider"
                    ),
                    'value' => $this->slider->get_setting(
                        'playText'
                    ) == 'false' ? '' : $this->slider->get_setting('playText'),
                ),
                'pauseText' => array(
                    'priority' => 24,
                    'type' => 'text',
                    'label' => esc_html__("Pause Text", "ml-slider"),
                    'class' => 'option flex',
                    'helptext' => esc_html__(
                        "Enter text for the Play/Pause Button. Leave the field empty to use the icon only.",
                        "ml-slider"
                    ),
                    'value' => $this->slider->get_setting(
                        'pauseText'
                    ) == 'false' ? '' : $this->slider->get_setting('pauseText')
                ),
                'hoverPause' => array(
                    'priority' => 25,
                    'type' => 'checkbox',
                    'label' => esc_html__("Hover Pause", "ml-slider"),
                    'class' => 'option coin flex nivo responsive',
                    'checked' => $this->slider->get_setting(
                        'hoverPause'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Pause the slideshow when hovering over slider, then resume when no longer hovering.",
                        "ml-slider"
                    )
                ),
                'loop' => array(
                    'priority' => 26,
                    'type' => 'select',
                    'label' => __("Loop", "ml-slider"),
                    'class' => 'option flex nivo',
                    'helptext' => __('If you choose "Loop endlessly", the slides will loop infinitely. If you choose "Stop on first slide after looping", the slideshow will stop on the first slide after showing all the items. If you choose "Stop on last slide", the slides will stop on the last slide.', 'ml-slider'),
                    'value' => $this->slider->get_setting('loop'),
                    'options' => array(
                        'continuously' => array('label' => __("Loop Endlessly", "ml-slider"), 'class' => ''),
                        'stopOnLast' => array('label' => __("Stop On Last Slide", "ml-slider"), 'class' => ''),
                        'stopOnFirst' => array('label' => __("Stop On First Slide After Looping", "ml-slider"), 'class' => ''),
                    )
                ),
                'smartCrop' => array(
                    'priority' => 30,
                    'type' => 'select',
                    'label' => esc_html__("Image Crop", "ml-slider"),
                    'class' => 'option coin flex nivo responsive',
                    'value' => $this->slider->get_setting('smartCrop'),
                    'options' => array(
                        'true' => array(
                            'label' => esc_html__(
                                "Smart Crop",
                                "ml-slider"
                            ),
                            'class' => ''
                        ),
                        'false' => array(
                            'label' => esc_html__(
                                "Standard",
                                "ml-slider"
                            ),
                            'class' => ''
                        ),
                        'disabled' => array(
                            'label' => esc_html__(
                                "Disabled",
                                "ml-slider"
                            ),
                            'class' => ''
                        ),
                        'disabled_pad' => array(
                            'label' => esc_html__(
                                "Disabled (Smart Pad)",
                                "ml-slider"
                            ),
                            'class' => 'option flex'
                        ),
                    ),
                    'helptext' => esc_html__(
                        "Smart Crop ensures your responsive slides are cropped to a ratio that results in a consistent slideshow size.",
                        "ml-slider"
                    ),
                    'dependencies' => array(
                        array(
                            'show' => 'cropMultiply', // Show Image double size
                            'when' => array( // When Image crop is 'true' or 'false'
                                'true',
                                'false'
                            )
                        )
                    )
                ),
                'smartCropSource' => array(
                    'priority' => 31,
                    'type' => 'select',
                    'label' => __('Image Crop Source', 'ml-slider'),
                    'class' => 'option flex inline-block',
                    'value' => $this->slider->get_setting( 'smartCropSource' ),
                    'options' => array(
                        'slideshow' => array( 
                            'label' => __('Slideshow width/height', 'ml-slider' ) 
                        ),
                        'image' => array( 
                            'label' => __('Custom width/height (Pro)', 'ml-slider' ),
                            'addon_required' => true
                        )
                    ),
                    'helptext' => __(
                        'By default, MetaSlider will crop images using the main width and height of the slideshow. If you want smaller images, select Custom width/height and make sure the values are less than the main width and height.',
                        'ml-slider'
                    ),
                    'after' => metaslider_upgrade_pro_small_btn()
                ),
                'cropMultiply' => array(
                    'priority' => 34,
                    'type' => 'select',
                    'label' => __("Image Crop Size", "ml-slider"),
                    'class' => 'option flex',
                    'value' => $this->slider->get_setting('cropMultiply'),
                    'options' => array(
                        1 => array('label' => '1x'),
                        2 => array('label' => '2x'),
                        3 => array('label' => '3x'),
                        4 => array('label' => '4x')
                    ),
                    'helptext' => __(
                        "This will increase the size of the images in your slideshow. Larger images are higher quality. Smaller images load more quickly.",
                        "ml-slider"
                    ),
                    'extra_attrs' => array(
                        'data-value' => $this->slider->get_setting('cropMultiply')
                    )
                ),
                'smoothHeight' => array(
                    'priority' => 35,
                    'type' => 'checkbox',
                    'label' => __("Smooth Height", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'smoothHeight'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => __(
                        "Allow navigation to follow the slide's height smoothly.",
                        "ml-slider"
                    )
                ),
                'random' => array(
                    'priority' => 50,
                    'type' => 'select',
                    'label' => esc_html__("Slide Order", "ml-slider"),
                    'class' => 'option coin flex nivo responsive',
                    'value' => $this->slider->get_setting('random'),
                    'options' => array(
                        'newest' => array(
                            'label' => __("Newest First", "ml-slider")
                        ),
                        'oldest' => array(
                            'label' => __("Oldest First", "ml-slider")
                        ),
                        'false' => array(
                            'label' => __("Drag-and-drop", "ml-slider")
                        ),
                        'true' => array(
                            'label' => __("Random", "ml-slider")
                        ) 
                    ),
                    'helptext' => esc_html__(
                        "Select the order in which slides appear in the slideshow. This impacts the frontend view.",
                        "ml-slider"
                    )
                ),
                'touch' => array(
                    'priority' => 80,
                    'type' => 'checkbox',
                    'label' => esc_html__("Touch Swipe", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => 'true' == $this->slider->get_setting(
                        'touch'
                    ) ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Allow touch swipe navigation of the slider on touch-enabled devices.",
                        "ml-slider"
                    )
                ),
                'progressBar' => array( // Don't target 'progressBar' to show/hide with 'dependencies' array key
                    'priority' => 84,
                    'type' => 'checkbox',
                    'label' => esc_html__("Progress Bar", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'progressBar'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Displays a visual indicator showing the time left before the next slide.",
                        "ml-slider"
                    )
                ),
                'loading' => array(
                    'priority' => 85,
                    'type' => 'checkbox',
                    'label' => esc_html__("Loading Indicator", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'loading'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Displays a visual indicator while the slideshow is loading.",
                        "ml-slider"
                    )
                ),
                'prevText' => array(
                    'priority' => 86,
                    'type' => 'text',
                    'label' => esc_html__("Previous Text", "ml-slider"),
                    'class' => 'option coin flex responsive nivo',
                    'helptext' => esc_html__(
                        'Set the text for the "previous" direction item.',
                        "ml-slider"
                    ),
                    'value' => $this->slider->get_setting(
                        'prevText'
                    ) == 'false' ? '' : $this->slider->get_setting('prevText'),
                    'is_legacy' => true
                ),
                'nextText' => array(
                    'priority' => 87,
                    'type' => 'text',
                    'label' => esc_html__("Next Text", "ml-slider"),
                    'class' => 'option coin flex responsive nivo',
                    'helptext' => esc_html__(
                        'Set the text for the "next" direction item.',
                        "ml-slider"
                    ),
                    'value' => $this->slider->get_setting(
                        'nextText'
                    ) == 'false' ? '' : $this->slider->get_setting('nextText'),
                    'is_legacy' => true
                ),
                'sDelay' => array(
                    'priority' => 88,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 500,
                    'step' => 10,
                    'value' => $this->slider->get_setting('sDelay'),
                    'label' => esc_html__("Square Delay", "ml-slider"),
                    'class' => 'option coin',
                    'helptext' => esc_html__(
                        "Delay between squares in ms.",
                        "ml-slider"
                    ),
                    'after' => esc_html_x(
                        "ms",
                        "Short for milliseconds",
                        "ml-slider"
                    ),
                    'is_legacy' => true
                ),
                'opacity' => array(
                    'priority' => 89,
                    'type' => 'text',
                    'value' => $this->slider->get_setting('opacity'),
                    'label' => esc_html__("Opacity", "ml-slider"),
                    'class' => 'option coin',
                    'helptext' => esc_html__(
                        "Opacity of title and navigation, between 0 and 1.",
                        "ml-slider"
                    ),
                    'after' => '',
                    'is_legacy' => true
                ),
                'titleSpeed' => array(
                    'priority' => 90,
                    'type' => 'number',
                    'size' => 3,
                    'min' => 0,
                    'max' => 10000,
                    'step' => 100,
                    'value' => $this->slider->get_setting('titleSpeed'),
                    'label' => esc_html__("Caption Speed", "ml-slider"),
                    'class' => 'option coin',
                    'helptext' => esc_html__(
                        "Set the fade in speed of the caption.",
                        "ml-slider"
                    ),
                    'after' => esc_html_x(
                        "ms",
                        "Short for milliseconds",
                        "ml-slider"
                    ),
                    'is_legacy' => true
                ),
                'lazyLoad' => array( // Don't target 'lazyLoad' to show/hide with 'dependencies' array key
                    'priority' => 91,
                    'type' => 'checkbox',
                    'label' => __("Lazy Load Images", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'lazyLoad'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => __(
                        "This feature can speed up your site. MetaSlider will only load slides when they are required by your slideshow.",
                        "ml-slider"
                    )
                ),
            );

            $aFields = apply_filters('metaslider_advanced_settings', $aFields, $this->slider);
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>
    </div>

    <div class="ms-settings-box containerOptions ms-off">
        <div class="ms-highlight highlight">
            <?php esc_html_e( 'Container Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            // Container options
            $aFields = array(
                'container' => array(
                    'priority' => 10,
                    'type' => 'checkbox',
                    'label' => esc_html__("Container Box", "ml-slider"),
                    'class' => 'option flex disabled-checkbox',
                    'checked' => $this->slider->get_setting(
                        'container'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Customize the slideshow's container box.",
                        "ml-slider"
                    ),
                    'addon_required' => true,
                    'after' => metaslider_upgrade_pro_small_btn(
                        __( 'This feature is available in MetaSlider Pro', 'ml-slider' )
                    )
                )
            );

            $aFields = apply_filters('metaslider_container_settings', $aFields, $this->slider);
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>
    </div>

    <div class="ms-settings-box accessibilityOptions ms-off">
        <div class="ms-highlight">
            <?php esc_html_e( 'Accessibility Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            // Accessibility options
            $aFields = array(
                'keyboard' => array(
                    'priority' => 182,
                    'type' => 'checkbox',
                    'label' => esc_html__("Keyboard Controls", "ml-slider"),
                    'class' => 'option coin flex nivo responsive',
                    'checked' => 'true' == $this->slider->get_setting(
                        'keyboard'
                    ) ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Use arrow keys to get to the next slide.",
                        "ml-slider"
                    )
                ),
                'tabIndex' => array(
                    'priority' => 183,
                    'type' => 'checkbox',
                    'label' => esc_html__("Tabindex For Navigation", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'tabIndex'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "This helps make the slideshow navigation more accessible.",
                        "ml-slider"
                    )
                ),
                'ariaLive' => array(
                    'priority' => 185,
                    'type' => 'checkbox',
                    'label' => esc_html__("ARIA Live", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'ariaLive'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "If Autoplay is enabled, this causes screen readers to announce that the slides are changing.",
                        "ml-slider"
                    )
                ),
                'ariaCurrent' => array(
                    'priority' => 186,
                    'type' => 'checkbox',
                    'label' => esc_html__("ARIA Current", "ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'ariaCurrent'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "This is used on the navigation button for the active slide. It helps screen readers understand which slide is active.",
                        "ml-slider"
                    )
                )  
            );

            $aFields = apply_filters('metaslider_accessibility_settings', $aFields, $this->slider);
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>
    </div>
    <div class="ms-settings-box developerOptions ms-off">
        <div class="ms-highlight">
            <?php esc_html_e( 'Developer Options', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <?php
            // Developer options
            $aFields = array(
                'cssClass' => array(
                    'priority' => 200,
                    'type' => 'text',
                    'label' => esc_html__("CSS Classes", "ml-slider"),
                    'class' => 'option flex nivo responsive coin',
                    'helptext' => esc_html__(
                        "Enter custom CSS classes to apply to the slider wrapper. Separate multiple classes with a space.",
                        "ml-slider"
                    ),
                    'value' => $this->slider->get_setting(
                        'cssClass'
                    ) == 'false' ? '' : $this->slider->get_setting('cssClass')
                ),
                'printCss' => array(
                    'priority' => 210,
                    'type' => 'checkbox',
                    'label' => esc_html__("Print CSS", "ml-slider"),
                    'class' => 'option flex nivo responsive coin useWithCaution',
                    'checked' => $this->slider->get_setting(
                        'printCss'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Uncheck this if you would like to include your own CSS.",
                        "ml-slider"
                    )
                ),
                'printJs' => array(
                    'priority' => 220,
                    'type' => 'checkbox',
                    'label' => esc_html__("Print JS", "ml-slider"),
                    'class' => 'option flex nivo responsive coin useWithCaution',
                    'checked' => $this->slider->get_setting(
                        'printJs'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Uncheck this if you would like to include your own Javascript.",
                        "ml-slider"
                    )
                ),
                'noConflict' => array(
                    'priority' => 230,
                    'type' => 'checkbox',
                    'label' => esc_html__("No Conflict Mode","ml-slider"),
                    'class' => 'option flex',
                    'checked' => $this->slider->get_setting(
                        'noConflict'
                    ) == 'true' ? 'checked' : '',
                    'helptext' => esc_html__(
                        "Delay adding the flexslider class to the slideshow.",
                        "ml-slider"
                    )
                )   
            );

            $aFields = apply_filters('metaslider_developer_settings', $aFields, $this->slider);
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->build_settings_rows($aFields);
            ?>
        </table>
    </div>
    <div class="ms-settings-box shortcodeOptions ms-on">
        <div class="ms-highlight">
            <?php esc_html_e( 'Shortcode', 'ml-slider' ) ?>
            <a href="#" class="ms-toggle-static">
                <span class="dashicons"></span>
            </a>
        </div>
        <table class="ms-settings-box-inner">
            <tr>
                <td>
                    <?php include METASLIDER_PATH . "admin/views/pages/parts/shortcode.php"; ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
    // Show the restore button if there are trashed posts
    // Also, render but hide the link in case we want to show
    // it when the user deletes their first slide
    $count = count(metaslider_has_trashed_slides($this->slider->id));
    if ( ! metaslider_viewing_trashed_slides( $this->slider->id ) ) { 
        ?>
        <div class="ms-settings-box trasedSlidesOptions ms-off" style="<?php echo ! $count ? 'display: none;' : ''  ?>">
            <div class="ms-highlight">
                <?php esc_html_e( 'Trashed Slides', 'ml-slider' ) ?>
                <a href="#" class="ms-toggle-static">
                    <span class="dashicons"></span>
                </a>
            </div>
            <table class="ms-settings-box-inner">
                <tr class="trashed-slides-cont">
                    <td>    
                        <a class="restore-slide-link text-blue-dark hover:text-orange" href="<?php echo esc_url(admin_url("admin.php?page=metaslider&id={$this->slider->id}&show_trashed=true")); ?>">
                            <i><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></i> 
                            <?php esc_attr_e( 'View trashed slides', 'ml-slider' ); ?> 
                            <?php echo $count ? '(' . (int) $count . ')' : '' ?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        } 
    ?>
</div>
