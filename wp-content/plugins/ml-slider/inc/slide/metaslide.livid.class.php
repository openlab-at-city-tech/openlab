<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Livid video-embed slide. A user pastes a livid.com video URL, and the slide
 * keeps only that URL - the player markup is built from it at render time (see
 * build_embed_html()), the way MetaSlider Pro's YouTube slide type works.
 *
 * @since 3.113.0
 */
class MetaLividSlide extends MetaSlide
{
    /**
     * Slide type
     *
     * @var string
     */
    public $identifier = 'livid';

    /**
     * Slide type name, as shown in the "Add Slide" media menu. Unlike
     * $identifier (a lowercase, one-word slug) this is a translated,
     * human-readable label - set on init by set_name(), never in the
     * constructor, which runs too early to translate anything.
     *
     * @var string
     */
    public $name = '';

    /**
     * Hosts we trust enough to (a) ask WordPress to fetch oEmbed discovery
     * data for, and (b) trust the returned <iframe> markup from.
     *
     * @var string[]
     */
    private $allowed_hosts = array('livid.com', 'www.livid.com');

    /**
     * Per-slide player settings (mute, controls, autoplay, loop), stored as
     * their own "ml-slider_settings" postmeta on the slide itself - not to
     * be confused with $this->settings (the slideshow's own settings).
     *
     * @var array
     */
    private $slide_settings = array();

    /**
     * Whether the lazy-load click-to-play CSS has already been added to this
     * page - wp_add_inline_style() appends every time it's called, and this
     * class's single registered instance renders every Livid slide on the
     * page, so this avoids duplicating the same CSS block per slide.
     *
     * @var bool
     */
    private static $lazyload_styles_enqueued = false;

    /**
     * Register slide type
     */
    public function __construct()
    {
        parent::__construct();

        add_action('init', array($this, 'set_name'));

        if (is_admin()) {
            // Run ahead of ml-slider.php's own custom_media_upload_tab_name() (priority 998,
            // which adds the Vimeo/YouTube/... pro upsell tabs) so this real, working slide
            // type lands before those in the "Add Slide" media menu.
            add_filter('media_upload_tabs', array($this, 'custom_media_upload_tab_name'), 20, 1);
            add_action("media_upload_{$this->identifier}", array($this, 'get_iframe'));
            add_action('wp_ajax_create_livid_slide', array($this, 'ajax_create_livid_slide'));
            add_action('wp_ajax_livid_preview_embed', array($this, 'ajax_preview_embed'));
            add_action('wp_ajax_update_livid_embed', array($this, 'ajax_update_embed'));
            add_action('wp_ajax_duplicate_livid_slide', array($this, 'ajax_duplicate_slide'));
            add_action('metaslider_register_admin_components', array($this, 'enqueue_admin_components'));
        }

        add_filter('metaslider_get_livid_slide', array($this, 'get_slide'), 10, 2);
        add_action('metaslider_save_livid_slide', array($this, 'save_slide'), 5, 3);

        // Same architecture MetaSlider Pro's YouTube/Vimeo slide types use:
        // pause/resume/autoplay is driven by JS injected into FlexSlider's own
        // before/after/start callbacks, not a generic poll. Flex only - there
        // is no equivalent parameters filter for Responsive (confirmed: Pro
        // registers one for its own video types too, but nothing anywhere
        // ever applies it, so it is unreachable there as well) -
        // livid-lazyload.js falls back to polling for Responsive only.
        add_filter('metaslider_flex_slider_parameters', array($this, 'flex_slider_parameters'), 10, 3);
    }

    /**
     * Inject pause/resume/autoplay handling for Livid slides into FlexSlider's
     * own before/after/start callbacks.
     *
     * @param array $options   Flex slider JS options
     * @param int   $slider_id Slideshow ID
     * @param array $settings  Slideshow settings
     * @return array
     */
    public function flex_slider_parameters($options, $slider_id, $settings)
    {
        $playing_selector = "#metaslider_{$slider_id} .flex-active-slide .ms-livid-lazy-wrap[data-playing=\"1\"], " .
            "#metaslider_{$slider_id} .flex-active-slide .ms-livid-eager";
        $unplayed_autoplay_selector = "#metaslider_{$slider_id} .flex-active-slide .ms-livid-lazy-wrap[data-autoplay-on-active=\"1\"]:not([data-playing=\"1\"])";
        $autoplay_selector = "#metaslider_{$slider_id} .flex-active-slide .ms-livid-lazy-wrap[data-autoplay-on-active=\"1\"], " .
            "#metaslider_{$slider_id} .flex-active-slide .ms-livid-eager[data-autoplay-on-active=\"1\"]";

        // In carousel mode FlexSlider never marks a slide flex-active-slide,
        // and every selector above keys off it - so nothing would hold, play
        // or resume. Mark the current slide ourselves first, exactly as Pro's
        // YouTube/Vimeo slide types do for the same reason.
        $add_active_class = '';
        if (isset($settings['carouselMode']) && 'true' == $settings['carouselMode']) {
            $add_active_class = "jQuery(slider).find('.slides > li')" .
                ".removeClass('flex-active-slide').eq(slider.currentSlide).addClass('flex-active-slide');";
        }

        // Before a slide transitions - pause any playing Livid video in the
        // slide being left, same as Pro's Vimeo/YouTube slide types pausing
        // their own player in this same callback.
        $options['before'] = isset($options['before']) ? $options['before'] : array();
        $options['before'][] = "
            jQuery('{$playing_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.pause(this);
            });
        ";

