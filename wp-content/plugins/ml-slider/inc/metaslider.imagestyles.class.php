<?php
if (! defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Slide image-styles feature.
 *
 * Adds a per-slide "Image Styles" tab (image filter presets, rounded corners,
 * border, box shadow, opacity, rotate/flip) plus a slideshow-level "Image
 * Styles" set of defaults. Settings are stored as post meta / slideshow
 * settings and emitted as scoped front-end CSS via the `metaslider_css` filter.
 *
 * Available on the built-in Image slide type and, when Pro is active, every
 * Pro slide type whose markup renders a real image: Gradient, WooCommerce
 * Product, Post Feed, Image Folder, Post Images, External Image, Layer
 * (HTML Overlay), and the Livid/YouTube/Vimeo/TikTok video slides (styling their
 * lazy-load poster image — the uploaded/featured thumbnail shown before the
 * video loads; nothing to style once lazy load is off, since no image is
 * rendered at all). Local/external video and Custom HTML aren't included —
 * neither reliably renders an `<img>` for the CSS to target.
 *
 * The generated CSS targets `.msDefaultImage` rather than a bare `img`
 * selector: every slide type's real content image carries that class, while
 * decorative elements (e.g. the video slide types' 75px play-button icon)
 * don't — so styling never leaks onto them.
 *
 * Self-contained: it touches no render markup because the FlexSlider `<li>`
 * already carries the `.slide-{ID}` class these rules target.
 *
 * @since 3.111.0
 * @package MetaSlider
 */
class MetaSlider_Image_Styles
{
    /**
     * Per-request cache of slide IDs, keyed by slideshow ID. Populated as a
     * side effect of MetaSlider::get_slides() (via the metaslider_get_slides_query
     * filter it already fires) so generate_css() can reuse that result instead
     * of running its own duplicate WP_Query for the same slideshow.
     *
     * @var array
     */
    private static $slide_ids_cache = array();

    public function __construct()
    {
        // Capture the slide IDs MetaSlider::get_slides() already queried for this
        // slideshow, so get_slide_ids() doesn't have to run an equivalent query again.
        add_filter('metaslider_get_slides_query', array($this, 'cache_slide_ids_from_query'), 10, 2);

        // Admin: per-slide "Image Styles" tab. Every slide type funnels its
        // get_admin_tabs() result through this one generic filter (see
        // inc/slide/metaslide.class.php), so hooking it once covers every
        // current and future slide type - add_tab() itself excludes the
        // slide types with nothing for the generated CSS to target.
        add_filter('metaslider_slide_tabs', array($this, 'add_tab'), 20, 4);
        add_action('metaslider_save_slide', array($this, 'save'), 10, 3);

        // Slideshow-level defaults.
        add_filter('metaslider_default_parameters', array($this, 'register_default_setting'));
        add_filter('metaslider_image_styles_settings', array($this, 'add_image_styles_fields'), 10, 2); // Image Styles accordion (filter + styles).

        // Front end.
        add_filter('metaslider_css', array($this, 'generate_css'), 12, 3);

        // Admin behaviour + live preview (delegated; survives the Vue re-render).
        add_action('admin_print_footer_scripts', array($this, 'print_admin_preview_js'));
    }

    /* =====================================================================
     * Admin: the per-slide "Image Styles" tab
     * ===================================================================== */

    public function add_tab($tabs, $slide, $slider, $settings)
    {
        $slide_id = absint($slide->ID);
        $type     = get_post_meta($slide_id, 'ml-slider_type', true);
        $type     = $type ? $type : 'image';

        // These slide types don't reliably render an <img> for the generated
        // CSS to target (see the class docblock), so skip the tab entirely.
        if (in_array($type, array('local_video', 'external_video', 'custom_html'), true)) {
            return $tabs;
        }

        $meta           = $this->get_meta($slide_id);
        $preset_labels  = $this->slide_filter_labels();
        $filter_presets = $this->filter_presets();

        ob_start();
        include METASLIDER_PATH . 'admin/views/slides/tabs/background.php';
        $content = ob_get_clean();

        $tab = array(
            'title'   => __('Image Styles', 'ml-slider'),
            'content' => $content,
        );

        // Every slide type keys its "Device" tab 'mobile' (it's only present
        // when Settings > mobileSettings is on); place Styles right after it
        // when it exists, otherwise fall back to appending like before.
        // Namespaced key ('ms_image_styles', not 'background') so it can't
        // collide with a slide type's own pre-existing tab of that name.
        return $this->insert_tab_after($tabs, 'mobile', 'ms_image_styles', $tab);
    }

    /**
     * Insert a tab immediately after another tab, by array key. Falls back to
     * appending at the end if the reference key isn't present.
     *
     * @param array  $tabs      Existing tabs, keyed by tab id.
     * @param string $after_key Tab id to insert after.
     * @param string $new_key   New tab's id.
     * @param array  $new_tab   New tab's ['title' => ..., 'content' => ...].
     * @return array
     */
    private function insert_tab_after($tabs, $after_key, $new_key, $new_tab)
    {
        $pos = array_search($after_key, array_keys($tabs), true);
        if (false === $pos) {
            $tabs[$new_key] = $new_tab;
            return $tabs;
        }

        $before = array_slice($tabs, 0, $pos + 1, true);
        $after  = array_slice($tabs, $pos + 1, null, true);

        return $before + array($new_key => $new_tab) + $after;
    }

    /**
     * Persist the posted style fields for a slide.
     *
     * @param int   $slide_id  Slide ID.
     * @param int   $slider_id Slideshow ID.
     * @param array $fields    Posted attachment[ID] field array.
     * @return void
     */
    public function save($slide_id, $slider_id, $fields)
    {
        if (! is_array($fields)) {
            return;
        }
        $slide_id = absint($slide_id);

        /* ---- Filter ---- */
        $preset = isset($fields['filter_preset']) ? sanitize_key($fields['filter_preset']) : 'default';
        $valid  = array_merge(array('default', 'none'), array_keys($this->filter_presets()));
        if (! in_array($preset, $valid, true)) {
            $preset = 'default';
        }
        $this->update($slide_id, 'filter_preset', $preset, 'default');

        /* ---- Styles (empty number / 'default' select == inherit slideshow default) ---- */
        $this->save_int_or_inherit($slide_id, 'corner_radius', $fields, 0, 200);
        $this->save_int_or_inherit($slide_id, 'border_width', $fields, 0, 50);
        $this->save_int_or_inherit($slide_id, 'opacity', $fields, 0, 100);

        $bstyle = isset($fields['border_style']) ? $this->sanitize_border_style($fields['border_style']) : '';
        $this->update($slide_id, 'border_style', $bstyle);
        $this->update($slide_id, 'border_color', $this->sanitize_color(isset($fields['border_color']) ? $fields['border_color'] : ''));

        $shadow = isset($fields['box_shadow']) ? sanitize_key($fields['box_shadow']) : 'default';
        if (! in_array($shadow, array('default', 'none', 'light', 'medium', 'heavy'), true)) {
            $shadow = 'default';
        }
        $this->update($slide_id, 'box_shadow', $shadow, 'default');

        $rotate = isset($fields['rotate']) ? sanitize_key($fields['rotate']) : 'default';
        if (! in_array($rotate, array('default', '0', '90', '180', '270'), true)) {
            $rotate = 'default';
        }
        $this->update($slide_id, 'rotate', $rotate, 'default');

        $flip = isset($fields['flip']) ? sanitize_key($fields['flip']) : 'default';
        if (! in_array($flip, array('default', 'none', 'h', 'v', 'both'), true)) {
            $flip = 'default';
        }
        $this->update($slide_id, 'flip', $flip, 'default');
    }

    /* =====================================================================
     * Front end: CSS
     * ===================================================================== */

    public function generate_css($css, $settings, $slider_id)
    {
        $default_filter = is_array($settings) && isset($settings['filter']) ? (string) $settings['filter'] : '';
        $presets        = $this->filter_presets();
        $style_defaults = $this->style_defaults(is_array($settings) ? $settings : array());
        $width          = (is_array($settings) && isset($settings['width'])) ? (int) $settings['width'] : 700;
        $height         = (is_array($settings) && isset($settings['height'])) ? (int) $settings['height'] : 300;

        foreach ($this->get_slide_ids($slider_id, is_array($settings) ? $settings : array()) as $slide_id) {
            $m = $this->get_meta($slide_id);
            // Target each slide type's real content image (.msDefaultImage —
            // not a bare "img", so this never matches decorative elements
            // like the video slide types' play-button icon) plus the
            // Gradient slide's background div.
            $sel = "#metaslider_{$slider_id} li.slide-{$slide_id} .msDefaultImage,#metaslider_{$slider_id} li.slide-{$slide_id} .ms-gradient-bg";

            // Filter (separate property so it doesn't clash with transform etc).
            if ('default' === $m['filter_preset']) {
                $filter = ('' !== $default_filter && isset($presets[$default_filter])) ? $presets[$default_filter] : '';
            } elseif ('none' === $m['filter_preset']) {
                $filter = '';
            } else {
                $filter = $this->build_filter($m, $presets);
            }
            if ('' !== $filter) {
                $css .= "\n{$sel}{-webkit-filter:{$filter};filter:{$filter};}";
            }

            // Corner radius / border / shadow / opacity / transform.
            $decl = $this->build_style_declarations($m, $style_defaults, $width, $height);
            if ('' !== $decl) {
                $css .= "\n{$sel}{{$decl}}";
            }
        }

        return $css;
    }

    /**
     * Build the non-filter style declarations for a slide's image.
     *
     * @param array $m      Per-slide meta.
     * @param array $d      Resolved slideshow-level style defaults.
     * @param int   $width  Slideshow width (px), for the rotate fit-scale below.
     * @param int   $height Slideshow height (px), for the rotate fit-scale below.
     * @return string Declarations joined by ';' (no trailing ';').
     */
    private function build_style_declarations($m, $d, $width = 0, $height = 0)
    {
        $decl = array();

        $radius = ('' !== $m['corner_radius']) ? max(0, (int) $m['corner_radius']) : $d['corner_radius'];
        if ($radius > 0) {
            $decl[] = "border-radius:{$radius}px";
        }

        $bw = ('' !== $m['border_width']) ? max(0, (int) $m['border_width']) : $d['border_width'];
        $bs = ('default' !== $m['border_style']) ? $m['border_style'] : $d['border_style'];
        $bc = $m['border_color'] ? $m['border_color'] : $d['border_color'];
        if ($bw > 0) {
            // No colour set anywhere (slide or slideshow) - omit it so the
            // border falls back to currentColor instead of a forced gray.
            $decl[] = "border:{$bw}px {$bs}" . ('' !== $bc ? " {$bc}" : '');
        }

        $shadow = ('default' !== $m['box_shadow']) ? $m['box_shadow'] : $d['box_shadow'];
        $shadow_css = $this->shadow_recipe($shadow);
        if ('' !== $shadow_css) {
            $decl[] = "box-shadow:{$shadow_css}";
        }

        $opacity = ('' !== $m['opacity']) ? max(0, min(100, (int) $m['opacity'])) : $d['opacity'];
        if ($opacity < 100) {
            $decl[] = 'opacity:' . round($opacity / 100, 2);
        }

        $rotate = ('default' !== $m['rotate']) ? $m['rotate'] : $d['rotate'];
        $flip   = ('default' !== $m['flip']) ? $m['flip'] : $d['flip'];
        $transform = $this->transform_recipe($rotate, $flip, $width, $height);
        if ('' !== $transform) {
            $decl[] = "transform:{$transform}";
        }

        return implode(';', $decl);
    }

    /**
     * The box-shadow recipes for the "light"/"medium"/"heavy" presets. Single
     * source of truth - also wp_json_encode()'d into the admin preview JS
     * below, so the two can never drift apart.
     *
     * @return array
     */
    private function shadow_presets()
    {
        return array(
            'light'  => '0 2px 8px rgba(0,0,0,0.15)',
            'medium' => '0 4px 16px rgba(0,0,0,0.25)',
            'heavy'  => '0 8px 30px rgba(0,0,0,0.35)',
        );
    }

    private function shadow_recipe($key)
    {
        $map = $this->shadow_presets();
        return isset($map[$key]) ? $map[$key] : '';
    }

    private function transform_recipe($rotate, $flip, $width = 0, $height = 0)
    {
        $parts = array();
        $deg   = (int) $rotate;
        if (0 !== $deg) {
            $parts[] = "rotate({$deg}deg)";
            // A 90/270 rotation swaps the image's visual footprint (width and
            // height trade places), so on a non-square slideshow it would
            // overflow/get clipped by the slide's fixed-size box - scale it
            // down to fit within that box instead.
            if ((90 === $deg || 270 === $deg) && $width > 0 && $height > 0 && $width !== $height) {
                $parts[] = 'scale(' . round(min($width, $height) / max($width, $height), 4) . ')';
            }
        }
        if ('h' === $flip) {
            $parts[] = 'scaleX(-1)';
        } elseif ('v' === $flip) {
            $parts[] = 'scaleY(-1)';
        } elseif ('both' === $flip) {
            $parts[] = 'scale(-1,-1)';
        }
        return implode(' ', $parts);
    }

    /**
     * Resolve the slideshow-level style defaults from the settings array.
     *
     * These values are spliced directly into raw CSS in build_style_declarations(),
     * so border_style/border_color must be validated the same way as their
     * per-slide equivalents in save() — never trust them as pass-through strings.
     *
     * @param array $s Slideshow settings.
     * @return array
     */
    private function style_defaults($s)
    {
        $border_style = isset($s['style_border_style']) ? $this->sanitize_border_style($s['style_border_style']) : '';
        $border_color = isset($s['style_border_color']) ? $this->sanitize_color($s['style_border_color']) : '';

        return array(
            'corner_radius' => isset($s['style_corner_radius']) ? max(0, min(200, (int) $s['style_corner_radius'])) : 0,
            'border_width'  => isset($s['style_border_width']) ? max(0, min(50, (int) $s['style_border_width'])) : 0,
            'border_style'  => '' !== $border_style ? $border_style : 'solid',
            // No fallback colour - a genuinely unset value means "no fixed
            // colour", not gray; build_style_declarations() omits it from the
            // border shorthand in that case, letting currentColor apply.
            'border_color'  => $border_color,
            'box_shadow'    => isset($s['style_box_shadow']) ? $s['style_box_shadow'] : 'none',
            'opacity'       => isset($s['style_opacity']) ? max(0, min(100, (int) $s['style_opacity'])) : 100,
            'rotate'        => isset($s['style_rotate']) ? $s['style_rotate'] : '0',
            'flip'          => isset($s['style_flip']) ? $s['style_flip'] : 'none',
        );
    }

    /* =====================================================================
     * Slideshow-level default settings (sidebar)
     * ===================================================================== */

    public function register_default_setting($defaults)
    {
        $style_defaults = array(
            'filter'              => '',
            'style_corner_radius' => 0,
            'style_border_width'  => 0,
            'style_border_style'  => 'solid',
            'style_border_color'  => '',
            'style_box_shadow'    => 'none',
            'style_opacity'       => 100,
            'style_rotate'        => '0',
            'style_flip'          => 'none',
        );
        foreach ($style_defaults as $key => $value) {
            if (! isset($defaults[$key])) {
                $defaults[$key] = $value;
            }
        }
        return $defaults;
    }

    /**
     * Build the fields for the "Image Styles" sidebar accordion (the slideshow
     * default filter plus the default corner/border/shadow/opacity/transform).
     */
    public function add_image_styles_fields($fields, $slider)
    {
        $filter_options = array();
        foreach ($this->filter_preset_labels() as $key => $label) {
            $filter_options[$key] = array('label' => $label);
        }
        $filter_value = $slider->get_setting('filter');
        $fields['filter'] = array(
            'priority' => 0,
            'type'     => 'select',
            'label'    => __('Filter', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            // get_setting() returns the string 'false' for a genuinely empty
            // value (e.g. filter explicitly set to 'None') - same as
            // style_border_color below, show the real empty value instead.
            'value'    => ('false' !== $filter_value) ? $filter_value : '',
            'options'  => $filter_options,
            'helptext' => __('Apply a filter to every slide. Slides can override this on their Image Styles tab.', 'ml-slider'),
        );

        $fields['style_corner_radius'] = array(
            'priority' => 5,
            'type'     => 'number',
            'label'    => __('Rounded corners', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'min'      => 0,
            'max'      => 200,
            'step'     => 1,
            'value'    => $slider->get_setting('style_corner_radius'),
            'after'    => esc_html__('px', 'ml-slider'),
            'helptext' => __('Corner radius (px) applied to every slide image.', 'ml-slider'),
        );
        $fields['style_border_width'] = array(
            'priority' => 10,
            'type'     => 'number',
            'label'    => __('Border width', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'min'      => 0,
            'max'      => 50,
            'step'     => 1,
            'value'    => $slider->get_setting('style_border_width'),
            'after'    => esc_html__('px', 'ml-slider'),
            'helptext' => __('Image border width (px). 0 = no border.', 'ml-slider'),
        );
        $fields['style_border_style'] = array(
            'priority' => 15,
            'type'     => 'select',
            'label'    => __('Border style', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'value'    => $slider->get_setting('style_border_style'),
            'options'  => array(
                'none'   => array('label' => __('None', 'ml-slider')),
                'solid'  => array('label' => __('Solid', 'ml-slider')),
                'dashed' => array('label' => __('Dashed', 'ml-slider')),
                'dotted' => array('label' => __('Dotted', 'ml-slider')),
                'double' => array('label' => __('Double', 'ml-slider')),
            ),
        );
        $style_border_color = $slider->get_setting('style_border_color');
        $fields['style_border_color'] = array(
            'priority' => 20,
            'type'     => 'color',
            'label'    => __('Border colour', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            // get_setting() returns the string 'false' for a genuinely empty
            // value - show a blank swatch instead of that literal text.
            'value'    => ('false' !== $style_border_color) ? $style_border_color : '',
            'helptext' => __('Leave blank for no fixed border colour. If a filter is applied, it may also alter how this colour renders.', 'ml-slider'),
        );
        $fields['style_box_shadow'] = array(
            'priority' => 25,
            'type'     => 'select',
            'label'    => __('Box shadow', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'value'    => $slider->get_setting('style_box_shadow'),
            'options'  => array(
                'none'   => array('label' => __('None', 'ml-slider')),
                'light'  => array('label' => __('Light', 'ml-slider')),
                'medium' => array('label' => __('Medium', 'ml-slider')),
                'heavy'  => array('label' => __('Heavy', 'ml-slider')),
            ),
        );
        $fields['style_opacity'] = array(
            'priority' => 30,
            'type'     => 'number',
            'label'    => __('Opacity', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'min'      => 0,
            'max'      => 100,
            'step'     => 1,
            'value'    => $slider->get_setting('style_opacity'),
            'after'    => esc_html__('%', 'ml-slider'),
            'helptext' => __('Image opacity (%).', 'ml-slider'),
        );
        $fields['style_rotate'] = array(
            'priority' => 35,
            'type'     => 'select',
            'label'    => __('Rotate', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'value'    => $slider->get_setting('style_rotate'),
            'options'  => array(
                '0'   => array('label' => __('None', 'ml-slider')),
                '90'  => array('label' => '90°'),
                '180' => array('label' => '180°'),
                '270' => array('label' => '270°'),
            ),
        );
        $fields['style_flip'] = array(
            'priority' => 40,
            'type'     => 'select',
            'label'    => __('Flip', 'ml-slider'),
            'class'    => 'option coin flex nivo responsive',
            'value'    => $slider->get_setting('style_flip'),
            'options'  => array(
                'none' => array('label' => __('None', 'ml-slider')),
                'h'    => array('label' => __('Horizontal', 'ml-slider')),
                'v'    => array('label' => __('Vertical', 'ml-slider')),
                'both' => array('label' => __('Both', 'ml-slider')),
            ),
        );

        return $fields;
    }

    /* =====================================================================
     * Filter presets / labels
     * ===================================================================== */

    public function filter_presets()
    {
        return array(
            'noir'     => 'grayscale(100%) contrast(120%)',
            'silver'   => 'grayscale(100%) contrast(130%) brightness(105%)',
            'vintage'  => 'sepia(55%) contrast(110%) brightness(105%)',
            'golden'   => 'sepia(35%) saturate(150%) hue-rotate(-15deg) brightness(105%)',
            'toaster'  => 'sepia(40%) contrast(120%) brightness(95%) saturate(110%)',
            'warm'     => 'saturate(130%) sepia(20%)',
            'cool'     => 'saturate(110%) hue-rotate(15deg) brightness(105%)',
            'fade'     => 'contrast(85%) brightness(110%) saturate(80%)',
            'matte'    => 'contrast(80%) brightness(112%) saturate(85%)',
            'pastel'   => 'brightness(115%) saturate(75%) contrast(90%)',
            'vivid'    => 'saturate(160%) contrast(110%)',
            'crisp'    => 'contrast(140%) saturate(135%) brightness(102%)',
            'dramatic' => 'contrast(140%) brightness(95%) saturate(120%)',
            'negative' => 'invert(100%)',
        );
    }

    public function filter_preset_labels()
    {
        return array(
            ''         => __('None', 'ml-slider'),
            'noir'     => __('Noir (B&W)', 'ml-slider'),
            'silver'   => __('Silver (B&W)', 'ml-slider'),
            'vintage'  => __('Vintage', 'ml-slider'),
            'golden'   => __('Golden Hour', 'ml-slider'),
            'toaster'  => __('Toaster', 'ml-slider'),
            'warm'     => __('Warm', 'ml-slider'),
            'cool'     => __('Cool', 'ml-slider'),
            'fade'     => __('Fade', 'ml-slider'),
            'matte'    => __('Matte', 'ml-slider'),
            'pastel'   => __('Pastel', 'ml-slider'),
            'vivid'    => __('Vivid', 'ml-slider'),
            'crisp'    => __('Crisp', 'ml-slider'),
            'dramatic' => __('Dramatic', 'ml-slider'),
            'negative' => __('Negative', 'ml-slider'),
        );
    }

    public function slide_filter_labels()
    {
        $labels = $this->filter_preset_labels();
        unset($labels['']);

        $out = array(
            'default' => __('Default', 'ml-slider'),
            'none'    => __('None', 'ml-slider'),
        );
        foreach ($labels as $key => $label) {
            $out[$key] = $label;
        }

        return $out;
    }

    private function build_filter($m, $presets)
    {
        $preset = $m['filter_preset'];
        if ('' === $preset || 'none' === $preset) {
            return '';
        }
        return isset($presets[$preset]) ? $presets[$preset] : '';
    }

    /* =====================================================================
     * Meta + helpers
     * ===================================================================== */

    private function get_meta($slide_id)
    {
        $filter_preset = (string) get_post_meta($slide_id, 'ml-slider_filter_preset', true);
        $box_shadow    = (string) get_post_meta($slide_id, 'ml-slider_box_shadow', true);
        $rotate        = (string) get_post_meta($slide_id, 'ml-slider_rotate', true);
        $flip          = (string) get_post_meta($slide_id, 'ml-slider_flip', true);
        $border_style  = $this->sanitize_border_style(get_post_meta($slide_id, 'ml-slider_border_style', true));

        return array(
            // Filter: '' (unset) means "Default" — inherit the slideshow filter.
            'filter_preset'     => ('' !== $filter_preset) ? $filter_preset : 'default',

            // Styles: '' (numbers) / 'default' (selects) means inherit.
            // border_style/border_color are re-validated here (not just trusted
            // from save()) since they're spliced directly into raw CSS below —
            // defends against meta written by any other path.
            'corner_radius'     => (string) get_post_meta($slide_id, 'ml-slider_corner_radius', true),
            'border_width'      => (string) get_post_meta($slide_id, 'ml-slider_border_width', true),
            'border_style'      => ('' !== $border_style) ? $border_style : 'default',
            'border_color'      => $this->sanitize_color(get_post_meta($slide_id, 'ml-slider_border_color', true)),
            'opacity'           => (string) get_post_meta($slide_id, 'ml-slider_opacity', true),
            'box_shadow'        => ('' !== $box_shadow) ? $box_shadow : 'default',
            'rotate'            => ('' !== $rotate) ? $rotate : 'default',
            'flip'              => ('' !== $flip) ? $flip : 'default',
        );
    }

    /**
     * Slide IDs to generate CSS for. Mirrors the filtering MetaSlider::get_slides()
     * applies (hidden-slide exclusion, trashed-slide viewing, multilingual-plugin
     * args, and the metaslider_populate_slides_args/metaslider_get_slides_query
     * filters) so this never styles slides that can't actually render on the
     * front end.
     *
     * @param int   $slider_id Slideshow ID.
     * @param array $settings  Slideshow settings, passed through to the filters above.
     * @return array
     */
    /**
     * Cache the slide IDs from MetaSlider::get_slides()'s own query (captured via
     * the metaslider_get_slides_query filter it already applies) so get_slide_ids()
     * can reuse them instead of running an equivalent WP_Query a second time.
     *
     * @param WP_Query $query        The slides query MetaSlider::get_slides() just ran.
     * @param int      $slideshow_id Slideshow ID.
     * @return WP_Query Unmodified — this is a passive observer, not a real filter.
     */
    public function cache_slide_ids_from_query($query, $slideshow_id)
    {
        $ids = array();
        foreach ((array) $query->posts as $post) {
            $ids[] = absint(is_object($post) ? $post->ID : $post);
        }
        self::$slide_ids_cache[absint($slideshow_id)] = $ids;

        return $query;
    }

    private function get_slide_ids($slider_id, $settings = array())
    {
        if (isset(self::$slide_ids_cache[absint($slider_id)])) {
            return self::$slide_ids_cache[absint($slider_id)];
        }

        $page = is_admin() && isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        $args = array(
            'post_type'        => array('attachment', 'ml-slide'),
            'post_status'      => array('inherit', 'publish'),
            'lang'             => '', // Polylang: ignore language filter.
            // phpcs:ignore WordPressVIPMinimum.Performance.NoPaging.posts_per_page_posts_per_page
            'posts_per_page'   => -1,
            'fields'           => 'ids',
            'suppress_filters' => 1, // WPML: ignore language filter.
            'no_found_rows'    => true,
            'tax_query'        => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field'    => 'slug',
                    'terms'    => $slider_id,
                ),
            ),
        );

        if (metaslider_viewing_trashed_slides($slider_id)) {
            $args['post_status'] = array('trash');
        }

        // Skip hidden slides (never rendered on the front end), matching
        // MetaSlider::get_slides()'s same admin/theme-editor exception.
        if (! is_admin() || 'metaslider-theme-editor' === $page) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_meta_slider_slide_is_hidden',
                    'value'   => '1',
                    'compare' => '!=',
                ),
                array(
                    'key'     => '_meta_slider_slide_is_hidden',
                    'compare' => 'NOT EXISTS',
                ),
            );
        }

        // Match MetaSlider::get_slides()'s own filter pipeline so this fallback
        // (only used when its cache-populating hook hasn't run yet - see above)
        // can't diverge from the slides it's actually building CSS selectors for.
        $args  = apply_filters('metaslider_populate_slides_args', $args, $slider_id, $settings);
        $query = apply_filters('metaslider_get_slides_query', new WP_Query($args), $slider_id, $settings);

        return array_map('absint', $query->posts);
    }

    private function update($slide_id, $name, $value, $neutral = '')
    {
        $key = 'ml-slider_' . $name;
        if ($value === $neutral || '' === $value || null === $value) {
            delete_post_meta($slide_id, $key);
        } else {
            update_post_meta($slide_id, $key, $value);
        }
    }

    /**
     * Store an integer, or delete the meta (inherit) when the field is blank.
     *
     * @return void
     */
    private function save_int_or_inherit($slide_id, $name, $fields, $min, $max)
    {
        if (! isset($fields[$name]) || '' === $fields[$name]) {
            delete_post_meta($slide_id, 'ml-slider_' . $name);
            return;
        }
        update_post_meta($slide_id, 'ml-slider_' . $name, max($min, min($max, (int) $fields[$name])));
    }

    /**
     * The whitelist of valid CSS border-style keywords this feature supports.
     *
     * @return array
     */
    private function border_styles()
    {
        return array('none', 'solid', 'dashed', 'dotted', 'double');
    }

    /**
     * Validate a border-style value against the whitelist. Used for both the
     * per-slide value (save()) and the slideshow-level default (style_defaults()),
     * since both get spliced directly into raw CSS.
     *
     * @param string $value Raw value.
     * @return string A whitelisted border-style keyword, or '' if invalid.
     */
    private function sanitize_border_style($value)
    {
        $value = sanitize_key((string) $value);
        return in_array($value, $this->border_styles(), true) ? $value : '';
    }

    /**
     * Validate a colour value (hex, rgb(), or rgba()). Used for both the
     * per-slide value (save()) and the slideshow-level default
     * (style_defaults()), since both get spliced directly into raw CSS.
     *
     * Delegates to MetaSlider_Themes::sanitize_color(), the same validator
     * the theme customizer colours use — the "color" field type this feature
     * reuses is an alpha-enabled colour picker, so it can submit rgba(), not
     * just hex.
     *
     * @param string $value Raw value.
     * @return string A validated colour string, or '' if invalid.
     */
    private function sanitize_color($value)
    {
        if (empty($value)) {
            return '';
        }
        $color = MetaSlider_Themes::get_instance()->sanitize_color((string) $value);
        return false !== $color ? $color : '';
    }

    /* =====================================================================
     * Admin live preview
     * ===================================================================== */

    public function print_admin_preview_js()
    {
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (false === strpos($page, 'metaslider')) {
            return;
        }
        ?>
<script>
(function () {
    if (window.__msStylesPreviewInit) { return; }
    window.__msStylesPreviewInit = true;

    var PRESETS = <?php echo wp_json_encode($this->filter_presets()); // phpcs:ignore ?>;
    var SHADOW = <?php echo wp_json_encode($this->shadow_presets()); // phpcs:ignore ?>;

    // MutationObserver-driven (not a one-off init) since slide rows can be added/re-rendered by the editor's Vue app after page load.
    function initColorPickers(root) {
        var $ = window.jQuery;
        if (!$ || !window.metaslider || !window.metaslider.init_color_picker) { return; }
        var text = typeof metaslider !== 'undefined' ? metaslider : null;
        window.metaslider.init_color_picker($(root).find('.colorpicker').addBack('.colorpicker'), text);
    }

    function initColorPickersAll() { initColorPickers(document); }

    new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) { initColorPickers(node); }
            });
        });
    }).observe(document.body, { childList: true, subtree: true });

    function field(tab, suffix) { return tab.querySelector('[name$="[' + suffix + ']"]'); }
    function fv(tab, suffix) { var el = field(tab, suffix); return el ? el.value : ''; }
    function sv(name) { var el = document.querySelector('[name="settings[' + name + ']"]'); return el ? el.value : ''; }

    function computeFilter(tab) {
        var preset = fv(tab, 'filter_preset');
        if (preset === 'none' || preset === '') { return ''; }
        if (preset === 'default') { var dv = sv('filter'); return (dv && PRESETS[dv]) ? PRESETS[dv] : ''; }
        return PRESETS[preset] || '';
    }

    function computeStyles(tab) {
        // Corner radius.
        var crS = fv(tab, 'corner_radius');
        var cr = (crS !== '') ? (parseInt(crS, 10) || 0) : (parseInt(sv('style_corner_radius'), 10) || 0);

        // Border.
        var bwS = fv(tab, 'border_width');
        var bw = (bwS !== '') ? (parseInt(bwS, 10) || 0) : (parseInt(sv('style_border_width'), 10) || 0);
        var bsS = fv(tab, 'border_style') || 'default';
        var bs = (bsS !== 'default') ? bsS : (sv('style_border_style') || 'solid');
        var bc = fv(tab, 'border_color') || sv('style_border_color') || '';

        // Shadow.
        var shS = fv(tab, 'box_shadow') || 'default';
        var sh = (shS !== 'default') ? shS : (sv('style_box_shadow') || 'none');

        // Opacity.
        var opS = fv(tab, 'opacity');
        var op = (opS !== '') ? parseInt(opS, 10) : (sv('style_opacity') !== '' ? parseInt(sv('style_opacity'), 10) : 100);
        if (isNaN(op)) { op = 100; }

        // Rotate / flip.
        var rotS = fv(tab, 'rotate') || 'default';
        var rot = (rotS !== 'default') ? rotS : (sv('style_rotate') || '0');
        var flipS = fv(tab, 'flip') || 'default';
        var flip = (flipS !== 'default') ? flipS : (sv('style_flip') || 'none');
        var t = [];
        var deg = parseInt(rot, 10) || 0;
        if (deg !== 0) {
            t.push('rotate(' + deg + 'deg)');
            // Matches the PHP fit-scale in transform_recipe() - a 90/270
            // rotation swaps width/height, so scale down to avoid overflow.
            if (deg === 90 || deg === 270) {
                var sw = parseInt(sv('width'), 10) || 0;
                var sh = parseInt(sv('height'), 10) || 0;
                if (sw > 0 && sh > 0 && sw !== sh) {
                    t.push('scale(' + (Math.min(sw, sh) / Math.max(sw, sh)).toFixed(4) + ')');
                }
            }
        }
        if (flip === 'h') { t.push('scaleX(-1)'); } else if (flip === 'v') { t.push('scaleY(-1)'); } else if (flip === 'both') { t.push('scale(-1,-1)'); }

        return {
            borderRadius: cr > 0 ? cr + 'px' : '',
            border: bw > 0 ? (bw + 'px ' + bs + (bc ? ' ' + bc : '')) : '',
            boxShadow: SHADOW[sh] || '',
            opacity: (op < 100) ? (op / 100) : '',
            transform: t.join(' ')
        };
    }

    function preview(tab) {
        var row = tab.closest('tr[id^="slide-"]');
        var img = row ? row.querySelector('.metaslider-slide-thumb .thumb img') : null;
        if (!img) { return; }

        var f = computeFilter(tab);
        img.style.filter = f;
        img.style.webkitFilter = f;

        var s = computeStyles(tab);
        img.style.borderRadius = s.borderRadius;
        img.style.border = s.border;
        img.style.boxShadow = s.boxShadow;
        img.style.opacity = s.opacity;
        img.style.transform = s.transform;
        img.style.webkitTransform = s.transform;
    }

    function previewAll() {
        document.querySelectorAll('.ms-slide-background-tab').forEach(preview);
    }

    // A change to a slideshow-level default (Image Styles sidebar) should
    // re-preview every slide that inherits it.
    function isSlideshowStyle(target) {
        var n = (target && target.name) || '';
        return n.indexOf('settings[filter') === 0 || n.indexOf('settings[style_') === 0;
    }

    document.addEventListener('change', function (e) {
        var tab = e.target.closest && e.target.closest('.ms-slide-background-tab');
        if (tab) { preview(tab); }
        else if (isSlideshowStyle(e.target)) { previewAll(); }
    });
    document.addEventListener('input', function (e) {
        var tab = e.target.closest && e.target.closest('.ms-slide-background-tab');
        if (tab) { preview(tab); }
        else if (isSlideshowStyle(e.target)) { previewAll(); }
    });

    // Initial pass so slides reflect saved/default styles when the editor opens.
    function init() { previewAll(); initColorPickersAll(); }
    if (document.readyState !== 'loading') { setTimeout(init, 300); }
    else { document.addEventListener('DOMContentLoaded', function () { setTimeout(init, 300); }); }
}());
</script>
        <?php
    }
}
