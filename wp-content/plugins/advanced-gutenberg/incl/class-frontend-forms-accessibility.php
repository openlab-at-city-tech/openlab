<?php

defined('ABSPATH') || die;

if (! class_exists('AdvancedGutenbergFormsA11y')) {
    /**
     * Front-end accessibility enhancements applied at render time,
     * so saved block markup is never changed.
     *
     * Enhancements that need WP_HTML_Tag_Processor (WordPress 6.2+) are skipped on older versions.
     */
    class AdvancedGutenbergFormsA11y
    {
        /**
         * Readable names for the social-links icon slugs
         *
         * @var array
         */
        private $social_networks = [
            'blogger'    => 'Blogger',
            'facebook'   => 'Facebook',
            'flickr'     => 'Flickr',
            'instagram'  => 'Instagram',
            'linkedin'   => 'LinkedIn',
            'picasa'     => 'Picasa',
            'pinterest'  => 'Pinterest',
            'reddit'     => 'Reddit',
            'skype'      => 'Skype',
            'soundcloud' => 'SoundCloud',
            'tumblr'     => 'Tumblr',
            'twitter'    => 'Twitter',
            'vimeo'      => 'Vimeo',
            'youtube'    => 'YouTube',
        ];

        public function __construct()
        {
            add_filter('render_block', [ $this, 'filterBlock' ], 10, 2);
        }

        /**
         * Dispatch render_block to the matching block handler
         *
         * @param string $content Rendered block HTML
         * @param array  $block   Parsed block
         *
         * @return string
         */
        public function filterBlock($content, $block)
        {
            if (
                empty($content)
                || empty($block['blockName'])
                || strpos($block['blockName'], 'advgb/') !== 0
                || is_admin()
                || ( defined('REST_REQUEST') && REST_REQUEST )
            ) {
                return $content;
            }

            switch ($block['blockName']) {
                case 'advgb/summary':
                    return $this->summary($content);
            }

            if (! class_exists('WP_HTML_Tag_Processor')) {
                return $content;
            }

            switch ($block['blockName']) {
                case 'advgb/social-links':
                    return $this->socialLinks($content, $block);
                case 'advgb/icon':
                    return $this->icons($content);
                case 'advgb/button':
                case 'advgb/infobox':
                    return $this->hideMaterialIcons($content);
                case 'advgb/list':
                    return $this->listRole($content);
                case 'advgb/search-bar':
                    return $this->hideSvgs($content);
            }

            return $content;
        }

        /**
         * Expose the table of contents as a navigation landmark
         *
         * @param string $content
         *
         * @return string
         */
        private function summary($content)
        {
            $label   = esc_attr__('Table of contents', 'advanced-gutenberg');
            $trimmed = ltrim($content);

            // Minimized layout: <div class="align..."><div class="advgb-toc-header">...</div><ul class="advgb-toc">
            if (preg_match('/^<div\b(?![^>]*\brole=)/i', $trimmed)) {
                return preg_replace('/^(\s*<div\b)/i', '$1 role="navigation" aria-label="' . $label . '"', $content, 1);
            }

            // Default layout: the <ul> is the root element. It is left as is, because a wrapper
            // would break theme alignment rules that target the block's direct children.
            return $content;
        }

        /**
         * Name each social link and hide its decorative icon
         *
         * @param string $content
         * @param array  $block
         *
         * @return string
         */
        private function socialLinks($content, $block)
        {
            $items = isset($block['attrs']['items']) && is_array($block['attrs']['items'])
                ? array_values($block['attrs']['items'])
                : [];

            $tags  = new WP_HTML_Tag_Processor($content);
            $index = -1;
            while ($tags->next_tag()) {
                $tag = strtoupper($tags->get_tag());

                if ('A' === $tag && $this->hasClass($tags, 'advgb-social-icon')) {
                    $index++;
                    if (null !== $tags->get_attribute('aria-label')) {
                        continue;
                    }

                    $item = isset($items[ $index ]) && is_array($items[ $index ]) ? $items[ $index ] : [];
                    $name = '';
                    foreach ([ 'label', 'title' ] as $key) {
                        if (! empty($item[ $key ]) && is_string($item[ $key ])) {
                            $name = wp_strip_all_tags($item[ $key ]);
                            break;
                        }
                    }
                    if ('' === $name) {
                        $name = $this->socialNetworkName(
                            isset($item['icon']) ? (string) $item['icon'] : '',
                            (string) $tags->get_attribute('href')
                        );
                    }

                    $tags->set_attribute('aria-label', $this->withNewTabNotice($tags, $name));
                } elseif ('SVG' === $tag) {
                    $tags->set_attribute('aria-hidden', 'true');
                    $tags->set_attribute('focusable', 'false');
                } elseif ('IMG' === $tag) {
                    // The link carries the name
                    $tags->set_attribute('alt', '');
                }
            }

            return $tags->get_updated_html();
        }

        /**
         * Readable network name from the icon slug, else from the link
         *
         * @param string $icon Icon slug or custom image URL
         * @param string $href Link URL
         *
         * @return string
         */
        private function socialNetworkName($icon, $href)
        {
            if ('mail' === $icon || 0 === stripos($href, 'mailto:')) {
                return __('Email', 'advanced-gutenberg');
            }

            if (isset($this->social_networks[ $icon ])) {
                return $this->social_networks[ $icon ];
            }

            // Unknown slug (not a custom image URL)
            if ('' !== $icon && preg_match('/^[a-z0-9_-]+$/i', $icon)) {
                return ucfirst($icon);
            }

            // Custom image or no icon: try the link host, e.g. www.facebook.com => Facebook
            $host = wp_parse_url($href, PHP_URL_HOST);
            if ($host) {
                $parts = explode('.', preg_replace('/^www\./i', '', strtolower($host)));
                $slug  = $parts[0];

                return isset($this->social_networks[ $slug ]) ? $this->social_networks[ $slug ] : ucfirst($slug);
            }

            return __('Social link', 'advanced-gutenberg');
        }

        /**
         * Hide icon glyphs and name linked icons
         *
         * @param string $content
         *
         * @return string
         */
        private function icons($content)
        {
            $tags = new WP_HTML_Tag_Processor($content);
            while ($tags->next_tag()) {
                $tag = strtoupper($tags->get_tag());

                if ('A' === $tag && null === $tags->get_attribute('aria-label')) {
                    $title = trim((string) $tags->get_attribute('title'));
                    $name  = '' !== $title ? $title : __('Icon link', 'advanced-gutenberg');
                    $tags->set_attribute('aria-label', $this->withNewTabNotice($tags, $name));
                } elseif ('I' === $tag && $this->hasClass($tags, 'material-icons', true)) {
                    $tags->set_attribute('aria-hidden', 'true');
                }
            }

            return $tags->get_updated_html();
        }

        /**
         * Hide decorative material icon glyphs (their text is the ligature name)
         *
         * @param string $content
         *
         * @return string
         */
        private function hideMaterialIcons($content)
        {
            $tags = new WP_HTML_Tag_Processor($content);
            while ($tags->next_tag('i')) {
                if ($this->hasClass($tags, 'material-icons', true)) {
                    $tags->set_attribute('aria-hidden', 'true');
                }
            }

            return $tags->get_updated_html();
        }

        /**
         * Restore list semantics removed by list-style: none on custom icon lists
         *
         * @param string $content
         *
         * @return string
         */
        private function listRole($content)
        {
            $tags = new WP_HTML_Tag_Processor($content);
            while ($tags->next_tag('ul')) {
                if ($this->hasClass($tags, 'advgb-list') && null === $tags->get_attribute('role')) {
                    $tags->set_attribute('role', 'list');
                }
                break;
            }

            return $tags->get_updated_html();
        }

        /**
         * Hide decorative SVG icons
         *
         * @param string $content
         *
         * @return string
         */
        private function hideSvgs($content)
        {
            $tags = new WP_HTML_Tag_Processor($content);
            while ($tags->next_tag('svg')) {
                $tags->set_attribute('aria-hidden', 'true');
                $tags->set_attribute('focusable', 'false');
            }

            return $tags->get_updated_html();
        }

        /**
         * Append the new tab notice when the link opens in a new tab
         *
         * @param WP_HTML_Tag_Processor $tags
         * @param string                $name
         *
         * @return string
         */
        private function withNewTabNotice($tags, $name)
        {
            if ('_blank' === strtolower((string) $tags->get_attribute('target'))) {
                /* translators: %s: link name */
                return sprintf(__('%s (opens in a new tab)', 'advanced-gutenberg'), $name);
            }

            return $name;
        }

        /**
         * Class check that works with WP_HTML_Tag_Processor from WordPress 6.2
         *
         * @param WP_HTML_Tag_Processor $tags
         * @param string                $class_name
         * @param bool                  $prefix     Also match classes starting with $class_name (e.g. material-icons-outlined)
         *
         * @return bool
         */
        private function hasClass($tags, $class_name, $prefix = false)
        {
            $classes = preg_split('/\s+/', (string) $tags->get_attribute('class'), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($classes as $class) {
                if ($class === $class_name || ( $prefix && 0 === strpos($class, $class_name) )) {
                    return true;
                }
            }

            return false;
        }
    }

    new AdvancedGutenbergFormsA11y();
}