        // After a slide transitions - resume the newly active slide's Livid
        // video (resumeVideo() only actually sends play if that slide's own
        // Auto Play setting allows it), and build+play a not-yet-loaded
        // lazy+autoplay slide.
        // Hold first, before anything starts playing: landing on an
        // auto-playing video slide stops the slideshow advancing so the video
        // is watched to the end. Done here rather than waiting for the player
        // to report that it started - Pro's Vimeo slide type calls
        // flexslider('pause') in this same callback for the same reason.
        $options['after'] = isset($options['after']) ? $options['after'] : array();
        $options['after'][] = "
            {$add_active_class}
            window.metaslider && window.metaslider.livid && window.metaslider.livid.markTransitioned('metaslider_{$slider_id}');
            jQuery('{$autoplay_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.hold(this);
            });
            jQuery('{$playing_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.resume(this);
            });
            jQuery('{$unplayed_autoplay_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.triggerAutoplay(this);
            });
        ";

        // Whether a finished video should move on to the next slide depends on
        // the slideshow's own Auto Play setting, and on its Loop setting -
        // both of which only PHP knows about, so hand them to the JS side once,
        // up front. "stopOnFirst" has no FlexSlider option of its own (unlike
        // "stopOnLast", which just sets animationLoop false and is already
        // visible to the JS): MetaFlexSlider::metaslider_flex_loop() implements
        // it as its own "after" callback that pauses on arrival at slide 0, so
        // the mode has to be passed explicitly to be honored here.
        $slideshow_autoplay = isset($settings['autoPlay'])
            && filter_var($settings['autoPlay'], FILTER_VALIDATE_BOOLEAN);
        $loop_mode = isset($settings['loop']) ? (string) $settings['loop'] : '';

        // When the slideshow first loads - FlexSlider's "after" callback never
        // fires for the very first slide shown, so the initially active slide
        // needs the same auto play handling done here instead.
        $options['start'] = isset($options['start']) ? $options['start'] : array();
        $options['start'][] = "
            if (window.metaslider && window.metaslider.livid) {
                window.metaslider.livid.setAdvanceOnEnd('metaslider_{$slider_id}', " . ($slideshow_autoplay ? 'true' : 'false') . ", '" . esc_js($loop_mode) . "');
            }
            {$add_active_class}
            jQuery('{$autoplay_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.hold(this);
            });
            jQuery('{$playing_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.resume(this);
            });
            jQuery('{$unplayed_autoplay_selector}').each(function () {
                window.metaslider && window.metaslider.livid && window.metaslider.livid.triggerAutoplay(this);
            });
        ";

        return $options;
    }

    /**
     * Set the slide, and load its own player settings alongside it.
     *
     * @param int $id Slide ID
     */
    public function set_slide($id)
    {
        parent::set_slide($id);
        $settings = get_post_meta($id, 'ml-slider_settings', true);
        $this->slide_settings = is_array($settings) ? $settings : array();
    }

    /**
     * Set this slide type's display name.
     *
     * Hooked to init rather than done in the constructor, which runs on
     * plugins_loaded: translating before init makes WordPress load the text
     * domain "just in time" and emit a _doing_it_wrong notice, which on a
     * WP_DEBUG site prints output ahead of the headers.
     *
     * @since 3.113.0
     *
     * @return void
     */
    public function set_name()
    {
        /* translators: Livid is a video hosting service - keep the name as-is. */
        $this->name = __('Livid Video', 'ml-slider');
    }

    /**
     * Add this slide type to the "Add Slide" media menu.
     *
     * @param array $tabs Existing media manager tabs
     * @return array
     */
    public function custom_media_upload_tab_name($tabs)
    {
        // Only add our tab on MetaSlider's own screens, and inside our own tab's iframe
        if (
            (isset($_GET['page']) && 'metaslider' === $_GET['page'])
            || (isset($_GET['tab']) && $this->identifier === $_GET['tab'])
        ) {
            return array_merge((array) $tabs, array($this->identifier => $this->name));
        }

        return $tabs;
    }

    /**
     * Return the media manager iframe for this slide type
     *
     * @return void
     */
    public function get_iframe()
    {
        return wp_iframe(array($this, 'iframe'));
    }

    /**
     * Media manager iframe HTML - a single field to paste a Livid video link.
     *
     * @return void
     */
    public function iframe()
    {
        wp_enqueue_style('media-views');
        wp_enqueue_style(
            'metaslider-livid-styles',
            METASLIDER_ADMIN_ASSETS_URL . 'css/livid' . metaslider_asset_suffix() . '.css',
            false,
            METASLIDER_ASSETS_VERSION
        );
        wp_enqueue_script(
            'metaslider-livid-iframe',
            METASLIDER_ADMIN_ASSETS_URL . 'js/livid-iframe' . metaslider_asset_suffix() . '.js',
            array('jquery'),
            METASLIDER_ASSETS_VERSION,
            true
        );
        wp_localize_script('metaslider-livid-iframe', 'metaslider_livid_iframe', array(
            'nonce' => wp_create_nonce('metaslider_create_livid_slide'),
            'error_invalid_url' => esc_html__('Please enter a valid link to a video hosted on livid.com.', 'ml-slider'),
        ));

        ?>
        <div class="metaslider">
            <div class="livid">
                <h2 class="ms-slide-type-heading-desc"><?php /* translators: Livid is a video hosting service - keep the name as-is. */ esc_html_e('Create slideshows with your Livid videos', 'ml-slider'); ?></h2>
                <div class="media-embed">
                    <label class="embed-url">
                        <span><?php /* translators: Livid is a video hosting service - keep the name as-is. */ esc_html_e('Enter the URL of a Livid video:', 'ml-slider'); ?></span>
                        <input type="text" placeholder="https://livid.com/watch/IoMGtM7uIDai" class="livid_url ms-super-wide">
                        <span class="spinner" style="display:none"></span>
                    </label>
                    <div class="embed-link-settings"></div>
                </div>
            </div>
        </div>
        <div class="media-frame-toolbar">
            <div class="media-toolbar">
                <div class="media-toolbar-primary">
                    <a href="#" class="button media-button button-primary button-large" disabled="disabled" aria-disabled="true"><?php esc_html_e('Add to slideshow', 'ml-slider'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue the small admin script that wires up the "duplicate" button for
     * this slide type - mirrors the per-type asset pattern MetaSlider Pro's
     * own slide-type modules (Vimeo, YouTube, etc) use for the same purpose.
     */
    public function enqueue_admin_components()
    {
        wp_enqueue_script(
            'metaslider-livid-admin',
            METASLIDER_ADMIN_ASSETS_URL . 'js/livid-admin' . metaslider_asset_suffix() . '.js',
            array('jquery'),
            METASLIDER_ASSETS_VERSION,
            true
        );

        wp_localize_script('metaslider-livid-admin', 'metaslider_livid', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'duplicate_slide_nonce' => wp_create_nonce('metaslider_duplicate_livid_slide'),
            'update_embed_nonce' => wp_create_nonce('metaslider_update_livid_embed'),
            /* translators: Livid is a video hosting service - keep the name as-is. */
            'updating_embed' => esc_html__('Updating the Livid video...', 'ml-slider'),
            /* translators: Livid is a video hosting service - keep the name as-is. */
            'embed_updated' => esc_html__('Livid video updated.', 'ml-slider'),
            'error_invalid_url' => esc_html__('Please enter a valid link to a video hosted on livid.com.', 'ml-slider'),
            /* translators: Livid is a video hosting service - keep the name as-is. */
            'error_update_failed' => esc_html__('We could not update the Livid video. Please check the link and try again.', 'ml-slider'),
            'error_duplicate_failed' => esc_html__('Duplicating the slide failed.', 'ml-slider'),
        ));
    }

    /**
     * Ajax handler to create a new Livid slide from a pasted video URL.
     *
     * @since 3.114.0 Only creates slides in a slideshow the user can edit
     *
     * @return void
     */
    public function ajax_create_livid_slide()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_create_livid_slide')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                array(
                    'message' => __('Access denied. Sorry, you do not have permission to complete this task.', 'ml-slider')
                ),
                403
            );
        }

        if (! isset($_POST['slider_id']) || ! isset($_POST['url'])) {
            wp_send_json_error(
                array(
                    'message' => __('Bad request', 'ml-slider'),
                ),
                400
            );
        }

        $url = esc_url_raw(wp_unslash($_POST['url']));
        $slider_id = absint($_POST['slider_id']);
        metaslider_verify_slideshow_for_ajax($slider_id);

        $embed_html = $this->build_embed_html($url);

        if (false === $embed_html) {
            wp_send_json_error(array(
                'message' => __('Please enter a valid link to a video hosted on livid.com.', 'ml-slider')
            ), 409);
        }

        $slide_id = $this->insert_slide(0, 'livid', $slider_id);
        if (is_wp_error($slide_id)) {
            wp_send_json_error(array(
                'message' => $slide_id->get_error_message()
            ), 409);
        }

        $this->set_slide($slide_id);
        $this->set_slider($slider_id);

        $this->add_or_update_or_delete_meta($slide_id, 'livid_url', $url);

        $thumbnail_id = $this->sideload_thumbnail($url);
        if ($thumbnail_id) {
            set_post_thumbnail($slide_id, $thumbnail_id);
        }

        $this->tag_slide_to_slider();

        wp_send_json_success(array('slide_id' => $slide_id, 'html' => $this->get_admin_slide()), 200);
    }

    /**
     * Ajax handler that previews a pasted URL's embed, without creating a
     * slide - lets the "Add Slide" iframe show a live preview as the user
     * types, the same way the Vimeo/YouTube slide types do.
     *
     * @return void
     */
    public function ajax_preview_embed()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_create_livid_slide')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                array(
                    'message' => __('Access denied. Sorry, you do not have permission to complete this task.', 'ml-slider')
                ),
                403
            );
        }

        if (! isset($_POST['url'])) {
            wp_send_json_error(array('message' => __('Bad request', 'ml-slider')), 400);
        }

        $url = esc_url_raw(wp_unslash($_POST['url']));

        $embed_html = $this->build_embed_html($url);

        if (false === $embed_html) {
            wp_send_json_error(array(
                'message' => __('Please enter a valid link to a video hosted on livid.com.', 'ml-slider')
            ), 409);
        }

        wp_send_json_success(array('html' => $embed_html), 200);
    }

    /**
     * Ajax handler for editing an existing slide's video URL in the admin.
     *
     * Stores the new URL and refreshes the slide's thumbnail as soon as the URL
     * field changes, rather than waiting for the whole slideshow to be saved -
     * the same immediate-feedback flow MetaSlider Pro's YouTube slide type uses
     * (wp_ajax_update_youtube_thumbnail).
     *
     * @since 3.113.0
     *
     * @return void
     */
    public function ajax_update_embed()
    {
        if (! isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'metaslider_update_livid_embed')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                array(
                    'message' => __('Access denied. Sorry, you do not have permission to complete this task.', 'ml-slider')
                ),
                403
            );
        }

        if (! isset($_POST['slide_id']) || ! isset($_POST['url'])) {
            wp_send_json_error(array('message' => __('Bad request', 'ml-slider')), 400);
        }

        $slide_id = absint($_POST['slide_id']);
        $url = esc_url_raw(wp_unslash($_POST['url']));

        if (! $slide_id || 'ml-slide' !== get_post_type($slide_id)) {
            wp_send_json_error(array('message' => __('Bad request', 'ml-slider')), 400);
        }

        // Leave the slide's existing URL alone if the new one isn't a video
        // link, so a typo can't blank out a working video.
        if (false === $this->build_embed_html($url)) {
            wp_send_json_error(array(
                'message' => __('Please enter a valid link to a video hosted on livid.com.', 'ml-slider')
            ), 409);
        }

        $this->set_slide($slide_id);
        $this->add_or_update_or_delete_meta($slide_id, 'livid_url', $url);

        $response = array('slide_id' => $slide_id);

        $thumbnail_id = $this->sideload_thumbnail($url);
        if ($thumbnail_id) {
            set_post_thumbnail($slide_id, $thumbnail_id);

            $response['thumbnail_url_small'] = $this->get_intermediate_image_src(240, $thumbnail_id);
            $response['thumbnail_url_medium'] = $this->get_intermediate_image_src(768, $thumbnail_id);
            $response['thumbnail_url_large'] = $this->get_intermediate_image_src(1024, $thumbnail_id);
        }

        wp_send_json_success($response, 200);
    }

    /**
     * Pull the video ID out of a livid.com video URL.
     *
     * The same approach MetaSlider Pro's YouTube slide type takes: the ID is
     * all we need to build a player, so it is read back out of the stored URL
     * on demand rather than kept as its own postmeta.
     *
     * @since 3.113.0
     *
     * @param string $url A livid.com video URL.
     * @return string|false The video ID, or false if the URL isn't one.
     */
    private function get_video_id($url)
    {
        if (! $this->is_allowed_livid_url($url)) {
            return false;
        }

        $path = (string) wp_parse_url($url, PHP_URL_PATH);

        if (! preg_match('#^/(?:watch|embed)/([A-Za-z0-9_-]+)/?$#', $path, $matches)) {
            return false;
        }

        return $matches[1];
    }

    /**
     * Build the player markup for a Livid video URL.
     *
     * Built here in PHP from the URL, rather than stored as its own postmeta,
     * so a slide has one source of truth and markup can never go stale against
     * the URL it came from. This is how MetaSlider Pro's YouTube slide type
     * works too - it keeps only 'ml-slider_youtube_url' and derives the player
     * from it at render time.
     *
     * @since 3.113.0
     *
     * @param string $url A livid.com video URL.
     * @return string|false The embed HTML, or false if the URL isn't a video.
     */
    private function build_embed_html($url)
    {
        $video_id = $this->get_video_id($url);

        if (false === $video_id) {
            return false;
        }

        // "dnt" is Livid's own do-not-track parameter - on by default so an
        // embed doesn't track a site's visitors without them choosing to.
        $src = add_query_arg('dnt', 'true', 'https://livid.com/embed/' . rawurlencode($video_id));

        // Deliberately no width/height attributes: they may only hold integers,
        // and themes do arithmetic on them. Twenty Twenty-One's
        // responsive-embeds.js, for one, only touches an iframe that has both,
        // and caps it with max-height: (parentWidth / (width / height))px -
        // which with percentage values collapses the player to a sliver. Sizing
        // is the inline style's job (see apply_livid_settings()).
        return sprintf(
            '<iframe src="%1$s" style="width:100%%;height:100%%;border:0;" frameborder="0" allow="%2$s" allowfullscreen title="%3$s"></iframe>',
            esc_url($src),
            esc_attr('autoplay; fullscreen; picture-in-picture; encrypted-media'),
            /* translators: Livid is a video hosting service - keep the name as-is. */
            esc_attr__('Livid video player', 'ml-slider')
        );
    }

    /**
     * Apply this slide's player settings (mute, controls, autoplay, loop) to
     * the embed's iframe src, using Livid's own documented advanced embedding
     * parameters (support.livid.com/article/46-advanced-embedding-parameters).
     * Only autoplay, loop and muted work on every account there - the rest,
     * "controls" included, are ignored unless the video is hosted on a Livid
     * Pro or Premium account, which is why hiding the controls can look like
     * it does nothing.
     *
     * @param string $embed_html The <iframe> markup from build_embed_html().
     * @param array  $settings   This slide's settings (mute/controls/autoPlay/loop).
     * @return string The iframe markup with settings applied to its src.
     */
    private function apply_livid_settings($embed_html, $settings)
    {
        if (! preg_match('#src="([^"]+)"#i', $embed_html, $matches)) {
            return $embed_html;
        }

        $controls = ! (isset($settings['controls']) && 'off' === $settings['controls']);
        $loop = isset($settings['loop']) && 'on' === $settings['loop'];

        // Mute and autoplay are deliberately NOT passed in the URL. A
        // muted=true param locks the player muted - verified against a real
        // embed: after it, an unmute command (and the player's own control) is
        // ignored, so a visitor could never turn sound on. Applying mute
        // through the player.js API instead leaves the control working, and
        // playback started that way still counts as muted for the browser's
        // autoplay policy (also verified). livid-lazyload.js does both from
        // the data attributes below, which is how MetaSlider Pro's
        // YouTube/Vimeo slide types drive mute/play as well.
        // The src is HTML-escaped (build_embed_html() returns it through
        // esc_url(), which writes & as &#038;) - decode it first. add_query_arg()
        // parses the query with parse_str(), which would otherwise read
        // "&#038;dnt=1" as a parameter named "#038;dnt" and destroy the real
        // one, so any embed URL carrying more than one parameter would break.
        $src = wp_specialchars_decode($matches[1], ENT_QUOTES);

        $src = add_query_arg(
            array(
                'controls' => $controls ? 'true' : 'false',
                'loop' => $loop ? 'true' : 'false',
            ),
            $src
        );

        $embed_html = str_replace($matches[1], esc_url($src), $embed_html);

        // Drop percentage width/height attributes. They're invalid there (only
        // integers are allowed) and themes do arithmetic on them: Twenty
        // Twenty-One's responsive-embeds.js caps any iframe that has both with
        // max-height: (parentWidth / (width / height))px, which collapsed the
        // player to a 100px sliver. Only eager slides hit it, since a lazy
        // slide's iframe is injected after that script has already run.
        $embed_html = preg_replace('#\s(?:width|height)="\d+(?:\.\d+)?%"#i', '', $embed_html);

        // Size the iframe inline so it fills its wrapper without depending on
        // a stylesheet reaching the page - percentage attributes alone don't
        // reliably size an iframe either.
        if (! preg_match('#<iframe[^>]*\sstyle=#i', $embed_html)) {
            $embed_html = preg_replace(
                '#<iframe#i',
                '<iframe style="width:100%;height:100%;border:0;"',
                $embed_html,
                1
            );
        }

        return $embed_html;
    }

    /**
     * Fetch a video's thumbnail from its oEmbed data and sideload it into the
     * media library, so the admin slide list can show a real preview image
     * (matching MetaSlider Pro's Vimeo/YouTube slide types) instead of the
     * generic placeholder. Best-effort only - any failure here just leaves
     * the slide without a thumbnail, it never blocks slide creation/saving.
     *
     * @param string $url A livid.com video URL, already host-validated.
     * @return int|false The new attachment ID, or false on failure.
     */
    private function sideload_thumbnail($url)
    {
        if (! function_exists('_wp_oembed_get_object')) {
            return false;
        }

        $data = _wp_oembed_get_object()->get_data($url);

        if (! $data || empty($data->thumbnail_url)) {
            return false;
        }

        $thumbnail_url = esc_url_raw($data->thumbnail_url);
        $response = wp_safe_remote_get($thumbnail_url);

        if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        if (! $body) {
            return false;
        }

        $filename = 'livid-' . md5($url) . '.jpg';
        $upload = wp_upload_bits($filename, null, $body);

        if (! empty($upload['error'])) {
            return false;
        }

        $attachment_id = wp_insert_attachment(
            array(
                'post_title' => sprintf(
                    /* translators: Livid is a video hosting service - keep the name as-is. %s: Livid video URL */
                    __('Livid video thumbnail - %s', 'ml-slider'),
                    $url
                ),
                'post_mime_type' => 'image/jpeg',
                'post_status' => 'inherit',
            ),
            $upload['file']
        );

        if (is_wp_error($attachment_id) || ! $attachment_id) {
            return false;
        }

        if (! function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $upload['file']));

        return $attachment_id;
    }

    /**
     * Check whether a URL's host is one we trust for Livid embeds.
     *
     * @param string $url
     * @return bool
     */
    private function is_allowed_livid_url($url)
    {
        if (empty($url)) {
            return false;
        }

        $host = wp_parse_url($url, PHP_URL_HOST);

        return $host && in_array(strtolower($host), $this->allowed_hosts, true);
    }

    /**
     * Copy a slide (postmeta, terms, etc) to a new slide post.
     * Duplicated here (rather than reused from MetaImageSlide) since this
     * class isn't a subclass of it and the logic isn't type-specific.
     *
     * @param int $slideshow_id The id of the slider
     * @param int $slide_id     The id of the slide being duplicated
     * @return array|int The slide_id and html content, or 0 on failure
     */
    public function duplicate_slide($slideshow_id, $slide_id)
    {
        $old_slide = get_post($slide_id);
        if (!$old_slide) {
            return 0;
        }

        $title = $old_slide->post_title;
        $post_excerpt = isset($old_slide->post_excerpt) ? $old_slide->post_excerpt : '';

        $new_slide = array(
            'post_title' => $title,
            'post_name' => sanitize_title($title),
            'post_status' => 'publish',
            'post_type' => $old_slide->post_type,
            'post_excerpt' => $post_excerpt
        );
        $new_slide_id = wp_insert_post($new_slide);

        $slide_meta = get_post_custom($slide_id);
        foreach ($slide_meta as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_slide_id, $key, maybe_unserialize($value));
            }
        }

        $taxonomies = get_post_taxonomies($slide_id);
        foreach ($taxonomies as $taxonomy) {
            $term_ids = wp_get_object_terms($slide_id, $taxonomy, array('fields' => 'ids'));
            wp_set_object_terms($new_slide_id, $term_ids, $taxonomy);
        }

        $this->set_slide($new_slide_id);
        $this->set_slider($slideshow_id);

        return array('slide_id' => $new_slide_id, 'html' => $this->get_admin_slide());
    }

    /**
     * Ajax wrapper for duplicate_slide().
     *
     * @since 3.114.0 Only duplicates a slide the user can edit, into a slideshow they can edit
     *
     * @return void
     */
    public function ajax_duplicate_slide()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_duplicate_livid_slide')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                array(
                    'message' => __('Access denied', 'ml-slider')
                ),
                403
            );
        }

        if (! isset($_POST['slide_id']) || ! isset($_POST['slider_id'])) {
            wp_send_json_error(
                array(
                    'message' => __('Bad request', 'ml-slider'),
                ),
                400
            );
        }

        $slider_id = absint($_POST['slider_id']);
        $slide_id  = absint($_POST['slide_id']);
        metaslider_verify_slideshow_for_ajax($slider_id);
        metaslider_verify_slide_for_ajax($slide_id);

        $result = $this->duplicate_slide($slider_id, $slide_id);

        wp_send_json_success($result, 200);
    }

    /**
     * Return the HTML used to display this slide in the admin screen
     *
     * @return string|bool slide html
     */
    public function get_admin_slide()
    {
        if (! is_admin() && ! defined('REST_REQUEST') && ! defined('DOING_AJAX')) {
            return false;
        }

        /* translators: Livid is a video hosting service - keep the name as-is. */
        $slide_label = apply_filters('metaslider_livid_slide_label', esc_html__('Livid Video Slide', 'ml-slider'), $this->slide, $this->settings);

        ob_start();
        echo $this->get_delete_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_update_image_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_duplicate_slide_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_hide_slide_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        do_action('metaslider-slide-edit-buttons', 'livid', $this->slide->ID, $this->get_attachment_id());
        $edit_buttons = ob_get_clean();

        $row = "<tr id='slide-" . esc_attr($this->slide->ID) . "' class='slide livid flex responsive' data-slide-type='" . esc_attr($this->identifier) . "' data-attachment-id='" . esc_attr($this->get_attachment_id()) . "'>
                    <td class='col-1'>
                        <div class='metaslider-ui-controls ui-sortable-handle rtl:pl-0 rtl:pr-3'>
                        <h4 class='slide-details'>" .
                            apply_filters('metaslider_slide_details', '', $this->slide->ID) .
                            esc_html($slide_label) . " | ID: " .
                            esc_html($this->slide->ID) . "</h4>";
        if (metaslider_this_is_trash($this->slide)) {
            $row .= '<div class="row-actions trash-btns">';
            $row .= "<span class='untrash'>{$this->get_undelete_button_html()}</span>";
            $row .= ' | ';
            $row .= "<span class='delete'>{$this->get_permanent_delete_button_html()}</span>";
            $row .= '</div>';
        } else {
            $row .= $edit_buttons;
        }
        $row .=         "</div>
                    </td>
                    <td class='col-2'>" .
                    "<metaslider-slide id='" . esc_attr($this->slide->ID) . "' inline-template>
                        <div class='metaslider-ui-inner flex flex-col h-full'>
                            " . $this->get_admin_slide_tabs_html() . "
                            <input type='hidden' name='attachment[" . esc_attr($this->slide->ID) . "][type]' value='livid' />
                            <input type='hidden' class='menu_order' name='attachment[" . esc_attr($this->slide->ID) . "][menu_order]' value='" . esc_attr($this->slide->menu_order) . "' />
                        </div>
                    </metaslider-slide>
                    </td>
                </tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs()
    {
        $slide_id = absint($this->slide->ID);
        $livid_url = get_post_meta($slide_id, 'ml-slider_livid_url', true);
        $slide_settings = $this->slide_settings;

        ob_start();
        include METASLIDER_PATH . 'admin/views/slides/tabs/livid.php';
        $general_tab = ob_get_clean();

        $tabs = array(
            'general' => array(
                'title' => __('General', 'ml-slider'),
                'content' => $general_tab
            )
        );

        $global_settings = metaslider_global_settings();
        if (
            !isset($global_settings['mobileSettings']) ||
            (isset($global_settings['mobileSettings']) && true == $global_settings['mobileSettings'])
        ) {
            // A Livid slide is just the video embed - it has no caption to hide
            $show_hide_caption = false;

            ob_start();
            include METASLIDER_PATH . 'admin/views/slides/tabs/mobile.php';
            $mobile_tab = ob_get_clean();

            $tabs['mobile'] = array(
                'title' => __('Device', 'ml-slider'),
                'content' => $mobile_tab
            );
        }

        $tabs = $this->add_pro_upsell_tabs($tabs);

        return apply_filters('metaslider_livid_slide_tabs', $tabs, $this->slide, $this->slider, $this->settings);
    }

    /**
     * Save
     *
     * @param array $fields Fields to save
     */
    protected function save($fields)
    {
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));

        $settings = isset($fields['settings']) && is_array($fields['settings']) ? $fields['settings'] : array();
        foreach (array('mute', 'controls', 'autoPlay', 'loop', 'lazyLoad') as $setting) {
            if (! isset($settings[$setting])) {
                $settings[$setting] = 'off';
            }
        }
        $this->add_or_update_or_delete_meta($this->slide->ID, 'settings', $settings);

        $new_url = isset($fields['livid_url']) ? esc_url_raw($fields['livid_url']) : '';
        $current_url = get_post_meta($this->slide->ID, 'ml-slider_livid_url', true);

        // Only keep a URL we can actually build a player from, so a typo can't
        // blank out a working slide.
        if ($new_url && $new_url !== $current_url && false !== $this->get_video_id($new_url)) {
            $this->add_or_update_or_delete_meta($this->slide->ID, 'livid_url', $new_url);

            $thumbnail_id = $this->sideload_thumbnail($new_url);
            if ($thumbnail_id) {
                set_post_thumbnail($this->slide->ID, $thumbnail_id);
            }
        }

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_smartphone',
            isset($fields['hide_slide_smartphone']) && $fields['hide_slide_smartphone'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_tablet',
            isset($fields['hide_slide_tablet']) && $fields['hide_slide_tablet'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_laptop',
            isset($fields['hide_slide_laptop']) && $fields['hide_slide_laptop'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_desktop',
            isset($fields['hide_slide_desktop']) && $fields['hide_slide_desktop'] === 'on'
        );

        update_post_meta(
            $this->slide->ID,
            '_meta_slider_slide_is_hidden',
            isset($fields['hide_slide']) && $fields['hide_slide'] === 'on'
        );
    }

    /**
     * Build a click-to-play placeholder (poster image + play button) that
     * only loads the real iframe once a visitor clicks it, matching what
     * MetaSlider Pro's YouTube/Vimeo slide types call "lazy load" - the
     * video never loads at all until someone actually wants to watch it.
     *
     * @param string $embed_html The real <iframe> markup to load on click.
     * @return string The placeholder markup.
     */
    private function build_lazy_placeholder($embed_html)
    {
        if (! self::$lazyload_styles_enqueued) {
            self::$lazyload_styles_enqueued = true;
            wp_register_style('metaslider-livid-lazyload', false, array(), METASLIDER_ASSETS_VERSION);
            wp_enqueue_style('metaslider-livid-lazyload');
            wp_add_inline_style(
                'metaslider-livid-lazyload',
                '.ms-livid-lazy-wrap{display:block;width:100%;height:100%}' .
                '.ms-livid-lazy-wrap iframe{width:100%;height:100%}' .
                '.ms-livid-lazy{position:relative;width:100%;height:100%;cursor:pointer;overflow:hidden}' .
                '.ms-livid-lazy img.msDefaultImage{display:block;width:100%;height:100%;object-fit:cover;object-position:center}' .
                '.ms-livid-play-button{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;align-items:center;justify-content:center;width:100px;height:100px;background:#4e48f9;border:none;border-radius:50%;cursor:pointer;padding:0;transition:background-color .15s ease-in-out;opacity:0.9}' .
                '.ms-livid-play-button:hover,.ms-livid-play-button:focus{background:#3d38c7;opacity:1}' .
                '.ms-livid-play-button:before{content:"";width:0;height:0;border-style:solid;border-width:20px 0 20px 34px;border-color:transparent transparent transparent #fff;margin-left:6px}'
            );
        }

        $poster = $this->get_intermediate_image_src(1024);

        // The wrap element stays put once played too - clicking the button
        // replaces this placeholder with the real iframe, and from then on
        // livid-lazyload.js pauses/resumes that same iframe in place rather
        // than ever bringing this placeholder back.
        // Sizing is inline rather than left to the stylesheet: the embed that
        // replaces this placeholder on click fills this wrap, so if the wrap
        // had no real height the player would render at its tiny intrinsic
        // size instead.
        // A real <img>, not a CSS background: every other slide type's content
        // image carries .msDefaultImage, which is what Image Styles and the
        // metaslider_flex_slider_image_attributes filter key off (#2465)
        $image = $this->build_image_tag(apply_filters(
            'metaslider_flex_slider_image_attributes',
            array(
                'src' => $poster,
                'alt' => get_post_meta($this->slide->ID, '_wp_attachment_image_alt', true),
                'class' => 'msDefaultImage',
            ),
            array('id' => $this->slide->ID),
            $this->slider->ID
        ));

        return sprintf(
            '<div class="ms-livid-lazy-wrap" style="display:block;width:100%%;height:100%%;" data-embed="%1$s"%2$s>
                <div class="ms-livid-lazy" style="position:relative;width:100%%;height:100%%;">
                    %3$s
                    <button type="button" class="ms-livid-play-button" aria-label="%4$s"></button>
                </div>
            </div>',
            esc_attr($embed_html),
            $this->player_data_attributes(),
            $image,
            esc_attr__('Play video', 'ml-slider')
        );
    }

    /**
     * Data attributes livid-lazyload.js reads to drive the player over its
     * API: whether this slide starts muted, and whether it plays itself once
     * it becomes the active slide.
     *
     * Mute is implied by Auto Play, because browsers only allow unattended
     * playback while muted. Applying it over the API rather than through the
     * embed URL is what keeps the player's own mute control usable - a
     * muted=true URL param locks it muted for good.
     *
     * @return string
     */
    private function player_data_attributes()
    {
        $autoplay = isset($this->slide_settings['autoPlay']) && 'on' === $this->slide_settings['autoPlay'];
        $mute = (isset($this->slide_settings['mute']) && 'on' === $this->slide_settings['mute']) || $autoplay;

        return ($mute ? ' data-mute="1"' : '') . ($autoplay ? ' data-autoplay-on-active="1"' : '');
    }

    /**
     * Returns the HTML for the public slide
     *
     * @return string slide html
     */
    protected function get_public_slide()
    {
        $url = get_post_meta($this->slide->ID, 'ml-slider_livid_url', true);
        $embed_html = $url ? $this->build_embed_html($url) : false;

        if (empty($embed_html)) {
            return '';
        }

        // Needed for both lazy (click-to-play) and eager slides - pause/resume
        // (and, on Flex, autoplay triggering) both rely on it.
        wp_enqueue_script(
            'metaslider-livid-lazyload',
            METASLIDER_ASSETS_URL . 'metaslider/livid-lazyload' . metaslider_asset_suffix() . '.js',
            array('jquery'),
            METASLIDER_ASSETS_VERSION,
            true
        );

        $embed_html = $this->apply_livid_settings($embed_html, $this->slide_settings);

        $lazy_load = ! isset($this->slide_settings['lazyLoad']) || 'on' === $this->slide_settings['lazyLoad'];
        if ($lazy_load) {
            $embed_html = $this->build_lazy_placeholder($embed_html);
        }

        $device = array('smartphone', 'tablet', 'laptop', 'desktop');
        $mobile_class = '';
        foreach ($device as $value) {
            $hidden_slide = get_post_meta($this->slide->ID, 'ml-slider_hide_slide_' . $value, true);
            if (!empty($hidden_slide)) {
                $mobile_class .= 'hidden_' . $value . ' ';
            }
        }

        // The slideshow's own width and height, the same pair the other video
        // slide types use, so a Livid slide is exactly as tall as they are and
        // does not grow the slideshow. Falls back to 16:9 with no usable size.
        $width = isset($this->settings['width']) ? (int) $this->settings['width'] : 0;
        $height = isset($this->settings['height']) ? (int) $this->settings['height'] : 0;
        $ratio = ($width > 0 && $height > 0) ? ($height / $width) * 100 : 9 / 16 * 100;

        // Eager (non-lazy) slides render the real iframe immediately, so
        // there's no placeholder to pause back to - mark the wrapper instead.
        // livid-lazyload.js pauses/resumes it in place (over the player.js
        // API) once its slide stops/starts being active, and reads the same
        // data attributes to apply mute and auto play.
        $inner_class = $lazy_load ? '' : ' class="ms-livid-eager"' . $this->player_data_attributes();

        // Same wrapper markup the other video slide types use (div.youtube inside
        // div.ms-img-inner), so themes that lay slides out in a grid row place a
        // Livid video the same way. Themes style .ms-img-inner, so keep both
        // classes if this markup ever changes.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- iframe markup this class builds itself from a host-validated URL (see build_embed_html()); escaping would strip the <iframe>.
        $embed_wrap = '<div class="ms-img-inner">' .
            '<div class="livid" style="position:relative;padding-bottom:' . esc_attr($ratio) . '%;height:0;overflow:hidden;">' .
                '<div' . $inner_class . ' style="position:absolute;top:0;left:0;width:100%;height:100%;">' . $embed_html . '</div>' .
            '</div>' .
        '</div>';

        switch ($this->settings['type']) {
            case 'responsive':
                return apply_filters('metaslider_livid_responsive_slider_markup', $embed_wrap, $this->slide, $this->settings);
            case 'flex':
            default:
                $attributes = apply_filters('metaslider_livid_flex_slider_list_item_attributes', array(
                    'style' => 'display: none; width: 100%;',
                    'class' => "slide-{$this->slide->ID} ms-livid {$mobile_class}",
                    'aria-roledescription' => 'slide',
                    'data-date' => $this->slide->post_date,
                    'data-slide-type' => $this->identifier
                ), $this->slide, $this->settings);

                // Pro's Advanced settings (repeat, first loop, custom CSS classes) hook this
                // shared filter, so they reach this slide type too - it reads the attributes
                // back out of the rendered markup, and Livid's iframe carries no id to clash
                $attributes = apply_filters(
                    'metaslider_flex_slider_li_attributes',
                    $attributes,
                    $this->slide->ID,
                    isset($this->slider->ID) ? $this->slider->ID : 0,
                    $this->settings
                );

                $li = '<li';
                foreach ($attributes as $att => $val) {
                    if (strlen($val)) {
                        $li .= ' ' . $att . '="' . esc_attr($val) . '"';
                    }
                }
                $li .= '>' . $embed_wrap . '</li>';

                return apply_filters('metaslider_livid_flex_slider_markup', $li, $this->slide, $this->settings);
        }
    }
}
