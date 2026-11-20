<?php

if (!defined('ABSPATH')) {
die('No direct access.');
}
/**
 * MS Theme base - Use to instanciate and store theme functions. 
 * Extend it if you need to change / add functionality to your theme.
 */
class MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id;

    /**
     * Assets
     *
     * @var array
     */
    public $assets;

    /**
     * Registered Themes - used to give access to the themes options and settings
     *
     * @var array
     */
    public static $themes = array();

    /**
     * Parameters
     *
     * @var string
     */
    public $slider_parameters = array();

    /**
     * Theme Version
     *
     * @var string
     */
    public $version;

    /**
     * Construct - set private for singleton pattern.
     *
     * @param int   $id      ID
     * @param int   $version Version
     * @param array $assets  Assets array
     */
    public function __construct($id, $version, $assets = array())
    {
        $this->id = $id;
        $this->version = $version;
        $this->assets = apply_filters('metaslider_theme_assets', $assets, $this->id);
        
        // store the current instance, to give it global access via MetaSlider_Theme_Base::$themes['theme_id']
        self::$themes[$this->id] = $this;

        $this->init();

        // Customize theme design
        add_filter('metaslider_theme_css', array($this, 'theme_customize'), 10, 3);

        // @since 3.101 - Container
        add_filter( 'metaslider_slideshow_output', array( $this, 'output_container' ), 10, 3 );
        add_filter( 'metaslider_css', array( $this, 'css_container' ), 11, 3 );
    }

    /**
     * Initialize hooks
     */
    public function init()
    {
        // Enqueue assets
        add_action('metaslider_register_public_styles', array($this, 'enqueue_assets'));

        // override the arrows markup
        add_filter('metaslider_flex_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_responsive_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_nivo_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_coin_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_flex_slider_parameters', array($this, 'responsive_arrows'), 99, 3);

        // Pro - override the arrows markup for the filmstrip
        add_filter('metaslider_flex_slider_filmstrip_parameters', array($this, 'update_parameters'), 99, 3);
    }

    /**
     * Enqueues theme specific styles and scripts
     */
    public function enqueue_assets()
    {
        foreach ($this->assets as $asset) {
            if ('css' == $asset['type']) {
                wp_enqueue_style('metaslider_' . $this->id . '_theme_styles', METASLIDER_THEMES_URL . $this->id . $asset['file'], isset($asset['dependencies']) ? $asset['dependencies'] : array(), METASLIDER_VERSION);
            }

            if ('js' == $asset['type']) {
                wp_enqueue_script('metaslider_' . $this->id . '_theme_script', METASLIDER_THEMES_URL . $this->id . $asset['file'], isset($asset['dependencies']) ? $asset['dependencies'] : array(), METASLIDER_VERSION, isset($asset['in_footer']) ? $asset['in_footer'] : true);
            }
        }
    }

    /**
     * Adjust arrows in mobile when negative value is in place to avoid horizontal scrollbar
     *
     * @since 3.98
     * 
     * @param array      $options      The slider plugin options
     * @param int|string $slideshow_id The slideshow options
     * @param array      $settings     The slideshow settings
     */
    public function responsive_arrows( $options, $slideshow_id, $settings ) {
        $enable     = apply_filters( 'metaslider_flex_slider_responsive_arrows_enable', false );
        $prev_class = apply_filters( 'metaslider_flex_slider_responsive_arrows_prev_class', '.flex-prev' );
        $next_class = apply_filters( 'metaslider_flex_slider_responsive_arrows_next_class', '.flex-next' );

        // @since 3.100 - Use these filters to add custom JS
        $mobile_on_js   = apply_filters( 'metaslider_flex_slider_responsive_arrows_mobile_on_js', '' );
        $mobile_off_js  = apply_filters( 'metaslider_flex_slider_responsive_arrows_mobile_off_js', '' );
        
        if ( ! is_admin() && $enable ) {
            $options['start'] = isset( $options['start'] ) ? $options['start'] : array();
            $options['start'] = array_merge( $options['start'], array(
                "function responsive_arrows__slide_width() {
                    var width = parseInt($('#metaslider_{$slideshow_id}').width());
                    if ( width > 0 ) {
                        return width;
                    }
                    return $('#metaslider-id-{$slideshow_id}').attr('data-width');
                }
                function responsive_arrows__adjust_arrows(prevStartVal, nextStartVal) {
                    if ( ! prevStartVal || ! nextStartVal) {
                        return;
                    }

                    var screenWidth = $(window).innerWidth();
                    var parentContainer = $('#metaslider_container_{$slideshow_id}');
                    var liWidth = responsive_arrows__slide_width();
                    var prev = parentContainer.find('.flexslider:not(.filmstrip) {$prev_class}');
                    var next = parentContainer.find('.flexslider:not(.filmstrip) {$next_class}');

                    /* 200 = give some breathe considering arrow size and position from edge */
                    if ((screenWidth - 200) < liWidth && (parseInt(prevStartVal, 10) < 0 || parseInt(nextStartVal, 10) < 0)) {
                        prev.css('left', '10px');
                        next.css('right', '10px');
                        {$mobile_on_js}
                    } else {
                        prev.css('left', prevStartVal);
                        next.css('right', nextStartVal);
                        {$mobile_off_js}
                    }
                }
                    
                var parentContainer = $('#metaslider_container_{$slideshow_id}');
                var prevStartVal = parentContainer.find('{$prev_class}').css('left') || null;
                var nextStartVal = parentContainer.find('{$next_class}').css('right') || null;
                responsive_arrows__adjust_arrows(prevStartVal, nextStartVal);

                $(window).on('resize', function() {
                    responsive_arrows__adjust_arrows(prevStartVal, nextStartVal);
                });"
            ));
        }

        return $options;
    }

    /**
     * Adds parameters for this theme. Used mainly for changing the Arrows text + icons
     *
     * @param array      $options      The slider plugin options
     * @param int|string $slideshow_id The slideshow options
     * @param array      $settings     The slideshow settings
     */
    public function update_parameters($options, $slideshow_id, $settings)
    {
        $theme_id = false;

        if (!$this->slider_parameters) {
return $options;
        }

        // if preview
        if (isset($_REQUEST['action']) && 'ms_get_preview' == $_REQUEST['action']) {
            if (isset($_REQUEST['theme_id'])) {
                $theme_id = sanitize_text_field($_REQUEST['theme_id']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            }
        }
    
        // only fetch the saved theme if the preview theme isn't set
        if (!$theme_id) {
            $theme = get_post_meta($slideshow_id, 'metaslider_slideshow_theme', true);
            if (isset($theme['folder'])) {
                $theme_id = $theme['folder'];
            }
        }

        if ($this->id == $theme_id) {
            return array_merge($options, apply_filters('metaslider_theme_' . $this->id . '_slider_parameters', $this->slider_parameters));
        }

        return $options;
    }

    /**
     * Add manual controls to this theme
     * Important: be careful when using as it will override 
     * the default dots navigation of other slideshows in same page.
     * 
     * @param array  $html         - The flexslider options
     * @param string $slideshow_id - the id of the slideshow
     * @param array  $settings     - the id of the slideshow
     *
     * @return array
     */
    public function add_title_to_replace_dots($html, $slideshow_id, $settings)
    {
        // We want to insert this after the closing ul but before the container div
        $nav = "</ul>";

        // Only enable this for dots nav
        if ('true' === $settings['navigation'] && 'false' === $settings['carouselMode']) {
            $nav .= "<ol class='flex-control-nav titleNav-{$slideshow_id}'>";
            foreach ($this->get_slides($slideshow_id) as $count => $slide) {
                // Check if the title is inherited or manually set
                if ((bool) get_post_meta($slide->ID, 'ml-slider_inherit_image_title', true)) {
                    $attachment = get_post(get_post_thumbnail_id($slide->ID));
                    $title = $attachment->post_title;
                } else {
                    $title = get_post_meta($slide->ID, 'ml-slider_title', true);
                }

                // Check if it's a string and not '' and use the count + 1
                if (!is_string($title) || empty($title)) {
                    $title = $count;
                }
                $nav .= "<li><a href='#'>{$title}</a></li>";
            }
            $nav .= "</ol>";
        }
        return str_replace('</ul>', $nav, $html);
    }

    /**
     * Copy the query from ml-slider
     *
     * @param int $slideshow_id - the id of the slideshow
     * @return WP_Query
     */
    private function get_slides($slideshow_id)
    {
        $settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);
        $args = array(
            'force_no_custom_order' => true,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_type' => array('attachment', 'ml-slide'),
            'post_status' => array('inherit', 'publish'),
            'lang' => '', // polylang, ingore language filter
            'suppress_filters' => 1, // wpml, ignore language filter
            // phpcs:ignore WordPressVIPMinimum.Performance.NoPaging.posts_per_page_posts_per_page
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => $slideshow_id
                )
            )
        );

        $args = apply_filters('metaslider_populate_slides_args', $args, $slideshow_id, $settings);
        $slides = get_posts($args);
        
        $available_slides = array();
        foreach ($slides as $slide) {
            $type = get_post_meta($slide->ID, 'ml-slider_type', true);
            $type = $type ? $type : 'image'; // Default ot image

            // @since 3.96 - Skip slide if is an image type but doesn't have an actual image (invisible slide in admin)
            if ( $type == 'image' && get_post_meta( $slide->ID, '_thumbnail_id', true ) == false ) {
                continue;
            }
            
            $is_hidden = get_post_meta($slide->ID, '_meta_slider_slide_is_hidden', true);
            if($is_hidden != true){
                // If this filter exists, that means the slide type is available (i.e. pro slides)
                if (has_filter("metaslider_get_{$type}_slide")) {
                    array_push($available_slides, $slide);
                }
            }
            
        }
        return $available_slides;
    }

    /**
     * Build CSS to customize theme colors
     *
     * @since 3.93
     *
     * @param string $theme        Theme name in lowercase. e.g. 'bitono'
     * @param array  $stored_data  The customize values to build CSS from - a custom v2 theme's
     *                              own stored data, or the slideshow's ml-slider_settings one.
     * @param int    $slideshow_id Slideshow id
     *
     * @return string
     */
    public function theme_customize_css($theme, $stored_data, $slideshow_id)
    {
        $themes_class = MetaSlider_Themes::get_instance();

        // $theme is always the base theme's own id (custom v2 themes reuse their base theme's
        // class), so its own registered type - not whether this is a custom v2 wrapper - is what
        // decides whether the manifest lookup needs to fall back to premium/extra theme folders.
        $type     = $themes_class->get_theme_type($theme);
        $type     = $type ? $type : 'free';
        $manifest = $themes_class->get_theme_manifest($theme, $type);

        $output = $themes_class->build_customize_css($manifest, $stored_data, $slideshow_id);

        return $output;
    }

    /**
     * Add inline CSS to customize theme design
     *
     * @since 3.93.0 - Moved from each theme.php file
     * @since 3.114.0 Uses the previewed theme on REST preview requests too
     */
    public function theme_customize($css, $settings, $slideshow_id)
    {
        // /wp-admin/admin.php?page=metaslider-theme-editor&theme_slug=<slug>1&version=v2
        $is_theme_editor_screen = is_admin()
            && function_exists('get_current_screen')
            && ($screen = get_current_screen())
            && 'slideshow-pro_page_metaslider-theme-editor' === $screen->id
            && isset($_GET['version'])
            && $_GET['version'] == 'v2';

        // This CSS only works with Flexslider markup, regardless of theme type.
        if ($is_theme_editor_screen || 'flex' !== $settings['type']) {
            // @since 3.98 - Reset css to avoid including css from a previous slideshow's instance
            return '';
        }

        // The slideshow's actually-saved theme - not the preview override - since
        // $settings['theme_customize'] always holds THIS theme's stored colors, whatever
        // theme is being previewed.
        $theme_settings = get_post_meta($slideshow_id, 'metaslider_slideshow_theme', true);
        $saved_theme_id = isset($theme_settings['folder']) ? $theme_settings['folder'] : false;

        /* Resolve the theme actually being rendered: an explicit preview override (e.g. the
         * theme picker's own Preview button, which can target a candidate theme that differs
         * from what's saved) if present, else the slideshow's saved theme. Everything below
         * stays paired against this SAME theme - mixing one theme's manifest with a different
         * theme's stored customize values produces mismatched CSS (see git history on this file). */
        $theme_id = false;
        // The theme picker loads its preview through the REST API, where is_admin() is false
        $is_preview_request = is_admin() || (defined('REST_REQUEST') && REST_REQUEST);
        if ($is_preview_request && isset($_REQUEST['action']) && 'ms_get_preview' == $_REQUEST['action'] && isset($_REQUEST['theme_id'])) {
            $theme_id = sanitize_text_field($_REQUEST['theme_id']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        }
        if (!$theme_id) {
            $theme_id = $saved_theme_id;
        }

        // Custom themes reuse a core theme's class under their own unique folder.
        $is_custom = $theme_id && '_theme' === substr($theme_id, 0, strlen('_theme'));

        if ($is_custom) {
            $custom_themes = get_option('metaslider-themes');
            $base          = isset($custom_themes[$theme_id]['base']) ? $custom_themes[$theme_id]['base'] : false;

            // Bail (including when the base couldn't be resolved at all) rather than let every
            // registered instance build/overwrite $css when we can't confirm which one owns it.
            if ($this->id !== $base) {
                return $css;
            }

            // A custom theme carries its own customize data, independent of this slideshow's
            // settings - it may be assigned to many slideshows, or just being previewed here.
            $stored_data = isset($custom_themes[$theme_id]['customize']) ? $custom_themes[$theme_id]['customize'] : array();
        } else {
            // $settings['theme_customize'] belongs to $saved_theme_id, not to whatever's being
            // previewed - bail unless the rendered theme IS the slideshow's own saved theme, or a
            // preview of a different core theme would inherit the saved theme's colors.
            if ($theme_id !== $saved_theme_id || ! isset($settings['theme_customize']) || $this->id !== $theme_id) {
                return $css;
            }

            $stored_data = $settings['theme_customize'];
        }

        return $this->theme_customize_css($this->id, $stored_data, $slideshow_id);
    }

    /**
     * Add a wrapper to the slideshow output based on container settings
     * 
     * @since 3.101
     * 
     * @return html
     */
    public function output_container( $html, $slider_id, $settings )
    {
        $container = metaslider_pro_is_active() && isset( $settings['container'] ) && $settings['container'] === 'true' 
            ? true : false;

        if ( ! $container ) {
            return $html;
        }

        $width = absint( $settings['width'] ) ?? 0;

        $style = $width ? "max-width: {$width}px;" : "";

        $new_html = array();
        $new_html[] = '<div id="metaslider_container_box_' . $slider_id . '" class="metaslider-container-box" style="' . esc_attr( $style ) . '">';
        $new_html[] = $html;
        $new_html[] = '</div>';

        return implode( "\n", $new_html );
    }

    /**
     * Add CSS to style container
     * 
     * @since 3.101
     */
    public function css_container( $css, $settings, $id )
    {
        $container = metaslider_pro_is_active() && isset( $settings['container'] ) && $settings['container'] === 'true' 
            ? true : false;

        // @since 2.49 - Add CSS to container box
        if ( $container) {
            
            $css .= '#metaslider_container_box_' . $id . ' {';

            // Background
            if ( ! empty( $settings['container_background'] ) ) {
                $css .= 'background:' . esc_html( $settings['container_background'] ) . ';';
            }

            // Padding
            foreach ( array( 'top', 'right', 'bottom', 'left' ) as $prop ) {
                if ( isset( $settings['containerPadding_' . $prop] ) ) {
                    $css .= 'padding-' . $prop . ':' . esc_html( (int) $settings['containerPadding_' . $prop] ) . 'px;';
                }
            }

            // Margin
            foreach ( array( 'top', 'bottom' ) as $prop ) {
                if ( isset( $settings['containerMargin_' . $prop] ) ) {
                    $css .= 'margin-' . $prop . ':' . esc_html( (int) $settings['containerMargin_' . $prop] ) . 'px;';
                }
            }

            $css .= '}';
        }

        return $css;
    }
}
