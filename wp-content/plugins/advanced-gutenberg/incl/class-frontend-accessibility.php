<?php
/**
 * Front-end accessibility enhancements applied at render time.
 *
 * Nothing here changes saved block markup or attribute defaults, so existing
 * posts keep validating in the editor. Markup tweaks use WP_HTML_Tag_Processor
 * (WordPress 6.2+) and are skipped on older versions.
 *
 * @package PublishPress Blocks
 */

defined('ABSPATH') || die;

if (! class_exists('AdvancedGutenbergFrontendAccessibility')) {
    /**
     * Class AdvancedGutenbergFrontendAccessibility
     */
    class AdvancedGutenbergFrontendAccessibility
    {
        /**
         * Script handle of the shared slider/lightbox helper.
         */
        const HELPER_HANDLE = 'advgb_frontend_a11y';

        /**
         * Handles whose presence means the helper is needed.
         *
         * @var string[]
         */
        private static $helper_triggers = [
            'slick_js',
            'colorbox_js',
        ];

        /**
         * Whether the helper has been enqueued during this request.
         *
         * @var bool
         */
        private static $helper_enqueued = false;

        /**
         * Register hooks.
         *
         * @return void
         */
        public static function init()
        {
            if (is_admin()) {
                return;
            }

            add_filter('render_block', [__CLASS__, 'filterRenderBlock'], 20, 2);
        }

        /**
         * Whether the HTML tag processor is available (WordPress 6.2+).
         *
         * @return bool
         */
        private static function canProcessTags()
        {
            return class_exists('WP_HTML_Tag_Processor');
        }

        /**
         * Main render_block callback.
         *
         * @param string $block_content Rendered block HTML.
         * @param array  $block         Parsed block.
         *
         * @return string
         */
        public static function filterRenderBlock($block_content, $block)
        {
            if (! is_string($block_content) || $block_content === '' || empty($block['blockName'])) {
                return $block_content;
            }

            $attrs = isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : [];

            switch ($block['blockName']) {
                case 'advgb/images-slider':
                    $block_content = self::imagesSliderAlt($block_content, $attrs);
                    break;

                case 'advgb/video':
                    $block_content = self::videoTitles($block_content, $attrs);
                    break;

                case 'advgb/image':
                    $block_content = self::imageLinkName($block_content, $attrs);
                    break;
            }

            // Asset loading for these blocks happens in render_block_data, before this filter.
            if (strpos($block['blockName'], 'advgb/') === 0 || $block['blockName'] === 'core/gallery') {
                self::maybeEnqueueHelper();
            }

            return $block_content;
        }

        /**
         * Use each image's Media Library alt text instead of its slide title.
         *
         * @param string $html  Block HTML.
         * @param array  $attrs Block attributes.
         *
         * @return string
         */
        private static function imagesSliderAlt($html, $attrs)
        {
            if (! self::canProcessTags() || empty($attrs['images']) || ! is_array($attrs['images'])) {
                return $html;
            }

            $images = array_values($attrs['images']);
            $tags   = new WP_HTML_Tag_Processor($html);
            $index  = 0;

            while ($tags->next_tag(['tag_name' => 'IMG', 'class_name' => 'advgb-image-slider-img'])) {
                $image = isset($images[$index]) && is_array($images[$index]) ? $images[$index] : [];
                $index++;

                if (empty($image['id'])) {
                    continue;
                }

                $alt = get_post_meta((int) $image['id'], '_wp_attachment_image_alt', true);
                $alt = is_string($alt) ? trim(wp_strip_all_tags($alt)) : '';

                if ($alt !== '') {
                    $tags->set_attribute('alt', $alt);
                }
            }

            return $tags->get_updated_html();
        }

        /**
         * Give embedded video iframes an accessible title and expose the video
         * title to the lightbox script.
         *
         * @param string $html  Block HTML.
         * @param array  $attrs Block attributes.
         *
         * @return string
         */
        private static function videoTitles($html, $attrs)
        {
            if (! self::canProcessTags()) {
                return $html;
            }

            $video_title = isset($attrs['videoTitle']) && is_string($attrs['videoTitle'])
                ? trim(wp_strip_all_tags($attrs['videoTitle']))
                : '';

            $tags = new WP_HTML_Tag_Processor($html);

            // The first tag is the block wrapper; it carries the lightbox class when enabled.
            if ($tags->next_tag()) {
                $class = $tags->get_attribute('class');
                if (
                    $video_title !== ''
                    && is_string($class)
                    && preg_match('/(^|\s)advgb-video-lightbox(\s|$)/', $class)
                ) {
                    $tags->set_attribute('data-advgb-video-title', $video_title);
                }
            }

            while ($tags->next_tag('IFRAME')) {
                $current = $tags->get_attribute('title');
                if (! is_string($current) || trim($current) === '') {
                    $tags->set_attribute(
                        'title',
                        $video_title !== '' ? $video_title : __('Video', 'advanced-gutenberg')
                    );
                }
            }

            return $tags->get_updated_html();
        }

        /**
         * The URL-mode overlay link of the Image block is empty: name it.
         *
         * @param string $html  Block HTML.
         * @param array  $attrs Block attributes.
         *
         * @return string
         */
        private static function imageLinkName($html, $attrs)
        {
            if (! self::canProcessTags()) {
                return $html;
            }

            if (empty($attrs['openOnClick']) || $attrs['openOnClick'] !== 'url' || empty($attrs['openUrl'])) {
                return $html;
            }

            $label = '';
            foreach (['imageAlt', 'title', 'subtitle'] as $key) {
                if (! empty($attrs[ $key ]) && is_string($attrs[ $key ])) {
                    $label = trim(wp_strip_all_tags($attrs[ $key ]));
                    if ($label !== '') {
                        break;
                    }
                }
            }

            if ($label === '') {
                $label = __('Open link', 'advanced-gutenberg');
            }

            $tags = new WP_HTML_Tag_Processor($html);

            while ($tags->next_tag(['tag_name' => 'A', 'class_name' => 'advgb-image-overlay'])) {
                if ($tags->get_attribute('href') === null || $tags->get_attribute('aria-label') !== null) {
                    continue;
                }

                $link_label = $label;
                if ($tags->get_attribute('target') === '_blank') {
                    /* translators: %s: link text */
                    $link_label = sprintf(__('%s (opens in a new tab)', 'advanced-gutenberg'), $label);
                }

                $tags->set_attribute('aria-label', $link_label);
            }

            return $tags->get_updated_html();
        }

        /**
         * Enqueue the shared slider/lightbox helper once a slider or lightbox
         * script has been enqueued by a rendered block.
         *
         * @return void
         */
        private static function maybeEnqueueHelper()
        {
            if (self::$helper_enqueued) {
                return;
            }

            $needed = false;
            foreach (self::$helper_triggers as $handle) {
                if (wp_script_is($handle, 'enqueued')) {
                    $needed = true;
                    break;
                }
            }

            if (! $needed) {
                return;
            }

            self::$helper_enqueued = true;

            wp_enqueue_script(
                self::HELPER_HANDLE,
                ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/js/frontend-a11y.js',
                ['jquery'],
                ADVANCED_GUTENBERG_VERSION
            );

            wp_localize_script(self::HELPER_HANDLE, 'advgbA11yL10n', self::strings());
        }

        /**
         * Translatable strings used by the front-end scripts.
         *
         * @return array
         */
        public static function strings()
        {
            return [
                'slideshow'            => __('Slideshow', 'advanced-gutenberg'),
                /* translators: aria-roledescription of a slider region */
                'carousel'             => __('carousel', 'advanced-gutenberg'),
                /* translators: aria-roledescription of a single slide */
                'slide'                => __('slide', 'advanced-gutenberg'),
                /* translators: 1: slide number, 2: total number of slides */
                'slideOf'              => __('%1$s of %2$s', 'advanced-gutenberg'),
                /* translators: %s: slide number */
                'goToSlide'            => __('Go to slide %s', 'advanced-gutenberg'),
                'previousSlide'        => __('Previous slide', 'advanced-gutenberg'),
                'nextSlide'            => __('Next slide', 'advanced-gutenberg'),
                'pauseSlideshow'       => __('Pause slideshow', 'advanced-gutenberg'),
                'testimonials'         => __('Testimonials', 'advanced-gutenberg'),
                'previousTestimonials' => __('Previous testimonials', 'advanced-gutenberg'),
                'nextTestimonials'     => __('Next testimonials', 'advanced-gutenberg'),
                'recentPosts'          => __('Recent posts', 'advanced-gutenberg'),
                'products'             => __('Products', 'advanced-gutenberg'),
                /* translators: %s: image title */
                'openImage'            => __('Open image: %s', 'advanced-gutenberg'),
                'openImageNoTitle'     => __('Open image', 'advanced-gutenberg'),
                'openLink'             => __('Open link', 'advanced-gutenberg'),
                'newTab'               => __('(opens in a new tab)', 'advanced-gutenberg'),
                /* translators: %s: image title */
                'enlargeImage'         => __('Enlarge image: %s', 'advanced-gutenberg'),
                'enlargeImageNoTitle'  => __('Enlarge image', 'advanced-gutenberg'),
                /* translators: %s: video title */
                'playVideo'            => __('Play video: %s', 'advanced-gutenberg'),
                'playVideoNoTitle'     => __('Play video', 'advanced-gutenberg'),
                'imageViewer'          => __('Image viewer', 'advanced-gutenberg'),
                'videoPlayer'          => __('Video player', 'advanced-gutenberg'),
                'video'                => __('Video', 'advanced-gutenberg'),
                'close'                => __('Close', 'advanced-gutenberg'),
                'previous'             => __('Previous', 'advanced-gutenberg'),
                'next'                 => __('Next', 'advanced-gutenberg'),
                /* translators: {current} and {total} are placeholders, keep them as is */
                'imageCurrent'         => __('Image {current} of {total}', 'advanced-gutenberg'),
            ];
        }
    }

    AdvancedGutenbergFrontendAccessibility::init();
}
